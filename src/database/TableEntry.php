<?php

    include_once(ROOT."src/database/Condition.php");
    include_once(ROOT."src/database/Relation.php");
    include_once(ROOT."src/database/Personne.php");

    class TableEntry {

        var $table_name;

        var $id;
        var $values;
        var $updated;

        var $is_in_db;

        function __construct($table_name, $id = NULL){
            $this->table_name = $table_name;
            $this->id = $id;
            $this->values = [];
            $this->updated = [];
            $this->is_in_db = FALSE;

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
                    if(isset($this->updated[$k]))
                        unset($this->updated[$k]);
                }
                $this->is_in_db = TRUE;
                return TRUE;
            }
            $this->is_in_db = FALSE;
            return FALSE;
        }

        function into_db($id_require = FALSE){
            global $mysqli, $log;
            $result = FALSE;
            $new_id = NULL;

            if(count($this->updated) == 0)
                return $this->id;

            if(!isset($this->id)){
                $new_id = $mysqli->next_id($this->table_name);
                if($new_id == 0){
                    $log->e("Aucun nouvel id trouvé pour l'insert dans $this->table_name");
                    return FALSE;
                }
            }

            if($this->is_in_db && isset($this->id)){
                $result = $mysqli->update(
                    $this->table_name,
                    $this->updated,
                    "id='$this->id'"
                );
            }else{
                if($id_require){
                    if(isset($new_id))
                        $this->id = $new_id;
                    $this->updated["id"] = $this->id;
                }else
                    $this->id = $new_id;

                $result = $mysqli->insert(
                    $this->table_name,
                    $this->updated
                );
            }

            if($result === TRUE)
                return $this->id;
            return FALSE;
        }

        function get_same($values){
            global $mysqli;

            $s = "";
            $i = 0;
            foreach ($values as $k => $v) {
                if(strcmp($v, "NULL") == 0)
                    $s .= "$k IS NULL";
                else
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

        function set_var($name, $value){
            if(!isset($this->values[$name]) || $this->values[$name] != $value){
                $this->values[$name] = $value;
                $this->updated[$name] = $value;
            }
        }

        function set_periode($ref_periode_id = NULL){
            $periode;

            $ref_periode = NULL;
            if(isset($ref_periode_id))
                $ref_periode = new Periode($ref_periode_id);

            if(isset($this->values["periode_id"])){
                $periode = new Periode($this->values["periode_id"]);
                if(isset($ref_periode))
                    $periode->add_periode($ref_periode);
            }else{
                $periode = new Periode();
                $periode->default_periode();
                if(isset($ref_periode))
                    $periode->copy($ref_periode);
            }
            $rep = $periode->into_db();
            if($rep != FALSE){
                $this->set_var("periode_id", $rep);
                return TRUE;
            }
            return TRUE;
        }
    }

?>
