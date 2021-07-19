<?php

include_once(ROOT."src/class/io/DatabaseIO.php");

include_once(ROOT."src/class/io/PreDatabase.php");

include_once(ROOT."src/class/model/Nom.php");
include_once(ROOT."src/class/model/Prenom.php");
include_once(ROOT."src/class/model/Relation.php");
include_once(ROOT."src/class/model/Condition.php");

class Personne extends PreDatabase implements DatabaseIO {

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
        //  *** tests-dispatch-database 
        parent::from_db($this, $update_obj = FALSE, $get_relations_conditions = TRUE);
    }

    public function add_prenom_str($s){
        $this->add_prenom(new Prenom(NULL, $s));
    }

    public function add_prenom($prenom){
        foreach($this->prenoms as $_prenom){
            if((isset($_prenom->id, $prenom->id)
                    && $_prenom->id == $prenom->id)
                || $_prenom->no_accent == $prenom->no_accent)
                return;
        }
        $this->prenoms[] = $prenom;
        $str = $this->prenoms_str;
        $this->prenoms_str = ($str == "" ? "" : $str . " ") . $prenom->to_string();
    }

    public function add_nom_str($s, $attributes){
        $this->add_nom(new Nom(NULL, $s, NULL, $attributes));
    }

    public function add_nom($nom){
        foreach($this->noms as $_nom){
            if((isset($_nom->id, $nom->id)
            && $_nom->id == $nom->id)
            || ($_nom->no_accent == $nom->no_accent)){
                if(isset($nom->attribut)){
                    $_nom->attribut = $nom->attribut;
                }
                return;
            }
        }
        $this->noms[] = $nom;
        $str = $this->noms_str;
        $this->noms_str = ($str == "" ? "" : $str . " ") . $nom->to_string();
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

    public function from_db($obj, $update_obj = FALSE, $get_relations_conditions = TRUE) {
        global $log, $mysqli, $row;
        
        if(isset($obj->id)) {
            $row = parent::from_db($obj, $update_obj,
                $get_relations_conditions);
            $mysqli->from_db_personne_noms_prenoms($obj);
            if($get_relations_conditions){  //  *** && ($this->id == $post_id) 
                $mysqli->from_db_personne_relations($obj);
                $mysqli->from_db_personne_conditions($obj);
            }
        } else 
            $row = $mysqli->from_db_by_same_personne($obj);
        
        //  *** tests-dispatch-database 
        echo '<br>'.__METHOD__.' $row : ';
        var_dump($row);  //  *** NULL 
        //  fin test 
        
        return $row;
        // return $this;
    }

    // public function into_db($obj, $force_insert = FALSE, $skip_check_same = FALSE) {
    //     global $mysqli;
    //     $result = FALSE;

    //     if(!$force_insert && !$obj->pre_into_db())
    //         return;

    //     //  *** Tester si $obj == quelle classe, pour appeler le bon from_db()
    //     if(!$skip_check_same) {
    //         $values_db = $personne->from_db($obj, FALSE, FALSE);
    //     }
    //     if(isset($values_db["id"])) {
    //         $obj->id = $values_db["id"];
    //     }
    //     $values_obj = $obj->values_into_db();
    //     $values_updated = $mysqli->updated_values($values_db, $values_obj);

    //     if(isset($values_db, $obj->id))
    //         $result = $this->into_db_update($obj, $values_updated);
    //     else
    //         $result = $this->into_db_insert($obj, $values_updated);

    //     $obj->post_into_db();

    //     if($result === FALSE)
    //         return FALSE;
        
    //     return $obj->id;
    // }

    public function get_relations_by_type() {
        $this->dispatch_relations_by_type();
        return $this->relations_by_type;
    }

    // DATABASE IO

    public function get_table_name(){
        return "personne";
    }

    public function get_same_values(){
        return [];
    }

    public function result_from_db($row){
        if($row == NULL)
            return;
        $this->id = $row["id"];
    }

    public function values_into_db(){
        return [];
    }

    public function pre_into_db(){
        global $mysqli;

        if(!$this->is_valid())
            return FALSE;

        foreach($this->prenoms as $prenom){
            // $mysqli->into_db($prenom);
            $this->into_db($prenom);
        }

        foreach($this->noms as $nom){
            // $mysqli->into_db($nom);
            $this->into_db($nom);
        }

        return TRUE;
    }

    public function post_into_db(){
        global $mysqli;

        //  *** Récupérer le dernier id inséré 
        //  ==> Voir plus bas pour le remplacer par une variable déjà existante // 
        if(!isset($this->id) || ($this->id == 0)) {
            $this->id = $mysqli->insert_id;
        }

        $this->is_updated_in_db = TRUE;
        
        /* *** Je comprends pas pourquoi on supprime la personne avant de l'enregistrer (pour nom et prenom).
          Avant mes modifs sur la branche "fix-bug-sans-nom", c'est ce qui remplaçait les mêmes prénoms par la nouvelle personne. 
          De toute façon c'est toujours risqué de faire ça, au minimum il faut faire des vérifs sûres avant.  
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
            // $mysqli->into_db($relation);
            $this->into_db($relation);

        foreach($this->conditions as $condition)
            // $mysqli->into_db($condition);
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

    //  *** tests-dispatch-database
    //  ajouté purge_personne() à $acte->remove_from_db()
    //  à la place de $personne->remove_from_db() 
    public function remove_from_db($anyway = FALSE) {
        global $mysqli;

        if(! $anyway)
        if($this->is_in_anything()) return FALSE;

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

        $removed = [];

        if($personne->remove_from_db(TRUE))
        $removed[] = $personne;

        return $removed;
    }

	/*
	  Import from db is in src/io/IO/Database.php from_db()
	  (which is ugly i know)
	 */
    //  *** Commentaire périmé non ? 

}

?>
