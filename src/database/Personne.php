<?php

    include_once(ROOT."src/database/TableEntry.php");
    include_once(ROOT."src/database/Nom.php");
    include_once(ROOT."src/database/Prenom.php");

    class Personne extends TableEntry{

        var $list_prenom;
        var $list_nom;
        var $id_pere;
        var $id_mere;

        function __construct($id = NULL){
            $this->list_prenom = [];
            $this->list_nom = [];
            parent::__construct("personne", $id);
        }

        function from_db(){
            global $mysqli;

            parent::from_db();

            $rep = $mysqli->select("prenom_personne", ["*"], "personne_id='$this->id'", "ORDER BY ordre ASC");
            if($rep->num_rows > 0){
                while($row = $rep->fetch_assoc()){
                    $this->list_prenom[] = $row["prenom_id"];
                }
            }

            $rep = $mysqli->select("nom_personne", ["*"], "personne_id='$this->id'", "ORDER BY ordre ASC");
            if($rep->num_rows > 0){
                while($row = $rep->fetch_assoc()){
                    $this->list_nom[] = $row["nom_id"];
                }
            }
        }

        function from_xml($xml, $acte){
            if($xml == NULL)
                return;

            $this->acte = $acte;
	    $attr = $xml->attributes();
	    
            $id = $attr["id"];
            if(isset($id)){
                $this->id = $id;
                $this->from_db();
            }

            $this->set_periode($this->acte->values["periode_id"]);

            if(isset($attr["don"]) && ($attr["don"] === "true")
	       $this->conditions[] = "don";

            $prenoms = [];
            $noms = [];
            foreach($xml->children() as $child){
                switch($child->getName()){
                    case "prenom":
                        $rep = $this->set_prenom($child->__toString());
                        if($rep != FALSE)
                            $prenoms[] = $rep;
                        break;
                    case "nom":
                        $rep = $this->set_nom($child->__toString());
                        if($rep != FALSE)
                            $noms[] = $rep;
                        break;
                    case "pere":
                        $rep = $this->set_personne($child);
                        if($rep != FALSE){
                            $this->id_pere = $rep;
                        }
                        break;
                    case "mere":
                        $rep = $this->set_personne($child);
                        if($rep != FALSE){
                            $this->id_mere = $rep;
                        }
                        break;
                    case "condition":
                        $this->conditions[] = $child->__toString();
                        break;
                }
            }

            $this->list_prenom = $prenoms;
            $this->list_nom = $noms;
        }

        function into_db(){
            global $mysqli;

            if(!isset($this->id))
                $this->id = intval($this->get_last_id()) +1;

            $rep = TRUE;
            if(db_has_personne($this->id)){
                if(count($this->updated) > 0)
                    $rep =  $mysqli->update($this->table_name, $this->updated, "id='$this->id'");
            }else{
                if(count($this->updated) > 0){
                    $this->updated["id"] = $this->id;
                    $rep = $mysqli->insert($this->table_name, $this->updated);
                }
            }

            if($rep === FALSE)
                return FALSE;

            if(isset($this->id_pere, $this->acte)){
                $id_rela = $this->set_relation(
                    $this->id,
                    $this->id_pere,
                    STATUT_PERE
                );
                if($id_rela != FALSE)
                    $this->acte->relations[] = $id_rela;
            }

            if(isset($this->id_mere, $this->acte)){
                $id_rela = $this->set_relation(
                    $this->id,
                    $this->id_mere,
                    STATUT_MERE
                );
                if($id_rela != FALSE)
                    $this->acte->relations[] = $id_rela;
            }

            foreach ($this->conditions as $k) {
                $this->acte->conditions[] = [$this->id, $k];
            }

            $this->update_nom_prenom();
            return $this->id;
        }

        function update_nom_prenom(){
            global $mysqli;

            $mysqli->delete("prenom_personne", "personne_id='$this->id'");
            $mysqli->delete("nom_personne", "personne_id='$this->id'");

            $i = 1;
            foreach ($this->list_prenom as $k) {
                $values = [
                    "personne_id" => $this->id,
                    "prenom_id" => $k,
                    "ordre" => $i
                ];
                $mysqli->insert("prenom_personne", $values);
                $i++;
            }

            $i = 1;
            foreach ($this->list_nom as $k) {
                $values = [
                    "personne_id" => $this->id,
                    "nom_id" => $k,
                    "ordre" => $i
                ];
                $mysqli->insert("nom_personne", $values);
                $i++;
            }
        }

        function set_prenom($prenom){
            $obj = new Prenom();
            $obj->get_same(["no_accent" => no_accent($prenom)]);
            $obj->set_prenom($prenom);

            return $obj->into_db();
        }

        function set_nom($nom, $attribut = NULL){
            $obj = new Nom();

            $id_attribut;
            if(isset($attribut))
                $id_attribut = db_has_attribut($attribut);

            if(isset($id_attribut)){
                if($obj->get_same([
                    "no_accent" => no_accent($nom),
                    "attribut_id" => $id_attribut
                ])){
                    return $obj->into_db();
                }
            }

            if(!$obj->set_nom($nom))
                return FALSE;

            if(isset($attribut) && !$obj->set_attribut($attribut))
                return FALSE;

            return $obj->into_db();
        }

    }

    function db_has_personne($id){
        global $mysqli;

        $rep = $mysqli->select("personne", ["id"], "id='$id'");
        return $rep->num_rows > 0;
    }


?>
