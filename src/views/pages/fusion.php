<?php

    include_once(ROOT."src/html_entities.php");


    function html_select_personnes(){
        return "
            <section>
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

    function html_fusion_ids($id_A, $id_B){
        return "
            <div>
                <input type='radio' name='id' id='pers-$id_A' value='$id_A' checked>
                <label for='pers-$id_A'>$id_A</label>
            </div>
            <div>
                <input type='radio' name='id' id='pers-$id_B' value='$id_B'>
                <label for='pers-$id_B'>$id_B</label>
            </div>";
    }

    function has_prenom($prenoms, $prenom){
        foreach($prenoms as $p){
            if($p->id == $prenom->id)
                return TRUE;
        }
        return FALSE;
    }

    function has_nom($noms, $nom){
        foreach($noms as $n){
            if($n->id == $nom->id)
                return TRUE;
        }
        return FALSE;
    }

    function default_input_prenoms($prenoms_A, $prenoms_B){
        $str = "";
        $start = TRUE;
        foreach($prenoms_A as $prenom){
            if($start)
                $start = FALSE;
            else
                $str .= ", ";
            $str .= $prenom->to_string();
        }
        foreach($prenoms_B as $prenom){
            if(has_prenom($prenoms_A, $prenom))
                continue;
            if($start)
                $start = FALSE;
            else
                $str .= ", ";
            $str .= $prenom->to_string();
        }
        return $str;
    }

    function default_input_noms($noms_A, $noms_B){
        $str = "";
        $start = TRUE;
        foreach($noms_A as $nom){
            if($start)
                $start = FALSE;
            else
                $str .= ", ";
            if(isset($nom->attribut))
                $str .= "($nom->attribut) ";
            $str .= $nom->nom;
        }
        foreach($noms_B as $nom){
            if(has_nom($noms_A, $nom))
                continue;
            if($start)
                $start = FALSE;
            else
                $str .= ", ";
            if(isset($nom->attribut))
                $str .= "($nom->attribut) ";
            $str .= $nom->nom;
        }
        return $str;
    }

    function html_fusion($personne_A, $personne_B){
        $html_noms = html_fusion_noms($personne_A->noms)
            . html_fusion_noms($personne_B->noms);
        $html_prenoms = html_fusion_prenoms($personne_A->prenoms)
            . html_fusion_prenoms($personne_B->prenoms);
        $html_conditions = html_fusion_conditions($personne_A->conditions)
            . html_fusion_conditions($personne_B->conditions);
        $html_relations = html_fusion_relations($personne_A->relations)
            . html_fusion_relations($personne_B->relations);
        $html_ids = html_fusion_ids($personne_A->id, $personne_B->id);
        $input_prenoms = default_input_prenoms($personne_A->prenoms, $personne_B->prenoms);
        $input_noms = default_input_noms($personne_A->noms, $personne_B->noms);

        ?>
        <div id="fusion-form">
            <button class="btn btn-primary" id="fusion-submit" disabled>Fusionner</button>
            <section>
                <h4>ID  <i>(Choisir l'ID à conserver)</i></h4>
                <div class="fusion-ids flex-horizontal">
                    <?php echo $html_ids; ?>
                </div>
            </section>
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
        </div>
        <?php
    }

    if(isset($ARGS["personne-A"], $ARGS["personne-B"])){
        $personne_A = new Personne($ARGS["personne-A"]);
        $personne_B = new Personne($ARGS["personne-B"]);
        $mysqli->from_db($personne_A);
        $mysqli->from_db($personne_B);

        echo html_fusion($personne_A, $personne_B);
    }else{
        echo html_select_personnes();
    }

?>
