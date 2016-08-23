<?php

    include_once(ROOT."src/class/io/DatabaseIO.php");
    include_once(ROOT."src/class/model/Personne.php");

    class Relation implements DatabaseIO {

        var $id;

        var $personne_source;
        var $personne_destination;
        var $statut_id;
        var $actes;

        function __construct($id = NULL, $personne_source = NULL, $personne_destination = NULL, $statut_id = NULL){
            $this->id = $id;
            $this->set_personne_source($personne_source);
            $this->set_personne_destination($personne_destination);
            $this->set_statut_id($statut_id);
            $this->actes = [];
        }

        function set_personne_source($personne_source){
            $this->personne_source = $personne_source;
        }

        function set_personne_destination($personne_destination){
            $this->personne_destination = $personne_destination;
        }

        function set_statut_id($statut_id){
            $this->statut_id = $statut_id;
        }

        function get_statut_name(){
            global $mysqli;

            $result = $mysqli->select(
                "statut",
                ["valeur"],
                "id='$this->statut_id'"
            );
            if($result != FALSE && $result->num_rows > 0){
                $row = $result->fetch_assoc();
                return $row["valeur"];
            }
            return "";
        }


        // DATABASE IO

        public function get_table_name(){
            return "relation";
        }

        public function get_same_values(){
            return [
                "pers_source_id" => $this->personne_source->id,
                "pers_destination_id" => $this->personne_destination->id,
                "statut_id" => $this->statut_id
            ];
        }

        public function result_from_db($row){
            if($row == NULL)
                return;

            $this->id = $row["id"];
            $this->set_personne_source(new Personne($row["pers_source_id"]));
            $this->set_personne_destination(new Personne($row["pers_destination_id"]));
            $this->set_statut_id($row["statut_id"]);
        }

        public function values_into_db(){
            return [
                "pers_source_id" => $this->personne_source->id,
                "pers_destination_id" => $this->personne_destination->id,
                "statut_id" => $this->statut_id
            ];
        }

        public function pre_into_db(){
            return TRUE;
        }

        public function post_into_db(){}
    }
?>
