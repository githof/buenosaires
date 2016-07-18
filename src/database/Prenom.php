<?php

    class Prenom extends Table {

        function __construct($id = NULL){
            parent::__construct("prenom", $id);
        }

        function set_prenom($default){
            $this->set_var("value", $default);
            $this->set_var("no_accent", no_accent($default));
        }
    }

?>
