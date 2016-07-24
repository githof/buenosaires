<?php

    include_once(ROOT."src/database/TableEntry.php");


    class Condition extends TableEntry {


        function __construct($id = NULL){
            parent::__construct("cond", $id);
        }

        function set_text($text){
            $this->set_var("text", $text);
        }

        function set_source($source_id){
            $this->set_var("source_id", $source_id);
        }

        function set_personne($personne_id){
            $this->set_var("personne_id", $personne_id);
        }

        function set_acte($acte_id){
            $this->set_var("acte_id", $acte_id);
        }
    }

    function create_condition($text, $source_id, $personne, $acte){
        global $log;

        $condition = new Condition();
        $condition->get_same([
            "text" => $text,
            "source_id" => $source_id,
            "personne_id" => $personne->id,
            "acte_id" => $acte->id
        ]);

        $condition->set_text($text);
        $condition->set_source($source_id);
        $condition->set_personne($personne->id);
        $condition->set_acte($acte->id);

        $periode_id_ref = NULL;
        if(isset($acte->values["periode_id"]))
            $periode_id_ref = $acte->values["periode_id"];
        $condition->set_periode($periode_id_ref);

        $result = $condition->into_db();

        if($result === FALSE){
            $log->e("Erreur lors de l'ajout de la condition text=$text, source=$source_id, personne=$personne->id, acte=$acte->id");
            return NULL;
        }
        return $condition;
    }

?>
