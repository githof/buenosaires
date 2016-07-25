<?php

    include_once(ROOT."src/database/Attribute.php");
    include_once(ROOT."src/database/TableEntry.php");

    class Nom extends TableEntry{

        function __construct($id = NULL){
            parent::__construct("nom", $id);
        }

        function set_nom($default){
            $this->set_var("nom", $default);
            $this->set_var("no_accent", no_accent($default));

            return TRUE;
        }

        function set_attribute($attribute_text){
            global $log;

            if($attribute_text == NULL)
                return TRUE;

            $attribute = new Attribute();
            $attribute->set_attribute($attribute_text);
            $attribute->get_same();

            $result = $attribute->into_db();
            if($result != FALSE){
                $this->set_var("attribut_id", $result);
                return TRUE;
            }

            $log->e("Erreur lors de l'ajout de l'attribut $attribute_text");
            return FALSE;
        }

        function get_same($values = NULL){
            $vals = [];
            $vals["no_accent"] = $this->values["no_accent"];

            if(isset($this->values["attribut_id"]))
                $vals["attribut_id"] = $this->values["attribut_id"];
            else
                $vals["attribut_id"] = "NULL";

            return parent::get_same($vals);
        }
    }

?>
