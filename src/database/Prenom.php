<?php

    include_once(ROOT."src/database/TableEntry.php");

    class Prenom extends TableEntry {

        function __construct($id = NULL){
            parent::__construct("prenom", $id);
        }

        function set_prenom($default){
            $default = trim($default);
            $this->set_var("prenom", $default);
            $this->set_var("no_accent", no_accent($default));
        }

        function looking_for_same_in_db($vals = NULL){
            $values = [
                "no_accent" => $this->values["no_accent"]
            ];
            return parent::looking_for_same_in_db($values);
        }
    }

?>
