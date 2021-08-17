<?php


abstract class DatabaseEntity implements DatabaseIO {

  //  DATABASEIO  

  public function get_table_name(){
    return strtolower(get_class($this));
  }
  // public function get_same_values(){}
  // public function result_from_db($row){}
  // public function values_into_db(){}
  // public function pre_into_db(){}
  // public function post_into_db(){}


 public function from_db(
      $update_obj = TRUE,
      $get_relations_conditions = TRUE)
  {
    /*
      De ce qu'il me semble, $update_obj sert à renseigner l'id
      de $this s'il ne l'est pas.
      En tout cas il ne sert à pas à indiquer si on veut
      modifier $this :
      en pratique $this est toujours rempli par les fonctions
      appelées ici.
    */
    global $log, $mysqli;

    $log->d("from database: "
            .get_class($this)." id=$this->id");

    //  *** tests-dispatch-database
    if(isset($this->id)){
      $row = $mysqli->from_db_by_id($this);
    } else
        $row = $mysqli->from_db_by_same($this);

    if($update_obj)
      $this->result_from_db($row);

    return $row;
  } 

  //  *** Pour import d'un acte 
  public function into_db($obj, $force_insert = FALSE, $skip_check_same = FALSE) {  
    global $mysqli;

    $result = FALSE;
    
    if(!$force_insert && !$obj->pre_into_db())
        return;

    //  *** Tester si $obj == quelle classe, pour appeler le bon from_db() 
    if(!$skip_check_same) {
        // $values_db = $mysqli->from_db($obj, FALSE, FALSE);
        $values_db = $obj->from_db(FALSE, FALSE);
    }
    
    if(isset($values_db["id"])) { 
            $obj->id = $values_db["id"];
    }
    $values_obj = $obj->values_into_db();
    $values_updated = $mysqli->updated_values($values_db, $values_obj);

    if(isset($values_db, $obj->id))
        $result = $mysqli->into_db_update($obj, $values_updated);
    else
        $result = $mysqli->into_db_insert($obj, $values_updated);

    $obj->post_into_db();

    if($result === FALSE)
        return FALSE;
    
    return $obj->id;
  }

}




?>