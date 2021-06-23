<?php

    include_once(ROOT."src/class/model/Attribut.php");
    include_once(ROOT."src/class/io/DatabaseIO.php");

/*
  Nom et Prenom pourraient hériter d'une même classe...
 */

    class Nom implements DatabaseIO{

        var $id;

        var $attribut;
        var $nom;
        var $no_accent;

        function __construct($id = NULL, $nom = NULL, $no_accent = NULL, $attribut = NULL){
            $this->id = $id;
            $this->attribut = $attribut;
            $this->set_nom($nom, $no_accent);
        }

        function set_nom($default, $no_accent = NULL){
            if($default == NULL)
                return;

            $default = trim(strtoupper(accent_uppercase($default)));
            $this->nom = $default;
            if(!isset($no_accent))
                $this->no_accent = no_accent($default);
            else
                $this->no_accent = $no_accent;
        }

        function to_string($no_accent = FALSE){
            $attr = "";
            if(isset($this->attribut))
                $attr = $this->attribut . " ";
	    $nom = $no_accent ?
	      $this->nom :
	      $this->no_accent;

            return $attr . $nom;
        }


        // DATABASE IO

        public function get_table_name(){
            return "nom";
        }

        public function get_same_values(){
            $values = [];
            $values["no_accent"] = $this->no_accent;
            return $values;
        }

        public function result_from_db($row){
            if($row == NULL)
                return;

            $this->id = $row["id"];
            $this->set_nom($row["nom"], $row["no_accent"]);
        }

        public function values_into_db(){
            $values = [];
            $values["nom"] = $this->nom;
            $values["no_accent"] = $this->no_accent;
            return $values;
        }

        public function pre_into_db(){
            return TRUE;
        }

        public function post_into_db(){}
    }

?>
