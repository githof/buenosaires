<?php

    include_once(ROOT."src/database/Periode.php");
    include_once(ROOT."src/database/TableEntry.php");
    include_once(ROOT."src/database/Personne.php");
    include_once(ROOT."src/database/Relation.php");

    class Acte extends TableEntry{

        var $xml;
        var $id_source;

        function __construct($id){
            parent::__construct("acte", $id);
            $this->acte_parent = $this;
            $this->id_source = SOURCE_DEFAULT_ID;
        }

        function from_xml($xml){
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
                        $rep = $this->set_personne($child);
                        if($rep != FALSE){
                            $this->set_var("epoux", $rep);
                        }
                        break;
                    case "epouse":
                        $rep = $this->set_personne($child);
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
                $id_rela = $this->set_relation(
                    $this->values["epoux"],
                    $this->values["epouse"],
                    STATUT_EPOUX
                );
                if($id_rela != FALSE)
                    $this->relations[] = $id_rela;

                $id_rela = $this->set_relation(
                    $this->values["epouse"],
                    $this->values["epoux"],
                    STATUT_EPOUSE
                );
                if($id_rela != FALSE)
                    $this->relations[] = $id_rela;
            }

            if(isset($temoins)){
                foreach ($temoins->children() as $child) {
                    if($child->getName() === "temoin"){
                        $rep = $this->set_personne($child);
                        if($rep != FALSE){
                            if(isset($this->values["epoux"])){
                                $id_rela = $this->set_relation(
                                    $rep,
                                    $this->values["epoux"],
                                    STATUT_TEMOIN
                                );
                                if($id_rela != FALSE)
                                    $this->relations[] = $id_rela;
                            }


                            if(isset($this->values["epouse"])){
                                $id_rela = $this->set_relation(
                                    $rep,
                                    $this->values["epouse"],
                                    STATUT_TEMOIN
                                );
                                if($id_rela != FALSE)
                                    $this->relations[] = $id_rela;
                            }
                        }
                    }
                }
            }
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

            if($tmp !== FALSE){
                $this->set_var("periode_id", $tmp);
                return TRUE;
            }
            return FALSE;
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
            $this->create_conditions();

            if($rep)
                return $this->contenu_into_db();
            return FALSE;
        }

        function create_conditions(){
            foreach ($this->conditions as $k) {
                $this->set_condition(
                    $k[1],
                    $this->id_source,
                    $k[0],
                    $this->id
                );
            }
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
