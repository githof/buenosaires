<?php

class XMLExport {

    // public $actes_id;
    public static $actes_id;

    // public function __construct($actes_id = []){
    //     $this->actes_id = $actes_id;
    // }

    //  PRIVATE METHODS //

    // private function export_line($line){
    private static function export_line($line){
        echo html_entity_decode($line, ENT_NOQUOTES, 'UTF-8') . PHP_EOL;    //  ENT_XML1
    }

    //  PUBLIC  //

    // public function export($actes_id){
    // public static function export($actes_id){
    public static function export(){
        global $mysqli;

        // $this->XML_entete();
        self::XML_entete();

        //  *** pour l'instant je n'ai pas trouvé comment utiliser les méthodes statiques avec un paramètre 
        // foreach($this->actes_id as $acte_id){
        // foreach($actes_id as $acte_id){

            // $results = $mysqli->select("acte_contenu", ["contenu"], "acte_id = '$acte_id'");
            $results = $mysqli->select("acte_contenu", ["contenu"], "acte_id = '$actes_id'");
            if($results != FALSE && $results->num_rows == 1){
                $row = $results->fetch_assoc()["contenu"];
                // $this->export_line($results->fetch_assoc()["contenu"]);
                // self::export_line($results->fetch_assoc()["contenu"]);   //  *** $line = NULL si on ne passe pas par une variable 
                self::export_line($row);
            }
        // }

        // $this->footer();
        self::footer();
    }

    //  *** test static (br rewrite-export) 
    //  public function export_all(){
    public static function export_all(){
        global $mysqli;

        //  *** test static 
        self::XML_entete();
        //  $this->entete();

        $results = $mysqli->select("acte_contenu", ["contenu"]);
        if($results != FALSE && $results->num_rows > 0){
            while($row = $results->fetch_assoc()){
                // echo '<br>'.__METHOD__;
                // echo '<br>$row : ';
                // var_dump($row);
                self::export_line($row["contenu"]);
                // $this->export_line($row["contenu"]);
            }
        }

        // $this->footer();
        self::footer();
    }

    //  PRIVATE METHODS //

    // private function entete(){
    private static function XML_entete(){
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="export.xml"');

        // var_dump(header); 
        
        self::export_line('<?xml version="1.0" encoding="UTF-8"?>');
        self::export_line('<document>');
        self::export_line('<ACTES>');

        /*
        $this->export_line('<?xml version="1.0" encoding="UTF-8"?>');
        $this->export_line('<document>');
        $this->export_line('<ACTES>');
        */
    }

    // private function footer(){
    public static function footer(){
        self::export_line('</ACTES>');
        self::export_line('</document>');
    }
}


?>
