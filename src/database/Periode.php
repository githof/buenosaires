<?php

    class Periode {

        var $id;

        var $debut_min;
        var $debut_max;

        var $fin_min;
        var $fin_max;

        var $values;

        function Periode($id = NULL){
            $this->id = $id;
            $this->values = [];

            if($id != NULL){
                $this->from_db();
            }
        }

        function with_date($date){
            $split = explode('-', trim($date));

            if(count($split) == 3){
                $tmp = $this->encode($split[2], $split[1], $split[0]);
                $this->set_debut_min($tmp);
                $this->set_debut_max($tmp);
                $this->set_fin_min($tmp);
                $this->set_fin_max($tmp);
            }else if(count($split) == 1){
                $this->set_debut_min($this->encode($split[0], "01", "01"));
                $this->set_debut_max($this->encode($split[0], "12", "31"));
                $this->set_fin_min($this->encode($split[0], "01", "01"));
                $this->set_fin_max($this->encode($split[0], "12", "31"));
            }
        }

        function default_periode(){
            $this->set_debut_min("0000-00-00");
            $this->set_debut_max("0000-00-00");
            $this->set_fin_min("0000-00-00");
            $this->set_fin_max("0000-00-00");
        }

        private function encode($year, $month, $day){
            return $this->complete($year, 4)
                . "-" . $this->complete($month, 2)
                . "-" . $this->complete($day, 2);
        }

        private function complete($str, $length){
            while(strlen($str) < $length)
                $str = "0" . $str;
            return $str;
        }

        function from_db(){
            global $mysqli;

            if(!isset($this->id))
                return FALSE;

            $rep = $mysqli->select("periode", ["*"], "id='$this->id'");
            if($rep->num_rows == 1){
                $row = $rep->fetch_assoc();
                $this->debut_min = $row["debut_min"];
                $this->debut_max = $row["debut_max"];
                $this->fin_min = $row["fin_min"];
                $this->fin_max = $row["fin_max"];

                return TRUE;
            }
            return FALSE;
        }

        function into_db(){
            global $mysqli;

            if(count($this->values) === 0)
                return $this->id;

            if(isset($this->id)){
                $rep = $mysqli->update("periode", $this->values, "id='$this->id'");
                if($rep === TRUE)
                    return $this->id;
            }else{
                $rep = $mysqli->insert("periode", $this->values);
                if($rep === TRUE)
                    return $this->get_last_id();
            }
            return FALSE;
        }

        function get_last_id(){
            global $mysqli;

            $rep = $mysqli->select("periode", ["id"], "", "ORDER BY id DESC LIMIT 1");
            if($rep->num_rows == 1){
                $row = $rep->fetch_assoc();
                return intval($row["id"]);
            }
            return FALSE;
        }

        function set_debut_min($new){
            if(!isset($this->debut_min) || $this->debut_min !== $new){
                $this->debut_min = $new;
                $this->values["debut_min"] = $new;
            }
        }

        function set_debut_max($new){
            if(!isset($this->debut_max) || $this->debut_max !== $new){
                $this->debut_max = $new;
                $this->values["debut_max"] = $new;
            }
        }

        function set_fin_min($new){
            if(!isset($this->fin_min) || $this->fin_min !== $new){
                $this->fin_min = $new;
                $this->values["fin_min"] = $new;
            }
        }

        function set_fin_max($new){
            if(!isset($this->fin_max) || $this->fin_max !== $new){
                $this->fin_max = $new;
                $this->values["fin_max"] = $new;
            }
        }
    }

?>
