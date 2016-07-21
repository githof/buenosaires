<?php

    include_once(ROOT."src/database/Attribut.php");
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

        function set_attribut($attribut){
            $obj = new Attribut();
            $obj->get_same(["value" => $attribut]);
            $obj->set_attribut($attribut);

            $rep = $obj->into_db();
            if($rep != FALSE){
                $this->set_var("attribut_id", $rep);
                return TRUE;
            }
            return FALSE;
        }
    }

?>
