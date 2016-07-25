<?php

    include_once(ROOT."src/database/TableEntry.php");

    class Attribute extends TableEntry{

        function __construct($id = NULL){
            parent::__construct("attribut", $id);
        }

        function set_attribute($attribute){
            $this->set_var("value", $attribute);
        }

        function looking_for_same_in_db($values = NULL){
            $vals = [
                "value" => $this->values["value"]
            ];

            return parent::looking_for_same_in_db($vals);
        }
    }

?>
