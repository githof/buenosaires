<?php

    include_once(ROOT."src/database/Attribut.php");
    include_once(ROOT."src/database/DatabaseIO.php");
    include_once(ROOT."src/database/Database.php");

    class Nom implements DatabaseIO{

        var $id;

        var $attribut;
        var $nom;
        var $no_accent;

        function __construct($id = NULL, $nom = NULL, $no_accent = NULL, $attribut = NULL){
            $this->id = $id;
            $this->set_attribut($attribut);
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

        function set_attribut($attribut){
            $this->attribut = $attribut;
        }

        function to_String(){
            $str = "";
            if(isset($this->attribut, $this->attribut->value))
                $str = $this->attribut->value . " ";
            return $str . "$this->nom";
        }


        // DATABASE IO

        public function get_table_name(){
            return "nom";
        }

        public function get_same_values(){
            $values = [];
            $values["no_accent"] = $this->no_accent;

            if(isset($this->attribut, $this->attribut->id))
                $values["attribut_id"] = $this->attribut->id;
            else
                $values["attribut_id"] = "NULL";

            return $values;
        }

        public function result_from_db($row){
            if($row == NULL)
                return;

            $this->id = $row["id"];
            $this->set_nom($row["nom"], $row["no_accent"]);
            if(isset($row["attribut_id"]) && $row["attribut_id"] != "NULL")
                $this->set_attribut(new Attribut($row["attribut_id"]));
            else
                $this->attribut = NULL;
        }

        public function values_into_db(){
            $values = [];
            $values["nom"] = $this->nom;
            $values["no_accent"] = $this->no_accent;
            if(isset($this->attribut, $this->attribut->id))
                $values["attribut_id"] = $this->attribut->id;

            return $values;
        }

        public function pre_into_db(){
            global $mysqli;

            if(isset($this->attribut))
                $mysqli->into_db($this->attribut);

            return TRUE;
        }

        public function post_into_db(){}
    }

?>
