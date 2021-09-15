<?php

include_once(ROOT."src/class/model/Personne.php");
include_once(ROOT."src/html_entities.php");

//  *** [rewrite_requete]
//  stocker l'id entrée dans l'url pour pouvoir le comparer avec $personne->id 
//  et arrêter la boucle from_db 
$post_id = $url_parsed["id"];

$personne = new Personne($url_parsed["id"]);

//  *** pour boutons d'actions 
function html_actions_personne($page) {
    global $personne, $access_pages; 

    if($page == 'dissocier' && can_access($access_pages["dissocier"])){ 
        $html = '<a href="dissocier?personne-A='.$personne->id.'">' 
            . html_button('', 'btn btn-info btn-sm', '', 'Dissocier')
            . '</a>';
    }

    if($page == 'supprimer' && can_access($access_pages["supprimer"])){ 
        $html = 
            html_button('', 'btn btn-danger btn-sm', 'id="personne-suppr-1"', 'Supprimer la personne') 
            . html_button('', 'btn btn-danger btn-sm', 'id="personne-suppr-2"', 'Vous êtes sûr ?')
            . html_button('', 'btn btn-danger btn-sm', 'id="personne-suppr-3"', 'Parce que vous allez vraiment le faire')
            . html_button('', 'btn btn-danger btn-sm', 'id="personne-suppr-4"', 'Dernière chance ?')
            . '<a class="btn btn-danger btn-sm" id="personne-suppr-5" href="supprimer/personne/'.$personne->id.'">Okay, okay</a>'; 
    } 

    return $html;

}

// //  *** pour aligner boutons d'actions (déplacé dans html_entities.php) 
// function html_div_actions($contents) {
//     return '<div class="detail_options">'
//         . $contents . 
//     '</div>';
// }

//  *** pour page_title 
function html_personne_name() {
    global $personne, $page_title; 

    /*  signature : public function to_string($no_accent) */
    $page_title = "";
    foreach($personne->prenoms as $prenom)
        $page_title .= $prenom->to_string(FALSE) . " ";

    /*  public function to_string($attr, $no_accent) */ 
    foreach($personne->noms as $nom)
        $page_title .= $nom->to_string(TRUE, FALSE) . " ";

    return $page_title;
}

// //  *** pour affichage sections (déplacé dans html_entities.php) 
// function html_section($title, $id, $contents) {
//     $html =
//         '<section>
//             <h4>'.$title.'</h4>
//             <div id="'.$id.'">'
//                 . $contents . 
//             '</div>
//         </section>'; 
//     return $html; 
// }

//  *** affichage général 
function html_affichage_personne() {
    global $personne, $access_pages; 

    /* signature : public function from_db(
        $update_obj = FALSE,
        $get_relations_conditions = TRUE, 
        $attr = TRUE,
        $no_accent = FALSE);
    */
    // $result = $personne->from_db(FALSE, TRUE, TRUE, FALSE); 
    $result = $personne->from_db(); 

    if($result == NULL){
        $html = "Aucune personne enregistrée avec cet id";
    }else{

        html_personne_name(); 

        $html = html_div_actions(html_actions_personne('dissocier') . 
                                html_actions_personne('supprimer')) . 
            '<section>'
                . html_personne($personne, FALSE, FALSE) . 
            '</section>'
            . html_section('ID', '', $personne->id) 
            . html_section('PERIODE', '', html_personne_periode($personne->id)) 
            . html_section('CONDITIONS', '', html_conditions($personne->conditions, FALSE)) 
            . html_section('RELATIONS', '', html_personne_relations($personne));
    }

    return $html; 

}

echo html_affichage_personne(); 

?>
