<?php

    include_once(ROOT."src/database/DatabaseIO.php");


    class Condition implements DatabaseIO{

        var $id;

        var $text;
        var $personne;
        var $source_id;

        function __construct($id = NULL, $text = NULL, $personne = NULL, $source_id = NULL){
            $this->id = $id;
            $this->set_text($text);
            $this->set_personne($personne);
            $this->set_source_id($source_id);
        }

        function set_text($test){
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

        function looking_for_same_in_db($vals = NULL){
            $values = [
                "text" => $this->values["text"],
                "source_id" => $this->values["source_id"],
                "personne_id" => $this->values["personne_id"],
                "acte_id" => $this->values["acte_id"]
            ];
            return parent::looking_for_same_in_db($values);
        }


        // DATABASE IO

        public function get_table_name(){
            return "condition";
        }

        public function get_same_values(){
            return [
                "text" => $this->text,
                "personne_id" => $this->personne,
                "source_id" => $this->source_id
            ];
        }

        public function result_from_db($row){
            if($row == NULL)
                return;

            $this->id = $row["id"];
            $this->set_text($row["text"]);
            $this->set_personne(new Personn($row["personne_id"]));
            $this->set_source_id($row["source_id"]);
        }

        public function values_into_db(){
            return [
                "text" => $this->text,
                "personne_id" => $this->personne,
                "source_id" => $this->source_id
            ];
        }

        public function pre_into_db(){
            return TRUE;
        }

        public function post_into_db(){}
    }

?>
