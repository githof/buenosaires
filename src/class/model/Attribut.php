<?php

    include_once(ROOT."src/class/io/DatabaseIO.php");

    class Attribut implements DatabaseIO{

        var $id;

        var $value;

        function __construct($id = NULL, $value = NULL){
            $this->id = $id;
            $this->set_value($value);
        }

        function set_value($value){
            $this->value = $value;
        }


        // DATABASE IO

        public function get_table_name(){
            return "attribut";
        }

        public function get_same_values(){
            return [
                "value" => $this->value
            ];
        }

        public function result_from_db($row){
            if($row == NULL)
                return;
            $this->id = $row["id"];
            $this->set_value($row["value"]);
        }

        public function values_into_db(){
            return [
                "value" => $this->value
            ];
        }

        public function pre_into_db(){
            return TRUE;
        }

        public function post_into_db(){}
    }

?>
