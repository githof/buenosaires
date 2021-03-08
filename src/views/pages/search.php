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
                'name_date'=>'A partir de : '
            ),
            array(
                'label_date'=>'acte_date_end',
                'name_date'=>'Avant : '
            )
        );
        //  params pour html_form_hidden() :
        $name = 'type';
        $value = 'acte';

        foreach($date as $d) {
            $contents .= 
                html_form_date($d['label_date'], $d['name_date']);
        }
        $contents .= html_form_personnes(
            'Contenant les personnes avec pour nom de famille', 
            'acte_noms', 
            'nom'
        );

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
        //  params pour html_form_hidden() :
        $name = 'type';
        $value = 'personne';
        foreach($personne as $pers){
            $contents .= html_form_personnes($pers['label_personne'], $pers['name_personne'], $pers['nom_ou_prenom']);
        }
    }

    $contents .= 
        html_submit('', 'Rechercher').
        html_hidden_type($name, $value);

    return '<div>
        <form method="get" action="resultat">'
            .$contents.
        '</form>
    </div>';
}

//  onglets "Actes" et "Personnes"
function html_liste_li($href, $class, $label) {

    return '<li role="presentation" class="' . $class . '">
                <a href="recherche#' . $href . '" aria-controls="' . $href . '" role="tab" data-toggle="tab">' . $label . '</a>
            </li>';
                
}
function html_liste_ul(){

    return '<ul class="nav nav-tabs" role="tablist">'
                . html_liste_li('actes', 'active', 'Actes')
                . html_liste_li('personnes', '', 'Personnes')
            . '</ul>';

}

//  divs "Actes" et "Personnes"
function html_tabpanel($class, $name, $acte_or_personne) {

    return '<div role="tabpanel" class="tab-pane ' . $class . '" id="' . $name . '">
                <section>'
                        . html_form_search($acte_or_personne)
                . '</section>
            </div>';
}
function html_div_tabpanel() {

    return '<div class="tab-content">'
                . html_tabpanel('active', 'actes', 'acte')
                . html_tabpanel('', 'personnes', 'personne')
            . '</div>';
}


//  Affichage :
echo html_liste_ul();
echo html_div_tabpanel();

?>

<!-- <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
        <a href="recherche#actes" aria-controls="actes" role="tab" data-toggle="tab">Actes</a>
    </li>
    <li role="presentation">
        <a href="recherche#personnes" aria-controls="personnes" role="tab" data-toggle="tab">Personnes</a>
    </li>
</ul> -->

<!-- <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="actes">
        <section>
            <?php 
            //    echo html_form_search('acte');
            ?>
        </section>
    </div>

    <div role="tabpanel" class="tab-pane" id="personnes">
        <section>
            <?php 
            //    echo html_form_search('personne');
            ?>
        </section>
    </div>
</div> -->
