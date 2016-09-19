<?php

    include_once(ROOT."src/html_entities.php");


    function dissocier_noms_prenoms($personne_source, $personne_new){
        global $mysqli, $ARGS;

        $personne_source->prenoms = parse_prenoms($ARGS["prenoms-A"]);
        $personne_source->noms = parse_noms($ARGS["noms-A"]);

        $personne_new->prenoms = parse_prenoms($ARGS["prenoms-B"]);
        $personne_new->noms = parse_noms($ARGS["noms-B"]);

        $mysqli->delete("prenom_personne", "personne_id='$personne_source->id'");
        $i = 1;
        foreach($personne_source->prenoms as $prenom){
            $mysqli->into_db($prenom);
            $mysqli->into_db_prenom_personne($personne_source, $prenom, $i);
            $i++;
        }

        $mysqli->delete("nom_personne", "personne_id='$personne_source->id'");
        $i = 1;
        foreach($personne_source->noms as $nom){
            $mysqli->into_db($nom);
            $mysqli->into_db_nom_personne($personne_source, $nom, $i);
            $i++;
        }
    }

    function dissocier_conditions_relations($personne_source, $personne_new){
        global $mysqli, $ARGS;

        foreach($ARGS as $key => $value){
            if(startsWith($key, "condition-")){
                $split = explode("-", $key);
                $condition_id = $split[1];
                if($value == "b"){
                    $mysqli->update("condition", ["personne_id" => "$personne_new->id"], "id='$condition_id'");
                }
            }else if(startsWith($key, "relation-")){
                $split = explode("-", $key);
                $relation_id = $split[1];
                if($value == "b"){
                    foreach($personne_source->relations as $relation){
                        if($relation->id == $relation_id){
                            $is_source = $relation->personne_source->id == $personne_source->id;
                            break;
                        }
                    }
                    $pers = "pers_destination_id";
                    if($is_source)
                        $pers = "pers_source_id";
                    $mysqli->update("relation", ["$pers" => "$personne_new->id"], "id='$relation_id'");
                }
            }
        }
    }

    function dissocier($id){
        global $mysqli;

        $personne_source = new Personne($id);
        $mysqli->from_db($personne_source);

        $personne_new = new Personne();

        dissocier_noms_prenoms($personne_source, $personne_new);

        $mysqli->into_db($personne_new);

        dissocier_conditions_relations($personne_source, $personne_new);
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
            <button class="btn btn-primary" id="dissocier-submit">Dissocier</button>
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

    // if(isset($ARGS["id"])){
    //     dissocier($ARGS["id"]);
    //     $alert->success("Dissociation réalisée avec succès");
    // }

    if(isset($ARGS["personne-A"])){
        $personne = new Personne($ARGS["personne-A"]);
        $mysqli->from_db($personne);
        html_dissocier($personne);
    }else{
        html_select_personne();
    }

?>
