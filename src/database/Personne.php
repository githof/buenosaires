<?php

    include_once("src/database/Table.php");

    class Personne extends Table{

        function __construct($id = NULL){
            parent::__construct("personne", $id);
        }
        
    }


?>
