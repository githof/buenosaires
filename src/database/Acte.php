<?php

    include_once(ROOT."src/database/Periode.php");
    include_once(ROOT."src/database/TableEntry.php");
    include_once(ROOT."src/database/Personne.php");
    include_once(ROOT."src/database/Relation.php");

    class Acte extends TableEntry{

        var $xml;
        var $id_source;
        var $personnes;

        function __construct($id){
            parent::__construct("acte", $id);
            $this->acte = $this;
            $this->id_source = SOURCE_DEFAULT_ID;
            $this->personnes = [];
        }

        function from_xml($xml){
            $temoinsXML = NULL;
            $this->xml = $xml;

            if($xml == NULL)
                return;

            if(isset($this->xml->date))
                $this->set_date_xml($this->xml->date);
            else
                $this->set_date_xml(NULL);

            foreach($this->xml->children() as $childXML){
                switch($childXML->getName()){
                    case "epoux":
                        $epoux = $this->set_personne($childXML);
                        if($epoux != NULL){
                            $this->personnes[] = $epoux;
                            $this->set_var("epoux", $epouse->id);
                        }
                        break;
                    case "epouse":
                        $epouse = $this->set_personne($childXML);
                        if($epouse != NULL){
                            $this->personnes[] = $epouse;
                            $this->set_var("epouse", $epouse->id);
                        }
                        break;
                    case "temoins":
                        $temoinsXML = $childXML;
                        break;
                }
            }

            $this->create_relations_epoux_epouse();
            $this->from_xml_temoins($temoinsXML);
        }

        function from_xml_temoins($temoinsXML){
            if(!isset($temoinsXML))
                return;

            foreach($temoinsXML->children() as $childXML) {
                if($childXML->getName() === "temoin"){
                    $temoin = $this->set_personne($childXML);
                    if($temoin != NULL){
                        $this->personnes[] = $temoin;
                        $this->create_relations_temoin_epoux_epouse($temoin);
                    }
                }
            }
        }

        function set_date_xml($xml){
            $periode;
            if(isset($this->values["periode_id"])){
                $periode = new Periode(intval($this->values["periode_id"]));
            }else{
                $periode = new Periode();
            }

            if(isset($xml)){
                $periode->with_date($xml->__toString());
            }else {
                $periode->default_periode();
            }

            $tmp = $periode->into_db();

            if($tmp !== FALSE){
                $this->set_var("periode_id", $tmp);
                return TRUE;
            }
            return FALSE;
        }

        function into_db(){
            global $alert;

            if(!isset($this->id)){
                $alert->add_error("L'acte ne contient pas de num");
                return FALSE;
            }

            $result = parent::into_db(TRUE);

            if($result === FALSE)
                return FALSE;

            $result = $this->contenu_into_db();

            $this->link_relation_to_acte();
            $this->create_conditions();

            if($result === FALSE)
                return FALSE;
            return $this->id;
        }

        function create_conditions(){
            foreach ($this->conditions as $c) {
                $this->set_condition(
                    $c[1],
                    $this->id_source,
                    $c[0],
                    $this->id
                );
            }
        }

        function create_relations_epoux_epouse(){
            if(isset($this->values["epoux"], $this->values["epouse"])){
                $relation = $this->set_relation(
                    $this->values["epoux"],
                    $this->values["epouse"],
                    STATUT_EPOUX
                );
                if($relation != NULL)
                    $this->relations[] = $relation;

                $relation = $this->set_relation(
                    $this->values["epouse"],
                    $this->values["epoux"],
                    STATUT_EPOUSE
                );
                if($relation != NULL)
                    $this->relations[] = $relation;
            }
        }

        function create_relations_temoin_epoux_epouse($temoin){
            if(isset($this->values["epoux"])){
                $relation = $this->set_relation(
                    $temoin->id,
                    $this->values["epoux"],
                    STATUT_TEMOIN
                );
                if($relation != NULL)
                    $this->relations[] = $relation
            }

            if(isset($this->values["epouse"])){
                $relation = $this->set_relation(
                    $temoin->id,
                    $this->values["epouse"],
                    STATUT_TEMOIN
                );
                if($relation != NULL)
                    $this->relations[] = $relation;
            }
        }

        function link_all_relations_to_acte(){
            global $mysqli;

            foreach($this->relations as $relation) {
                $this->link_relation_to_acte($relation);
            }

            foreach($this->personnes as $personne){
                foreach($personne->relations as $relation){
                    $this->link_relation_to_acte($relation);
                }
            }
        }

        function link_relation_to_acte($relation){
            global $log;

            $values = [
                "acte_id" => $this->id,
                "relation_id" => $relation->id
            ];
            $result = $mysqli->insert("acte_has_relation", $values);

            if($result === FALSE){
                $log->e("Erreur lors du lien entre relation=$relation->id et acte=$this->id dans acte_has_relation");
                return FALSE;
            }
            return TRUE;
        }

        function contenu_into_db(){
            global $mysqli;

            $contenu = $mysqli->real_escape_string($this->xml->asXML());
            $values = [
                "acte_id" => $this->id,
                "contenu" => $contenu
            ];

            return $mysqli->insert(
                "acte_contenu",
                $values,
                " ON DUPLICATE KEY UPDATE contenu='$contenu'");
        }

    }

    function db_has_acte($id){
        global $mysqli;

        $rep = $mysqli->select("acte", ["id"], "id='$id'");
        return $rep->num_rows > 0;
    }

?>
