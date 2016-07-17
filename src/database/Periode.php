<?php

    include_once("src/database/Table.php");

    class Periode extends Table{

        function __construct($id = NULL){
            parent::__construct("periode", $id);
        }

        function with_date($date){
            $split = explode('-', trim($date));

            if(count($split) == 3){
                $tmp = $this->encode($split[2], $split[1], $split[0]);
                $this->set_var("debut_min", $tmp);
                $this->set_var("debut_max", $tmp);
                $this->set_var("fin_min", $tmp);
                $this->set_var("fin_max", $tmp);
            }else if(count($split) == 1){
                $this->set_var("debut_min", $this->encode($split[0], "01", "01"));
                $this->set_var("debut_max", $this->encode($split[0], "12", "31"));
                $this->set_var("fin_min", $this->encode($split[0], "01", "01"));
                $this->set_var("fin_max", $this->encode($split[0], "12", "31"));
            }
        }

        function default_periode(){
            $this->set_var("debut_min", "0000-00-00");
            $this->set_var("debut_max", "0000-00-00");
            $this->set_var("fin_min", "0000-00-00");
            $this->set_var("fin_max", "0000-00-00");
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
    }

?>
