<?php

class XMLExport {

    public static $actes_id;

    // public function __construct($actes_id = []){
    //     $this->actes_id = $actes_id;
    // }

    //  PRIVATE METHODS //

    private static function export_line($line){
        echo html_entity_decode($line, ENT_NOQUOTES, 'UTF-8') . PHP_EOL;    //  ENT_XML1
    }

    //  PUBLIC  //

    //  *** Cette fonction ne sert pas pour l'instant, il faut voir comment utiliser une méthode statique avec des paramètres :
    // public static function export($actes_id){
    public static function export(){
        global $mysqli;

        self::XML_entete();

        //  *** pour l'instant je n'ai pas trouvé comment utiliser les méthodes statiques avec un paramètre 
        // foreach($this->actes_id as $acte_id){    //  code d'origine 
        // foreach($actes_id as $acte_id){          //  *** mon test 

            $results = $mysqli->select("acte_contenu", ["contenu"], "acte_id = '$actes_id'");
            if($results != FALSE && $results->num_rows == 1){
                $row = $results->fetch_assoc()["contenu"];
                // self::export_line($results->fetch_assoc()["contenu"]);   //  *** $line = NULL si on ne passe pas par une variable ($row ici) 
                self::export_line($row);
            }
        // }

        self::footer();
    }

    public static function export_all(){
        global $mysqli;

        self::XML_entete();

        $results = $mysqli->select("acte_contenu", ["contenu"]);
        if($results != FALSE && $results->num_rows > 0){
            while($row = $results->fetch_assoc()){
                self::export_line($row["contenu"]);
            }
        }

        self::footer();
    }

    //  PRIVATE METHODS //

    private static function XML_entete(){
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="export.xml"');

        self::export_line('<?xml version="1.0" encoding="UTF-8"?>');
        self::export_line('<document>');
        self::export_line('<ACTES>');

    }

    public static function footer(){
        self::export_line('</ACTES>');
        self::export_line('</document>');
    }
}


?>
