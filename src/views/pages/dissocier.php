<?php

include_once(ROOT."src/html_entities.php");

//  *** pas utilisée ? 
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
        // $source; $new; $item; $split;
        $source; $new; $obj; $split;
        if(startsWith($key, "condition")){
            $split = dissocier_split_input($key);
            $source = $conditions_source;
            $new = $conditions_new;
            // $item = new Condition($split["id"]);
            $obj = new Condition($split["id"]);
        }else if(startsWith($key, "relation")){
            $split = dissocier_split_input($key);
            $source = $relations_source;
            $new = $relations_new;
            // $item = new Relation($split["id"]);
            $obj = new Relation($split["id"]);
        }else
            continue;
        // $mysqli->from_db($item, TRUE);
        $obj->from_db($obj, TRUE);
        $id = "{$split["id"]}";
        switch($value){
            case "a":
                if(isset($source[$id]))
                    $source[$id]->actes[] = new Acte($split["acte"]);
                else{
                    // $item->actes[] = new Acte($split["acte"]);
                    $obj->actes[] = new Acte($split["acte"]);
                    // $source[$id] = $item;
                    $source[$id] = $obj;
                }
                break;
            case "b":
                // $item->id = NULL;
                $obj->id = NULL;
                if(isset($new[$id]))
                    $new[$id]->actes[] = new Acte($split["acte"]);
                else{
                    // $item->actes[] = new Acte($split["acte"]);
                    $obj->actes[] = new Acte($split["acte"]);
                    // $new[$id] = $item;
                    $new[$id] = $obj;
                }
                break;
            case "2":
                if(isset($source[$id]))
                    $source[$id]->actes[] = new Acte($split["acte"]);
                else{
                    // $item->actes[] = new Acte($split["acte"]);
                    $obj->actes[] = new Acte($split["acte"]);
                    // $source[$id] = $item;
                    $source[$id] = $obj;
                }

                if(startsWith($key, "condition"))
                    // $item = new Condition($id);
                    $obj = new Condition($id);
                else if(startsWith($key, "relation"))
                    // $item = new Relation($id);
                    $obj = new Relation($id);
                // $mysqli->from_db($item, TRUE);
                $obj->from_db($obj, TRUE);

                $item->id = NULL;
                if(isset($new[$id]))
                    $new[$id]->actes[] = new Acte($split["acte"]);
                else{
                    // $item->actes[] = new Acte($split["acte"]);
                    $obj->actes[] = new Acte($split["acte"]);
                    // $new[$id] = $item;
                    $new[$id] = $obj;
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
                while(list( , $node) = each($results)){ //  *** Deprecated: The each() function is deprecated. This message will be suppressed on further calls     
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
$relations_new) {
    global $mysqli, $log, $obj;

    $personne_new = new Personne();

    $personne_source->prenoms = $prenoms_source;
    $personne_source->noms = $noms_source;
    $personne_new->prenoms = $prenoms_new;
    $personne_new->noms = $noms_new;

    // $mysqli->into_db($personne_new, FALSE, TRUE); // marchera pas 
    $obj->into_db($personne_new, FALSE, TRUE);

    $personne_source->condition = [];
    $personne_new->relations = [];
    // $mysqli->into_db($personne_source);  //   pareil 
    $obj->into_db($personne_source);

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
        // $mysqli->into_db($condition);
        $obj->into_db($condition);
        foreach($condition->actes as $actes)
            $mysqli->into_db_acte_has_condition($acte, $condition);
    }

    foreach($relations_source as $relation){
        foreach($relation->actes as $acte)
            $mysqli->into_db_acte_has_relation($acte, $relation);
    }

    foreach($relations_new as $relation){
        if($relation->personne_source->id == $relation->personne_destination->id){ //   *** Notice: Trying to get property 'id' of non-object 
            $relation->personne_source = $personne_new;
            $relation->personne_destination = $personne_source;
        }else if($relation->personne_source->id == $personne_source->id)
            $relation->personne_source = $personne_new;
        else
            $relation->personne_destination = $personne_new;
        // $mysqli->into_db($relation, TRUE);
        $obj->into_db($relation, TRUE);
        foreach($relation->actes as $acte)
            $mysqli->into_db_acte_has_relation($acte, $relation);
    }

    dissocier_update_contenu_acte(
        $relations_new,
        $personne_source->id,
        $personne_new->id
    );
}

//  *** à mettre dans html_entities.php (elle sert aussi dans fusion.php) 
function html_select_personne(){
    ?>
    <section class='max-1'>
        <h4>Choisir une personne à dissocier</h4>
        <div>
            <input type='text' name='autocomplete' placeholder='Recherche parmi les personnes'>
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
        <div>Personne d'origine : $id_personne</div>
        <div>Nouvelle personne : automatiquement générée</div>
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
    // if($entity instanceof Condition){
    if(get_class($entity) == 'Condition'){
        $name = "condition-$entity->id";
        $html_entity = html_condition($entity, FALSE, FALSE);
    // }else if($entity instanceof Relation){ //' 
    }else if(get_class($entity) == 'Relation'){        
        $name = "relation-$entity->id";
        $html_entity = html_relation($entity, FALSE);
    }

    $html_actes = "";
    
    foreach($entity->actes as $acte){
        //  *** tests-dispatch-database 
        echo '<br>'.__METHOD__.' $acte : ';
        var_dump($acte);
        //  fin test 
        // $name_acte = $name."-".$acte->id;   //  *** Notice: Trying to get property 'id' of non-object 
        $name_acte = $name."-".$acte;       //<div>Acte $acte->id</div>".   //  *** Notice: Trying to get property 'id' of non-object 
        $html_actes .= "
            <div class='dissocier-radios'>
            
            <div>Acte $acte</div>
                
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
    $noms_B = parse_noms($ARGS["noms-B"]);  //  *** Notice: Undefined index: noms-B si nouvelle_perosnne-nom = '' 

    // $personne = new Personne($ARGS["id"]);
    $obj = new Personne($ARGS["id"]);
    // $mysqli->from_db($personne);
    $obj->from_db($obj);

    $res = dissocier_input_conditions_relations();
    $conditions_A = $res[0];
    $conditions_B = $res[1];
    $relations_A = $res[2];
    $relations_B = $res[3];

    dissocier(
        // $personne,
        $obj,
        $prenoms_A, $noms_A,
        $prenoms_B, $noms_B,
        $conditions_A, $conditions_B,
        $relations_A, $relations_B
    );

}else if(isset($ARGS["personne-A"])){
    // $personne = new Personne($ARGS["personne-A"]);
    $obj = new Personne($ARGS["personne-A"]);
    // $mysqli->from_db($personne);
    $obj->from_db($obj);
    // html_dissocier($personne);
    html_dissocier($obj);
}else{
    html_select_personne();
}

/**** */
// function change_id_personne_contenus($personne, $new_id)
function change_id_personne_contenus($obj, $new_id)
// nouvelle version de fusion_update_contenu_acte (plus haut)
/*
Peut-être que pour dissoc on a besoin exactement de la même fonction,
auquel cas il faudrait la mettre qq part genre utils.php:
// code...
break;
*/
{
    // $actes = recense_actes($personne);
    $actes = recense_actes($obj);
    foreach($actes as $acte) {
        // change_id_personne_contenu($acte, $personne->id, $new_id);
        change_id_personne_contenu($acte, $obj->id, $new_id);
    }
}

?>
