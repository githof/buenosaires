<?php

    include_once("src/database/Periode.php");
    include_once("src/database/Table.php");

    class Acte extends Table{

        var $xml;

        function __construct($id){
            parent::__construct("acte", $id);
        }

        function set_xml($xml){
            $this->xml = $xml;

            if($xml == NULL)
                return;

            foreach($this->xml->children() as $child){
                switch($child->getName()){
                    case "date":
                        $this->set_date_xml($child);
                        break;
                }
            }

            if(!isset($this->values["periode_id"]))
                $this->set_date_xml(NULL);
        }

        function get_num(){
            if(isset($this->xml->attributes()["num"]))
                return $this->xml->attributes()["num"];
            return FALSE;
        }

        function set_date_xml($xml){
            $periode;
            if(isset($this->values["periode_id"])){
                $periode = new Periode(intval($this->values["periode_id"]));
            }else{
                $periode = new Periode();
            }

            if(isset($xml)){
                $periode->with_date($xml->__toString());
            }else {
                $periode->default_periode();
            }

            $tmp = $periode->into_db();

            if($tmp !== FALSE)
                $this->set_var("periode_id", $tmp);
        }

        function into_db(){
            global $mysqli, $alert;

            if(!isset($this->id)){
                $alert->add_error("L'acte ne contient pas de num");
                return FALSE;
            }

            $rep = TRUE;
            if(db_has_acte($this->id)){
                if(count($this->updated) > 0)
                    $rep =  $mysqli->update($this->table_name, $this->updated, "id='$this->id'");
            }else{
                if(count($this->updated) > 0){
                    $this->updated["id"] = $this->id;
                    $rep = $mysqli->insert("acte", $this->updated);
                }
            }

            if($rep)
                return $this->contenu_into_db();
            return FALSE;
        }

        function contenu_into_db(){
            global $mysqli;

            $contenu = $mysqli->real_escape_string($this->xml->asXML());
            $values = [
                "acte_id" => $this->get_num(),
                "contenu" => $contenu
            ];

            return $mysqli->insert(
                "acte_contenu",
                $values,
                " ON DUPLICATE KEY UPDATE contenu='$contenu'");
        }
    }

    function db_has_acte($id){
        global $mysqli;

        $rep = $mysqli->select("acte", ["id"], "id='$id'");
        return $rep->num_rows > 0;
    }

?>
