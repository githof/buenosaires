<?php

    class XMLExport {

        var $actes_id;

        function __construct($actes_id = []){
            $this->actes_id = $actes_id;
        }

	private function export_line($line){
	  echo html_entity_decode($line, ENT_NOQUOTES, 'UTF-8') . PHP_EOL;
	}
	
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

        public function export_all(){
            global $mysqli;

            $this->entete();

            $results = $mysqli->select("acte_contenu", ["contenu"]);
            if($results != FALSE && $results->num_rows > 0){
                while($row = $results->fetch_assoc()){
		  $this->export_line($row["contenu"]);
                }
            }

            $this->footer();
        }

        private function entete(){
	  header('Content-type: text/xml');
	  header('Content-Disposition: attachment; filename="export.xml"');
	  
	  $this->export_line('<?xml version="1.0" encoding="UTF-8"?>');
	  $this->export_line('<document>');
	  $this->export_line('<ACTES>');
        }

        private function footer(){
	  $this->export_line('</ACTES>');
	  $this->export_line('</document>');
        }
    }


?>
