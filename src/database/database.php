<?php

    class Database extends mysqli{

        public function __construct(){
            global $log;

            parent::__construct(SQL_SERVER,
                                SQL_USER,
                                SQL_PASS,
                                SQL_DATABSE_NAME);

            if(mysqli_connect_error()){
                $log->e("Erreur de connexion (" . mysqli_connect_errno() . ') ' . mysqli_connect_error());
            }
        }

        public function select($table, $columns, $where, $more = ""){
            global $log;

            $s = "SELECT ";

            for($i = 0; $i < count($columns); $i++){
                $s .= $columns[$i];
                if($i < count($columns) -1)
                    $s .= ", ";
            }

            $s .= " FROM " . $table;

            if(strlen($where) > 0)
                $s .= " WHERE " . $where;

            $s .= " " . $more;

            $log->i($s);

            $rep = parent::query($s);
            if($rep === FALSE){
                $log->e("SQL error : $this->error");
                return FALSE;
            }
            return $rep;
        }

        public function insert($table, $values, $more = ""){
            global $log;

            $s = "INSERT INTO " . $table . " (";

            $keys = "";
            $vals = "";
            $i = 0;

            foreach($values as $key => $value){
                $keys .= $key;

                if(strcmp($value, "NULL") == 0 || strcmp($value, "now()") == 0)
                    $vals .= $value;
                else
                    $vals .= "'" . $value . "'";

                if($i < count($values) -1){
                    $keys .= ", ";
                    $vals .= ", ";
                }

                $i++;
            }

            $s .= $keys . ") VALUES (" . $vals . ")";

            if(strlen($more) > 0)
                $s .= " " . $more;

            $log->i($s);

            $rep = parent::query($s);
            if($rep === FALSE){
                $log->e("SQL error : $this->error");
                return FALSE;
            }
            return $rep;
        }

        public function update($table, $values, $where, $more = ""){
            global $log;
            $s = "UPDATE " . $table . " SET ";

            $i = 0;

            foreach($values as $key => $value){
                $s .= " " . $key . " = ";

                if(strcmp($value, "NULL") == 0 || strcmp($value, "now()") == 0)
                    $s .= $value;
                else
                    $s .= "'" . $value . "'";

                if($i < count($values) -1)
                    $s .= ", ";

                $i++;
            }

            if(strlen($where) > 0)
                $s .= " WHERE " . $where;

            if(strlen($more) > 0)
                $s .= " " . $more;

            $log->i($s);

            $rep = parent::query($s);
            if($rep === FALSE){
                $log->e("SQL error : $this->error");
                return FALSE;
            }
            return $rep;
        }

        public function delete($table, $where, $more = ""){
            global $log;

            $s = "DELETE FROM " . $table;

            if(strlen($where) > 0)
                $s .= " WHERE " . $where;

            if(strlen($more) > 0)
                $s .= " " . $more;

            $log->i($s);

            $rep = parent::query($s);
            if($rep === FALSE){
                $log->e("SQL error : $this->error");
                return FALSE;
            }
            return $rep;
        }
    }


?>
