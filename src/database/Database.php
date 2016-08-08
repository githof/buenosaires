<?php

    include_once(ROOT."src/database/Acte.php");
    include_once(ROOT."src/database/Personne.php");
    include_once(ROOT."src/database/Relation.php");
    include_once(ROOT."src/database/Condition.php");
    include_once(ROOT."src/database/Nom.php");
    include_once(ROOT."src/database/Prenom.php");

    class Database extends mysqli{

        var $mutex;

        public function __construct(){
            global $log;

            parent::__construct(SQL_SERVER,
                                SQL_USER,
                                SQL_PASS,
                                SQL_DATABASE_NAME);

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

            $s .= " FROM `$table`";

            if(strlen($where) > 0)
                $s .= " WHERE " . $where;

            $s .= " " . $more;

            return $this->query($s);
        }

        public function insert($table, $values, $more = ""){
            global $log;

            $s = "INSERT INTO `$table` (";

            $keys = "";
            $vals = "";
            $i = 0;

            foreach($values as $key => $value){
                $keys .= $key;

                if(strcmp($value, "now()") == 0)
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

            return $this->query($s);
        }

        public function update($table, $values, $where, $more = ""){
            global $log;
            $s = "UPDATE `$table` SET ";

            $i = 0;

            foreach($values as $key => $value){
                $s .= " " . $key . " = ";

                if(strcmp($value, "now()") == 0)
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

            return $this->query($s);
        }

        public function delete($table, $where, $more = ""){
            global $log;

            $s = "DELETE FROM `$table`";

            if(strlen($where) > 0)
                $s .= " WHERE " . $where;

            if(strlen($more) > 0)
                $s .= " " . $more;

            return $this->query($s);
        }

        public function query($requete){
            global $log;

            $m = microtime(TRUE);
            $result = parent::query($requete);
            $m = microtime(TRUE) - $m;
            if($result === FALSE){
                $log->e("SQL error : $this->error");
                return FALSE;
            }
            $log->i(trim($requete));
            $log->d("exec time: ".($m*1000)." ms");
            return $result;
        }

        public function next_id($table){
            global $log, $mysqli;

            if($table === "personne"){
                $result = $mysqli->select(
                    "variable",
                    ["*"],
                    "nom='PERSONNE_ID_MAX'"
                );
                if($result != FALSE && $result->num_rows == 1){
                    $row = $result->fetch_assoc();
                    $value = intval($row["valeur"]) +1;
                    $mysqli->update("variable", ["valeur" => $value], "nom='PERSONNE_ID_MAX'");
                    return $row["valeur"];
                }
                return FALSE;
            }

            $database_name = SQL_DATABASE_NAME;
            $s = "SELECT AUTO_INCREMENT as id FROM information_schema.tables WHERE table_name='$table' AND table_schema='$database_name'";

            $result = $this->query($s);

            if($result->num_rows != 1)
                return FALSE;

            $row = $result->fetch_assoc();
            return $row["id"];
        }

        public function start_transaction(){
            return $this->query("START TRANSACTION");
        }

        public function end_transaction(){
            return $this->query("COMMIT");
        }



        public function from_db($obj, $update_obj = FALSE){
            $row = NULL;
            if(isset($obj->id)){
                $row = $this->from_db_by_id($obj);
                if($obj instanceof Personne)
                    $this->from_db_personne_noms_prenoms($obj);
            }else{
                if($obj instanceof Personne)
                    $row = $this->from_db_by_same_personne($obj);
                else
                    $row = $this->from_db_by_same($obj);
            }

            if($update_obj)
                $obj->result_from_db($row);
            return $row;
        }

        private function from_db_by_id($obj){
            $row = NULL;
            $result = $this->select(
                $obj->get_table_name(),
                ["*"],
                "id='$obj->id'"
            );
            if($result->num_rows == 1)
                $row = $result->fetch_assoc();
            return $row;
        }

        private function from_db_by_same($obj){
            $row = NULL;
            $s = "";
            $i = 0;
            $values = $obj->get_same_values();
            if($values == NULL){
                $row = NULL;
                break;
            }

            foreach ($values as $k => $v) {
                if(strcmp($v, "NULL") == 0)
                    $s .= "$k IS NULL";
                else
                    $s .= $k . "='" . $v . "'";

                if($i < count($values) -1)
                    $s .= " AND ";
                $i++;
            }

            $result = $this->select(
                $obj->get_table_name(),
                ["*"],
                $s
            );
            if($result->num_rows == 1)
                $row = $result->fetch_assoc();
            return $row;
        }

        private function from_db_personne_noms_prenoms($personne){
            $result = $this->query("
                SELECT prenom.id AS p_id, prenom, no_accent
                FROM prenom_personne INNER JOIN prenom
                ON prenom_personne.prenom_id = prenom.id
                WHERE prenom_personne.personne_id = '$personne->id'
                ORDER BY prenom_personne.ordre"
            );
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc())
                    $personne->add_prenom(new Prenom($row["p_id"], $row["prenom"], $row["no_accent"]));
            }

            $result = $this->query("
                SELECT nom.id AS n_id, nom, no_accent, value, attribut.id AS a_id
                FROM nom_personne INNER JOIN nom
                ON nom_personne.nom_id = nom.id
                LEFT JOIN attribut
                ON attribut.id = nom.attribut_id
                WHERE nom_personne.personne_id = '$personne->id'
                ORDER BY nom_personne.ordre"
            );
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $attribut = NULL;
                    if(isset($row["value"]))
                        $attribut = new Attribut($row["a_id"], $row["value"]);
                    $personne->add_nom(new Nom($row["n_id"], $row["nom"], $row["no_accent"], $attribut));
                }
            }
        }

        private function from_db_by_same_personne($personne){
            $ids = NULL;
            $ids_tmp = NULL;

            foreach($personne->noms as $k => $nom){
                $result = $this->query("
                SELECT personne_id
                FROM nom_personne INNER JOIN nom
                ON nom_personne.nom_id = nom.id
                WHERE nom.no_accent = '$nom->no_accent'
                ");
                if($result === FALSE || $result->num_rows == 0)
                    return FALSE;

                $ids_tmp = [];
                while($row = $result->fetch_assoc())
                    $ids_tmp[] = $row["personne_id"];

                if(isset($ids))
                    $ids = array_intersect($ids, $ids_tmp);
                else
                    $ids = $ids_tmp;

                if(count($ids) == 0)
                    return FALSE;
            }

            foreach($personne->prenoms as $k => $prenom){
                $result = $this->query("
                SELECT personne_id
                FROM prenom_personne INNER JOIN prenom
                ON prenom_personne.prenom_id = prenom.id
                WHERE prenom.no_accent = '$prenom->no_accent'
                ");
                if($result === FALSE || $result->num_rows == 0)
                    return NULL;

                $ids_tmp = [];
                while($row = $result->fetch_assoc())
                    $ids_tmp[] = $row["personne_id"];

                if(isset($ids))
                    $ids = array_intersect($ids, $ids_tmp);
                else
                    $ids = $ids_tmp;

                if(count($ids) == 0)
                    return NULL;
            }

            if(isset($ids)){
                return ["id" => array_shift($ids)];
            }
            return NULL;
        }

        private function updated_values($values_db, $values_obj){
            $updated = [];

            if($values_db == NULL)
                return $values_obj;

            foreach($values_db as $key => $val){
                if(isset($values_obj[$key]) && $values_obj[$key] != $val)
                    $updated[$key] = $values_obj[$key];
            }

            foreach($values_obj as $key => $val)
                $updated[$key] = $val;

            return $updated;
        }

        public function into_db($obj){
            $result = FALSE;
            $new_id = NULL;

            if(!$obj->pre_into_db())
                return;

            $values_db = $this->from_db($obj);
            if(isset($values_db["id"]))
                $obj->id = $values_db["id"];
            $values_obj = $obj->values_into_db();
            $values_updated = $this->updated_values($values_db, $values_obj);

            if(isset($values_db, $obj->id))
                $result = $this->into_db_update($obj, $values_updated);
            else
                $result = $this->into_db_insert($obj, $values_updated);

            $obj->post_into_db();

            if($result === FALSE)
                return FALSE;
            return $obj->id;
        }

        private function into_db_update($obj, $values){
            if(count($values) == 0)
                return TRUE;

            return $this->update(
                $obj->get_table_name(),
                $values,
                "id='$obj->id'"
            );
        }

        private function into_db_insert($obj, $values){
            global $log;
            $new_id = NULL;
            $result = FALSE;
            $max_try = 2;

            while($result === FALSE && $max_try > 0){
                if(!isset($obj->id)){
                    $new_id = $this->next_id($obj->get_table_name());
                    if($new_id == 0){
                        $log->e("Aucun nouvel id trouvé pour l'insert dans $obj->table_name");
                        return FALSE;
                    }
                    $obj->id = $new_id;
                }

                $values["id"] = $obj->id;
                $result = $this->insert($obj->get_table_name(), $values);
                $max_try--;
            }
            return $result;
        }

        public function into_db_prenom_personne($personne, $prenom, $ordre){
            $values = [
                "personne_id" => $personne->id,
                "prenom_id" => $prenom->id,
                "ordre" => $ordre
            ];
            return $this->insert(
                "prenom_personne",
                $values,
                "ON DUPLICATE KEY UPDATE ordre='$ordre'"
            );
        }

        public function into_db_nom_personne($personne, $nom, $ordre){
            $values = [
                "personne_id" => $personne->id,
                "nom_id" => $nom->id,
                "ordre" => $ordre
            ];
            return $this->insert(
                "nom_personne",
                $values,
                "ON DUPLICATE KEY UPDATE ordre='$ordre'"
            );
        }

        public function into_db_acte_has_relation($acte, $relation){
            return $this->query("
            INSERT IGNORE `acte_has_relation` (acte_id, relation_id) VALUES ('$acte->id', '$relation->id')
            ");
        }

        public function into_db_acte_has_condition($acte, $condition){
            return $this->query("
            INSERT IGNORE `acte_has_condition` (acte_id, condition_id) VALUES ('$acte->id', '$condition->id')
            ");
        }
    }

?>
