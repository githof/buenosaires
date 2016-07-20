<?php

    class TableEntry {

        var $table_name;

        var $id;
        var $values;
        var $updated;

        var $id_periode_ref;
        var $acte_relations;

        function __construct($table_name, $id = NULL){
            $this->table_name = $table_name;
            $this->id = $id;
            $this->values = [];
            $this->updated = [];
            $this->id_periode_ref = NULL;
            $this->acte_relations = [];

            if($this->id != NULL)
                $this->from_db();
        }

        function from_db(){
            global $mysqli, $log;

            if(!isset($this->id)){
                $log->e("Impossible de récupérer les infos de $this->table_name sans id");
                return FALSE;
            }

            $rep = $mysqli->select($this->table_name, ["*"], "id='$this->id'");
            if($rep->num_rows == 1){
                $row = $rep->fetch_assoc();
                foreach ($row as $k => $v) {
                    $this->values[$k] = $v;
                }
                return TRUE;
            }
            return FALSE;
        }

        function into_db(){
            global $mysqli;

            if(count($this->updated) == 0)
                return $this->id;

            if(isset($this->id)){
                $rep = $mysqli->update($this->table_name, $this->updated, "id='$this->id'");
                if($rep === TRUE)
                    return $this->id;
            }else{
                $rep = $mysqli->insert($this->table_name, $this->updated);
                if($rep === TRUE)
                    return $this->get_last_id();
            }
            return FALSE;
        }

        function get_same($values){
            global $mysqli;

            $s = "";
            $i = 0;
            foreach ($values as $k => $v) {
                $s .= $k . "='" . $v . "'";
                if($i < count($values) -1)
                    $s .= " AND ";
                $i++;
            }

            $rep = $mysqli->select($this->table_name, ["id"], $s);
            if($rep->num_rows == 1){
                $row = $rep->fetch_assoc();
                $this->id = $row["id"];
                $this->from_db();
                return TRUE;
            }
            return FALSE;
        }

        function get_last_id(){
            global $mysqli;

            $rep = $mysqli->select($this->table_name, ["id"], "", "ORDER BY id DESC LIMIT 1");
            if($rep->num_rows == 1){
                $row = $rep->fetch_assoc();
                return intval($row["id"]);
            }
            return FALSE;
        }

        function set_var($name, $value){
            if(!isset($this->values[$name]) || $this->values[$name] != $value){
                $this->values[$name] = $value;
                $this->updated[$name] = $value;
            }
        }

        function set_periode($ref_periode_id = NULL){
            $periode;

            $ref_periode = NULL;
            if(isset($ref_periode_id))
                $ref_periode = new Periode($ref_periode_id);

            if(isset($this->values["periode_id"])){
                $periode = new Periode($this->values["periode_id"]);
                if(isset($ref_periode))
                    $periode->add_periode($ref_periode);
            }else{
                $periode = new Periode();
                $periode->default_periode();
                if(isset($ref_periode))
                    $periode->copy($ref_periode);
            }
            $rep = $periode->into_db();
            if($rep != FALSE){
                $this->set_var("periode_id", $rep);
                return TRUE;
            }
            return TRUE;
        }

        function set_personne($xml){
            $id_pers = NULL;

            if(isset($this->values[$xml->getName()]))
                $id_pers = $this->values[$xml->getName()];

            $pers = new Personne($id_pers);
            $pers->set_xml($xml, $this->id_periode_ref, $this->acte_relations);
            $rep = $pers->into_db();

            if($rep != FALSE){
                return $rep;
            }
            return FALSE;
        }

        function set_relation($source, $destination, $statut){
            global $log;

            $relation = new Relation();
            $relation->get_same([
                "source" => $source,
                "destination" => $destination,
                "statut_id" => $statut
            ]);
            $relation->setup(
                $source,
                $destination,
                $statut,
                $this->id_periode_ref
            );
            $rep = $relation->into_db();

            if($rep === FALSE){
                $log->e("Erreur lors de l'ajout de la relation source=$source, destination=$destination, statut=$statut");
                return FALSE;
            }
            return $rep;
        }
    }

?>
