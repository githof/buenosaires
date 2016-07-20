<?php

    include_once("src/database/Table.php");

    class Relation extends Table {


        function __construct($id = NULL){
            parent::__construct("relation", $id);
        }

        function set_relation($source, $destination, $statut, $ref_periode_id){
            $this->set_var("source", $source);
            $this->set_var("destination", $destination);
            $this->set_var("statut_id", $statut);
            $this->set_periode($ref_periode_id);
        }

    }

?>
