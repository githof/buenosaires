<?php

include_once(ROOT."src/html_entities.php");

//  *** ne sert pas ? 
// function all_acte_id(){
//     global $mysqli;
//     $str = "<option value='0'>Aucun</option>";

//     $result = $mysqli->select("acte", ["id"]);
//     if($result != FALSE && $result->num_rows > 0){
//         while($row = $result->fetch_assoc())
//             $str .= "<option value='{$row["id"]}'>{$row["id"]}</option>";
//     }
//     return $str;
// }


//  *** ex function all_noms() modifiée et divisée 
/*** Il faut que l'utilisateur choisisse bien l'onglet qu'il veut 
 * pour sélectionner la/les lettre/s 
 * Si on choisit une letttre dans "actes", on n'a pas de prénoms sélectionnés pour "personnes". 
 */ 
function selected($nom_ou_prenom){
    global $mysqli;
    $str = "";
    $name_select = "letter_$nom_ou_prenom";

    if(isset($_GET[$name_select])) {
        $letter = $_GET[$name_select];

        echo '<br>'. __METHOD__.' $letter : ';
        var_dump($letter);

        if(($letter == 'pas de nom') || (empty($letter))) {
            $result = $mysqli->query("
            SELECT id, no_accent
            FROM $nom_ou_prenom
            ORDER BY no_accent
            ");
        } else {
            //  lettre
            $result = $mysqli->query("
            SELECT id, no_accent
            FROM  $nom_ou_prenom
            WHERE $nom_ou_prenom LIKE '$letter%'
            ORDER BY no_accent
            ");
        }

        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $str .= "<option value='{$row["id"]}'>{$row["no_accent"]}</option>";
            }
        }
    }
    return $str;
} 

function html_input_date($label, $name){
    return html_form_group ('
        <label for="'.$name.'">'.$label.'</label>
        <input type="date" name="'.$name.'" id="'.$name.'">
    ');
}

function html_select_personnes($title, $name_select, $nom_ou_prenom) {

    return html_form_group('
        <label for="'.$name_select.'">'.$title.'</label>
        <select multiple="multiple" name="'.$name_select.'[]" id="'.$name_select.'">'
            .selected($nom_ou_prenom).
        '</select>
    ');
}

function html_tab_titles(){
  
  return '<ul class="nav nav-tabs" role="tablist">'
  . html_tab_title('recherche#actes', 'active', 'Actes')
  . html_tab_title('recherche#personnes', '', 'Personnes')
  . '</ul>';
}


//  *** tests [divide-search-form]

//  Recherche noms/prénoms à partir de la/des lettre/s  

function html_search_acte()
{
    return html_input_date('A partir de : ', 'acte_date_start')
        . html_input_date('Avant : ', 'acte_date_end')
        . html_select_personnes(
            'Contenant les personnes avec pour nom de famille', 
            'acte_noms', 
            'nom'
        );
}

function html_search_personne() {
    return html_select_personnes('Avec pour nom(s) de famille',
            'personne_noms', 'nom') 
          . html_select_personnes('Avec pour prénom(s)',
          'personne_prenoms', 'prenom');
}

function html_form_search($acte_or_personne) {
    $search = "html_search_$acte_or_personne";

    return '<form method="get" action="resultat"> '
        . $search()
        . html_submit('', 'Rechercher')
        . html_hidden_type('type', $acte_or_personne)
        . '</form>';
}


//  sélectionner la/les lettre/s 

function html_select_letters($title, $letter_select, $nom_ou_prenom) {

    $letter_select = 'letter_'.$nom_ou_prenom;
    $letters_base = array(
        '(', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 
          'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'pas de nom'
    );
    if($nom_ou_prenom == 'prenom') 
        $trash = array_shift($letters_base);
    
    $str = '';
    foreach($letters_base as $letter) {
        $str .= '<option value="'.$letter.'">'.$letter.'</option>';
    }
    return html_form_group('
        <label for="letters">' . $title . ' : &nbsp; </label>
        <select name="' . $letter_select . '" id="' . $letter_select . '">
            <option value="">Choisir</option>'
            . $str . 
        '</select>
    ');
}

function html_form_letters($acte_or_personne) {

    $contents = '';

    if($acte_or_personne == 'acte') {
        $contents .=  '<p>(Pour récupérer tous les noms, ne rien choisir.)</p>'
                        . html_select_letters(
                          'Avec pour initiale du nom de famille', 
                          'letter', 'nom');
    } else {
        $contents .= '<p>(Pour récupérer tous les noms/prénoms, ne rien choisir.)</p>'
                    . html_select_letters(
                      'Avec pour initiale d\un des noms de famille', 
                      'letter', 'nom')
                    . ' ou '
                    . html_select_letters(
                      'Avec pour initiale d\'un des prénoms', 
                      'letter', 'prenom');
    }

    return '<form method="GET" action="">'
                . $contents 
                . html_submit('', 'Sélectionner')
                . html_hidden_type('type', $acte_or_personne) . 
            '</form>';
}

//  divs "Actes" et "Personnes"
function html_tabpanel($class, $name, $acte_or_personne) {
  /* Le deuxième div, après la section, ce serait bien qu'on puisse s'en passer (faut voir si y'a pas un truc en js qui le prend en compte)
      ==> Il suffit de le retirer, c'est du Bootstrap ; mais ça rapproche les inputs du coin haut-gauche du from-group, c'est moins beau ;) 
  */

    if(isset($_GET["letter_nom"])) {  //  || $_GET["letter_prenom"] --> error isset with expression ??? 
        $contents = html_form_search($acte_or_personne); 
    } else {
        $contents = html_form_letters($acte_or_personne);  
    }

    return '<div role="tabpanel" class="tab-pane '
        . $class . '" id="' . $name . '">
                <section>
                    <div>' 
                        $contents . 
                    '</div>
                </section>
            </div>';
}

function html_tab_contents() {
    $contents = ''; 

  return '<div class="tab-content">'
              . html_tabpanel('active', 'actes', 'acte')
              . html_tabpanel('', 'personnes', 'personne') 
          . '</div>';
}

//  Affichage :
echo html_tab_titles();
echo html_tab_contents();

?>

