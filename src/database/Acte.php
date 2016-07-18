<?php

    include_once("src/database/Periode.php");
    include_once("src/database/Table.php");
    include_once("src/database/Personne.php");

    class Acte extends Table{

        var $xml;

        function __construct($id){
            parent::__construct("acte", $id);
        }

        function set_xml($xml){
            $this->xml = $xml;

            if($xml == NULL)
                return;

            if(isset($this->xml->date)){
                $this->set_date_xml($this->xml->date);
            }else{
                $this->set_date_xml(NULL);
            }

            foreach($this->xml->children() as $child){
                switch($child->getName()){
                    case "epoux":
                        $this->set_epoux($child);
                        break;
                    case "epouse":
                        $this->set_epouse($child);
                        break;
                }
            }
        }

        function set_relation($source, $destination, $statu, $periode_id){
            $relation = new Relation();
            
        }

        function set_epoux($xml){
            $epoux_id = NULL;

            if(isset($this->values["epoux"]))
                $epoux_id = $this->values["epoux"];

            $obj = new Personne($epoux_id);
            $obj->set_xml($xml, $this->values["periode_id"]);
            $rep = $obj->into_db();

            if($rep != FALSE){
                $this->set_var("epoux", $rep);
                return TRUE;
            }
            return FALSE;
        }

        function set_epouse($xml){
            $epouse_id = NULL;

            if(isset($this->values["epouse"]))
                $epouse_id = $this->values["epouse"];

            $obj = new Personne($epouse_id);
            $obj->set_xml($xml, $this->values["periode_id"]);
            $rep = $obj->into_db();

            if($rep != FALSE){
                $this->set_var("epouse", $rep);
                return TRUE;
            }
            return FALSE;
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
