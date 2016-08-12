<?php

    include_once(ROOT."src/database/DatabaseIO.php");
    include_once(ROOT."src/database/Personne.php");
    include_once(ROOT."src/database/Relation.php");
    include_once(ROOT."src/database/Condition.php");

    class Acte implements DatabaseIO{

        var $id;

        var $contenu;
        var $epoux;
        var $epouse;
        var $temoins;
        var $parrains;
        var $source_id;
        var $date_start;
        var $date_end;

        function __construct($id = NULL, $contenu = NULL){
            $this->id = $id;
            $this->source_id = SOURCE_DEFAULT_ID;
            $this->contenu = NULL;
            $this->epoux = NULL;
            $this->epouse = NULL;
            $this->temoins = array();
            $this->parrains = array();
            $this->date_start = NULL;
            $this->date_end = NULL;
        }

        function set_contenu($contenu){
            $this->contenu = $contenu;
        }

        function set_epoux($epoux){
            $this->epoux = $epoux;
        }

        function set_epouse($epouse){
            $this->epouse = $epouse;
        }

        function set_date($date){
            $dates = read_date($date);
            $this->date_start = $dates[0];
            $this->date_end = $dates[1];
        }

        function add_temoin($temoin){
            $this->temoins[] = $temoin;
        }

        function add_parrain($parrain){
            $this->parrains[] = $parrain;
        }

        function recursive_link_conditions_and_relations($personne){
            global $mysqli;
            
            if(isset($personne->id) && $personne->is_valid()){
                foreach($personne->relations as $relation)
                    $mysqli->into_db_acte_has_relation($this, $relation);

                foreach($personne->conditions as $condition)
                    $mysqli->into_db_acte_has_condition($this, $condition);

                if(isset($personne->pere))
                    $this->recursive_link_conditions_and_relations($personne->pere);
                if(isset($personne->mere))
                    $this->recursive_link_conditions_and_relations($personne->mere);
            }
        }


        // DATABASE IO

        public function get_table_name(){
            return "acte";
        }

        public function get_same_values(){
            return [];
        }

        public function result_from_db($row){
            if($row == NULL)
                return;

            $this->id = $row["id"];
            if(isset($row["epoux"]))
                $this->set_epoux(new Personne($row["epoux"]));
            if(isset($row["epouse"]))
                $this->set_epouse(new Personne($row["epouse"]));
            if(isset($row["date_start"]))
                $this->date_start = $row["date_start"];
            if(isset($row["date_end"]))
                $this->date_end = $row["date_end"];
        }

        public function values_into_db(){
            $values = [];
            if(isset($this->epoux, $this->epoux->id))
                $values["epoux"] = $this->epoux->id;
            if(isset($this->epouse, $this->epouse->id))
                $values["epouse"] = $this->epouse->id;
            if(isset($this->date_start))
                $values["date_start"] = $this->date_start;
            if(isset($this->date_end))
                $values["date_end"] = $this->date_end;
            return $values;
        }

        public function pre_into_db(){
            global $mysqli, $log, $alert;

            if(!isset($this->id)){
                $alert->error("L'acte ne contient pas de num/id");
                $log->e("L'acte ne contient pas de num/id");
                return FALSE;
            }

            $valid_epoux = isset($this->epoux) && $this->epoux->is_valid();
            $valid_epouse = isset($this->epouse) && $this->epouse->is_valid();

            foreach($this->temoins as $temoin){
                if($temoin->is_valid()){
                    if($valid_epoux)
                        $temoin->add_relation($temoin, $this->epoux, STATUT_TEMOIN);
                    if($valid_epouse)
                        $temoin->add_relation($temoin, $this->epouse, STATUT_TEMOIN);
                }
            }

            foreach($this->parrains as $parrain){
                if($parrain->is_valid()){
                    if($valid_epoux)
                        $parrain->add_relation($parrain, $this->epoux, STATUT_PARRAIN);
                    if($valid_epouse)
                        $parrain->add_relation($parrain, $this->epouse, STATUT_PARRAIN);
                }
            }

            if($valid_epoux && $valid_epouse){
                $this->epouse->add_relation($this->epoux, $this->epouse, STATUT_EPOUX);
                $this->epouse->add_relation($this->epouse, $this->epoux, STATUT_EPOUSE);
            }

            if(isset($this->epoux))
                $mysqli->into_db($this->epoux);

            if(isset($this->epouse))
                $mysqli->into_db($this->epouse);

            foreach($this->temoins as $temoin)
                $mysqli->into_db($temoin);

            foreach($this->parrains as $parrain)
                $mysqli->into_db($parrain);

            return TRUE;
        }

        public function post_into_db(){
            global $mysqli;

            $personnes = [];
            if(isset($this->epoux))
                $personnes[] = $this->epoux;
            if(isset($this->epouse))
                $personnes[] = $this->epouse;
            $personnes = array_merge($personnes, $this->temoins, $this->parrains);

            $mysqli->start_transaction();
            foreach($personnes as $personne){
                $this->recursive_link_conditions_and_relations($personne);
            }

            $this->contenu_into_db();
            $mysqli->end_transaction();
        }

        function contenu_into_db(){
            global $mysqli;

            $contenu = $mysqli->real_escape_string($this->contenu);
            $values = [
                "acte_id" => $this->id,
                "contenu" => $contenu
            ];

            return $mysqli->insert(
                "acte_contenu",
                $values,
                " ON DUPLICATE KEY UPDATE contenu='$contenu'");
        }

        function get_conditions(){
            global $mysqli;
            $conditions = [];

            $result = $mysqli->query("
                SELECT *
                FROM acte_has_condition INNER JOIN `condition`
                ON acte_has_condition.condition_id = `condition`.id
                WHERE acte_has_condition.acte_id = $this->id
            ");
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $conditions[] = new Condition(
                        $row["id"],
                        $row["text"],
                        new Personne($row["personne_id"]),
                        $row["source_id"]
                    );
                }
            }
            return $conditions;
        }

        function get_relations(){
            global $mysqli;
            $relations = [];

            $result = $mysqli->query("
                SELECT *
                FROM acte_has_relation INNER JOIN relation
                ON acte_has_relation.relation_id = relation.id
                WHERE acte_has_relation.acte_id = $this->id
            ");
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $relations[] = new Relation(
                        $row["id"],
                        new Personne($row["pers_source_id"]),
                        new Personne($row["pers_destination_id"]),
                        $row["statut_id"]
                    );
                }
            }
            return $relations;
        }

        function get_contenu(){
            global $mysqli;

            $result = $mysqli->select("acte_contenu", ["contenu"], "acte_id='$this->id'");
            if($result != FALSE && $result->num_rows == 1){
                $row = $result->fetch_assoc();
                return $row["contenu"];
            }
            return "";
        }
    }

?>
