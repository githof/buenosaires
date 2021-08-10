<?php

include_once(ROOT."src/class/model/Personne.php");
include_once(ROOT."src/html_entities.php");

//  *** [rewrite_requete]
//  stocker l'id entrée dans l'url pour pouvoir le comparer avec $personne->id 
//  et arrêter la boucle from_db 
$post_id = $url_parsed["id"];

$personne = new Personne($url_parsed["id"]);

//  *** pour boutons d'actions 
function html_actions_personne() {
    global $personne, $access_pages; 

    $html = '<div class="detail_options">';

    if(can_access($access_pages["dissocier"])){ 
        $html .= '<a href="dissocier?personne-A='.$personne->id.'">' 
            . html_button('', 'btn btn-info btn-sm', '', 'Dissocier')
            . '</a>';
    }

    if(can_access($access_pages["supprimer"])){ 
        $html .= 
            html_button('', 'btn btn-danger btn-sm', 'id="personne-suppr-1"', 'Supprimer la personne') 
            . html_button('', 'btn btn-danger btn-sm', 'id="personne-suppr-2"', 'Vous êtes sûr ?')
            . html_button('', 'btn btn-danger btn-sm', 'id="personne-suppr-3"', 'Parce que vous allez vraiment le faire')
            . html_button('', 'btn btn-danger btn-sm', 'id="personne-suppr-4"', 'Dernière chance ?')
            . '<a class="btn btn-danger btn-sm" id="personne-suppr-5" href="supprimer/personne/'.$personne->id.'">Okay, okay</a>'; 
    } 

    $html .= '</div>';

    return $html;

}

//  *** pour page_title 
function html_personne_name() {
    global $personne, $page_title; 

    $page_title = "";
    foreach($personne->prenoms as $prenom)
        $page_title .= $prenom->to_string() . " ";

    foreach($personne->noms as $nom)
        $page_title .= $nom->to_string() . " ";

    return $page_title;
}

//  *** pour affichage sections 
function html_section($title, $id, $contents) {
    $html =
        '<section>
            <h4>'.$title.'</h4>
            <div id="'.$id.'">'
                . $contents . 
            '</div>
        </section>'; 
    return $html; 
}

//  *** affichage général 
function html_affichage_personne() {
    global $personne, $access_pages; 

    $result = $personne->from_db(TRUE); 

    if($result == NULL){
        $html = "Aucune personne enregistrée avec cet id";
    }else{

        html_personne_name(); 

        $html = html_actions_personne() . 
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
