<?php

include_once(ROOT."src/class/model/Acte.php");
include_once(ROOT."src/class/model/Personne.php");
include_once(ROOT."src/class/model/Relation.php");
include_once(ROOT."src/class/model/Condition.php");
include_once(ROOT."src/class/model/Nom.php");
include_once(ROOT."src/class/model/Prenom.php");

class Database extends mysqli{

    public function __construct(){
        global $log;

        parent::__construct(SQL_SERVER, // erreur 210128 soon deprecated ***
                            SQL_USER, //  "
                            SQL_PASS, //  "
                            SQL_DATABASE_NAME); //  " + erreur : Warning: mysqli::__construct(): (HY000/2002): php_network_getaddresses: getaddrinfo failed: Temporary failure in name resolution in /home/morgan/internet/buenosaires/src/class/io/Database.php on line 18 ***

        if(mysqli_connect_error()){
            $log->e("Erreur de connexion (" . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }
    }

    //  docu ***
    //  requête select complétée avec les données envoyées via d'autres fichiers
    //  ==> tracer l'origine de ces données
    public function select($table, $columns, $where = "", $more = "") {    //  d'où viennent ces données ?  ***
        global $log;

        $s = "SELECT ";

            /*  Warning: count(): Parameter must be an array or an object 
                that implements Countable 
            ***/
            for($i = 0; $i < count($columns); $i++){
                $s .= $columns[$i];
                if($i < count($columns) -1)
                    $s .= ", ";
            }

        $s .= " FROM `$table`";

        if(strlen($where) > 0)
            $s .= " WHERE " . $where;

        $s .= " " . $more;

        //  docu ***
        // echo '<br>'. __METHOD__;
        // echo '<br>'.$s;

        return $this->query($s);
    }

    //  docu ***
    //  requête insert complétée avec les données envoyées via d'autres méthodes
    //  ==> tracer l'origine de ces données
    //  il manque (au moins) $values ***
    public function insert($table, $values, $more = "") {
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
        //  docu ***
        //  *** cf outputs/Database-insert.txt 
        //  *** https://stackoverflow.com/questions/41354898/method-and-function
        // echo '<br>'. __METHOD__;
        // echo  $s;

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

    //  docu ***
    //  requête delete complétée avec les données envoyées via d'autres fichiers
    //  ==> tracer l'origine de ces données
    public function delete($table, $where, $more = ""){
        global $log;

        $s = "DELETE FROM `$table`";

        if(strlen($where) > 0)
            $s .= " WHERE " . $where;

        if(strlen($more) > 0)
            $s .= " " . $more;

        return $this->query($s);
    }

    //  Warning: Declaration of Database::query($requete) should be compatible with mysqli::query($query, $resultmode = NULL)
    //  Depuis PHP7 il faut préciser quelle utilisation de $resultmode on va faire : MYSQLI_USE_RESULT ou MYSQLI_STORE_RESULT 
    //  Avant $resultmode = MYSQLI_USE_RESULT était appliqué par défaut
    //  mysqli::query ( string $query [, int $resultmode = MYSQLI_STORE_RESULT ] )

    /*  J'ai mis MYSQLI_USE_RESULT, je sais pas si c'est le cas. A changer quand je saurai ***/
    public function query($requete, $resultmode = MYSQLI_USE_RESULT) {    //  ***
        global $log;

        $log->i(trim($requete));
        $m = microtime(TRUE);
        $result = parent::query($requete);
        $m = microtime(TRUE) - $m;
        if($result === FALSE){
            $log->e("SQL error : $this->error");
            return FALSE;
        }
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

    // À généraliser, j'en avais besoin je l'ai mis là tant qu'à
    // faire
    // [ id => personne ]
    public function get_personnes($get_relations_conditions = TRUE) {
        $personnes = [];

        /*
            pas du tout optimal : je fais un premier select pour
            avoir la liste des ids, puis un select par id
            C'est juste pour pouvoir utiliser la fonction from_db où a
            priori tous les cas sont traités
        */
        $results = $this->select("personne", ["id"]);
        if($results != FALSE && $results->num_rows){
            while($row = $results->fetch_assoc()){
                $id = $row["id"];
                $personne = new Personne($id);
                $this->from_db($personne, $get_relations_conditions);
                $personnes[$id] = $personne;
            }
        }
        return $personnes;
    }

    /*
    Testé sans succès, mais j'ai avant de me casser la tête
    à savoir pourquoi, j'ai fait avec Acte->get_contenu
    */
    public function get_contenu_acte($acte_id) {
        $result = $this->select("acte_contenu",
                                ["contenu"],
                                "acte_id='$acte_id'"
        );
        $contenu = $result->fetch_assoc()["contenu"];
    }

    public function from_db($obj,
    $update_obj = FALSE,
    $get_relations_conditions = TRUE) {
    /*
    De ce qu'il me semble, $update_obj sert à renseigner l'id
    de $obj s'il ne l'est pas.
    En tout cas il ne sert à pas à indiquer si on veut
    modifier $obj :
    en pratique $obj est toujours rempli par les fonctions
    appelées ici.
    */
        global $log;

        $log->d("from database: ".get_class($obj)." id=$obj->id");  //  $obj->id : null log.txt ***

        $row = NULL;
        if(isset($obj->id)){
            $row = $this->from_db_by_id($obj);
            if($obj instanceof Personne){
            $this->from_db_personne_noms_prenoms($obj);
            if($get_relations_conditions){
                $this->from_db_personne_relations($obj);
                $this->from_db_personne_conditions($obj);
            }
            } else if ($obj instanceof Acte && $get_relations_conditions){
            $this->from_db_acte_conditions($obj);
            $this->from_db_acte_relations($obj);
            }
        } else {
            if($obj instanceof Personne)
                $row = $this->from_db_by_same_personne($obj);
            else
                $row = $this->from_db_by_same($obj);
        }

        if($update_obj)
            $obj->result_from_db($row);
        return $row;
    }

    //  PRIVATE METHODS   //

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
//                break;  
//  Fatal error: 'break' not in the 'loop' or 'switch' context  ***
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

    private function from_db_personne_noms_prenoms($personne) {
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
            SELECT nom.id as n_id, nom, no_accent, attribut, ordre
            FROM nom_personne INNER JOIN nom
            ON nom_personne.nom_id = nom.id
            WHERE nom_personne.personne_id = '$personne->id'
            ORDER BY nom_personne.ordre"
        );
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
            $personne->add_nom( new Nom($row["n_id"],
                                        $row["nom"],
                                        $row["no_accent"],
                                        $row["attribut"]));
            }
        }
    }

    private function from_db_personne_conditions($personne) {
        $result = $this->select("condition", ["*"], "personne_id='$personne->id'");
        $condition = NULL;
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
            $condition = new Condition(
                $row["id"],
                $row["text"],
                $personne,
                $row["source_id"]
            );
            $this->from_db_condition_list_acte($condition);
            $personne->conditions[] = $condition;
            }
        }
    }

    private function from_db_personne_relations($personne) {
        $result = $this->select("relation", ["*"], "pers_source_id='$personne->id' OR pers_destination_id='$personne->id'");
        $pers_source = NULL;
        $pers_destination = NULL;
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
            if($row["pers_source_id"] == $personne->id) {
                $pers_source = $personne;
                $pers_destination = new Personne($row["pers_destination_id"]);
            } else {
                $pers_source = new Personne($row["pers_source_id"]);
                $pers_destination = $personne;
            }
            $relation = new Relation(
                $row["id"],
                $pers_source,
                $pers_destination,
                $row["statut_id"]
            );
            $this->from_db_relation_list_acte($relation);
            $personne->relations[] = $relation;
            }
        }
    }

    private function from_db_acte_conditions($acte){
        $result = $this->query("
            SELECT *
            FROM acte_has_condition INNER JOIN `condition`
            ON acte_has_condition.condition_id = `condition`.id
            WHERE acte_has_condition.acte_id = '$acte->id'
        ");
        $condition = NULL;
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
            $condition = new Condition(
                $row["id"],
                $row["text"],
                new Personne($row["personne_id"]),
                $row["source_id"]
            );
            $this->from_db_condition_list_acte($condition);
            $acte->conditions[] = $condition;
            }
        }
    }

    private function from_db_acte_relations($acte){
        $result = $this->query("
            SELECT *
            FROM acte_has_relation INNER JOIN relation
            ON acte_has_relation.relation_id = relation.id
            WHERE acte_has_relation.acte_id = '$acte->id'
        ");
        $relation = NULL;
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
            $relation = new Relation(
                $row["id"],
                new Personne($row["pers_source_id"]),
                new Personne($row["pers_destination_id"]),
                $row["statut_id"]
            );
            $this->from_db_relation_list_acte($relation);
            $acte->relations[] = $relation;
            }
        }
    }

    //  public  //

    public function from_db_condition_list_acte($condition){
        $result = $this->select(
            "acte_has_condition",
            ["acte_id"],
            "condition_id='$condition->id'"
        );
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc())
            $condition->actes[] = new Acte($row["acte_id"]);
        }
    }

    public function from_db_relation_list_acte($relation){
        $result = $this->select(
            "acte_has_relation",
            ["acte_id"],
            "relation_id='$relation->id'"
        );
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc())
            $relation->actes[] = new Acte($row["acte_id"]);
        }
    }

    //  PRIVATE METHODS   //

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

        foreach($values_obj as $key => $val){
            if(!isset($values_db[$key]) || $values_db[$key] != $val)
            $updated[$key] = $val;
        }

        return $updated;
    }

    //  PUBLIC  //

    public function into_db($obj, $force_insert = FALSE, $skip_check_same = FALSE) {
        $result = FALSE;
        $new_id = NULL;

        if(!$force_insert && !$obj->pre_into_db())
            return;

        if(!$skip_check_same)
            $values_db = $this->from_db($obj, FALSE, FALSE);
        if(isset($values_db["id"]))
            $obj->id = $values_db["id"];
        $values_obj = $obj->values_into_db();                       //  *** ne prend pas l'id (ok)
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

    //  PRIVATE METHODS   //

    private function into_db_update($obj, $values){
        if(count($values) == 0)
            return TRUE;

        return $this->update(
            $obj->get_table_name(),
            $values,
            "id='$obj->id'"
        );
    }

    //  docu ***
    //  manque l'id des tables (voir *** dans le while())   ***
    private function into_db_insert($obj, $values){
        global $log;
        $new_id = NULL;
        $result = FALSE;
        $max_try = 2;

        while($result === FALSE && $max_try > 0){
            if(!isset($obj->id)){
                $new_id = $this->next_id($obj->get_table_name());
                if($new_id == 0){
                    /* Notice: Undefined property: Prenom::$table_name in /home/morgan/internet/buenosaires/src/class/io/Database.php on line 553 ***/  
                    //  *** Remplacé $obj->table_name par $obj->get_table_name
                    $log->e("Aucun nouvel id trouvé pour l'insert dans $obj->get_table_name");
                    return FALSE;
                }
            }

            $values["id"] = $obj->id;
            //  test    ***     ID : NULL
            // echo '<br>$obj->get_table_name : ';   
            // var_dump($obj->get_table_name());  //   nom de la table
            // echo '<br>$values : ';   
            // var_dump($values);  //  tableau des valeurs pour la table, manque l'id
            //  fin test  ***
            $result = $this->insert($obj->get_table_name(), $values);
            $max_try--;
        }
        //  test    ***     
        // echo '<br>$result : ';   
        // var_dump($result);  
        //  fin test  ***
        return $result;
    }

    //  PUBLIC  //

    public function into_db_prenom_personne($personne, $prenom, $ordre){
        $values = [
            "personne_id" => $personne->id,
            "prenom_id" => $prenom->id,
            "ordre" => $ordre
        ];
        // test *** // echo '<br> into_db_prenom_personne $values["personne_id"] : ';   // var_dump($values["personne_id"]);
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
        $attr = "";
        if(isset($nom->attribut)){
            $values["attribut"] = $nom->attribut;
            $attr = ", attribut='$nom->attribut'";
        }
        // $values["personne_id"] manque parfois et pas forcément les bons numéros   ***
        // echo '<br> into_db_nom_personne $values["personne_id"] : ';
        // var_dump($values["personne_id"]);
        //  ***  fin test
        return $this->insert(
            "nom_personne",
            $values,
            "ON DUPLICATE KEY UPDATE ordre='$ordre'$attr"
        );
    }

    public function into_db_acte_has_relation($acte, $relation){
        //  ***     vide
        // $qr = $this->query("INSERT IGNORE `acte_has_relation` (acte_id, relation_id) VALUES ('$acte->id', '$relation->id')");
        // echo '<br> $qr : ';
        // echo $q;
        //  ***  fin test
        return $this->query("
            INSERT IGNORE `acte_has_relation` (acte_id, relation_id) VALUES ('$acte->id', '$relation->id')
        ");
    }

    public function into_db_acte_has_condition($acte, $condition){
        //  *** vide
        $qc = $this->query("INSERT IGNORE `acte_has_condition` (acte_id, condition_id) VALUES ('$acte->id', '$condition->id')");
        echo '<br>$qc : ';
        echo $qc;
        //  fin
        return $this->query("
        INSERT IGNORE `acte_has_condition` (acte_id, condition_id) VALUES ('$acte->id', '$condition->id')
        ");
    }

    /*
    Supprime de la base les personnes de la liste qui n'apparaissent dans aucune table.
    Renvoie la liste des personnes supprimées.
    */
    public function purge_personnes($personnes) {
        $removed = [];

        foreach($personnes as $personne) {
            if($personne->remove_from_db(FALSE))
            $removed[] = $personne;
        }
        return $removed;
    }

    //  testé de commenter cette méthode pour vérifier qu'elle ne retire pas des personnes indus 
    //  ==> non c'est pareil, pas de nom, prénom, 1 seule relation etc  ***
    public function remove_unused_prenoms_noms(){
        $this->delete("prenom", "id NOT IN (SELECT prenom_id FROM prenom_personne)");
        $this->delete("nom", "id NOT IN (SELECT nom_id FROM nom_personne)");
    }
}

?>