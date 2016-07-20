<?php

    include_once("src/database/Periode.php");
    include_once("src/database/Table.php");
    include_once("src/database/Personne.php");
    include_once("src/database/Relation.php");

    class Acte extends Table{

        var $xml;
        var $relations;

        function __construct($id){
            $this->relations = [];
            parent::__construct("acte", $id);
        }

        function set_xml($xml){
            $this->xml = $xml;

            if($xml == NULL)
                return;

            if(isset($this->xml->date)){
                $this->set_date_xml($this->xml->date);
            }else{
                $this->set_date_xml(NULL);
            }

            $temoins;
            foreach($this->xml->children() as $child){
                switch($child->getName()){
                    case "epoux":
                        $rep = $this->set_personne($child, "epoux");
                        if($rep != FALSE){
                            $this->set_var("epoux", $rep);
                        }
                        break;
                    case "epouse":
                        $rep = $this->set_personne($child, "epouse");
                        if($rep != FALSE){
                            $this->set_var("epouse", $rep);
                        }
                        break;
                    case "temoins":
                        $temoins = $child;
                        break;
                }
            }

            if(isset($this->values["epoux"], $this->values["epouse"])){
                $this->set_relation(
                    $this->values["epoux"],
                    $this->values["epouse"],
                    STATUT_EPOUX
                );

                $this->set_relation(
                    $this->values["epouse"],
                    $this->values["epoux"],
                    STATUT_EPOUSE
                );
            }

            if(isset($temoins)){
                foreach ($temoins->children() as $child) {
                    if($child->getName() === "temoin"){
                        $rep = $this->set_personne($child);
                        if($rep != FALSE){
                            if(isset($this->values["epoux"]))
                                $this->set_relation(
                                    $rep,
                                    $this->values["epoux"],
                                    STATUT_TEMOIN
                                );

                            if(isset($this->values["epouse"]))
                                $this->set_relation(
                                    $rep,
                                    $this->values["epouse"],
                                    STATUT_TEMOIN
                                );
                        }
                    }
                }
            }
        }

        function set_relation($source, $destination, $statut){
            global $log;

            $relation = new Relation();
            $relation->get_same([
                "source" => $source,
                "destination" => $destination,
                "statut_id" => $statut
            ]);
            $relation->set_relation(
                $source,
                $destination,
                $statut,
                $this->values["periode_id"]
            );
            $rep = $relation->into_db();

            if($rep === FALSE){
                $log->e("Erreur lors de l'ajout de la relation source=$source, destination=$destination, statut=$statut");
                return FALSE;
            }

            $this->relations[] = $rep;
            return $rep;
        }

        function set_personne($xml){
            $id_pers = NULL;

            if(isset($this->values[$xml->getName()]))
                $id_pers = $this->values[$xml->getName()];

            $pers = new Personne($id_pers);
            $pers->set_xml($xml, $this->values["periode_id"]);
            $rep = $pers->into_db();

            if($rep != FALSE){
                return $rep;
            }
            return FALSE;
        }

        function get_num(){
            if(isset($this->xml->attributes()["num"]))
                return $this->xml->attributes()["num"];
            return FALSE;
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

            if($tmp !== FALSE)
                $this->set_var("periode_id", $tmp);
        }

        function into_db(){
            global $mysqli, $alert;

            if(!isset($this->id)){
                $alert->add_error("L'acte ne contient pas de num");
                return FALSE;
            }

            $rep = TRUE;
            if(db_has_acte($this->id)){
                if(count($this->updated) > 0)
                    $rep =  $mysqli->update($this->table_name, $this->updated, "id='$this->id'");
            }else{
                if(count($this->updated) > 0){
                    $this->updated["id"] = $this->id;
                    $rep = $mysqli->insert("acte", $this->updated);
                }
            }

            $this->link_relation_to_acte();

            if($rep)
                return $this->contenu_into_db();
            return FALSE;
        }

        function link_relation_to_acte(){
            global $mysqli, $log;

            foreach ($this->relations as $k) {
                $values = [
                    "acte_id" => $this->id,
                    "relation_id" => $k
                ];
                $rep = $mysqli->insert("acte_has_relation", $values);

                if($rep === FALSE){
                    $log->e("Erreur lors du lien entre relation=$k et acte=$this->id dans acte_has_relation");
                }
            }
        }

        function contenu_into_db(){
            global $mysqli;

            $contenu = $mysqli->real_escape_string($this->xml->asXML());
            $values = [
                "acte_id" => $this->get_num(),
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
