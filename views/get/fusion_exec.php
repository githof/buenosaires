<?php

    function is_fusion_possible($personne_A, $personne_B){
        $relations_A = $personne_A->get_relations();
        $relations_B = $personne_B->get_relations();

        if(count($relations_A) > 0 && count($relations_B) > 0){
            foreach($relations_A as $relation_A){
                foreach($relations_B as $relation_B){
                    if($relation_A->personne_source->id == $relation_B->personne_source->id ||
                        $relation_A->personne_destination->id == $relation_B->personne_destination->id)
                        return "<div class='alert'>Des relations sont en conflits</div>";
                }
            }
        }

        return TRUE;
    }

    function has_same_id($array, $id){
        foreach($array as $obj){
            if($obj->id == $id)
                return TRUE;
        }
        return FALSE;
    }

    function fusion($personne_keep, $personne_throw){
        global $mysqli;

        foreach($personne_throw->prenoms as $prenom){
            $where = "prenom_id='$prenom->id' && personne_id='$personne_throw->id'";
            if(has_same_id($personne_keep->prenoms, $prenom->id))
                $mysqli->delete("prenom_personne", $where);
            else
                $mysqli->update("prenom_personne", ["personne_id" => "$personne_keep->id"], $where);
        }

        foreach($personne_throw->noms as $nom){
            $where = "nom_id='$nom->id' && personne_id='$personne_throw->id'";
            if(has_same_id($personne_keep->noms, $nom->id))
                $mysqli->delete("nom_personne", $where);
            else
                $mysqli->update("nom_personne", ["personne_id" => "$personne_keep->id"], $where);
        }

        $mysqli->update("acte", ["epoux" => "$personne_keep->id"], "epoux=$personne_throw->id");
        $mysqli->update("acte", ["epouse" => "$personne_keep->id"], "epouse=$personne_throw->id");

        
    }

    $html = "";

    if(isset(ARGS["id-personne-A"], ARGS["id-personne-B"], ARGS["id"])){
        $personne_A = new Personne(ARGS["id-personne-A"]);
        $personne_B = new Personne(ARGS["id-personne-B"]);

        $mysqli->from_db($personne_A);
        $mysqli->from_db($personne_B);

        $result = is_fusion_possible($persnne_A, $personne_B);
        if($result === TRUE){
            if(ARGS["id"] == $personne_A->id || ARGS["id"] == $personne_B->id){
                if(ARGS["id"] == $personne_A->id)
                    fusion($personne_A, $personne_B);
                else
                    fusion($personne_B, $personne_A);
            }else{
                $html = "<div class='alert'>Erreur dans les ID des personnes</div>";
            }
            // CLEAR PRENOM NOM UNUSED
            // personne get_conditions/get_relations into from_db !!!
        }else
            $html = $result;
    }

    echo $html;

?>
