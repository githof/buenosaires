<?php

    include_once(ROOT."src/class/model/Acte.php");


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
            $i = 1;
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
                foreach ($actesXML as $xml_acte){
                    if($this->read_acte($xml_acte, $i, $only_new_acte))
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

        private function read_acte($xml_acte, $position = NULL, $only_new_acte = FALSE){
            global $log, $alert, $mysqli;
            $acte_id = NULL;
            $xml_acte_attr = $xml_acte->attributes();

            if($position != NULL)
                $position = " (en position $position)";
            else
                $position = "";

            $log->i("Ajout de l'acte$position à partir d'xml");

            if(isset($xml_acte_attr["num"])){
                $acte_id = intval($xml_acte_attr["num"]);
                unset($xml_acte_attr["num"]);
                $xml_acte->addAttribute("id", "$acte_id");
            }else if(isset($xml_acte_attr["id"]))
                $acte_id = $xml_acte_attr["id"];
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
            $this->read_acte_node($acte, $xml_acte);
            if($mysqli->into_db($acte)){
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

        function read_acte_node($acte, $xml_acte){
            foreach($xml_acte->children() as $xml_child){
                switch($xml_child->getName()){
                    case "date":
                        $acte->set_date($xml_child->__toString());
                        break;
                    case "epoux":
                        $acte->set_epoux($this->read_personne_node($xml_child));
                        break;
                    case "epouse":
                        $acte->set_epouse($this->read_personne_node($xml_child));
                        break;
                    case "temoins":
                        foreach($xml_child->children() as $xml_temoin){
                            if($xml_temoin->getName() === "temoin")
                                $acte->add_temoin($this->read_personne_node($xml_temoin));
                        }
                        break;
                    case "parrains":
                        foreach($xml_child->children() as $xml_parrain){
                            if($xml_parrain->getName() === "parrain")
                                $acte->add_parrain($this->read_personne_node($xml_parrain));
                        }
                        break;
                }
            }
            $acte->set_contenu($xml_acte->asXML());
        }

        function read_personne_node($xml_personne){
            $personne = new Personne();
            $personne->set_xml($xml_personne);
            $xml_personne_attributes = $xml_personne->attributes();

            if(isset($xml_personne_attributes["id"]))
                $personne->id = $xml_personne->attributes()["id"];

            if(isset($xml_personne_attributes["don"]) && $xml_personne_attributes["don"] == "true")
                $personne->add_condition("Don", $this->source_id);

            foreach($xml_personne->children() as $xml_child){
                switch($xml_child->getName()){
                    case "prenom":
                        $personne->add_prenom(new Prenom(NULL, $xml_child->__toString()));
                        break;
                    case "nom":
                        $this->all_nom_attributes_in_one($xml_child);
                        $attribut = NULL;
                        if(isset($xml_child->attributes()["attr"]))
                            $attribut = $xml_child->attributes()["attr"];
                        $personne->add_nom(new Nom(NULL, $xml_child->__toString(), NULL, $attribut));
                        break;
                    case "pere":
                        $personne->set_pere($this->read_personne_node($xml_child));
                        break;
                    case "mere":
                        $personne->set_mere($this->read_personne_node($xml_child));
                        break;
                    case "condition":
                        $personne->add_condition($xml_child->__toString(), $this->source_id);
                        break;
                }
            }
            return $personne;
        }

        function all_nom_attributes_in_one($xml_nom){
            $new_attr = "";
            $attrs = $xml_nom->attributes();
            $attrs_to_unset = [];
            $i = 0;

            if(isset($attrs["attr"]))
                return;

            foreach ($attrs as $key => $value){
                if(strcmp($key, "id") != 0 && strcmp($value, "true") == 0){
                    $new_attr .= "$key ";
                    $attrs_to_unset[] = $key;
                }
            }

            foreach($attrs_to_unset as $key => $value){
                unset($attrs[$value]);
            }

            if(strlen($new_attr) == 0)
                return;

            $xml_nom->addAttribute("attr", trim($new_attr));
        }
    }
?>
