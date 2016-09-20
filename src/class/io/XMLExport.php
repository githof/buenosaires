<?php

    class XMLExport {

        var $actes_id;

        function __construct($actes_id = []){
            $this->actes_id = $actes_id;
        }

        public function export(){
            global $mysqli;

            $this->entete();

            foreach($this->actes_id as $acte_id){
                $results = $mysqli->select("acte_contenu", ["contenu"], "acte_id = '$acte_id'");
                if($results != FALSE && $results->num_rows == 1){
                    echo $results->fetch_assoc()["contenu"] . PHP_EOL;
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
                    echo $row["contenu"] . PHP_EOL;
                }
            }

            $this->footer();
        }

        private function entete(){
            header('Content-type: text/xml');
            header('Content-Disposition: attachment; filename="export.xml"');

            echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.
                '<document>'.PHP_EOL.
                '<ACTES>'.PHP_EOL;

        }

        private function footer(){
            echo '</ACTES>'.PHP_EOL.'</document>';
        }
    }


?>
