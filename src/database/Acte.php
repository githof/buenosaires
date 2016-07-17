<?php

    include_once("src/database/Periode.php");

    class Acte {

        var $xml;
        var $periode_id;
        var $epoux;
        var $epouse;
        var $cond_id;
        var $id;

        var $values;

        function Acte($xml){
            $this->xml = $xml;

            $this->values = [];

            $num = $this->get_num();
            if($num != FALSE){
                $this->id = intval($num);
                $this->from_db();
            }
        }

        function get_num(){
            if(isset($this->xml->attributes()["num"]))
                return $this->xml->attributes()["num"];
            return FALSE;
        }

        function read_xml(){
            $date = NULL;
            if(!isset($this->xml))
                return;

            foreach($this->xml->children() as $child){
                switch($child->getName()){
                    case "date":
                        $date = $child;
                        break;
                }
            }

            $this->set_date_xml($date);
        }

        function set_date_xml($xml){
            $periode;
            if(isset($this->periode_id)){
                $periode = new Periode(intval($this->periode_id));
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
                $this->set_periode_id($tmp);
        }

        function from_db(){
            global $mysqli;

            $rep = $mysqli->select("acte", ["*"], "id='$this->id'");

            if($rep->num_rows == 1){
                $row = $rep->fetch_assoc();
                $this->periode_id = $row["periode_id"];
                $this->epoux = $row["epoux"];
                $this->epouse = $row["epouse"];
                $this->cond_id = $row["cond_id"];
                return TRUE;
            }
            return FALSE;
        }

        function into_db(){
            global $mysqli, $alert;

            if(!isset($this->id)){
                $alert->add_error("L'acte ne contient pas de num");
                return FALSE;
            }

            $this->read_xml();

            $rep = TRUE;
            if(db_has_acte($this->id)){
                if(count($this->values) > 0)
                    $rep =  $mysqli->update("acte", $this->values, "id='$this->id'");
            }else{
                if(count($this->values) > 0){
                    $this->values["id"] = $this->id;
                    $rep = $mysqli->insert("acte", $this->values);
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

        function set_id($new){
            if(!isset($this->id) || $this->id != $new){
                $this->id = $new;
                $this->values["id"] = $new;
            }
        }

        function set_epoux($new){
            if(!isset($this->epoux) || $this->epoux != $new){
                $this->epoux = $new;
                $this->values["epoux"] = $new;
            }
        }

        function set_epouse($new){
            if(!isset($this->epouse) || $this->epouse != $new){
                $this->epouse = $new;
                $this->values["epouse"] = $new;
            }
        }

        function set_periode_id($new){
            if(!isset($this->periode_id) || $this->periode_id != $new){
                $this->periode_id = $new;
                $this->values["periode_id"] = $new;
            }
        }

        function set_cond_id($new){
            if(!isset($this->cond_id) || $this->cond_id != $new){
                $this->cond_id = $new;
                $this->values["cond_id"] = $new;
            }
        }
    }

    function db_has_acte($id){
        global $mysqli;

        $rep = $mysqli->select("acte", ["id"], "id='$id'");
        return $rep->num_rows > 0;
    }

?>
