<?php

//  XMLExport et CSVExport ne pourraient pas hériter d'une même classe ?    ***

class XMLExport {

    public $actes_id;

    public function __construct($actes_id = []){
        $this->actes_id = $actes_id;
    }

    //  PRIVATE METHODS //

    // private function export_line($line){
    public static function export_line($line){
        echo html_entity_decode($line, ENT_NOQUOTES, 'UTF-8') . PHP_EOL;
    }

    //  PUBLIC  //

    public function export(){
        global $mysqli;

        $this->entete();

        foreach($this->actes_id as $acte_id){
            $results = $mysqli->select("acte_contenu", ["contenu"], "acte_id = '$acte_id'");
            if($results != FALSE && $results->num_rows == 1){
                $this->export_line($results->fetch_assoc()["contenu"]);
            }
        }

        $this->footer();
    }

    //  *** test export
    //  public function export_all(){
    public static function EXPORT_ALL(){
        global $mysqli;

        // XMLExport::entete();
        // $this->entete();

        $results = $mysqli->select("acte_contenu", ["contenu"]);
        if($results != FALSE && $results->num_rows > 0){
            while($row = $results->fetch_assoc()){
                XMLExport::export_line($row["contenu"]);
                // $this->export_line($row["contenu"]);
            }
        }

        // $this->footer();
        // XMLExport::footer();
    }

    //  PRIVATE METHODS //

    // private function entete(){
    public static function entete(){
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="export.xml"');
        
        XMLExport::export_line('<?xml version="1.0" encoding="UTF-8"?>');
        XMLExport::export_line('<document>');
        XMLExport::export_line('<ACTES>');

        /*
        $this->export_line('<?xml version="1.0" encoding="UTF-8"?>');
        $this->export_line('<document>');
        $this->export_line('<ACTES>');
        */
    }

    // private function footer(){
    public static function footer(){
        XMLExport::export_line('</ACTES>');
        XMLExport::export_line('</document>');
    }
}


?>
