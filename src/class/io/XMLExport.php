<?php

    class XMLExport {


        var $xml;
        var $actes_id;

        function __construct($actes_id){
            $this->actes_id = $actes_id;
        }

        public function export(){
            global $mysqli;

            foreach($this->actes_id as $acte_id){
                $results = $mysqli->select("acte_contenu", ["contenu"], "acte_id = '$acte_id'");
                if($results != FALSE && $results->num_rows == 1){
                    $row = $results->fetch_assoc();
                    $this->xml .= $row["contenu"];
                }
            }

            $this->add_entete();
            $this->write();
        }

        private function add_entete(){
            $this->xml =
                '<?xml version="1.0" encoding="UTF-8"?><document><ACTES>' .
                $this->xml .
                '</ACTES></document>';
        }

        private function write(){
            header('Content-type: text/xml');
            header('Content-Disposition: attachment; filename="export.xml"');

            echo $this->xml;
        }
    }


?>
