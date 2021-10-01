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


    //  METHODES POUR EFFECTUER LES REQUETES (CRUD) À ENVOYER À LA BDD via query()   //

    public function select($table, $columns, $where = "", $more = "") { 
      global $log;

      $s = "SELECT ";

      /*  Warning: count(): Parameter must be an array or an object 
          that implements Countable 
          $columns est un string 
      echo '<br>'.__METHOD__.' $columns : ';
      var_dump($columns);
         ==> $columns : string(1) "*" 
         Alors qu'un tableau est envoyé : Database::from_db_by_id() 
       fin test 
      */
      for($i = 0; $i < count([$columns]); $i++){
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

    public function insert($table, $values, $more = "") {
        global $log;

        $s = "INSERT INTO `$table` (";

        $keys = "";
        $vals = "";
        $i = 0;

        //  stocke dans $s les colonnes et les valeurs
        //  il faut exclure la colonne qui stocke l'id
        //  pour les tables qui ont l'auto_increment

        //  Pour insérer un enregistrement sans données (1 seule colonne qui a AI, tapble `personne` par exemple) :
        //  insert into personne (`id`) values (null);
        foreach($values as $key => $value){
        
            $keys .= $key;
            
            //  pour `utilisateur`
            if(strcmp($value, "now()") == 0)
                $vals .= $value;
            //  *** pour toutes les autres tables : 
            //  si l'id n'est pas défini, on insère "NULL" pour lui attribuer un id avec l'auto_increment 
            elseif($key === 'id' && empty($value)) {
                $vals .= 'NULL';
            } else {
            //  *** sinon  on insère l'id dans la bdd 
                    $vals .= "'" . $value . "'";
            }
            
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
        global $log, $obj;
        $id = $this->insert_id;

        $s = "DELETE FROM `$table`";

        if(strlen($where) > 0)
            $s .= " WHERE " . $where;

        if(strlen($more) > 0)
            $s .= " " . $more;

        return $this->query($s);
    }

    //  Warning: Declaration of Database::query($requete) should be compatible with mysqli::query($query, $resultmode = NULL)
    /*  Depuis PHP7 il faut préciser quelle utilisation de $resultmode on va faire : MYSQLI_USE_RESULT ou MYSQLI_STORE_RESULT 
        Avant $resultmode = MYSQLI_USE_RESULT était appliqué par défaut
        mysqli::query ( string $query [, int $resultmode = MYSQLI_STORE_RESULT ] )
        J'ai mis MYSQLI_USE_RESULT, je sais pas si c'est le cas. 
        A changer si on voit que c'est pas bon 
    ***/
    public function query($requete, $resultmode = MYSQLI_USE_RESULT) {
        global $log;

        $log->i(trim($requete));
        $m = microtime(TRUE);
        
        //  *** tests-dispatch-database 
        // echo '<br>'.__METHOD__.'<br>$requete : ';
        // var_dump($requete);
        //  fin test 

        $result = parent::query($requete);

        $m = microtime(TRUE) - $m;
        if($result === FALSE){
            $log->e("SQL error : $this->error");
            return FALSE;
        }
        $log->d("exec time: ".($m*1000)." ms");

        return $result;
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
    // public function get_personnes($get_relations_conditions = TRUE, $attr = TRUE) {
    public function get_personnes($get_relations_conditions = TRUE, 
                                  $attr, 
                                  $no_accent) {
        $personnes = array();

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
                $personne->from_db(FALSE, 
                                    $get_relations_conditions, 
                                    $attr, 
                                    $no_accent);
                $personnes[$id] = $personne;
            }
        }
        return $personnes;
    }


    //  *** à tester avec CSVExport::export_relations 
    /*  *** fix-add-date
      Test avec propriété $date ajoutée à Relation 
      Récupère toutes les relations et retourne une liste des id des relations 
    */
    public function get_relations($get_relations_conditions = TRUE) { //  , $attr = TRUE, $no_accent = FALSE 
      $relations = array();

      $results = $this->select("relation", ["*"]);
      // $results = $this->query("
      //   SELECT * FROM relation LIMIT 20
      // ");
      if($results != FALSE && $results->num_rows) {
        while($row = $results->fetch_assoc()) {
          $id = $row["id"];
          $relation = new Relation($id, 
                                    $row["pers_source"], 
                                    $row["pers_destination"], 
                                    $row["statut_id"]);
          $relation->from_db();
          $relations[$id] = $relation;
        }
      }
      return $relations;
    }

    /*  (fix ok) 
    Testé sans succès, mais avant de me casser la tête
    à savoir pourquoi, j'ai fait avec Acte->get_contenu
    */
    //  *** Corrigé  // 
    //  *** doubon avec $acte->get_contenu() 
    public function get_contenu_acte($acte_id)
    {
      $result = $this->select(
        "acte_contenu",
        ["`contenu`"],
        "acte_id='$acte_id'"
      );
      return $result->fetch_assoc()["contenu"];
    }


    //  PRIVATE METHODS   //

    //  SELECT by id 
    // return NULL si rien trouvé
    public function from_db_by_id($obj){
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
    //  mis en public en déplaçant from_db() dans DatabaseEntity 
    // private function from_db_by_same($obj){
    public function from_db_by_same($obj){
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

    //  *** rewrite-noms-export
    public function from_db_personne_noms_prenoms($personne, $attr, $no_accent){ 

      //  *** $no_accent est le param de add_prenom() pour indiquer 
      //  si on veut les accents ou pas. C'est != de $row["no_accent"]'  
      $result = $this->query("
        SELECT prenom.id AS p_id, prenom, no_accent
        FROM prenom_personne INNER JOIN prenom
        ON prenom_personne.prenom_id = prenom.id
        WHERE prenom_personne.personne_id = '$personne->id'
        ORDER BY prenom_personne.ordre"
      );  
      if($result != FALSE && $result->num_rows > 0){
        while($row = $result->fetch_assoc())
          $personne->add_prenom(new Prenom($row["p_id"], 
                                          $row["prenom"], 
                                          $row["no_accent"]), 
                                $no_accent); 
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
                                      $row["attribut"]), 
                              $attr, 
                              $no_accent); 
        }
      } 
    }
    
    //  *** tests-has-memory 
    /*  *** appelée depuis personne_memory() : pas de re-création d'une personne déjà existante :
    ==> Dans html_entities on a (par ex.) la personne source avec ses infos, 
    on crée une new Personne($id) (has_memory()) pour récupérer les infos de la pers_destination, 
    from_db_personne_relations() crée une nouvelle personne pour la pers_destination.
    */
    //  SELECT relations by personne 
    // private function from_db_personne_relations($personne){
    public function from_db_personne_relations($personne){
      
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
          $this->from_db_relation_list_actes($relation);
          $personne->relations[] = $relation;
        }
      }
    }
    
    //  SELECT conditions by personne
    // private function from_db_personne_conditions($personne){
    public function from_db_personne_conditions($personne){
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
          $this->from_db_condition_list_actes($condition);
          $personne->conditions[] = $condition;
        }
      }
    }

    //  SELECT acte_has_condition conditions == acte  
    // private function from_db_acte_conditions($acte){
    public function from_db_acte_conditions($acte){
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
          $this->from_db_condition_list_actes($condition);
          $acte->conditions[] = $condition;
        }
      }
    }

    //  *** tests-dispatch-database
    //  SELECT acte_has_relation relations == acte 
    // private function from_db_acte_relations($acte){
    public function from_db_acte_relations($acte){
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
          $this->from_db_relation_list_actes($relation);
          $acte->relations[] = $relation;
        }
      }
    }


    //  ~~public~~  //

    //  *** tests-dispatch-database
    //  SELECT actes by condition 
    public function from_db_condition_list_actes($condition){
      $result = $this->select(
        "acte_has_condition",
        ["acte_id"],
        "condition_id='$condition->id'"
      );
      if($result != FALSE && $result->num_rows > 0){
        while($row = $result->fetch_assoc()) {
          // $relation->actes[] = new Acte($row["acte_id"]);
          $condition->actes[] = $row["acte_id"];
        }
      }
    }

    //  *** tests-dispatch-database 
    //  new Acte() manque pour CSVExport.php 
    //  corrigé dans Relation->get_date() 
    //  ==> causé problème ou il y était avant ? 
    //  SELECT actes by relation 
    public function from_db_relation_list_actes($relation){
      $result = $this->select(
        "acte_has_relation",
        ["acte_id"],
        "relation_id='$relation->id'"
      );
      if($result != FALSE && $result->num_rows > 0){
        while($row = $result->fetch_assoc()) {
          // $relation->actes[] = new Acte($row["acte_id"]);
          $relation->actes[] = $row["acte_id"];
          /*  *** fix-add-date
          Test avec propriété $date ajoutée à Relation 
          L'objet $relation est déjà créé dans from_db_relation_list_actes() 
          Pour chaque objet $elation, on récupère la date de la relation 
          */
          $this->from_db_relation_date($relation);
        }
      }
    }

    /*  *** fix-add-date
      Test avec propriété $date ajoutée à Relation 
      Récupérer la date de chaque relation 
      et l'attribuer à la propriété de l'objet : $relation->date 
    */
    public function from_db_relation_date($relation) {
      $result = $this->query(" 
        SELECT acte_has_relation.relation_id, acte.date_start 
        FROM `acte_has_relation` 
        INNER JOIN `acte`
        ON `acte_has_relation`.`acte_id` = `acte`.`id` 
        WHERE acte_has_relation.relation_id = '$relation->id'"
      );
      if($result != FALSE && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          $relation->date = $row["date_start"];
        }
      }
    }


    //  ~~PRIVATE METHODS~~   //

    //  SELECT personne by nom + prenom  
    //  Si prenom seul ou prénom + nom identiques : 
    //  retourne les ids des personnes prénoms+noms identiques
    //  pour alerte dans log.txt via from_db()
    //  et crée une nouvelle personne 
    // private function from_db_by_same_personne($personne){
    public function from_db_by_same_personne($personne){
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

            $ids_tmp = array();
            while($row = $result->fetch_assoc())
                $ids_tmp[] = $row["personne_id"];   

            //  *** pourquoi cette condition ? $ids est toujours NULL, 
            //  il n'a pas bougé depuis son init à NULL 
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

            $ids_tmp = array();
            while($row = $result->fetch_assoc())
                $ids_tmp[] = $row["personne_id"];

            //  *** $ids = $ids_tmp du foreach $personne->noms 
            if(isset($ids)) 
                $ids = array_intersect($ids, $ids_tmp);
            else 
                $ids = $ids_tmp;

            if(count($ids) == 0)
                return NULL;
        } 

        //  retourne les ids des identiques pour l'alerte 
        return $ids;
    }

    //  *** passée en public par le déplacement de from_db() dans DatabaseEntity  
    // private function updated_values($values_db, $values_obj){
    public function updated_values($values_db, $values_obj){
        $updated = array();

        if($values_db == NULL)
            return $values_obj;

        foreach($values_obj as $key => $val){
            if(!isset($values_db[$key]) || $values_db[$key] != $val)
                $updated[$key] = $val;
        }

        return $updated;
    }

    //  PUBLIC  //


    //  ~~PRIVATE METHODS~~   //

    //  *** ne sert que là, on peut pas la défactoriser ? 
    //  Non : elle appelle get_table_name() de toutes les classes' 
    //  et c'est préférable de n'avoir que des variables et méthodes dans into_db() 
    // private function into_db_update($obj, $values){
    public function into_db_update($obj, $values){
        if(count($values) == 0)
            return TRUE;

        return $this->update(
            $obj->get_table_name(),
            $values,
            "id='$obj->id'"
        );
    }

    // private function into_db_insert($obj, $values){
    public function into_db_insert($obj, $values){
        global $log;
        $result = FALSE;
        $max_try = 2;

        while($result === FALSE && $max_try > 0){
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

    /*  *** fix-add-date
      Test avec propriété $date ajoutée à Relation 
      Insérer chaque relation avec sa date (en paramètres) dans la base 
    */
    // public function into_db_relation_date($relation, $acte) {
    //     $mysqli->query("
    //         INSERT INTO `relation_date` (relation_id, date_start) 
    //         VALUES ('$relation->id', '$acte->date_start')
    //     ");
    // }

    //  INSERT données pour acte_has_condition
    public function into_db_acte_has_condition($acte, $condition){
        return $this->query("
            INSERT IGNORE `acte_has_condition` (acte_id, condition_id) VALUES ('$acte->id', '$condition->id')
        ");
    }

    //  *** Appelée dans Acte::remove_from_db() 
    public function remove_unused_prenoms_noms(){
        $this->delete("prenom", "id NOT IN (SELECT prenom_id FROM prenom_personne)");
        $this->delete("nom", "id NOT IN (SELECT nom_id FROM nom_personne)");
    }

  }

?>
