<?php

    class XMLDecoder {

        var $filename;

        function XMLDecoder($filename){
            $this->filename = $filename;
        }

        function get_actes(){
            $xml = simplexml_load_file($this->filename);
            return $xml->ACTES->ACTE;
        }
    }
?>
