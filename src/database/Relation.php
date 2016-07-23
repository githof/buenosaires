<?php

    include_once(ROOT."src/database/TableEntry.php");

    class Relation extends TableEntry {


        function __construct($id = NULL){
            parent::__construct("relation", $id);
        }

        function set_source($source_id){
            $this->set_var("source", $source_id);
        }

        function set_destination($destination_id){
            $this->set_var("destination", $destination_id);
        }

        function set_statut($statut_id){
            $this->set_var("statut_id", $statut_id);
        }

    }

    function create_relation($personne_source, $personne_destination, $statut_id, $periode_ref_id){
        global $log;

        $relation = new Relation();
        $relation->get_same([
            "source" => $personne_source->id,
            "destination" => $personne_destination->id,
            "statut_id" => $statut_id
        ]);

        $relation->set_source($personne_source->id);
        $relation->set_destination($personne_destination->id);
        $relation->set_statut($statut_id);
        $relation->set_periode($periode_ref_id)

        $result = $relation->into_db();

        if($result === FALSE){
            $log->e("Erreur lors de l'ajout de la relation source=$source, destination=$destination, statut=$statut");
            return NULL;
        }
        return $relation;
    }

    function link_relation_acte_into_db($acte, $relation){
        global $log;

        $values = [
            "acte_id" => $acte->id,
            "relation_id" => $relation->id
        ];
        $result = $mysqli->insert("acte_has_relation", $values);

        if($result === FALSE){
            $log->e("Erreur lors du lien entre relation=$relation->id et acte=$acte->id dans acte_has_relation");
            return FALSE;
        }
        return TRUE;
    }

?>
