<?php

include_once(ROOT."src/html_entities.php");

function all_acte_id(){
    global $mysqli;
    $str = "<option value='0'>Aucun</option>";

    $result = $mysqli->select("acte", ["id"]);
    if($result != FALSE && $result->num_rows > 0){
        while($row = $result->fetch_assoc())
            $str .= "<option value='{$row["id"]}'>{$row["id"]}</option>";
    }
    return $str;
}

function all_noms(){
    global $mysqli;
    $str = "";

    $result = $mysqli->query("
        SELECT id, no_accent
        FROM nom
        ORDER BY no_accent
    ");
    if($result != FALSE && $result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $str .= "<option value='{$row["id"]}'>{$row["no_accent"]}</option>";
        }
    }
    return $str;
}

function all_prenoms(){
    global $mysqli;
    $str = "";

    $result = $mysqli->query("
        SELECT id, no_accent
        FROM prenom
        ORDER BY no_accent
    ");
    if($result != FALSE && $result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $str .= "<option value='{$row["id"]}'>{$row["no_accent"]}</option>";
        }
    }
    return $str;
}


function html_form_date($name_date, $label_date){
    return html_form_group ('
        <label for="'.$name_date.'">'.$label_date.'</label>
        <input type="date" name="'.$name_date.'" id="'.$name_date.'">
    ');
}

function html_form_personnes($label_personne, $name_personne, $nom_ou_prenom) {
    if($nom_ou_prenom == 'nom') 
        $nom_ou_prenom = all_noms();
    else 
        $nom_ou_prenom = all_prenoms();

    return html_form_group('
        <label for="'.$name_personne.'">'.$label_personne.'</label>
        <select multiple="multiple" name="'.$name_personne.'[]" id="'.$name_personne.'">'
            .$nom_ou_prenom.
        '</select>
    ');
}

function html_form_search($acte_or_personne) {
    $contents;

    if($acte_or_personne == 'acte') {
        $date = array(
            array(
                'label_date'=>'acte_date_start',
                'name_date'=>'A partir de'
            ),
            array(
                'label_date'=>'acte_date_end',
                'name_date'=>'Avant'
            )
        );
        //  params pour html_form_hidden() :
        $name = 'type';
        $value = 'acte';

        $contents = 
            html_form_date($date[0]['label_date'], $date[0]['name_date']).
            html_form_date($date[1]['label_date'], $date[1]['name_date']).
            html_form_personnes('Contenant les personnes avec pour nom de famille', 'acte_noms', 'nom');

    } else {
        $personne = array(
            array(
                'label_personne' => 'Avec pour nom(s) de famille',
                'name_personne' => 'personne_noms',
                'nom_ou_prenom' => 'nom'
            ),
            array(
                'label_personne' => 'Avec pour prenom(s)',
                'name_personne' => 'personne_prenoms',
                'nom_ou_prenom' => 'prenom'
            )
        );
        $name = 'type';
        $value = 'personne';
        foreach($personne as $pers){
            $contents .= html_form_personnes($pers['label_personne'], $pers['name_personne'], $pers['nom_ou_prenom']);
        }
        // $contents = 
            // html_form_personnes($personne[0]['label_personne'], $personne[0]['name_personne'], $personne[0]['nom_ou_prenom']);
            // html_form_personnes($personne[1]['label_personne'], $personne[1]['name_personne'], $personne[1]['nom_ou_prenom']);
    }

    $contents .= 
        html_submit('', 'Rechercher').
        html_hidden_type($name, $value);


    $html = '<div>
        <form method="get" action="resultat">'
            .$contents.
        '</form>
    </div>';

    return $html;
}


?>

<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
        <a href="recherche#actes" aria-controls="actes" role="tab" data-toggle="tab">Actes</a>
    </li>
    <li role="presentation">
        <a href="recherche#personnes" aria-controls="personnes" role="tab" data-toggle="tab">Personnes</a>
    </li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="actes">
        <section>
            <!-- <div>
                <form method="get" action="resultat"> -->
                    <?php 
                        echo html_form_search('acte');
                        // echo html_form_date('acte_date_start', 'A partir de');
                        // echo html_form_date('acte_date_end', 'Avant');
                        // echo html_form_personnes('Contenant les personnes avec pour nom de famille', 'acte_noms', 'nom');
                        // echo html_submit('', 'Rechercher');
                        // echo html_hidden_type('type', 'acte');
                    ?>
                    <!-- <div class="form-group">
                        <label for="acte_date_start">A partir de</label>
                        <input type="date" name="acte_date_start" id="acte_date_start"> --> 
                        <!--    Je n'ai pas mis ce <p>, le type date fournit le format
                        <p class="help-block">Format: AAAA-MM-JJ</p>
                    </div> -->
                    <!-- <div class="form-group">
                        <label for="acte_date_end">Avant</label>
                        <input type="date" name="acte_date_end" id="acte_date_end">
                        <p class="help-block">Format: AAAA-MM-JJ</p>
                    </div> -->
                    <!-- <div class="form-group">
                        <label for="acte_noms">Contenant les personnes avec pour nom de famille</label>
                        <select multiple="multiple" name="acte_noms[]" id="acte_noms">
                            <?php // echo all_noms(); ?>
                        </select>
                    </div> -->
                    <!-- <div class="form-group">
                        <input type="submit" value="Rechercher">
                    </div> -->
                    <!-- <input type="hidden" name="type" value="acte"> -->
                <!-- </form>
            </div> -->
        </section>
    </div>

    <div role="tabpanel" class="tab-pane" id="personnes">
        <section>
            <!-- <div>
                <form method="get" action="resultat"> -->
                    <!-- <div class="form-group">                   commenté d'origine 
                        <label for="personne_date_start">A partir de</label>
                        <input type="date" name="personne_date_start" id="personne_date_start">
                    </div>
                    <div class="form-group">                        commenté d'origine 
                        <label for="personne_date_end">Avant</label>
                        <input type="date" name="personne_date_end" id="personne_date_end">
                    </div> -->
                    <?php 
                        echo html_form_search('personne');
                        // echo html_form_personnes('Avec pour nom(s) de famille', 'personne_noms', 'nom');
                        // echo html_form_personnes('Avec pour prenom(s)', 'personne_prenoms', 'prenom');
                        // echo html_submit('', 'Rechercher');
                        // echo html_hidden_type('type', 'personne');
                    ?>
                    <!-- <div class="form-group">
                        <label for="personne_noms">Avec pour nom(s) de famille</label>
                        <select multiple="multiple" name="personne_noms[]" id="personne_noms">
                            <?php // echo all_noms(); ?>
                        </select>
                    </div> -->
                    <!-- <div class="form-group">
                        <label for="personne_prenoms">Avec pour prenom(s)</label>
                        <select multiple="multiple" name="personne_prenoms[]" id="personne_prenoms">
                            <?php // echo all_prenoms(); ?>
                        </select> -->
                    </div>
                    <!-- <div class="form-group">
                        <input type="submit" value="Rechercher">
                    </div> -->
                    <!-- <input type="hidden" name="type" value="personne"> -->
                <!-- </form>
            </div> -->
        </section>
    </div>
</div>
