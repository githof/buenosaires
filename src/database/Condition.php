<?php

    include_once("src/database/TableEntry.php");


    class Condition extends TableEntry {


        function __construct($id = NULL){
            parent::__construct("cond", $id);
        }

        function setup($text, $source, $personne, $acte, $id_periode_ref){
            $this->set_var("text", $text);
            $this->set_var("source_id", $source);
            $this->set_var("personne_id", $personne);
            $this->set_var("acte_id", $acte);
            $this->set_periode($id_periode_ref);
        }
    }

?>
