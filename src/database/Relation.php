<?php

    include_once("src/database/Table.php");

    class Relation extends Table {


        function __construct($id = NULL){
            parent::__construct("relation", $id);
        }

        function set_relation($source, $destination, $statut, $ref_periode_id){
            $this->set_source($source);
            $this->set_destination($destination);
            $this->set_statut($statut);
            $this->set_periode($ref_periode_id);
        }

        function set_source($source){
            $this->set_source("source", $source);
        }

        function set_destination($destination){
            $this->set_destination("destination", $destination);
        }

        function set_statut($statut){
            global $mysqli;

            $rep = $mysqli->select("statut", ["id"], "value='$statut'");
            if($rep->num_rows == 1){
                $row = $retp->fetch_assoc();
                $this->set_var("statut_id", $row["id"]);
                return TRUE;
            }
            return FALSE;
        }

    }

?>
