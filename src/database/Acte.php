<?php

    include_once("src/database/Periode.php");

    class Acte {

        var $xml;
        var $periode_id;
        var $epoux;
        var $epouse;
        var $cond_id;
        var $id;

        function Acte($xml){
            $this->xml = $xml;

            $num = $this->get_num();
            if($num != false){
                $this->id = intval($num);
                $this->from_db();
            }
        }

        function get_num(){
            if(isset($this->xml->attributes()["num"]))
                return $this->xml->attributes()["num"];
            return false;
        }

        function read_xml(){
            if(!isset($this->xml))
                return;

            foreach($this->xml->children() as $child){
                switch($child->getName()){
                    case "date":
                        $this->set_date_xml($child);
                        break;
                }
            }
        }

        function set_date_xml($xml){
            $periode;
            if(isset($this->periode_id)){
                $periode = new Periode(intval($this->periode_id));
            }else{
                $periode = new Periode();
            }

            $periode->with_date($xml->__toString());
            $tmp = $periode->into_db();
            if($tmp !== FALSE)
                $this->periode_id = $tmp;
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
                return true;
            }
            return false;
        }

        function into_db(){
            global $mysqli;

            $this->read_xml();

            $values = [];

            if(isset($this->periode_id))
                $values["periode_id"] = $this->periode_id;
            if(isset($this->epoux))
                $values["epoux"] = $this->epoux;
            if(isset($this->epouse))
                $values["epouse"] = $this->epouse;
            if(isset($this->cond_id))
                $values["cond_id"] = $this->cond_id;

            $rep;
            if(!isset($this->id)){
                $this->get_next_available_id();
                $values["id"] = $this->id;

                $rep = $mysqli->insert("acte", $values);
            }else{
                if(db_has_acte($this->id))
                    $rep =  $mysqli->update("acte", $values, "id='$this->id'");
                else{
                    $values["id"] = $this->id;
                    $rep = $mysqli->insert("acte", $values);
                }
            }

            $this->contenu_into_db();
            return $rep;
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

        function get_next_available_id(){
            global $mysqli;

            $rep = $mysqli->select("acte", ["id"], "", " LIMIT 1 ORDER BY id DESC");
            if($rep->num_rows > 0){
                $row = $rep->fetch_assoc();
                $this->id = intval($row["id"]) + 1;
            }else {
                $this->id = 1;
            }

            $this->xml->addAttribute("num", "$this->id");
        }
    }

    function db_has_acte($id){
        global $mysqli;

        $rep = $mysqli->select("acte", ["id"], "id='$id'");
        return $rep->num_rows > 0;
    }

?>
