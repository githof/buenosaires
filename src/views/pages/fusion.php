<?php

    include_once(ROOT."src/html_entities.php");


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

    function fusion_update_contenu_acte($personne_id_old, $personne_id_new){
        global $mysqli;

        $personne = new Personne($personne_id_old);
        $mysqli->from_db($personne);

        $actes = [];

        $results = $mysqli->select(
            "acte",
            ["id"],
            "epoux='$personne_id_old' OR epouse='$personne_id_old'"
        );
        if($results != FALSE && $results->num_rows > 0){
            while($row = $results->fetch_assoc())
                $actes[] = $row["id"];
        }

        foreach($personne->conditions as $condition){
            foreach($condition->actes as $acte)
                $actes[] = $acte->id;
        }

        foreach($personne->relations as $relation){
            foreach($relation->actes as $acte)
                $actes[] = $acte->id;
        }

        $actes = array_unique($actes);

        foreach($actes as $acte){
            $results = $mysqli->select(
                "acte_contenu",
                ["contenu"],
                "acte_id='$acte'"
            );
            $contenu = $results->fetch_assoc()["contenu"];
            $xml = new SimpleXMLElement($contenu);

	    /* Ouch, d'où c'est exhaustif ?
	       Faudrait pas mettre ça ailleurs, un peu plus paramétrable ?
	       Voire, faire un parcours systématique plutôt ? */
            $paths = [
                "epoux", "epouse", "epoux/pere", "epoux/mere", "epouse/pere",
                "epouse/mere", "temoins/temoin", "temoins/temoin/pere",
                "temoins/temoin/mere"
            ];

            foreach($paths as $path){
                $results = $xml->xpath($path);
                while(list( , $node) = each($results)){
                    $attr = $node->attributes();
                    if($attr["id"] == $personne_id_old)
                        $attr["id"] = $personne_id_new;
                }
            }
            $contenu = $xml->asXML();

            $mysqli->update(
                "acte_contenu",
                ["contenu" => $contenu],
                "acte_id='$acte'"
            );
        }
    }

/*__ FUSION __ */
/*
BUG : la fusion ne se fait que sur les actes où la personne est époux/se
MAIS le prénom et le nom sont virés même quand la fusion n'est pas faite !!
Ce que je ne comprends pas encore c'est pourquoi l'id n'est pas modifié sur les relations et les conditions dans ce cas, parce que les appels sont quand même faits

 */
    function fusion($personne_keep, $personne_throw, $noms, $prenoms){
        global $mysqli, $log;

	/* Déjà faudrait ptet attendre d'être sûrs que les trucs soient créés
	   avant de supprimer */
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

	/* là-dedans on ne s'occupe que des actes où la personne est époux/se */
        fusion_update_contenu_acte($personne_throw->id, $personne_keep->id);

        $log->d("fusion actes"); // pourquoi ici ??

	/* idem, du coup */
        $mysqli->update("acte", ["epoux" => "$personne_keep->id"], "epoux='$personne_throw->id'");
        $mysqli->update("acte", ["epouse" => "$personne_keep->id"], "epouse='$personne_throw->id'");

        fusion_conditions($personne_keep, $personne_throw);
        fusion_relations($personne_keep, $personne_throw);

        $log->d("fusion remove personne");
        $mysqli->delete_personne($personne_throw->id);
    }


    function html_select_personnes(){
        return "
            <section class='max-2'>
                <h4>Choisir deux personnes à fusionner</h4>
                <div>
                    <input type='text' name='autocomplete' placeholder='Recherche parmis les personnes'>
                    <span class='autocomplete-search'>recherche en cours ...</span>
                </div>
                <div id='auto-complete-results'>
                </div>
                <form id='form-fusion-select-personnes' method='get'>
                    <div></div>
                    <input type='submit' value='Prévisualisez la fusion'>
                </form>
            </section>
        ";
    }

    function html_fusion_debut(){
      return "
      <form method="get" id="fusion-form">
          <button class="btn btn-primary" id="fusion-submit">Fusionner</button>
      ";
    }
    function html_fusion_fin(){
      return "
      </form>
      ";
    }

    function html_fusion_radio_id($AB, $id){
        return "
            <div>
                <input type='radio' name='id' id='radio-pers-$AB' value='$id' checked='checked'>
                <label for='radio-pers-$AB'>$id</label>
                <input type='hidden' name='personne-$AB' value='$id'>
            </div>
        ";
        // je comprends pas à quoi sert le hidden ici
    }

    function html_fusion_keep($id_A, $id_B){
      echo "
      <section>
          <h4>ID  <i>(Choisir l'ID à conserver)</i></h4>
          <div class="fusion-ids flex-horizontal">
      "
      echo html_fusion_radio_id('A', $id_A);
      echo html_fusion_radio_id('B', $id_B);
      echo "
          </div>
      </section>
      "
    }


    function html_fusion_noms($noms){
        $html = "";
        foreach($noms as $nom){
            $attr = "";
            if(isset($nom->attribut))
                $attr = "<div class='nom-attribut'>$nom->attribut</div>";
            $html .= "
                <div id='nom-$nom->id' class='nom'>
                    $attr
                    <div class='nom-nom'>$nom->nom</div>
                </div>";
        }
        return $html;
    }

    function html_fusion_prenoms($prenoms){
        $html = "";
        foreach($prenoms as $prenom){
            $html .=
                "<div id='prenom-$prenom->id' class='prenom'>"
                .$prenom->to_string().
                "</div>";
        }
        return $html;
    }

    function html_fusion_conditions($conditions){
        $html = "";
        foreach($conditions as $condition){
            $html .= html_condition($condition);
        }
        return $html;
    }

    function html_fusion_relations($relations){
        $html = "";
        foreach($relations as $relation){
            $html .= html_relation($relation);
        }
        return $html;
    }

    // Une fois qu'on a sélectionné qui on fusionne, on arrive ici
    function html_fusion($pA, $pB){
        $html_noms = html_fusion_noms($pA->noms)
            . html_fusion_noms($pB->noms);
        $html_prenoms = html_fusion_prenoms($pA->prenoms)
            . html_fusion_prenoms($pB->prenoms);
        $html_conditions = html_fusion_conditions($pA->conditions)
            . html_fusion_conditions($pB->conditions);
        $html_relations = html_fusion_relations($pA->relations)
            . html_fusion_relations($pB->relations);
        $input_prenoms = default_input_prenoms($pA->prenoms, $pB->prenoms);
        $input_noms = default_input_noms($pA->noms, $pB->noms);

        echo html_fusion_debut();
        echo html_fusion_keep($pA->id, $pB->id);
        echo html_fusion_fin();
        ?>
            <section>
                <h4>Prenoms</h4>
                <div class="fusion-prenoms flex-horizontal">
                    <?php echo $html_prenoms; ?>
                </div>
                <div>
                    <div class="help-block">Les prénoms séparés par une virgule</div>
                    <input type="text" name="prenoms" placeholder="Ex: Maria, Josefa" value="<?php echo $input_prenoms; ?>">
                </div>
            </section>
            <section>
                <h4>Noms</h4>
                <div class="fusion-noms flex-horizontal">
                    <?php echo $html_noms; ?>
                </div>
                <div>
                    <div class="help-block">Les noms séparés par une virgule et leurs attributs en parenthèse si besoin</div>
                    <input type="text" name="noms" placeholder="Ex: PERI, (de) BELGRANO" value="<?php echo $input_noms; ?>">
                </div>
            </section>
            <section>
                <h4>Condition</h4>
                <div class="fusion-conditions flex-vertical">
                    <?php echo $html_conditions; ?>
                </div>
            </section>
            <section>
                <h4>Relations</h4>
                <div class="fusion-relations flex-vertical">
                    <?php echo $html_relations; ?>
                </div>
            </section>
        <?php
    }

    if(isset($ARGS["personne-A"],
            $ARGS["personne-B"],
            $ARGS["id"],
            $ARGS["noms"],
            $ARGS["prenoms"])){
        $personne_A = new Personne($ARGS["personne-A"]);
        $personne_B = new Personne($ARGS["personne-B"]);

        $mysqli->from_db($personne_A);
        $mysqli->from_db($personne_B);

        $noms = parse_noms($ARGS["noms"]);
        $prenoms = parse_prenoms($ARGS["prenoms"]);

/* L'appel à fusion est ici */
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
    }else if(isset($ARGS["personne-A"], $ARGS["personne-B"])){
        $personne_A = new Personne($ARGS["personne-A"]);
        $personne_B = new Personne($ARGS["personne-B"]);
        $mysqli->from_db($personne_A);
        $mysqli->from_db($personne_B);

        echo html_fusion($personne_A, $personne_B);
    }else{
        echo html_select_personnes();
    }

?>
