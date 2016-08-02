<?php

    include_once(ROOT."src/database/DatabaseIO.php");

    class Prenom implements DatabaseIO{

        var $id;

        var $prenom;
        var $no_accent;

        function __construct($id = NULL, $prenom = NULL){
            $this->id = $id;
            $this->set_prenom($prenom);
        }

        function set_prenom($default, $no_accent = NULL){
            if($default == NULL)
                return;

            $default = trim($default);
            if(!isset($no_accent))
                $no_accent = no_accent($default);

            $this->prenom = $default;
            $this->no_accent = $no_accent;
        }


        // DATABASE IO

        public function get_table_name(){
            return "prenom";
        }

        public function get_same_values(){
            return [
                "no_accent" => $this->no_accent
            ];
        }

        public function result_from_db($row){
            if($row == NULL)
                return;
            $this->id = $row["id"];
            $this->set_prenom($row["prenom"], $row["no_accent"]);
        }

        public function values_into_db(){
            return [
                "prenom" => $this->prenom,
                "no_accent" => $this->no_accent
            ];
        }

        public function pre_into_db(){}

        public function post_into_db(){}
    }

?>
