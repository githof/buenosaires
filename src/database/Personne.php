<?php

    include_once(ROOT."src/database/DatabaseIO.php");
    include_once(ROOT."src/database/Database.php");
    include_once(ROOT."src/database/Nom.php");
    include_once(ROOT."src/database/Prenom.php");
    include_once(ROOT."src/database/Relation.php");
    include_once(ROOT."src/database/Condition.php");

    class Personne implements DatabaseIO{

        var $id;

        var $prenoms;
        var $noms;
        var $relations;
        var $conditions;

        var $pere;
        var $mere;

        var $xml;

        function __construct($id = NULL){
            $this->id = $id;
            $this->prenoms = [];
            $this->noms = [];
            $this->conditions = [];
            $this->relations = [];
            $this->pere = NULL;
            $this->mere = NULL;
        }

        public function add_prenom($prenom){
            foreach($this->prenoms as $_prenom){
                if($_prenom->id == $prenom->id || $_prenom->no_accent == $prenom->no_accent)
                    return;
            }
            $this->prenoms[] = $prenom;
        }

        public function add_nom($nom){
            foreach($this->noms as $_nom){
                if($_nom->id == $nom->id ||
                    ($_nom->no_accent == $nom->no_accent &&
                    (isset($_nom->attribut, $nom->attribut) && $_nom->attribut->value == $nom->attribut->value) || $_nom->attribut == $nom->attribut))
                    return;
            }
            $this->noms[] = $nom;
        }

        public function add_condition($text, $source_id){
            $this->conditions[] = new Condition(NULL, $text, $this, $source_id);
        }

        public function add_relation($personne_source, $personne_destination, $statut_id){
            $this->relations[] = new Relation(
                NULL,
                $personne_source,
                $personne_destination,
                $statut_id
            );
        }

        public function set_pere($pere){
            $this->pere = $pere;
        }

        public function set_mere($mere){
            $this->mere = $mere;
        }

        public function set_xml($xml){
            $this->xml = $xml;
        }

        public function is_valid(){
            return count($this->prenoms) > 0 || count($this->noms) > 0;
        }

        // DATABASE IO

        public function get_table_name(){
            return "personne";
        }

        public function get_same_values(){
            return [];
        }

        public function result_from_db($row){
            if($row == NULL)
                return;
            $this->id = $row["id"];
        }

        public function values_into_db(){
            return [];
        }

        public function pre_into_db(){
            global $mysqli;

            if(!$this->is_valid())
                return FALSE;

            if(isset($this->pere) && $this->pere->is_valid()){
                $mysqli->into_db($this->pere);
                $this->add_relation($this->pere, $this, STATUT_PERE);
            }

            if(isset($this->mere) && $this->mere->is_valid()){
                $mysqli->into_db($this->mere);
                $this->add_relation($this->mere, $this, STATUT_MERE);
            }

            foreach($this->prenoms as $prenom){
                $mysqli->into_db($prenom);
            }

            foreach($this->noms as $nom){
                $mysqli->into_db($nom);
            }

            return TRUE;
        }

        public function post_into_db(){
            global $mysqli;

            $i = 1;
            $mysqli->delete("prenom_personne", "personne_id='$this->id'");
            foreach($this->prenoms as $prenom){
                $mysqli->into_db_prenom_personne($this, $prenom, $i);
                $i++;
            }

            $i = 1;
            $mysqli->delete("nom_personne", "personne_id='$this->id'");
            foreach($this->noms as $nom){
                $mysqli->into_db_nom_personne($this, $nom, $i);
                $i++;
            }

            foreach($this->relations as $relation)
                $mysqli->into_db($relation);

            foreach($this->conditions as $condition)
                $mysqli->into_db($condition);

            if(isset($this->xml)){
                $attributesXML = $this->xml->attributes();
                if(!isset($attributesXML["id"]))
                    $this->xml->addAttribute("id", "$this->id");
            }
        }


        // function from_xml($xml, $acte = NULL){
        //     if($xml == NULL)
        //         return;
        //
        //     $this->xml = $xml;
        //     $attr = $xml->attributes();
        //
        //     if(isset($acte))
        //         $this->set_periode($acte->values["periode_id"]);
        //     else
        //         $this->set_periode(NULL);
        //
        //     if(isset($attr["don"]) && ($attr["don"] === "true"))
        //         $this->conditions[] = "don";
        //
        //     $prenoms_id = [];
        //     $noms_id = [];
        //     foreach($xml->children() as $childXML){
        //         switch($childXML->getName()){
        //             case "prenom":
        //                 $rep = $this->set_prenom($childXML->__toString());
        //                 if($rep != FALSE)
        //                     $prenoms_id[] = $rep;
        //                 break;
        //             case "nom":
        //                 $rep = $this->set_nom($childXML);
        //                 if($rep != FALSE)
        //                     $noms_id[] = $rep;
        //                 break;
        //             case "pere":
        //                 $pere = personne_from_xml($childXML, $acte);
        //                 if($pere != NULL){
        //                     $this->pere = $pere;
        //                 }
        //                 break;
        //             case "mere":
        //                 $mere = personne_from_xml($childXML, $acte);
        //                 if($mere != NULL){
        //                     $this->mere = $mere;
        //                 }
        //                 break;
        //             case "condition":
        //                 $this->texte_conditions[] = $childXML->__toString();
        //                 break;
        //         }
        //     }
        //
        //     $this->prenoms_id = $prenoms_id;
        //     $this->noms_id = $noms_id;
        // }


        function get_prenoms(){
            global $mysqli;
            $prenoms = [];

            $result = $mysqli->query(
                "SELECT prenom
                FROM prenom INNER JOIN prenom_personne
                ON prenom.id = prenom_personne.prenom_id
                WHERE prenom_personne.personne_id = '$this->id'
                ORDER BY prenom_personne.ordre"
            );
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc())
                    $prenoms[] = $row["prenom"];
            }
            return $prenoms;
        }

        function get_noms(){
            global $mysqli;
            $noms = [];

            $result = $mysqli->query(
                "SELECT nom, value
                FROM nom INNER JOIN nom_personne
                ON nom.id = nom_personne.nom_id
                LEFT JOIN attribut
                ON attribut.id = nom.attribut_id
                WHERE nom_personne.personne_id = '$this->id'
                ORDER BY nom_personne.ordre"
            );
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $nom = $row["nom"];
                    if(isset($row["value"]))
                        $nom = $row["value"] . " " . $nom;
                    $noms[] = $nom;
                }
            }
            return $noms;
        }

        function get_conditions(){
            global $mysqli;
            $conditions = [];

            $result = $mysqli->select("condition", ["id"], "personne_id='$this->id'");
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

            $result = $mysqli->select("relation", ["id"], "source='$this->id' OR destination='$this->id'");
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $relations[] = new Relation($row["id"]);
                }
            }
            return $relations;
        }
    }

?>
