<?php

    include_once(ROOT."src/database/TableEntry.php");
    include_once(ROOT."src/database/Nom.php");
    include_once(ROOT."src/database/Prenom.php");
    include_once(ROOT."src/database/Relation.php");
    include_once(ROOT."src/database/Condition.php");

    class Personne extends TableEntry{

        var $xml;

        var $prenoms_id;
        var $noms_id;

        var $pere;
        var $mere;

        var $relations;
        var $texte_conditions;

        function __construct($id = NULL){
            $this->prenoms_id = [];
            $this->noms_id = [];
            $this->relations = [];
            $this->texte_conditions = [];
            $this->xml = NULL;
            parent::__construct("personne", $id);
        }

        function from_db(){
            global $mysqli;

            parent::from_db();

            $this->prenoms_id = [];
            $this->noms_id = [];

            $rep = $mysqli->select("prenom_personne", ["*"], "personne_id='$this->id'", "ORDER BY ordre ASC");
            if($rep->num_rows > 0){
                while($row = $rep->fetch_assoc()){
                    $this->prenoms_id[] = $row["prenom_id"];
                }
            }

            $rep = $mysqli->select("nom_personne", ["*"], "personne_id='$this->id'", "ORDER BY ordre ASC");
            if($rep->num_rows > 0){
                while($row = $rep->fetch_assoc()){
                    $this->noms_id[] = $row["nom_id"];
                }
            }
        }

        function from_xml($xml, $acte = NULL){
            if($xml == NULL)
                return;

            $this->xml = $xml;
            $attr = $xml->attributes();

            if(isset($acte))
                $this->set_periode($acte->values["periode_id"]);
            else
                $this->set_periode(NULL);

            if(isset($attr["don"]) && ($attr["don"] === "true"))
                $this->conditions[] = "don";

            $prenoms_id = [];
            $noms_id = [];
            foreach($xml->children() as $childXML){
                switch($childXML->getName()){
                    case "prenom":
                        $rep = $this->set_prenom($childXML->__toString());
                        if($rep != FALSE)
                            $prenoms_id[] = $rep;
                        break;
                    case "nom":
                        $rep = $this->set_nom($childXML);
                        if($rep != FALSE)
                            $noms_id[] = $rep;
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

            $this->prenoms_id = $prenoms_id;
            $this->noms_id = $noms_id;
        }

        function looking_for_same_in_db($vals = NULL){
            global $mysqli;
            $ids = NULL;
            $ids_tmp = NULL;

            if(isset($this->id))
                return FALSE;

            foreach($this->noms_id as $k => $nom_id){
                $result = $mysqli->query("SELECT personne_id FROM nom_personne WHERE nom_id='$nom_id'");
                if($result === FALSE || $result->num_rows == 0)
                    return FALSE;

                $ids_tmp = [];
                while($row = $result->fetch_assoc()){
                    $ids_tmp[] = $row["personne_id"];
                }

                if(isset($ids))
                    $ids = array_intersect($ids, $ids_tmp);
                else
                    $ids = $ids_tmp;

                if(count($ids) == 0)
                    return FALSE;
            }

            foreach($this->prenoms_id as $k => $prenom_id){
                $result = $mysqli->query("SELECT personne_id FROM prenom_personne WHERE prenom_id='$prenom_id'");
                if($result === FALSE || $result->num_rows == 0)
                    return FALSE;

                $ids_tmp = [];
                while($row = $result->fetch_assoc()){
                    $ids_tmp[] = $row["personne_id"];
                }

                if(isset($ids))
                    $ids = array_intersect($ids, $ids_tmp);
                else
                    $ids = $ids_tmp;

                if(count($ids) == 0)
                    return FALSE;
            }

            if(isset($ids)){
                $this->id = array_shift($ids);
                $this->from_db();
                return TRUE;
            }

            return FALSE;
        }

        function into_db($id_require = FALSE){
            $result = parent::into_db(TRUE);
            $this->update_nom_prenom();

            if(isset($this->xml)){
                $attributesXML = $this->xml->attributes();
                if(!isset($attributesXML["id"]))
                    $this->xml->addAttribute("id", "$this->id");
            }
            return $this->id;
        }

        function set_relations($acte = NULL){
            $periode_id_ref = NULL;

            if(isset($this->pere))
                $this->add_relation($this->pere, STATUT_PERE, $periode_id_ref);

            if(isset($this->mere))
                $this->add_relation($this->mere, STATUT_MERE, $periode_id_ref);
        }

        function add_relation($personne_destination, $statut, $acte){
            $periode_id_ref = NULL;

            if(isset($acte->values["periode_id"]))
                $periode_id_ref = $acte->values["periode_id"];

            $relation = create_relation(
                $this,
                $personne_destination,
                $statut,
                $periode_id_ref
            );

            if($relation != NULL && $acte ! = NULL)
                link_relation_acte_into_db($acte, $relation);
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

            $i = 1;
            foreach ($this->prenoms_id as $k) {
                $values = [
                    "personne_id" => $this->id,
                    "prenom_id" => $k,
                    "ordre" => $i
                ];
                $mysqli->insert(
                    "prenom_personne",
                    $values,
                    "ON DUPLICATE KEY UPDATE ordre='$i'"
                );
                $i++;
            }

            $i = 1;
            foreach ($this->noms_id as $k) {
                $values = [
                    "personne_id" => $this->id,
                    "nom_id" => $k,
                    "ordre" => $i
                ];
                $mysqli->insert(
                    "nom_personne",
                    $values,
                    "ON DUPLICATE KEY UPDATE ordre='$i'"
                );
                $i++;
            }
        }

        function set_prenom($prenom_text){
            $prenom = new Prenom();
            $prenom->set_prenom($prenom_text);
            $prenom->looking_for_same_in_db();

            return $prenom->into_db();
        }

        function set_nom($nomXML){
            $attribute_text = NULL;

            $this->all_nom_attributes_in_one($nomXML);
            if(isset($nomXML->attributes()["attr"]))
                $attribute_text = $nomXML->attributes()["attr"];

            $nom = new Nom();
            $nom->set_attribute($attribute_text);
            $nom->set_nom($nomXML->__toString());
            $nom->looking_for_same_in_db();

            return $nom->into_db();
        }

        function all_nom_attributes_in_one($nomXML){
            $new_attr = "";
            $attrs = $nomXML->attributes();
            $attrs_to_unset = [];
            $i = 0;

            if(isset($attrs["attr"]))
                return;

            foreach ($attrs as $key => $value){
                if(strcmp($key, "id") != 0 && strcmp($value, "true") == 0){
                    $new_attr .= "$key";
                    $attrs_to_unset[] = $key;
                    if($i < count($attrs) -1)
                        $new_attr .= " ";
                }
            }

            foreach($attrs_to_unset as $key => $value){
                unset($attrs[$value]);
            }

            if(strlen($new_attr) == 0)
                return;

            $nomXML->addAttribute("attr", $new_attr);
        }

        function get_prenoms(){
            global $mysqli;
            $prenoms = [];

            $result = $mysqli->query(
                "SELECT prenom
                FROM prenom INNER JOIN prenom_personne
                ON prenom.id = prenom_personne.prenom_id
                WHERE prenom_personne.personne_id = '$this->id'
                ORDER BY prenom_personne.ordre"
            );
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc())
                    $prenoms[] = $row["prenom"];
            }
            return $prenoms;
        }

        function get_noms(){
            global $mysqli;
            $noms = [];

            $result = $mysqli->query(
                "SELECT nom, value
                FROM nom INNER JOIN nom_personne
                ON nom.id = nom_personne.nom_id
                LEFT JOIN attribut
                ON attribut.id = nom.attribut_id
                WHERE nom_personne.personne_id = '$this->id'
                ORDER BY nom_personne.ordre"
            );
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $nom = $row["nom"];
                    if(isset($row["value"]))
                        $nom = $row["value"] . " " . $nom;
                    $noms[] = $nom;
                }
            }
            return $noms;
        }

        function get_conditions(){
            global $mysqli;
            $conditions = [];

            $result = $mysqli->select("cond", ["id"], "personne_id='$this->id'");
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $conditions[] = new Condition($row["id"]);
                }
            }
            return $conditions;
        }

        function get_relations(){
            global $mysqli;
            $relations = [];

            $result = $mysqli->select("relation", ["id"], "source='$this->id' OR destination='$this->id'");
            if($result != FALSE && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $relations[] = new Relation($row["id"]);
                }
            }
            return $relations;
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
        $pers->looking_for_same_in_db();
        $result = $pers->into_db();

        if($result === FALSE){
            $log->e("Erreur lors de l'ajout de la personne xml=$xml");
            return FALSE;
        }
        return $pers;
    }

?>
