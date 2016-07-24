<?php

    include_once(ROOT."src/database/Acte.php");


    class XMLActeReader {

        var $xml;
        var $source_id;

        function __construct($source_id){
            $this->source_id = $source_id;
        }

        public function use_xml_file($filename){
            global $log;

            if(!file_exists($filename)){
                $log->e("Impossible d'ouvrir le fichier $filename");
                return FALSE;
            }

            $this->xml = simplexml_load_file($filename);
            if($this->xml === FALSE){
                $log->e("Erreur lors de la lecture du fichier xml $filename");
                return FALSE;
            }
            return TRUE;
        }

        public function read_actes($only_new_acte = FALSE){
            global $log, $alert;
            $actesXML = NULL;
            $i = 0;
            $success_nb = 0;

            if(!isset($this->xml)){
                $log->e("L'objet xml est null");
                return FALSE;
            }

            if(isset($this->xml->ACTES, $this->xml->ACTES->ACTE)){
                $actesXML = $this->xml->ACTES->ACTE;
            }else if(isset($this->xml->ACTE)){
                $actesXML = $this->xml->ACTE;
            }

            if(isset($actesXML)){
                foreach ($actesXML as $acteXML){
                    if($this->read_acte($acteXML, $i, $only_new_acte))
                        $success_nb++;
                    $i++;
                }
            }

            if($success_nb > 0){
                $message = "$success_nb acte(s) ajouté(s)";
                $log->i($message);
                $alert->success($message);
            }
            return TRUE;
        }

        private function read_acte($acteXML, $position = NULL, $only_new_acte = FALSE){
            global $log, $alert;
            $acte_id = NULL;
            $acteXML_attr = $acteXML->attributes();

            if($position != NULL)
                $position = " (en position $position)";
            else
                $position = "";

            $log->i("Ajout de l'acte$position à partir d'xml");

            if(isset($acteXML_attr["num"]))
                $acte_id = $acteXML_attr["num"];
            else if(isset($acteXML_attr["id"]))
                $acte_id = $acteXML_attr["id"];
            else{
                $message = "L'acte$position ne contient pas d'attribut num/id. Impossible de l'ajouter";
                $log->w($message);
                $alert->warning($message);
                return FALSE;
            }

            if($only_new_acte && $this->db_has_acte($acte_id)){
                $message = "L'acte$position est déjà dans la base de donnée. L'option seulement nouveau acte activée. L'acte n'est pas importé";
                $log->i($message);
                $alert->warning($message);
                return FALSE;
            }

            $acte = new Acte($acte_id);
            $acte->source_id = $this->source_id;
            $acte->from_xml($acteXML);
            if($acte->into_db()){
                $log->i("Acte$position ajouté avec succès");
                return TRUE;
            }
            $message = "Erreur lors de l'ajout de l'acte$position";
            $log->e($message);
            $alert->error($message);
            return FALSE;
        }

        private function db_has_acte($acte_id){
            global $mysqli;

            $result = $mysqli->select("acte", ["id"], "id='$acte_id'");
            if($result != FALSE && $result->num_rows === 1)
                return TRUE;
            return FALSE;
        }
    }
?>
