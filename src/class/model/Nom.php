<?php

    include_once(ROOT."src/class/model/Attribut.php");
    include_once(ROOT."src/class/io/DatabaseIO.php");

    include_once(ROOT."src/class/io/DatabaseEntity.php");

/*
  Nom et Prenom pourraient hériter d'une même classe...
 */

    class Nom extends DatabaseEntity {

        public $id;

        public $attribut;
        public $nom;
        public $no_accent;

        public function __construct($id = NULL, $nom = NULL, $no_accent = NULL, $attribut = NULL){
            $this->id = $id;
            $this->attribut = $attribut;
            $this->set_nom($nom, $no_accent);
        }

        public function set_nom($default, $no_accent = NULL){
            if($default == NULL) 
                return;

            $default = trim(strtoupper(accent_uppercase($default)));
            $this->nom = $default;
            if(!isset($no_accent))
                $this->no_accent = no_accent($default);
            else
                $this->no_accent = $no_accent;
        }

        //  *** rewrite-noms-export
        //  test sans "de" : $attr pour $attribut 
        //  test sans_accents : $no_accent = true pour prendre le nom sans les accents 
        public function to_string($attr, $no_accent){ //    true, false 
        // public function to_string($no_accent = FALSE, $attr = FALSE){

            //  *** rewrite-noms-export 
            // echo '<br>'.__METHOD__.' $no_accent : ';
            // var_dump($no_accent);    //  false :( 
            // echo '<br>'.__METHOD__.' $attr : ';
            // var_dump($attr);
            //  fin test

            $nom = $no_accent ? $this->no_accent : $this->nom;  
            // $attr = "";
            // if(isset($this->attribut))
            // if(isset($this->attribut, $attr) && $attr == true)
            //     $attr = $this->attribut . " ";
            if(isset($this->attribut)) {
                if(isset($attr) && $attr == true)
                    $attr = $this->attribut . ' ';
                else 
                    $attr = null;
            } else {    //  !isset($this->attribut) 
                $attr = null;
            }

            return $attr . $nom;
        }


        // DATABASE IO

        // public function get_table_name(){
        //     return "nom";
        // }

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
            // $values = [];

            $values["nom"] = $this->nom;
            $values["no_accent"] = $this->no_accent;
            return $values;
        }

        // public function pre_into_db(){
        //     return TRUE;
        // }

        // public function post_into_db(){
        //     global $mysqli;

        //      *** Récupérer la dernier id inséré 
        //     if(!isset($this->id) || ($this->id == 0)) {
        //         $this->id = $mysqli->insert_id;
        //     }
        // }
    }

?>
