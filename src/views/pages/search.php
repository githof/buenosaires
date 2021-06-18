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
 * Si on choisit une letttre dans "actes", on n'a pas de prénoms sélectionnés dans "personnes". 
 */ 
  
function selected_noms($nom_ou_prenom){ // 'nom' 
    global $mysqli;
    $str = "";

    if(isset($_GET['letter_nom'])) {
        $letter = $_GET['letter_nom'];

        // echo '<br> $letter_nom : ';
        // var_dump($letter);

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
function selected_prenoms($nom_ou_prenom){  //  'prenom' '
  global $mysqli;
  $str = "";

  if(isset($_GET['letter_prenom'])) {
      $letter = $_GET['letter_prenom'];

      // echo '<br> $letter_prenom : ';
      // var_dump($letter);

      if($letter == 'pas de nom') {
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

//  $nom_ou_prenom = 'nom' 
function html_select_personnes_nom($title, $name_select, $nom_ou_prenom) {
    $html_option_list = selected_noms($nom_ou_prenom);

    return html_form_group('
        <label for="'.$name_select.'">'.$title.'</label>
        <select multiple="multiple" name="'.$name_select.'[]" id="'.$name_select.'">'
            .$html_option_list.
        '</select>
    ');
}
//  $nom_ou_prenom = 'prenom' 
function html_select_personnes_prenom($title, $name_select, $nom_ou_prenom) {
  $html_option_list = selected_prenoms($nom_ou_prenom);

  return html_form_group('
      <label for="'.$name_select.'">'.$title.'</label>
      <select multiple="multiple" name="'.$name_select.'[]" id="'.$name_select.'">'
          .$html_option_list.
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
        . html_select_personnes_nom(
            'Contenant les personnes avec pour nom de famille', 
            'acte_noms', 
            'nom'
        );
}

function html_search_personne()
{
    // $contents = '';

    // $contents .= html_select_personnes('Avec pour nom(s) de famille',
    //     'personne_noms', 'nom');
    // $contents .= html_select_personnes('Avec pour prénom(s)',
    //     'personne_prenoms', 'prenom');

    // return $contents;

    return html_select_personnes_nom('Avec pour nom(s) de famille',
            'personne_noms', 'nom') 
          . html_select_personnes_prenom('Avec pour prénom(s)',
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
        $contents .=  html_select_letters(
                          'Avec pour initiale du nom de famille', 
                          'letter', 'nom');
    } else {
        $contents .= html_select_letters(
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

    if(isset($_GET["letter_nom"])) {  //  , $_GET["letter_prenom"] 
        $contents = html_form_search($acte_or_personne); 
    } else {
        $contents = html_form_letters($acte_or_personne);  
    }

    return '<div role="tabpanel" class="tab-pane '
        . $class . '" id="' . $name . '">
                <section>
                    <div>' 
                        .// afficher_forms($name, $acte_or_personne) . 
                        //  html_form_search($acte_or_personne) . 
                        // html_form_letters($acte_or_personne) . 
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
