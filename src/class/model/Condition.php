<?php

    include_once(ROOT."src/class/io/DatabaseIO.php");


    class Condition implements DatabaseIO{

        var $id;

        var $text;
        var $personne;
        var $source_id;
        var $actes;

        function __construct($id = NULL, $text = NULL, $personne = NULL, $source_id = NULL){
            $this->id = $id;
            $this->set_text($text);
            $this->set_personne($personne);
            $this->set_source_id($source_id);
            $this->actes = [];
        }

        function set_text($text){
            $this->text = $text;
        }

        function set_personne($personne){
            $this->personne = $personne;
        }

        function set_source_id($source_id){
            $this->source_id = $source_id;
        }

        function get_source_name(){
            global $mysqli;

            $result = $mysqli->select(
                "source",
                ["valeur"],
                "id='$this->source_id'"
            );
            if($result != FALSE && $result->num_rows > 0){
                $row = $result->fetch_assoc();
                return $row["valeur"];
            }
            return "";
        }


        // DATABASE IO

        public function get_table_name(){
            return "condition";
        }

        public function get_same_values(){
            return [
                "text" => $this->text,
                "personne_id" => $this->personne->id,
                "source_id" => $this->source_id
            ];
        }

        public function result_from_db($row){
            if($row == NULL)
                return;

            $this->id = $row["id"];
            $this->set_text($row["text"]);
            $this->set_personne(new Personne($row["personne_id"]));
            $this->set_source_id($row["source_id"]);
        }

        public function values_into_db(){
            return [
                "text" => $this->text,
                "personne_id" => $this->personne->id,
                "source_id" => $this->source_id
            ];
        }

        public function pre_into_db(){
            return TRUE;
        }

        public function post_into_db(){}
    }

?>
