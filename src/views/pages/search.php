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

function html_select_personnes($label_personne, $name_personne, $nom_ou_prenom) {
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

function html_search_acte()
{
    $contents = '';

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

    foreach($date as $d) {
        $contents .= 
            html_form_date($d['label_date'], $d['name_date']);
    }
    $contents .= html_select_personnes(
        'Contenant les personnes avec pour nom de famille', 
        'acte_noms', 
        'nom'
    );

    return $contents;
}

function html_search_personne()
{
    $contents = '';

    $contents .= html_select_personnes('Avec pour nom(s) de famille',
        'personne_noms', 'nom');
    $contents .= html_select_personnes('Avec pour prénom(s)',
        'personne_prenoms', 'prenom');

    return $contents;
}

function html_form_search($acte_or_personne) {
    $search = "html_search_$acte_or_personne";

    return '<form method="get" action="resultat"> '
        . $search()
        . html_submit('', 'Rechercher')
        . html_hidden_type('type', $acte_or_personne)
        . '</form>';
}

//  onglets "Actes" et "Personnes"
function html_tab_title($href, $class, $label) {

    return '<li role="presentation" class="' . $class . '">
                <a href="recherche#' . $href . '" aria-controls="' . $href . '" role="tab" data-toggle="tab">' . $label . '</a>
            </li>';
                
}
function html_tab_titles(){

    return '<ul class="nav nav-tabs" role="tablist">'
                . html_tab_title('actes', 'active', 'Actes')
                . html_tab_title('personnes', '', 'Personnes')
            . '</ul>';

}

//  divs "Actes" et "Personnes"
function html_tabpanel($class, $name, $acte_or_personne) {
    /* Le deuxième div, après la section, ce serait bien qu'on puisse s'en passer (faut voir si y'a pas un truc en js qui le prend en compte)
    */
    return '<div role="tabpanel" class="tab-pane '
        . $class . '" id="' . $name . '">
                <section>
                    <div>
                        ' . html_form_search($acte_or_personne) . '
                    </div>
                </section>
            </div>';
}
function html_tab_contents() {

    return '<div class="tab-content">'
                . html_tabpanel('active', 'actes', 'acte')
                . html_tabpanel('', 'personnes', 'personne')
            . '</div>';
}


//  Affichage :
echo html_tab_titles();
echo html_tab_contents();

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
