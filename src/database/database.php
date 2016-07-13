<?php

    include("login.php");


    class database extends mysqli{

        public function __construct(){
            parent::__construct(SQL_SERVER,
                                SQL_USER,
                                SQL_PASS,
                                SQL_DATABSE_NAME);

            if(mysqli_connect_error()){
                die("Erreur de connexion (" . mysqli_connect_errno() . ') ' . mysqli_connect_error());
            }
        }

        public function select($columns, $table, $where, $more = ""){
            $s = "SELECT ";

            for($i = 0; $i < count($columns); $i++){
                $s .= $columns[$i];
                if($i < count($columns) -1)
                    $s .= ", ";
            }

            $s .= " FROM " . $table;

            $s .= " WHERE " . $where;

            $s .= " " . $more;

            printf("%s\n", $s);

            return parent::query($s);
        }

        public function insert($table, $values, $more = ""){
            $s = "INSERT INTO " . $table . " (";

            $keys = "";
            $vals = "";
            $i = 0;

            foreach($values as $key => $value){
                $keys .= $key;

                if(strcmp($value, "NULL") == 0)
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

            $s .= " " . $more;

            printf("%s\n", $s);

            return parent::query($s);
        }

        public function update($table, $values, $more = ""){
            $s = "UPDATE " . $table . " SET ";

            $i = 0;

            foreach($values as $key => $value){
                $s .= " " . $key . " = ";

                if(strcmp($value, "NULL") == 0)
                    $s .= $value;
                else
                    $s .= "'" . $value . "'";

                if($i < count($values) -1)
                    $s .= ", ";

                $i++;
            }

            printf("%s\n", $s);

            return parent::query($s);
        }

        public function delete($table, $where, $more = ""){
            $s = "DELETE FROM " . $table . " WHERE " . $where . " " . $more;

            printf("%s\n", $s);

            return parent::query($s);
        }
    }


?>
