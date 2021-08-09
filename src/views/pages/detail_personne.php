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

//  *** affichage général 
function html_affichage_personne() {
    global $personne, $access_pages; 

    $result = $personne->from_db(TRUE); 

    if($result == NULL){
        $html = "Aucune personne enregistrée avec cet id";
    }else{

        html_personne_name(); 

        $html = 
            '</div>
            <section>'
                . html_personne($personne, FALSE, FALSE) . 
            '</section>
            <section>
                <h4>ID</h4>
                <div>'
                    . $personne->id . 
                '</div>
            </section>
            <section>
                <h4>PERIODE</h4>'
                . html_personne_periode($personne->id) . 
            '</section>
            <section>
                <h4>CONDITIONS</h4>
                <div>'
                    . html_conditions($personne->conditions, FALSE) . 
                '</div>
            </section>
            <section>
                <h4>RELATIONS</h4>
                <div>'
                    . html_personne_relations($personne) . 
                '</div>
            </section>';

    }

    return $html; 

}

echo html_affichage_personne(); 

?>
