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
        var $date;

        function __construct($id = NULL, $contenu = NULL){
            $this->id = $id;
            $this->source_id = SOURCE_DEFAULT_ID;
            $this->contenu = NULL;
            $this->epoux = NULL;
            $this->epouse = NULL;
            $this->temoins = array();
            $this->parrains = array();
            $this->date = NULL;
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
            $this->date = $date;
        }

        function add_temoin($temoin){
            $this->temoins[] = $temoin;
        }

        function add_parrain($parrain){
            $this->parrains[] = $parrain;
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
            if(isset($row["date"]))
                $this->set_date($row["date"]);
        }

        public function values_into_db(){
            $values = [];
            if(isset($this->epoux, $this->epoux->id))
                $values["epoux"] = $this->epoux->id;
            if(isset($this->epouse, $this->epouse->id))
                $values["epouse"] = $this->epouse->id;
            if(isset($this->date))
                $values["date"] = $this->date;
            return $values;
        }

        public function pre_into_db(){
            global $mysqli, $log, $alert;

            if(!isset($this->id)){
                $alert->error("L'acte ne contient pas de num/id");
                $log->e("L'acte ne contient pas de num/id");
                return FALSE;
            }

            foreach($this->temoins as $temoin){
                if(isset($this->epoux))
                    $temoin->add_relation($temoin, $this->epoux, STATUT_TEMOIN);
                if(isset($this->epouse))
                    $temoin->add_relation($temoin, $this->epouse, STATUT_TEMOIN);
            }

            foreach($this->parrains as $parrain){
                if(isset($this->epoux))
                    $parrain->add_relation($parrain, $this->epoux, STATUT_PARRAIN);
                if(isset($this->epouse))
                    $parrain->add_relation($parrain, $this->epouse, STATUT_PARRAIN);
            }

            if(isset($this->epoux, $this->epouse)){
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

            foreach($personnes as $personne){
                foreach($personne->relations as $relation)
                    $mysqli->into_db_acte_has_relation($this, $relation);

                foreach($personne->conditions as $condition)
                    $mysqli->into_db_acte_has_condition($this, $condition);
            }

            $this->contenu_into_db();
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

            $result = $mysqli->select("acte_has_condition", ["condition_id"], "acte_id='$this->id'");
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $conditions[] = new Condition($row["id"]);
                }
            }
            return $conditions;
        }

        function get_relations(){
            global $mysqli;
            $relations = [];

            $result = $mysqli->select("acte_has_relation", ["relation_id"], "acte_id='$this->id'");
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $relations[] = new Relation($row["relation_id"]);
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
