<?php

    // function is_fusion_possible($personne_A, $personne_B){
    //     global $alert;
    //
    //     if(count($personne_A->relations) > 0 && count($personne_B->relations) > 0){
    //         foreach($personne_A->relations as $relation_A){
    //             foreach($personne_B->relations as $relation_B){
    //                 if($relation_A->personne_source->id == $relation_B->personne_source->id ||
    //                     $relation_A->personne_destination->id == $relation_B->personne_destination->id){
    //                         $alert->warning("Des relations sont en conflits");
    //                         return FALSE;
    //                     }
    //             }
    //         }
    //     }
    //
    //     return TRUE;
    // }

    function has_same_id($array, $id){
        foreach($array as $obj){
            if($obj->id == $id)
                return TRUE;
        }
        return FALSE;
    }

    function has_same_condition($conditions, $condition_cmp){
        foreach($conditions as $condition){
            if($condition->text == $condition_cmp->text)
                return $condition;
        }
        return FALSE;
    }

    function has_same_relation($relations, $relation_cmp, $personne_keep, $personne_throw){
        $is_source = $relation_cmp->personne_source->id == $personne_throw->id;
        foreach($relations as $relation){
            if($relation->statut_id == $relation_cmp->statut_id){
                if($is_source && $relation->personne_source->id == $personne_keep->id)
                    return $relation;
                else if(!$is_source && $relation->personne_destination->id == $personne_keep->id)
                    return $relation;
            }
        }
        return FALSE;
    }

    function has_same_acte($actes, $id){
        foreach($actes as $acte){
            if($acte->id == $id)
                return TRUE;
        }
        return FALSE;
    }

    function fusion_conditions($personne_keep, $personne_throw){
        global $mysqli, $log;

        $log->d("fusion conditions");
        foreach($personne_throw->conditions as $condition_throw){
            $same = has_same_condition($personne_keep->conditions, $condition_throw);
            if($same != FALSE){
                $acte_id_delete = [];
                $acte_id_update = [];
                $result = $mysqli->select("acte_has_condition", ["acte_id"], "condition_id = '$condition_throw->id'");
                if($result != FALSE && $result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        if(has_same_acte($same->actes, $row["acte_id"]))
                            $acte_id_delete[] = $row["acte_id"];
                        else
                            $acte_id_update[] = $row["acte_id"];
                    }
                }

                if(count($acte_id_delete) > 0){
                    $str = array_to_string_with_separator($acte_id_delete, ", ");
                    $mysqli->delete("acte_has_condition", "condition_id = '$condition_throw->id' AND acte_id IN ($str)");
                }

                if(count($acte_id_update) > 0){
                    $str = array_to_string_with_separator($acte_id_update, ", ");
                    $mysqli->update("acte_has_condition", ["condition_id" => "$same->id"], "condition_id = '$condition_throw->id' AND acte_id IN ($str)");
                }

                $mysqli->delete("condition", "id = '$condition_throw->id'");
            }else{
                $mysqli->update("condition", ["personne_id" => "$personne_keep->id"], "id = '$condition_throw->id'");
            }
        }
    }

    function fusion_relations($personne_keep, $personne_throw){
        global $mysqli, $log;

        $log->d("fusion relations");
        foreach($personne_throw->relations as $relation_throw){
            $is_source_throw = $relation_throw->personne_source->id == $personne_throw->id;
            $same = has_same_relation($personne_keep->relations, $relation_throw, $personne_keep, $personne_throw);
            if($same != FALSE){
                $acte_id_delete = [];
                $acte_id_update = [];
                $result = $mysqli->select("acte_has_relation", ["acte_id"], "relation_id = '$relation_throw->id'");
                if($result != FALSE && $result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        if(has_same_acte($same->actes, $row["acte_id"]))
                            $acte_id_delete[] = $row["acte_id"];
                        else
                            $acte_id_update[] = $row["acte_id"];
                    }
                }

                if(count($acte_id_delete) > 0){
                    $str = array_to_string_with_separator($acte_id_delete, ", ");
                    $mysqli->delete("acte_has_relation", "relation_id = '$relation_throw->id' AND acte_id IN ($str)");
                }

                if(count($acte_id_update) < 0){
                    $str = array_to_string_with_separator($acte_id_update, ", ");
                    $mysqli->update("acte_has_relation", ["relation_id" => "$same->id"], "relation_id = '$relation_throw->id' AND acte_id IN ($str)");
                }

                $mysqli->delete("relation", "id='$relation_throw->id'");
            }else{
                $pers = "pers_destination_id";
                if($is_source_throw)
                    $pers = "pers_source_id";
                $mysqli->update("relation", ["$pers" => "$personne_keep->id"], "id = '$relation_throw->id'");
            }
        }
    }

    function fusion($personne_keep, $personne_throw, $noms, $prenoms){
        global $mysqli, $log;

        $mysqli->delete("prenom_personne", "personne_id='$personne_keep->id' OR personne_id='$personne_throw->id'");
        $i = 1;
        foreach($prenoms as $prenom){
            $mysqli->into_db($prenom);
            $mysqli->into_db_prenom_personne($personne_keep, $prenom, $i);
            $i++;
        }

        $mysqli->delete("nom_personne", "personne_id='$personne_keep->id' OR personne_id='$personne_throw->id'");
        $i = 1;
        foreach($noms as $nom){
            $mysqli->into_db($nom);
            $mysqli->into_db_nom_personne($personne_keep, $nom, $i);
            $i++;
        }

        $log->d("fusion actes");
        $mysqli->update("acte", ["epoux" => "$personne_keep->id"], "epoux='$personne_throw->id'");
        $mysqli->update("acte", ["epouse" => "$personne_keep->id"], "epouse='$personne_throw->id'");

        fusion_conditions($personne_keep, $personne_throw);
        fusion_relations($personne_keep, $personne_throw);

        $log->d("fusion remove personee");
        $mysqli->delete_personne($personne_throw->id);
    }



    $html = "";

    if(isset($ARGS["id-personne-A"],
            $ARGS["id-personne-B"],
            $ARGS["id"],
            $ARGS["noms"],
            $ARGS["prenoms"])){
        $personne_A = new Personne($ARGS["id-personne-A"]);
        $personne_B = new Personne($ARGS["id-personne-B"]);

        $mysqli->from_db($personne_A);
        $mysqli->from_db($personne_B);

        $noms_str = explode(",", $ARGS["noms"]);
        $prenoms_str = explode(",", $ARGS["prenoms"]);

        foreach($noms_str as $nom_str){
            $split = explode(")", $nom_str);
            if(count($split) == 2){
                $split0 = explode("(", $split[0]);
                $attribut = $split0[1];
                $noms[] = new Nom(NULL, trim($split[1]), NULL, trim($attribut));
            }else{
                $noms[] = new Nom(NULL, trim($nom_str));
            }
        }

        foreach($prenoms_str as $prenom_str){
            $prenoms[] = new Prenom(NULL, $prenom_str);
        }


        $log->d("fusion possible");
        if($ARGS["id"] == $personne_A->id || $ARGS["id"] == $personne_B->id){
            if($ARGS["id"] == $personne_A->id)
                fusion($personne_A, $personne_B, $noms, $prenoms);
            else
                fusion($personne_B, $personne_A, $noms, $prenoms);
            $mysqli->remove_unused_prenoms_noms();
            $alert->success("Succès de la fusion");
        }else{
            $alert->warning("Erreur dans les ID des personnes");
        }
    }

    echo $html;

?>
