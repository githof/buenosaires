<?php

    include_once(ROOT."src/database/TableEntry.php");

    class Relation extends TableEntry {


        function __construct($id = NULL){
            parent::__construct("relation", $id);
        }

        function setup($source, $destination, $statut, $ref_periode_id){
            $this->set_var("source", $source);
            $this->set_var("destination", $destination);
            $this->set_var("statut_id", $statut);
            $this->set_periode($ref_periode_id);
        }

    }

?>
