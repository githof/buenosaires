<?php

    class Table {

        var $table_name;

        var $id;
        var $values;
        var $updated;

        function __construct($table_name, $id = NULL){
            $this->table_name = $table_name;
            $this->id = $id;
            $this->values = [];
            $this->updated = [];

            if($this->id != NULL)
                $this->from_db();
        }

        function from_db(){
            global $mysqli, $log;

            if(!isset($this->id)){
                $log->e("Impossible de récupérer les infos de $this->table_name sans id");
                return FALSE;
            }

            $rep = $mysqli->select($this->table_name, ["*"], "id='$this->id'");
            if($rep->num_rows == 1){
                $row = $rep->fetch_assoc();
                foreach ($row as $k => $v) {
                    $this->values[$k] = $v;
                }
                return TRUE;
            }
            return FALSE;
        }

        function into_db(){
            global $mysqli;

            if(count($this->updated) == 0)
                return $this->id;

            if(isset($this->id)){
                $rep = $mysqli->update($this->table_name, $this->updated, "id='$this->id'");
                if($rep === TRUE)
                    return $this->id;
            }else{
                $rep = $mysqli->insert($this->table_name, $this->updated);
                if($rep === TRUE)
                    return $this->get_last_id();
            }
            return FALSE;
        }

        function get_same($values){
            global $mysqli;

            $s = "";
            $i = 0;
            foreach ($values as $k => $v) {
                $s .= $k . "='" . $v . "'";
                if($i < count($values) -1)
                    $s .= " AND ";
                $i++;
            }

            $rep = $mysqli->select($this->table_name, ["id"], $s);
            if($rep->num_rows == 1){
                $row = $rep->fetch_assoc();
                $this->id = $row["id"];
                $this->from_db();
                return TRUE;
            }
            return FALSE;
        }

        function get_last_id(){
            global $mysqli;

            $rep = $mysqli->select($this->table_name, ["id"], "", "ORDER BY id DESC LIMIT 1");
            if($rep->num_rows == 1){
                $row = $rep->fetch_assoc();
                return intval($row["id"]);
            }
            return FALSE;
        }

        function set_var($name, $value){
            if(!isset($this->values[$name]) || $this->values[$name] != $value){
                $this->values[$name] = $value;
                $this->updated[$name] = $value;
            }
        }
    }

?>
