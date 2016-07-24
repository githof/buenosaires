<?php

    include_once(ROOT."src/database/Periode.php");
    include_once(ROOT."src/database/TableEntry.php");
    include_once(ROOT."src/database/Personne.php");
    include_once(ROOT."src/database/Relation.php");

    class Acte extends TableEntry{

        var $xml;
        var $source_id;
        var $personnes;

        var $epoux;
        var $epouse;

        var $temoins;
        var $relations;

        function __construct($id){
            parent::__construct("acte", $id);
            $this->source_id = SOURCE_DEFAULT_ID;
            $this->personnes = [];
            $this->temoins = [];
            $this->epoux = NULL;
            $this->epouse = NULL;
            $this->relations = [];
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
                        $epoux = personne_from_xml($childXML, $this);
                        if($epoux != NULL){
                            $this->personnes[] = $epoux;
                            $this->epoux = $epoux;
                            $this->set_var("epoux", $epoux->id);
                        }
                        break;
                    case "epouse":
                        $epouse = personne_from_xml($childXML, $this);
                        if($epouse != NULL){
                            $this->personnes[] = $epouse;
                            $this->epouse = $epouse;
                            $this->set_var("epouse", $epouse->id);
                        }
                        break;
                    case "temoins":
                        $temoinsXML = $childXML;
                        break;
                }
            }

            $this->from_xml_temoins($temoinsXML);
        }

        function from_xml_temoins($temoinsXML){
            if(!isset($temoinsXML))
                return;

            foreach($temoinsXML->children() as $childXML) {
                if($childXML->getName() === "temoin"){
                    $temoin = personne_from_xml($childXML, $this);
                    if($temoin != NULL){
                        $this->personnes[] = $temoin;
                        $this->temoins[] = $temoin;
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

        function into_db($id_require = FALSE){
            global $alert;

            if(!isset($this->id)){
                $alert->add_error("L'acte ne contient pas de num");
                return FALSE;
            }

            $result = parent::into_db(TRUE);

            if($result === FALSE)
                return FALSE;

            $result = $this->contenu_into_db();

            $this->set_relations();
            $this->set_conditions();

            if($result === FALSE)
                return FALSE;
            return $this->id;
        }

        function create_relations_epoux_epouse(){
            if(isset($this->values["epoux"], $this->values["epouse"])){
                $relation = create_relation(
                    $this->epoux,
                    $this->epouse,
                    STATUT_EPOUX,
                    $this->values["periode_id"]
                );
                if($relation != NULL)
                    $this->relations[] = $relation;

                $relation = create_relation(
                    $this->epouse,
                    $this->epoux,
                    STATUT_EPOUSE,
                    $this->values["periode_id"]
                );
                if($relation != NULL)
                    $this->relations[] = $relation;
            }
        }

        function create_relations_temoin_epoux_epouse(){
            foreach ($this->temoins as $temoin){
                if(isset($this->values["epoux"])){
                    $relation = create_relation(
                        $temoin,
                        $this->epoux,
                        STATUT_TEMOIN,
                        $this->values["periode_id"]
                    );
                    if($relation != NULL)
                        $this->relations[] = $relation;
                }

                if(isset($this->values["epouse"])){
                    $relation = create_relation(
                        $temoin,
                        $this->epouse,
                        STATUT_TEMOIN,
                        $this->values["periode_id"]
                    );
                    if($relation != NULL)
                        $this->relations[] = $relation;
                }
            }
        }

        function set_relations(){
            global $mysqli;

            $this->create_relations_epoux_epouse();
            $this->create_relations_temoin_epoux_epouse();

            foreach($this->relations as $relation) {
                link_relation_acte_into_db($this, $relation);
            }

            foreach($this->personnes as $personne){
                $personne->set_relations($this);
            }
        }

        function set_conditions(){
            foreach($this->personnes as $personne){
                $personne->set_conditions($this);
            }
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

?>
