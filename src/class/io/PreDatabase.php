<?php


abstract class PreDatabase {

  // public $obj;

  // public function __construct($obj) {
  //   $this->$obj = $obj;
  // }

  public function from_db($obj, $update_obj = FALSE, $get_relations_conditions = TRUE){
    /*
      De ce qu'il me semble, $update_obj sert à renseigner l'id
      de $obj s'il ne l'est pas.
      En tout cas il ne sert à pas à indiquer si on veut
      modifier $obj :
      en pratique $obj est toujours rempli par les fonctions
      appelées ici.
    */
    global $log, $mysqli;

    $log->d("from database: ".get_class($obj)." id=$obj->id");
    // $log->d("from database: ".$obj." id=$obj->id");
    // $row = NULL;
    //  *** tests-dispatch-database
    if(isset($obj->id)){
      $row = $mysqli->from_db_by_id($obj);
    }
    //  *** déplacé dans Personne : 
    //   // if(get_class($obj) == 'Personne'){
    //   //   $mysqli->from_db_personne_noms_prenoms($obj);
    //   //   if($get_relations_conditions){
    //   //     $mysqli->from_db_personne_relations($obj);
    //   //     $mysqli->from_db_personne_conditions($obj);
    //   //   }
    //   // } 
    //  *** déplacé dans Acte : 
    //   // elseif(get_class($obj) == 'Acte' && $get_relations_conditions){
    //   //   $mysqli->from_db_acte_conditions($obj);
    //   //   $mysqli->from_db_acte_relations($obj);
    //   // }
    // }else{ 
      //  *** déplacé dans Acte et Personne : 
      // Pour import d'actes 
      //  *** tests-dispatch-database 
      // if($obj instanceof Personne)
      //   $row = $mysqli->from_db_by_same_personne($obj);
      else
        $row = $mysqli->from_db_by_same($obj);
    
    if($update_obj)
      $obj->result_from_db($row);

    //  *** tests-dispatch-database 
    echo '<br>'.__METHOD__.' $row PreDb : ';
    var_dump($row);
    //  fin test 

    return $row;
  }

  //  *** Pour import d'un acte 
  public function into_db($obj, $force_insert = FALSE, $skip_check_same = FALSE) {
    global $mysqli;

    //  *** tests-dispatch-database 
    // echo '<br>'.__METHOD__.' $obj : ';
    // var_dump($obj);
    //  fin test 

    $result = FALSE;

    if(!$force_insert && !$obj->pre_into_db())
        return;

    //  *** Tester si $obj == quelle classe, pour appeler le bon from_db() 
    if(!$skip_check_same) {
        // $values_db = $mysqli->from_db($obj, FALSE, FALSE);
        $values_db = $obj->from_db($obj, FALSE, FALSE);
    }
    //  *** tests-dispatch-database 
    echo '<br>'.__METHOD__.' $values_db : ';
    var_dump($values_db);
    //  fin test 
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