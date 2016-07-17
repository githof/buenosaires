<?php

    class Periode {

        var $id;

        var $start_min;
        var $start_max;

        var $end_min;
        var $end_max;

        function Periode($id = NULL){
            $this->start_min = "0000-00-00";
            $this->start_max = "0000-00-00";

            $this->end_min = "0000-00-00";
            $this->end_max = "0000-00-00";

            $this->id = $id;

            if($id != NULL){
                $this->from_db();
            }
        }

        function with_date($date){
            $split = explode('-', trim($date));

            if(count($split) == 3){
                $tmp = $this->encode($split[2], $split[1], $split[0]);
                $this->start_min = $tmp;
                $this->start_max = $tmp;
                $this->end_min = $tmp;
                $this->end_max = $tmp;
            }else if(count($split) == 1){
                $this->start_min = $this->encode($split[0], "01", "01");
                $this->start_max = $this->encode($split[0], "12", "31");
                $this->end_min = $this->encode($split[0], "01", "01");
                $this->end_max = $this->encode($split[0], "12", "31");
            }
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

            if($this->id < 0)
                return false;

            $rep = $mysqli->select("periode", ["*"], "id='$this->id'");
            if($rep->num_rows == 1){
                $row = $rep->fetch_assoc();
                $this->start_min = $row["debut_min"];
                $this->start_max = $row["debut_max"];
                $this->end_min = $row["fin_min"];
                $this->end_max = $row["fin_max"];

                return true;
            }
            return false;
        }

        function into_db(){
            global $mysqli;

            $values = [
                "debut_min" => $this->start_min,
                "debut_max" => $this->start_max,
                "fin_min" => $this->end_min,
                "fin_max" => $this->end_max
            ];

            if(isset($this->id)){
                $rep =$mysqli->update("periode", $values, "id='$this->id'");
                if($rep === TRUE)
                    return $this->id;
            }else{
                $rep = $mysqli->insert("periode", $values);
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
            return false;
        }
    }

?>
