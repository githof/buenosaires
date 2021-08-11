<?php

include_once(ROOT."src/class/io/ExportInterface.php");

class XMLExport implements ExportInterface {

    public static $actes_id;

    //  *** Pour stocker le chemin vers le fichier à créer sur le disque 
    public static $fichier;
    public static $out;


    //  INTERFACE   // 

    //  *** fichier à enregistrer sur le disque 
    public static function attr_nom_fichier($object) {
        // if (($object === 'actes') || ($object === 'acte'))
            self::$fichier = ROOT.'exports/export_'.$object.'_'.date('Y-m-d_H-i-s').'.xml';

        self::$out = fopen(self::$fichier, 'a');
    }
    
    public static function entete($object) {
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="export_'.$object.'_'.date('Y-m-d_H-i-s').'.xml' . '"');
        
        //  *** exporter le fichier enregistré sous le même nom : 
        readfile(self::$fichier);
    }
    

    //  PRIVATE METHODS //

    private static function export_line($line){
        self::attr_nom_fichier('acte');
        fputs(self::$out, html_entity_decode($line, ENT_NOQUOTES, 'UTF-8') . PHP_EOL);
    }


    //  PUBLIC  //

    //  *** pour exporter un acte depuis detail_acte.php 
    public static function export($acte_id){
        global $mysqli, $acte;

        self::XML_entete('actes');

        $results = $mysqli->select("acte_contenu", ["contenu"], "acte_id = '$acte_id'");

        if($results != FALSE && $results->num_rows == 1){
            $row = $results->fetch_assoc()["contenu"];
            // self::export_line($results->fetch_assoc()["contenu"]);   //  *** $line = NULL si on ne passe pas par une variable ($row ici) 
            self::export_line($row);
        }

        self::footer();

        fclose(self::$out);

        self::entete('acte'); 
    }

    public static function export_all(){
        global $mysqli;

        self::attr_nom_fichier('actes');

        self::XML_entete('actes');
        
        $results = $mysqli->select("acte_contenu", ["contenu"]);
        if($results != FALSE && $results->num_rows > 0){
            while($row = $results->fetch_assoc()){
                self::export_line($row["contenu"]);
            }
        }
        //  timeline 
        // fputs(self::$out, date('Y-m-d_H-i-s'));
        // echo '<br>$fichier : ';
        // var_dump(self::$fichier);
        
        self::footer();

        
        fclose(self::$out);
        
        self::entete('actes');
    }


    //  PRIVATE METHODS //

    //  ***  entête xml, différente de l'entête d'export 
    private static function XML_entete(){
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
