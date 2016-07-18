<?php

    include_once("src/database/Table.php");

    class Attribut extends Table{

        function __construct($id = NULL){
            parent::__construct("attribut", $id);
        }

        function set_attribut($attribut){
            $this->set_var("value", $attribut);
        }
    }

    function db_has_attribut($attribut){
        global $mysqli;

        $rep =$mysqli->select("attribut", ["id"], "value='$attribut'");
        if($rep->num_rows == 1){
            $row = $rep->fetch_assoc();
            return intval($row["id"]);
        }
        return NULL;
    }

?>
