<?php

    include_once("src/database/TableEntry.php");

    class Prenom extends TableEntry {

        function __construct($id = NULL){
            parent::__construct("prenom", $id);
        }

        function set_prenom($default){
            $default = utf8_decode($default);
            $this->set_var("prenom", $default);
            $this->set_var("no_accent", no_accent($default));
        }
    }

?>
