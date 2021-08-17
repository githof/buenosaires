<?php

include_once(ROOT."src/class/io/DatabaseIO.php");

include_once(ROOT."src/class/io/DatabaseEntity.php");

include_once(ROOT."src/class/model/Personne.php");

class Relation extends DatabaseEntity {

    public $id;

    public $personne_source;
    public $personne_destination;
    public $statut_id;
    public $actes;

    public function __construct($id = NULL, $personne_source = NULL, $personne_destination = NULL, $statut_id = NULL){
        $this->id = $id;
        $this->set_personne_source($personne_source);
        $this->set_personne_destination($personne_destination);
        $this->set_statut_id($statut_id);
        $this->actes = [];
    }

    public function set_personne_source($personne_source){
        $this->personne_source = $personne_source;
    }

    public function set_personne_destination($personne_destination){
        $this->personne_destination = $personne_destination;
    }

    public function set_statut_id($statut_id){
        $this->statut_id = $statut_id;
    }

    public function get_statut_name(){
        global $mysqli;

        $result = $mysqli->select(
            "statut",
            ["valeur"],
            "id='$this->statut_id'"
        );
        if($result != FALSE && $result->num_rows > 0){
            $row = $result->fetch_assoc();
            return $row["valeur"];
        }
        return "";
    }

    public function check_source_id($id) {
        return $this->personne_source->id == $id;
    }

    
    //  *** bug-csvexport
    //  ajouté création new Acte() 
    //  méthode appelée nulle part ailleurs : pas de bug à cause de ça . 
    public function get_date(){
        global $mysqli;
        
        $mysqli->from_db_relation_list_actes($this);
        //  *** tests-dispatch-database 
        if(isset($this->actes[0])) {
            $acte_str = $this->actes[0];
            /* je prends le premier qui vient
            tfaçon y'aura une date pour chaque type de relation
            donc pour la relation epoux/se y'aura juste l'acte qui va
            bien
            */
            $acte = new Acte($acte_str);
            if($acte != null)

                return $acte->get_date();
        }
        return "";
    }

    // DATABASE IO

    // public function get_table_name(){
    //     return "relation";
    // }

    public function get_same_values(){
        return [
            "pers_source_id" => $this->personne_source->id,
            "pers_destination_id" => $this->personne_destination->id,
            "statut_id" => $this->statut_id
        ];
    }

    public function result_from_db($row){
        // if($row == NULL)
        //     return;

        $this->id = $row["id"];
        $this->set_personne_source(new Personne($row["pers_source_id"]));
        $this->set_personne_destination(new Personne($row["pers_destination_id"]));
        $this->set_statut_id($row["statut_id"]);
    }

    public function values_into_db(){
        return [
            "pers_source_id" => $this->personne_source->id,
            "pers_destination_id" => $this->personne_destination->id,
            "statut_id" => $this->statut_id
        ];
    }

    public function pre_into_db(){
        global $mysqli;

        if(!$this->personne_source->is_updated_in_db)
            // $mysqli->into_db($this->personne_source);
            $this->into_db($this->personne_source);

        if(!$this->personne_destination->is_updated_in_db)
            // $mysqli->into_db($this->personne_destination);
            $this->into_db($this->personne_destination);

        if(!$this->personne_source->is_valid() ||
            !$this->personne_destination->is_valid())
            return FALSE;

        return TRUE;
    }

    public function post_into_db(){
        global $mysqli;

        //  *** Récupérer le dernier id inséré 
        if(!isset($this->id) || ($this->id == 0)) {
            $this->id = $mysqli->insert_id;
        }
    }
}
?>
