<?php

    include_once(ROOT."src/html_entities.php");


    function dissocier_get_conditions($conditions){
        global $mysqli, $ARGS;
        $tab = [];

        foreach($conditions as $condition){
            $cond = new Condition($condition);
        }
    }

    function dissocier_split_input($input){
        $split = explode("-", $input);
        return [
            "id" => $split[1],
            "acte" => $split[2]
        ];
    }

    function dissocier_input_conditions_relations(){
        global $ARGS, $mysqli;
        $conditions_source = [];
        $conditions_new = [];
        $relations_source = [];
        $relations_new = [];

        foreach($ARGS as $key => $value){
            $source; $new; $item; $split;
            if(startsWith($key, "condition")){
                $split = dissocier_split_input($key);
                $source = $conditions_source;
                $new = $conditions_new;
                $item = new Condition($split["id"]);
            }else if(startsWith($key, "relation")){
                $split = dissocier_split_input($key);
                $source = $relations_source;
                $new = $relations_new;
                $item = new Relation($split["id"]);
            }else
                continue;
            $mysqli->from_db($item, TRUE);
            $id = "{$split["id"]}";
            switch($value){
                case "a":
                    if(isset($source[$id]))
                        $source[$id]->actes[] = new Acte($split["acte"]);
                    else{
                        $item->actes[] = new Acte($split["acte"]);
                        $source[$id] = $item;
                    }
                    break;
                case "b":
                    $item->id = NULL;
                    if(isset($new[$id]))
                        $new[$id]->actes[] = new Acte($split["acte"]);
                    else{
                        $item->actes[] = new Acte($split["acte"]);
                        $new[$id] = $item;
                    }
                    break;
                case "2":
                    if(isset($source[$id]))
                        $source[$id]->actes[] = new Acte($split["acte"]);
                    else{
                        $item->actes[] = new Acte($split["acte"]);
                        $source[$id] = $item;
                    }

                    if(startsWith($key, "condition"))
                        $item = new Condition($id);
                    else if(startsWith($key, "relation"))
                        $item = new Relation($id);
                    $mysqli->from_db($item, TRUE);

                    $item->id = NULL;
                    if(isset($new[$id]))
                        $new[$id]->actes[] = new Acte($split["acte"]);
                    else{
                        $item->actes[] = new Acte($split["acte"]);
                        $new[$id] = $item;
                    }
                    break;
            }

            if(startsWith($key, "condition")){
                $conditions_source = $source;
                $conditions_new = $new;
            }else if(startsWith($key, "relation")){
                $relations_source = $source;
                $relations_new = $new;
            }
        }

        return [
            $conditions_source,
            $conditions_new,
            $relations_source,
            $relations_new
        ];
    }

    function dissocier_update_contenu_acte($relations, $id_source, $id_new){
        global $mysqli;

        $paths = [
            STATUT_EPOUSE => [
                ["epouse"],
                ["epoux"]],
            STATUT_PERE => [
                ["epoux/pere", "epouse/pere"],
                ["epoux", "epouse"]],
            STATUT_MERE => [
                ["epoux/mere", "epouse/mere"],
                ["epoux", "epouse"]],
            STATUT_TEMOIN => [
                ["temoins/temoin"],
                ["epoux", "epouse"]],
            STATUT_PARRAIN => [
                ["parrains/parrain"],
                ["epoux", "epouse"]]
        ];

        foreach($relations as $relation){
            foreach($relation->actes as $acte){
                $is_source = ($relation->personne_source->id == $id_new) ?
                    0 : 1;
                $results = $mysqli->select(
                    "acte_contenu",
                    ["contenu"],
                    "acte_id='$acte->id'"
                );
                $contenu = $results->fetch_assoc()["contenu"];
                $xml = new SimpleXMLElement($contenu);
                foreach($paths[$relation->statut_id][$is_source] as $path){
                    $results = $xml->xpath($path);
                    while(list( , $node) = each($results)){
                        $attr = $node->attributes();
                        if($attr["id"] == $id_source)
                            $attr["id"] = $id_new;
                    }
                }
                $contenu = $xml->asXML();

                $mysqli->update(
                    "acte_contenu",
                    ["contenu" => $contenu],
                    "acte_id='$acte->id'"
                );
            }
        }
    }

    function dissocier(
        $personne_source,
        $prenoms_source,
        $noms_source,
        $prenoms_new,
        $noms_new,
        $conditions_source,
        $conditions_new,
        $relations_source,
        $relations_new)
        {
        global $mysqli, $log;

        $personne_new = new Personne();

        $personne_source->prenoms = $prenoms_source;
        $personne_source->noms = $noms_source;
        $personne_new->prenoms = $prenoms_new;
        $personne_new->noms = $noms_new;

        $mysqli->into_db($personne_new, FALSE, TRUE);

        $personne_source->condition = [];
        $personne_new->relations = [];
        $mysqli->into_db($personne_source);

        $mysqli->delete(
            "acte_has_condition",
            "condition_id IN (
                SELECT id
                FROM `condition`
                WHERE personne_id = '$personne_source->id')"
            );
        $mysqli->delete(
            "acte_has_relation",
            "relation_id IN (
                SELECT id
                FROM `relation`
                WHERE pers_source_id = '$personne_source->id'
                OR pers_destination_id = '$personne_source->id')"
            );

        $tab = [];
        foreach($conditions_source as $key => $value)
            $tab[] = $key;

        $mysqli->delete(
            "condition",
            "id NOT IN (".array_to_string_with_separator($tab, ", ").")
            AND personne_id = '$personne_source->id'"
        );

        $tab = [];
        foreach($relations_source as $key => $value)
            $tab[] = $key;

        $mysqli->delete(
            "relation",
            "id NOT IN (".array_to_string_with_separator($tab, ", ").")
            AND (pers_source_id = '$personne_source->id'
            OR pers_destination_id = '$personne_source->id')"
        );

        foreach($conditions_source as $condition){
            foreach($condition->actes as $acte)
                $mysqli->into_db_acte_has_condition($acte, $condition);
        }

        foreach($conditions_new as $condition){
            $condition->personne = $personne_new;
            $mysqli->into_db($condition);
            foreach($condition->actes as $actes)
                $mysqli->into_db_acte_has_condition($acte, $condition);
        }

        foreach($relations_source as $relation){
            foreach($relation->actes as $acte)
                $mysqli->into_db_acte_has_relation($acte, $relation);
        }

        foreach($relations_new as $relation){
            if($relation->personne_source->id == $relation->personne_destination->id){
                $relation->personne_source = $personne_new;
                $relation->personne_destination = $personne_source;
            }else if($relation->personne_source->id == $personne_source->id)
                $relation->personne_source = $personne_new;
            else
                $relation->personne_destination = $personne_new;
            $mysqli->into_db($relation, TRUE);
            foreach($relation->actes as $acte)
                $mysqli->into_db_acte_has_relation($acte, $relation);
        }

        dissocier_update_contenu_acte(
            $relations_new,
            $personne_source->id,
            $personne_new->id
        );
    }


    function html_select_personne(){
        ?>
        <section class='max-1'>
            <h4>Choisir une personne à dissocier</h4>
            <div>
                <input type='text' name='autocomplete' placeholder='Recherche parmis les personnes'>
                <span class='autocomplete-search'>recherche en cours ...</span>
            </div>
            <div id='auto-complete-results'>
            </div>
            <form id='form-dissocier-select-personnes' method='get'>
                <div></div>
                <input type='submit' value='Dissocier'>
            </form>
        </section>
        <?php
    }

    function html_dissocier_ids($id_personne){
        return "
            <input type='hidden' name='id' value='$id_personne'>
            <div>Personne d'origine: $id_personne</div>
            <div>Nouvelle personne: automatiquement généré</div>
        ";
    }

    function html_dissocier_noms($noms){
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

    function html_dissocier_prenoms($prenoms){
        $html = "";
        foreach($prenoms as $prenom){
            $html .=
                "<div id='prenom-$prenom->id' class='prenom'>"
                .$prenom->to_string().
                "</div>";
        }
        return $html;
    }

    function html_form_per_acte($entity, $personne_id){
        $name = "";
        $html_entity = "";
        if($entity instanceof Condition){
            $name = "condition-$entity->id";
            $html_entity = html_condition($entity, FALSE, FALSE);
        }else if($entity instanceof Relation){
            $name = "relation-$entity->id";
            $html_entity = html_relation($entity, FALSE);
        }

        $html_actes = "";
        foreach($entity->actes as $acte){
            $name_acte = $name."-".$acte->id;
            $html_actes .= "
                    <div class='dissocier-radios'>
                        <div>Acte $acte->id</div>
                        <div>
                            <input type='radio' id='$name_acte-A' name='$name_acte' value='a' checked>
                            <label for='$name_acte-A'>$personne_id</label>
                        </div>
                        <div>
                            <input type='radio' id='$name_acte-B' name='$name_acte' value='b'>
                            <label for='$name_acte-B'>Nouveau</label>
                        </div>
                        <div>
                            <input type='radio' id='$name_acte-2' name='$name_acte'
                            value='2'>
                            <label for='$name_acte-2'>Les deux</label>
                        </div>
                    </div>
                ";
        }

        return "
            <div class='flex-horizontal'>
                $html_actes
                $html_entity
            </div>
        ";
    }

    function html_dissocier_conditions($conditions, $personne_id){
        $html = "";
        foreach($conditions as $condition)
            $html .= html_form_per_acte($condition, $personne_id);
        return $html;
    }

    function html_dissocier_relations($relations, $personne_id){
        $html = "";
        foreach($relations as $relation)
            $html .= html_form_per_acte($relation, $personne_id);
        return $html;
    }

    function html_dissocier($personne){
        $html_ids = html_dissocier_ids($personne->id);
        $html_prenoms = html_dissocier_prenoms($personne->prenoms);
        $html_noms = html_dissocier_noms($personne->noms);
        $html_relations = html_dissocier_relations($personne->relations, $personne->id);
        $html_conditions = html_dissocier_conditions($personne->conditions, $personne->id);
        $input_noms = default_input_noms($personne->noms);
        $input_prenoms = default_input_prenoms($personne->prenoms);

        ?>
        <form id="dissocier-form">
            <input type="submit" class="btn btn-primary" id="dissocier-submit" value='Dissocier'>
            <section>
                <h4>ID</h4>
                <div class="dissocier-ids flex-vertical">
                    <?php echo $html_ids; ?>
                </div>
            </section>
            <section>
                <h4>Prenoms</h4>
                <div class="dissocier-prenoms flex-horizontal">
                    <?php echo $html_prenoms; ?>
                </div>
                <div>
                    <div class="help-block">Les prénoms séparés par une virgule</div>
                    <div>
                        <label for="dissocier-prenoms-A">Prenoms de la personne d'origine</label>
                        <input type="text" id="dissocier-prenoms-A" name="prenoms-A" placeholder="Ex: Maria, Josefa" value="<?php echo $input_prenoms; ?>">
                    </div>
                    <div>
                        <label for="dissocier-prenoms-B">Prenoms de la nouvelle personne</label>
                        <input type="text" id="dissocier-prenoms-B" name="prenoms-B" placeholder="Ex: Maria, Josefa" value="<?php echo $input_prenoms; ?>">
                    </div>
                </div>
            </section>
            <section>
                <h4>Noms</h4>
                <div class="dissocier-noms flex-horizontal">
                    <?php echo $html_noms; ?>
                </div>
                <div>
                    <div class="help-block">Les noms séparés par une virgule et leurs attributs en parenthèse si besoin</div>
                    <div>
                        <label for="dissocier-noms-A">Noms de la personne d'origine</label>
                        <input type="text" id="dissocier-noms-A" name="noms-A" placeholder="Ex: PERI, (de) BELGRANO" value="<?php echo $input_noms; ?>">
                    </div>
                    <div>
                        <label for="dissocier-noms-B">Noms de la nouvelle personne</label>
                        <input type="text" id="dissocier-noms-B" name="noms-B" placeholder="Ex: PERI, (de) BELGRANO" value="<?php echo $input_noms; ?>">
                    </div>
                </div>
            </section>
            <section>
                <h4>Condition</h4>
                <div class="dissocier-conditions flex-vertical">
                    <?php echo $html_conditions; ?>
                </div>
            </section>
            <section>
                <h4>Relations</h4>
                <div class="dissocier-relations flex-vertical">
                    <?php echo $html_relations; ?>
                </div>
            </section>
        </form>
    <?php
    }


    if(isset($ARGS["id"])){
        $prenoms_A = parse_prenoms($ARGS["prenoms-A"]);
        $prenoms_B = parse_prenoms($ARGS["prenoms-B"]);
        $noms_A = parse_noms($ARGS["noms-A"]);
        $noms_B = parse_noms($ARGS["noms-B"]);

        $personne = new Personne($ARGS["id"]);
        $mysqli->from_db($personne);

        $res = dissocier_input_conditions_relations();
        $conditions_A = $res[0];
        $conditions_B = $res[1];
        $relations_A = $res[2];
        $relations_B = $res[3];

        dissocier(
            $personne,
            $prenoms_A, $noms_A,
            $prenoms_B, $noms_B,
            $conditions_A, $conditions_B,
            $relations_A, $relations_B
        );

    }else if(isset($ARGS["personne-A"])){
        $personne = new Personne($ARGS["personne-A"]);
        $mysqli->from_db($personne);
        html_dissocier($personne);
    }else{
        html_select_personne();
    }

?>
