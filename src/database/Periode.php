<?php

    include_once(ROOT."src/database/TableEntry.php");

    class Periode extends TableEntry{

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

        function copy($periode){
            $this->set_var("debut_min", $periode->values["debut_min"]);
            $this->set_var("debut_max", $periode->values["debut_max"]);
            $this->set_var("fin_min", $periode->values["fin_min"]);
            $this->set_var("fin_max", $periode->values["fin_max"]);
        }

        function add_periode($periode){
            if(!isset($this->values["debut_min"])
                || $this->values["debut_min"] === PERIODE_DEFAULT
                || cmp_date($this->values["debut_min"], $periode->values["debut_min"]) === -1 ){
                $this->set_var("debut_min", $periode->values["debut_min"]);
            }

            if(!isset($this->values["debut_max"])
                || $this->values["debut_max"] === PERIODE_DEFAULT
                || cmp_date($this->values["debut_max"], $periode->values["debut_max"]) === 1 ){
                $this->set_var("debut_max", $periode->values["debut_max"]);
            }

            if(!isset($this->values["fin_min"])
                || $this->values["fin_min"] === PERIODE_DEFAULT
                || cmp_date($this->values["fin_min"], $periode->values["fin_min"]) === -1 ){
                $this->set_var("fin_min", $periode->values["fin_min"]);
            }

            if(!isset($this->values["fin_max"])
                || $this->values["fin_max"] === PERIODE_DEFAULT
                || cmp_date($this->values["fin_max"], $periode->values["fin_max"]) === 1 ){
                $this->set_var("fin_max", $periode->values["fin_max"]);
            }
        }

        function default_periode(){
            $this->set_var("debut_min", PERIODE_DEFAULT);
            $this->set_var("debut_max", PERIODE_DEFAULT);
            $this->set_var("fin_min", PERIODE_DEFAULT);
            $this->set_var("fin_max", PERIODE_DEFAULT);
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

    // Compare deux date
    // $a < $b  : 1
    // $a == $b : 0
    // $a  > $b : -1
    function cmp_date($a, $b){
        $split_a = explode('-', $a);
        $split_b = explode('-', $b);

        $y_a = intval($split_a[0]);
        $y_b = intval($split_b[0]);
        $m_a = intval($split_a[1]);
        $m_b = intval($split_b[1]);
        $d_a = intval($split_a[2]);
        $d_b = intval($split_b[2]);

        if($y_a < $y_b)
            return 1;
        if($y_a > $y_b)
            return -1;
        if($m_a < $m_b)
            return 1;
        if($m_a > $m_b)
            return -1;
        if($d_a < $d_b)
            return 1;
        if($d_a > $d_b)
            return -1;
        return 0;
    }

?>
