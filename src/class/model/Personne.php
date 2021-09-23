<?php

include_once(ROOT."src/class/io/DatabaseIO.php");

include_once(ROOT."src/class/io/DatabaseEntity.php");

include_once(ROOT."src/class/model/Nom.php");
include_once(ROOT."src/class/model/Prenom.php");
include_once(ROOT."src/class/model/Relation.php");
include_once(ROOT."src/class/model/Condition.php");

class Personne extends DatabaseEntity {

    public $id;

    public $prenoms;
    public $prenoms_str;
    public $noms;
    public $noms_str;
    public $relations;
    public $relations_by_type;
    public $conditions;

    public $pere;
    public $mere;

    public $xml;

    public $is_updated_in_db;

    public $personne;

    public function __construct($id = NULL){
        global $personne;

        $this->id = $id;
        $this->prenoms = [];
        $this->prenoms_str = "";
        $this->noms = [];
        $this->noms_str = "";
        $this->conditions = [];
        $this->relations = [];
        $this->relations_by_type = [];
        $this->pere = NULL;
        $this->mere = NULL;
        $this->is_updated_in_db = FALSE;
    }

    public function add_prenom_str($s){
        $this->add_prenom(new Prenom(NULL, $s), FALSE);
    }

    public function add_prenom($prenom, $no_accent){
        foreach($this->prenoms as $_prenom){
            if((isset($_prenom->id, $prenom->id)
                    && $_prenom->id == $prenom->id)
                || $_prenom->no_accent == $prenom->no_accent)
                return;
        }
        $this->prenoms[] = $prenom;
        $str = $this->prenoms_str;
        $this->prenoms_str = ($str == "" ? "" : $str . " ") . $prenom->to_string($no_accent); 
    }

    public function add_nom_str($s, $attributes){
        $this->add_nom(new Nom(NULL, $s, NULL, $attributes), TRUE, FALSE);
    }

    //  *** rewrite-noms-export
    //  sans "de" : $attr pour $attribut 
    public function add_nom($nom, $attr, $no_accent){
        foreach($this->noms as $_nom){
            if((isset($_nom->id, $nom->id)
            && $_nom->id == $nom->id)
            || ($_nom->no_accent == $nom->no_accent)){
                if($attr == TRUE) {
                    return;
                } else {
                    if(isset($nom->attribut)){
                        $_nom->attribut = $nom->attribut;
                    }
                }
            }
        }
        $this->noms[] = $nom;
        $str = $this->noms_str;
        $this->noms_str = ($str == "" ? "" : $str . " ") . $nom->to_string($attr, $no_accent);
    }

    public function add_condition($text, $source_id){
        $this->conditions[] = new Condition(NULL, $text, $this, $source_id);
    }

    public function add_relation($personne_source, $personne_destination, $statut_id){
        $this->relations[] = new Relation(
            NULL,
            $personne_source,
            $personne_destination,
            $statut_id
        );
    }

    public function set_pere($pere){
        $this->add_relation($pere, $this, STATUT_PERE);
        $this->pere = $pere;
    }

    public function set_mere($mere){
        $this->add_relation($mere, $this, STATUT_MERE);
        $this->mere = $mere;
    }

    public function set_xml($xml){
        $this->xml = $xml;
    }

    public function is_valid(){
        return count($this->prenoms) > 0 || count($this->noms) > 0;
    }

    //  PRIVATE METHODS //

	// En fait faudrait faire de relations_by_type l'unique
	// champ relations, sinon va y'avoir des problème de synchro
	// Pour le moment je vais appeler systématiquement cette
	// fonction à chaque requête de liste de relations
    private function dispatch_relations_by_type() {
        $mariage = [];
        $parents = [];
        $enfants = [];
        $a_temoins = [];
        $est_temoin = [];
        $a_parrains = [];
        $est_parrain = [];

        foreach($this->relations as $relation) {
            $is_source = ($this->id == $relation->personne_source->id);

            switch($relation->statut_id){
                case STATUT_EPOUX:  //  *** on ne peux pas trouver ce cas-là 
                case STATUT_EPOUSE:
                    $mariage[] = $relation;
                    break;
                case STATUT_PERE:
                case STATUT_MERE:
                    if($is_source)
                        $enfants[] = $relation;
                    else
                        $parents[] = $relation;
                    break;
                case STATUT_TEMOIN:
                    if($is_source)
                        $est_temoin[] = $relation;
                    else
                        $a_temoins[] = $relation;
                    break;
                case STATUT_PARRAIN:
                    if($is_source)
                        $est_parrain[] = $relation;
                    else
                        $a_parrains[] = $relation;
                    break;
            }
        }

        $match = [
            'mariage' => $mariage,
            'parents' => $parents,
            'enfants' => $enfants,
            'a_temoins' => $a_temoins,
            'est_temoin' => $est_temoin,
            'a_parrains' => $a_parrains,
            'est_parrain' => $est_parrain
        ];
        foreach($match as $word => $list) {
            $this->relations_by_type[$word] = $list;
        }
    }


    //  PUBLIC  //

    public function get_relations_by_type() {
        $this->dispatch_relations_by_type();
        return $this->relations_by_type;
    }

    //  *** rewrite-noms-export
    public function from_db(
        $update_obj = FALSE,
        $get_relations_conditions = TRUE, 
        $attr = TRUE,
        $no_accent = FALSE)
    {
        global $log, $mysqli; 
        if(isset($this->id)) {
            $row = parent::from_db($update_obj,
                $get_relations_conditions,
                $attr,
                $no_accent);
            $mysqli->from_db_personne_noms_prenoms($this, $attr, $no_accent);
            if(($get_relations_conditions) && ($this->id == $post_id)){   
                $mysqli->from_db_personne_relations($this);
                $mysqli->from_db_personne_conditions($this);
            }
        } else 
            $row = $mysqli->from_db_by_same_personne($this);
        
        return $row;
    }

    
    // DATABASE IO

    //  *** implémentées dans DatabaseEntity 
    // public function get_table_name(){
    //     return "personne";
    // }

    // public function get_same_values(){
    //     return [];
    // }

    public function result_from_db($row){
        if($row == NULL)
            return;
        $this->id = $row["id"];
    }

    //  *** implémentée dans DatabaseEntity 
    // public function values_into_db(){
    //     return [];
    // }

    public function pre_into_db(){
        global $mysqli;

        if(!$this->is_valid())
            return FALSE;

        foreach($this->prenoms as $prenom){
            $this->into_db($prenom);
        }

        foreach($this->noms as $nom){
            $this->into_db($nom);
        }

        return TRUE;
    }

    public function post_into_db(){
        global $mysqli;

        //  *** Récupérer le dernier id inséré 
        if(!isset($this->id) || ($this->id == 0)) {
            $this->id = $mysqli->insert_id;
        }

        $this->is_updated_in_db = TRUE;
        
        /* *** Je comprends pas pourquoi on supprime la personne avant de l'enregistrer 
            (pour nom et prenom).
          Avant mes modifs sur la branche "fix-bug-sans-nom", c'est ce qui remplaçait les mêmes 
          prénoms par la nouvelle personne. 
          De toute façon c'est toujours risqué de faire ça, au minimum il faut faire des vérifs 
          sûres avant.  
        */
        $mysqli->start_transaction();
        $i = 1;
        $mysqli->delete("prenom_personne", "personne_id='$this->id'");
        foreach($this->prenoms as $prenom){
            $mysqli->into_db_prenom_personne($this, $prenom, $i);
            $i++;
        }

        $mysqli->delete("nom_personne", "personne_id='$this->id'");
        $i = 1;
        foreach($this->noms as $nom){
            $mysqli->into_db_nom_personne($this, $nom, $i);
            $i++;
        }
        $mysqli->end_transaction();

        foreach($this->relations as $relation)
            $this->into_db($relation);

        foreach($this->conditions as $condition)
            $this->into_db($condition);

        if(isset($this->xml)){
            $attributesXML = $this->xml->attributes();
            if(!isset($attributesXML["id"]))
                $this->xml->addAttribute("id", "$this->id");
        }
    }

    //  PRIVATE METHODS //

    private function is_in($table)
    // 'condition', 'relation', 'acte'
    {
        global $mysqli;

        switch ($table) {
            case 'condition':
                $filter = "personne_id = $this->id";
                break;
            case 'relation':
                $filter = "pers_source_id = $this->id"
                . " OR pers_destination_id = $this->id";
                break;
            case 'acte':
                $filter = "epoux = $this->id"
                . " OR epouse = $this->id";
                break;
        }
        $count = 'COUNT(*) AS nb';
        $result = $mysqli->select($table, [$count], $filter);
        if($result && $result->num_rows > 0){
            $row = $result->fetch_assoc();
            return ($row["nb"] > 0);
        }
        return FALSE;
    }

    private function is_in_anything() {

        foreach(['condition', 'relation', 'acte'] as $table) {
            if($this->is_in($table)) return TRUE;
        }
        return FALSE;
    }

    //  PUBLIC  //

    public function remove_from_db($anyway = FALSE) {
        global $mysqli;
        
        if(! $anyway) {
            if($this->is_in_anything()) return FALSE;
        }
        /*
        Je vais pas me préoccuper de supprimer les prénoms/noms
        qui se retrouvent orphelins.
        Facile à faire mais pas immédiat non plus :)
        $prenoms_ids = $this->prenoms_ids_from_db();
        $noms_ids = $this->noms_ids_from_db();
        */
        //  *** C'est fait : Database::remove_unused_prenoms_noms() 
        //  appelée dans Acte::remove_from_db() 

        $mysqli->start_transaction();
        foreach(['prenom', 'nom'] as $field) {
            $table = $field.'_personne';
            $mysqli->delete($table, "personne_id=$this->id");
        } 
        //  *** test-personne-suppr 
        /*  Impossible à supprimer de cette façon, il faut d'abord supprimer 
            acte_condition/acte_relation 
        */
        foreach(['condition', 'relation'] as $field) {
            $table = $field;
            $mysqli->delete($table, "personne_id=$this->id");
        }
        $mysqli->delete("personne", "id=$this->id");
        $mysqli->end_transaction();

        return TRUE;
    }

    //  *** tests-dispatch-database
    //  Déplacé depuis Database  
    /*
    Supprime de la base les personnes de la liste qui n'apparaissent dans aucune table.
    Renvoie la liste des personnes supprimées.
    */
    public function purge_personnes($personne) {
        global $mysqli; 

        $removed = array();

        if($personne->remove_from_db(TRUE))
            $removed[] = $personne;

        return $removed;
    }
}

?>
