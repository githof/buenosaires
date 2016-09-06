<?php

    include_once(ROOT."src/class/io/DatabaseIO.php");
    include_once(ROOT."src/class/model/Nom.php");
    include_once(ROOT."src/class/model/Prenom.php");
    include_once(ROOT."src/class/model/Relation.php");
    include_once(ROOT."src/class/model/Condition.php");

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
                if((isset($_prenom->id, $prenom->id) && $_prenom->id == $prenom->id) || $_prenom->no_accent == $prenom->no_accent)
                    return;
            }
            $this->prenoms[] = $prenom;
        }

        public function add_nom($nom){
            foreach($this->noms as $_nom){
                if((isset($_nom->id, $nom->id) && $_nom->id == $nom->id) ||
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

            $mysqli->start_transaction();
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
            $mysqli->end_transaction();

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

    }

?>