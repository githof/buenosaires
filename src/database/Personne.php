<?php

    include_once(ROOT."src/database/TableEntry.php");
    include_once(ROOT."src/database/Nom.php");
    include_once(ROOT."src/database/Prenom.php");
    include_once(ROOT."src/database/Relation.php");
    include_once(ROOT."src/database/Condition.php");

    class Personne extends TableEntry{

        var $list_prenom;
        var $list_nom;

        var $pere;
        var $mere;

        var $relations;
        var $texte_conditions;

        function __construct($id = NULL){
            $this->list_prenom = [];
            $this->list_nom = [];
            $this->relations = [];
            $this->texte_conditions = [];
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

        function from_xml($xml, $acte = NULL){
            if($xml == NULL)
                return;

            $this->acte = $acte;
            $attr = $xml->attributes();

            if(isset($acte))
                $this->set_periode($acte->values["periode_id"]);
            else
                $this->set_periode(NULL);

            if(isset($attr["don"]) && ($attr["don"] === "true"))
                $this->conditions[] = "don";

            $prenoms = [];
            $noms = [];
            foreach($xml->children() as $childXML){
                switch($childXML->getName()){
                    case "prenom":
                        $rep = $this->set_prenom($childXML->__toString());
                        if($rep != FALSE)
                            $prenoms[] = $rep;
                        break;
                    case "nom":
                        $rep = $this->set_nom($childXML->__toString());
                        if($rep != FALSE)
                            $noms[] = $rep;
                        break;
                    case "pere":
                        $pere = personne_from_xml($childXML, $acte);
                        if($pere != NULL){
                            $this->pere = $pere;
                        }
                        break;
                    case "mere":
                        $mere = personne_from_xml($childXML, $acte);
                        if($mere != NULL){
                            $this->mere = $mere;
                        }
                        break;
                    case "condition":
                        $this->texte_conditions[] = $childXML->__toString();
                        break;
                }
            }

            $this->list_prenom = $prenoms;
            $this->list_nom = $noms;
        }

        function into_db($id_require = FALSE){
            $result = parent::into_db(TRUE);
            $this->update_nom_prenom();
            return $this->id;
        }

        function set_relations($acte = NULL){
            $periode_id_ref = NULL;

            if(isset($acte->values["periode_id"]))
                $periode_id_ref = $acte->values["periode_id"];

            if(isset($this->pere)){
                $relation = create_relation(
                    $this,
                    $this->pere,
                    STATUT_PERE,
                    $periode_id_ref
                );
                if($relation != NULL && $acte != NULL)
                    link_relation_acte_into_db($acte, $relation);
            }

            if(isset($this->mere)){
                $relation = create_relation(
                    $this,
                    $this->mere,
                    STATUT_MERE,
                    $periode_id_ref
                );
                if($relation != NULL && $acte != NULL)
                    link_relation_acte_into_db($acte, $relation);
            }
        }

        function set_conditions($acte){
            foreach ($this->texte_conditions as $texte_cond) {
                $condition = create_condition(
                    $texte_cond,
                    $acte->source_id,
                    $this,
                    $acte
                );
            }
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

            /*
            $id_attribut = NULL;
            if(isset($attribut))
                $id_attribut = db_has_attribut($attribut);

            if(isset($id_attribut)){
                if($obj->get_same([
                    "no_accent" => no_accent($nom)
                    "attribut_id" => $id_attribut
                ])){
                    return $obj->into_db();
                }
            }*/

            $obj->get_same([
                "no_accent" => no_accent($nom)
            ]);

            $obj->set_nom($nom);

            if(isset($attribut) && !$obj->set_attribut($attribut))
                return FALSE;

            return $obj->into_db();
        }

    }

    function personne_from_xml($xml, $acte = NULL){
        global $log;

        $id_pers = NULL;
        $xml_attr = $xml->attributes();

        $log->i("Ajout d'une personne à partir d'xml");

        if(isset($xml_attr["id"]))
            $id_pers = $xml_attr["id"];

        $pers = new Personne($id_pers);
        $pers->from_xml($xml, $acte);
        $result = $pers->into_db();

        if($result === FALSE){
            $log->e("Erreur lors de l'ajout de la personne xml=$xml");
            return FALSE;
        }
        return $pers;
    }

?>
