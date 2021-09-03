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
      ***/
      //  *** tests-dispatch-database  
    //   echo '<br>'.__METHOD__.' $columns : ';
    //   var_dump($columns);
      //    ==> $columns : string(1) "*" 
      //    Alors qu'un tableau est envoyé : Database::from_db_by_id() 
      //  fin test 

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

        //  insérer un enregistrement sans données (1 seule colonne qui a AI) :
        //  insert into personne (`id`) values (null);
        foreach($values as $key => $value){
        
            $keys .= $key;
            
            //  pour utilisateur
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
    //  Depuis PHP7 il faut préciser quelle utilisation de $resultmode on va faire : MYSQLI_USE_RESULT ou MYSQLI_STORE_RESULT 
    //  Avant $resultmode = MYSQLI_USE_RESULT était appliqué par défaut
    //  mysqli::query ( string $query [, int $resultmode = MYSQLI_STORE_RESULT ] )

    /*  J'ai mis MYSQLI_USE_RESULT, je sais pas si c'est le cas. A changer quand je saurai ***/
    public function query($requete, $resultmode = MYSQLI_USE_RESULT) {
        global $log;

        $log->i(trim($requete));
        $m = microtime(TRUE);
        
        //  *** tests-dispatch-database 
        // echo '<br>'.__METHOD__.' $requete : ';
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
    public function get_personnes($get_relations_conditions = TRUE, $attr = TRUE) {
        $personnes = [];
        //  *** rewrite-noms-export 
        // echo '<br>'.__METHOD__.'<br>attr : ';
        // var_dump($attr);
        //  fin test 
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
                if($attr == TRUE)
                  $personne->from_db(FALSE, $get_relations_conditions);
                else 
                  $personne->from_db(FALSE, $get_relations_conditions, FALSE);
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

    //  tous les cas de SELECT avant d'envoyer la requete INSERT 
    // public function from_db($obj,
    //   $update_obj = FALSE,
    //   $get_relations_conditions = TRUE){
    //   /*
    //     De ce qu'il me semble, $update_obj sert à renseigner l'id
    //     de $obj s'il ne l'est pas.
    //     En tout cas il ne sert à pas à indiquer si on veut
    //     modifier $obj :
    //     en pratique $obj est toujours rempli par les fonctions
    //     appelées ici.
    //   */
    //   global $log;

    //   $log->d("from database: ".get_class($obj)." id=$obj->id");
    //   $row = NULL;
    //   //  *** tests-dispatch-database
    // //   if(isset($obj->id)){
    // //     $row = $this->from_db_by_id($obj);
    //     /*  Déplacer les méthodes de from_db() qui son apparentées à des modèles en particulier, 
    //       pour pas avoir à tester si $obj est une personne par ex
    //       Test export personnes  
    //       Test detail_personne et detail_acte 
    //       Test import acte 
    //       Test export relations 
    //       Test fusion et dissocier --> bloquées par cors *** 
    //     */
    //     // if($obj instanceof Personne){
    //     //   $this->from_db_personne_noms_prenoms($obj);
    //     //   if($get_relations_conditions){
    //     //     $this->from_db_personne_relations($obj);
    //     //     $this->from_db_personne_conditions($obj);
    //     //   }
    //     // } else 
    //     // if($obj instanceof Acte && $get_relations_conditions){
    //     //   $this->from_db_acte_conditions($obj);
    //     //   $this->from_db_acte_relations($obj);
    //     // }
    //   // }else{ 
    //     // Pour import d'actes 
    //     //  *** tests-dispatch-database 
    //     // if($obj instanceof Personne)
    //     //   $row = $this->from_db_by_same_personne($obj);
    //     // else
    //       // $row = $this->from_db_by_same($obj);
    //   // }

    //   if($update_obj)
    //     $obj->result_from_db($row);
    //   return $row;
    // }

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

    //  *** tests-dispatch-database

    //  *** rewrite-noms-export
    //  test $attr = FALSE 
    //  SELECT personne by nom ou prenom 
    // private function from_db_personne_noms_prenoms($personne)'{'
    // public function from_db_personne_noms_prenoms($personne){ 
      public function from_db_personne_noms_prenoms($personne, $attr = FALSE){ 

        //  *** rewrite-noms-export 
        // echo '<br>'.__METHOD__.'<br>attr : ';
        // var_dump($attr);    //  false, ok :)
        //  fin test 

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

      if($attr == TRUE) {
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
      } else {
        $result = $this->query("
          SELECT nom.id as n_id, nom, no_accent, ordre
          FROM nom_personne INNER JOIN nom
          ON nom_personne.nom_id = nom.id
          WHERE nom_personne.personne_id = '$personne->id'
          ORDER BY nom_personne.ordre"
        );  
        if($result != FALSE && $result->num_rows > 0){
          while($row = $result->fetch_assoc()){
            $personne->add_nom( new Nom($row["n_id"],
                                        $row["nom"],
                                        $row["no_accent"]));
          }
        }
      }
    }
    
    //  *** tests-has-memory 
    /*  *** appelée depuis personne_memory() : pas de re-création d'une personne déjà existante :
    ==> Dans html_entities on a (par ex.) la personne source avec ses infos, 
    on crée une new Personne($id) (has_memory()) pour récupérer les infos de la pers_destination, 
    from_db_personne_relations() crée une nouvelle personne pour la pers_destination.
    */
    //  *** tests-dispatch-database
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

    //  *** tests-dispatch-database
    //  SELECT acte_has_condition conditions by acte  
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
    //  SELECT acte_has_relation relations by acte 
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

    //  public  //

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
    //  SELECT  actes by relation 
    public function from_db_relation_list_actes($relation){
      $result = $this->select(
        "acte_has_relation",
        ["acte_id"],
        "relation_id='$relation->id'"
      );
      if($result != FALSE && $result->num_rows > 0){
        while($row = $result->fetch_assoc())
          // $relation->actes[] = new Acte($row["acte_id"]);
          $relation->actes[] = $row["acte_id"];
      }
    }

    //  PRIVATE METHODS   //

    //  SELECT personne by nom + prenom  
    //  Pour l'instant : retourne les ids des personnes prénoms+noms identiques
    //  pour alerte dans log.txt via from_db()
    //  mais crée une nouvelle personne 
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

            $ids_tmp = [];
            while($row = $result->fetch_assoc())
                $ids_tmp[] = $row["personne_id"];   

            //  ==> pourquoi cette condition ? $ids est toujours NULL, 
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

            $ids_tmp = [];
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

        //  *** test sans-nom 
        // if(isset($ids)){
        //     //  *** array_shift($ids) retire le 1er mais du coup attribue l'id du 2è 
        //     //  il faut ou créer une nouvelle personne et alerter via log.txt 

        //     return ["id" => array_shift($ids)];     //  ==> remplacer array_shift ? 

        // }
        
        // return NULL;
        //  retourne les ids des identiques pour l'alerte 
        return $ids;
    }

    //  *** Cette méthode renvoie $values_obj 
    // private function updated_values($values_db, $values_obj){
    public function updated_values($values_db, $values_obj){
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

    // public function into_db($obj, $force_insert = FALSE, $skip_check_same = FALSE) {
    //     $result = FALSE;

    //     if(!$force_insert && !$obj->pre_into_db())
    //         return;

    //     //  *** Tester si $obj == quelle classe, pour appeler le bon from_db()
    //     // if(!$skip_check_same) {
    //     //     $values_db = $this->from_db($obj, FALSE, FALSE);
    //     // }
    //     if(isset($values_db["id"])) {
    //         $obj->id = $values_db["id"];
    //     }
    //     $values_obj = $obj->values_into_db();
    //     $values_updated = $this->updated_values($values_db, $values_obj);

    //     if(isset($values_db, $obj->id))
    //         $result = $this->into_db_update($obj, $values_updated);
    //     else
    //         $result = $this->into_db_insert($obj, $values_updated);

    //     $obj->post_into_db();

    //     if($result === FALSE)
    //         return FALSE;
        
    //     return $obj->id;
    // }

    //  PRIVATE METHODS   //

    //  *** ne sert que là, on peut pas la défactoriser ? 
    //  Non : elle appelle get_table_name() de toutes les classes 
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

    //  PUBLIC  //


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

    // //  *** tests-dispatch-database
    // //  déplacé dans Personne  
    // /*
    // Supprime de la base les personnes de la liste qui n'apparaissent dans aucune table.
    // Renvoie la liste des personnes supprimées.
    // */
    // public function purge_personnes($personnes) {
    //     $removed = [];

    //     foreach($personnes as $personne) {
    //         if($personne->remove_from_db(FALSE))
    //         $removed[] = $personne;
    //     }
    //     return $removed;
    // }

    //  *** Appelée dans Acte::remove_from_db() 
    public function remove_unused_prenoms_noms(){
        $this->delete("prenom", "id NOT IN (SELECT prenom_id FROM prenom_personne)");
        $this->delete("nom", "id NOT IN (SELECT nom_id FROM nom_personne)");
    }

  }

?>
