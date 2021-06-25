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

      parent::__construct(SQL_SERVER,
                          SQL_USER,
                          SQL_PASS,
                          SQL_DATABASE_NAME);

      if(mysqli_connect_error()){
        $log->e("Erreur de connexion (" . mysqli_connect_errno() . ') ' . mysqli_connect_error());
      }
    }

    //  METHODES POUR EFFECTUER LES REQUETES (CRUD) À ENVOYER À LA BDD   //

    public function select($table, $columns, $where = "", $more = ""){
      global $log;

      $s = "SELECT ";

      //  ***   Erreurs sur $columns :
      //  Warning: count(): Parameter must be an array or an object that implements Countable 
      //  in /home/morgan/internet/buenosaires/src/class/io/Database.php on line 33 et line 35 //
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

    //  envoie les requêtes (méthodes précédentes) à la bdd //
    //  Warning: Declaration of Database::query($requete) should be compatible with mysqli::query($query, $resultmode = NULL)
    //  Depuis PHP7 il faut préciser quelle utilisation de $resultmode on va faire : MYSQLI_USE_RESULT ou MYSQLI_STORE_RESULT 
    //  Avant $resultmode = MYSQLI_USE_RESULT était appliqué par défaut
    //  mysqli::query ( string $query [, int $resultmode = MYSQLI_STORE_RESULT ] )
    public function query($requete, $resultmode = MYSQLI_USE_RESULT){
      global $log;

      //  *** test
      // echo '<br>'.__METHOD__.'<br>';
      // var_dump($requete);
      //  fin test 

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

    //  Voir si auto_increment automatique ne serait pas mieux ? // 
    //  si on doit ajouter un enregistrement : checke la prochaine valeur de l'auto_increment et l'affecte comme id à l'enregistrement 
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

      //  ***  Problème d'auto_increment pas (toujours) incrémenté //
      //  Utilisé SELECT max(id) à la place de SELECT AUTO_INCREMENT ... FROM information_schema ... //
      //  Voir morgan/todoM.txt sur Dropbox   //
      $database_name = SQL_DATABASE_NAME;

      //  *** autre requête pour récupérer la valeur de l'AI 
      // $s = "SELECT AUTO_INCREMENT as id FROM information_schema.tables WHERE table_name='$table' AND table_schema='$database_name'";
      $s = "SELECT max(id) from `$table`";

      $result = $this->query($s);
      if($result->num_rows != 1)
          return FALSE;

      $row = $result->fetch_assoc();
      
      //  *** Ajout 1 à la valeur retournée pour simuler l'auto_increment
      // echo '<br>$row max(id) : ';
      // var_dump($row);
      $value = intval($row['max(id)']) +1;
      // echo '<br>$value+1 : '.var_dump($value);

      return $value;
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
    public function get_personnes($get_relations_conditions = TRUE)
    {
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

    //  *** à tester avec CSVExport::export_relations 
    // public function get_relations() {
    //   $relations = [];

    //   $results = $this->select("relation", ["*"]);
    //   if($results != FALSE && $results->num_rows) {
    //     while($row = $results->fetch_assoc()) {
    //       $relation = new Relation();
    //       $relation->result_from_db($row);
    //       $relations[$id] = $relation;
    //     }
    //   }
    //   return $relations;
    // }

    /*  (fix ok) 
    Testé sans succès, mais j'ai avant de me casser la tête
    à savoir pourquoi, j'ai fait avec Acte->get_contenu
    */
    //  *** Corrigé  // 
    public function get_contenu_acte($acte_id)
    {
      $result = $this->select(
        "acte_contenu",
        ["`contenu`"],
        "acte_id='$acte_id'"
      );
      return $result->fetch_assoc()["contenu"];
    }

    //  tous les cas de SELECT avant d'envoyer la requete INSERT 
    public function from_db($obj,
      $update_obj = FALSE,
      $get_relations_conditions = TRUE){
      /*
        De ce qu'il me semble, $update_obj sert à renseigner l'id
        de $obj s'il ne l'est pas.
        En tout cas il ne sert à pas à indiquer si on veut
        modifier $obj :
        en pratique $obj est toujours rempli par les fonctions
        appelées ici.
      */
      global $log;

      $log->d("from database: ".get_class($obj)." id=$obj->id");
      $row = NULL;
      if(isset($obj->id)){
        $row = $this->from_db_by_id($obj);
        if($obj instanceof Personne){
          $this->from_db_personne_noms_prenoms($obj);
          if($get_relations_conditions){
            $this->from_db_personne_relations($obj);
            $this->from_db_personne_conditions($obj);
          }
        }else if($obj instanceof Acte && $get_relations_conditions){
          $this->from_db_acte_conditions($obj);
          $this->from_db_acte_relations($obj);
        }
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

    //  SELECT by id 
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

    //  SELECT by value 
    private function from_db_by_same($obj){
      $row = NULL;
      $s = "";
      $i = 0;
      $values = $obj->get_same_values();
      if($values == NULL){
        $row = NULL;
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

    //  SELECT personne by nom ou prenom 
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

    //  SELECT personne by condition
    private function from_db_personne_conditions($personne){
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

    //  SELECT personne by relation 
    private function from_db_personne_relations($personne){
      $result = $this->select("relation", ["*"], "pers_source_id='$personne->id' OR pers_destination_id='$personne->id'");
      $pers_source = NULL;
      $pers_destination = NULL;
      if($result != FALSE && $result->num_rows > 0){
        while($row = $result->fetch_assoc()){
          if($row["pers_source_id"] == $personne->id){
            $pers_source = $personne;
            $pers_destination = new Personne($row["pers_destination_id"]);
          }else{
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

    //  SELECT acte_has_condition 
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

    //  SELECT acte_has_relation 
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

    //  SELECT conditions par acte 
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

    //  SELECT relations par acte 
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

    //  SELECT personne by nom + prenom  
    //  ==> factoriser avec from_db_personne_noms_prenoms ?   *** 
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

    //  attribuer une id aux objets (depuis le xml, ou depuis la bdd si différents) 
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

    //  stocke toutes les données avec id avant enregistrement 
    public function into_db($obj, $force_insert = FALSE, $skip_check_same = FALSE){
      $result = FALSE;
      $new_id = NULL;

      if(!$force_insert && !$obj->pre_into_db())
        return;

      if(!$skip_check_same)
        $values_db = $this->from_db($obj, FALSE, FALSE);
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

    //  données pour UPDATE 
    private function into_db_update($obj, $values){
      if(count($values) == 0)
        return TRUE;

      return $this->update(
        $obj->get_table_name(),
        $values,
        "id='$obj->id'"
      );
    }

    //  données pour insert  
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

    //  INSERT données pour prenom personne 
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

    //  INSERT données pour nom personne 
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
      return $this->insert(
        "nom_personne",
        $values,
        "ON DUPLICATE KEY UPDATE ordre='$ordre'$attr"
      );
    }

    //  INSERT données pour acte_has_relation 
    public function into_db_acte_has_relation($acte, $relation){
      return $this->query("
      INSERT IGNORE `acte_has_relation` (acte_id, relation_id) VALUES ('$acte->id', '$relation->id')
      ");
    }

    //  INSERT données pour acte_has_condition
    public function into_db_acte_has_condition($acte, $condition){
      return $this->query("
      INSERT IGNORE `acte_has_condition` (acte_id, condition_id) VALUES ('$acte->id', '$condition->id')
      ");
    }

    /*
    Supprime de la base les personnes de la liste qui n''apparaissent dans aucune table.
    Renvoie la liste des personnes supprimées.
    */
    public function purge_personnes($personnes)
    {
      $removed = [];

      foreach($personnes as $personne)
      {
        if($personne->remove_from_db(FALSE))
          $removed[] = $personne;
      }

      return $removed;
    }

    //  supprime les nom ou prénoms sans personne_id 
    //  ==> l'ajouter à $personne->remove_from_db ou dans Database::purge_personnes ? *** 
    public function remove_unused_prenoms_noms(){
      $this->delete("prenom", "id NOT IN (SELECT prenom_id FROM prenom_personne)");
      $this->delete("nom", "id NOT IN (SELECT nom_id FROM nom_personne)");
    }
  }

?>
