<?php

include_once(ROOT."src/class/io/DatabaseIO.php");

include_once(ROOT."src/class/io/PreDatabase.php");


/*
  Nom et Prenom pourraient hériter d'une même classe...
 */

class Prenom extends PreDatabase implements DatabaseIO{

    public $id;

    public $prenom;
    public $no_accent;

    public function __construct($id = NULL, $prenom = NULL, $no_accent = NULL){
        $this->id = $id;
        $this->set_prenom($prenom, $no_accent);
        parent::from_db($this, $update_obj = FALSE, $get_relations_conditions = TRUE);
    }

    public function set_prenom($default, $no_accent = NULL){
        if($default == NULL)
            return;

        $default = trim($default);
        if(!isset($no_accent))
            $no_accent = no_accent($default);

        $this->prenom = $default;
        $this->no_accent = $no_accent;
    }

    public function to_string($no_accent = FALSE){
        return $no_accent ?
        $this->no_accent :
        $this->prenom;
    }


    // DATABASE IO

    public function get_table_name(){
        return "prenom";
    }

    public function get_same_values(){
        return [
            "no_accent" => $this->no_accent
        ];
    }

    public function result_from_db($row){
        if($row == NULL)
            return;
        $this->id = $row["id"];
        $this->set_prenom($row["prenom"], $row["no_accent"]);
    }

    public function values_into_db(){
        return [
            "prenom" => $this->prenom,
            "no_accent" => $this->no_accent
        ];
    }

    public function pre_into_db(){
        return TRUE;
    }

    public function post_into_db(){
        global $mysqli;

        //  *** Récupérer le dernier id inséré 
        if(!isset($this->id) || ($this->id == 0)) {
            $this->id = $mysqli->insert_id;
        }
    }

    //  *** tests-dispatch-database 
    // public function from_db($obj, $update_obj = FALSE, $get_relations_conditions = TRUE) {
    //     global $log, $mysqli;
                
    //     if(isset($this->id)){
    //         $mysqli->from_db_by_id($this);
    //     } else 
    //         $mysqli->from_db_by_same($this);
        
    //     return $this;
    // }
}

?>
