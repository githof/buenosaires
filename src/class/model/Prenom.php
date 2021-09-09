<?php

include_once(ROOT."src/class/io/DatabaseIO.php");

include_once(ROOT."src/class/io/DatabaseEntity.php");


/*
  Nom et Prenom pourraient hériter d'une même classe...
 */

class Prenom extends DatabaseEntity {

    public $id;

    public $prenom;
    public $no_accent;

    public function __construct($id = NULL, $prenom = NULL, $no_accent = NULL){
        $this->id = $id;
        $this->set_prenom($prenom, $no_accent);
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
    //  *** rewrite-noms-export 
    //  test export sans accents 
    //  public function to_string($no_accent = FALSE){ 
    public function to_string($no_accent){ 

        //  *** rewrite-noms-export 
        echo '<br>'.__METHOD__.'<br>$no_accent : ';
        var_dump($no_accent);    //  true :) 
        //  fin test

        return $no_accent ? $this->no_accent : $this->prenom; 
    }


    // DATABASE IO

    // public function get_table_name(){
    //     return "prenom";
    // }

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

    // public function pre_into_db(){
    //     return TRUE;
    // }

    // public function post_into_db(){
    //     global $mysqli;

    //      *** Récupérer le dernier id inséré 
    //     if(!isset($this->id) || ($this->id == 0)) {
    //         $this->id = $mysqli->insert_id;
    //     }
    // }
}

?>
