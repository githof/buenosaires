<?php

include_once(ROOT."src/class/model/Acte.php");
include_once(ROOT."src/html_entities.php");
include_once(ROOT."src/class/io/XMLExport.php");
include_once(ROOT."src/utils.php");
include_once(ROOT."src/class/io/XMLActeReader.php");



if(can_access($access_pages["acte"]) &&
isset($_POST["raw_xml"], $_POST["source_id"])){
    $only_new = FALSE;
    $source_id = $_POST["source_id"];

    $reader = new XMLActeReader($source_id);
    $reader->use_xml_text($_POST["raw_xml"]);
    $reader->read_actes($only_new);

    $alert->info("Acte mis à jour");
}

//  *** [tests-has-memory]
//  stocker l'id entrée dans l'url pour pouvoir le comparer avec $acte->id 
//  et arrêter la boucle from_db si besoin 
$post_id = $url_parsed["id"];

$page_title = "Acte {$url_parsed["id"]}";
$acte = new Acte($url_parsed["id"]);    //  *** création objet new Acte() (pas de redondance ici) 

// $result = $acte->from_db(TRUE); 

//  *** pour boutons d'actions 
function html_actions_acte($page) {
    global $access_pages, $acte;

    if($page == "export" && can_access($access_pages['export'])){ 
        $html = '
        <a class="btn btn-info btn-sm" href="acte/'.$acte->id.'?export=xml">XML</a>
        <a class="btn btn-info btn-sm" href="acte/'.$acte->id.'?export=gdf">GDF</a>';
    }

    if($page == "supprimer" && can_access($access_pages['supprimer'])){ 
        $html = 
            html_button('', 'btn btn-danger btn-sm', 'id="acte-suppr-1"', 'Supprimer l\'acte') 
            . html_button('', 'btn btn-danger btn-sm', 'id="acte-suppr-2"', 'Vous êtes sûr ?')
            . html_button('', 'btn btn-danger btn-sm', 'id="acte-suppr-3"', 'Parce que vous allez vraiment le faire')
            . html_button('', 'btn btn-danger btn-sm', 'id="acte-suppr-4"', 'Dernière chance ?')
            . '<a class="btn btn-danger btn-sm" id="acte-suppr-5" href="supprimer/acte/'.$acte->id.'">Okay, okay</a>'; 
    } 

    if($page == 'fusion' &&can_access($access_pages['fusion'])){ 
        $html = 
        html_button('', 'btn-section-title', 'id="btn-edit-xml"', 'Editer XML') 
        . html_button('', 'btn-section-title', 'id="btn-cancel-edit-xml"', 'Annuler l\'édition') 
        . html_button('', 'btn-section-title', 'id="btn-save-edit-xml"', 'Sauvegarder les changements');
    } 
    
    return $html; 
}

//  *** pour aligner boutons d'actions 
function html_div_actions($contents) {
    return '<div class="detail_options">'
        . $contents . 
    '</div>';
}

//  *** pour affichage section contenu balisé 
function html_affichage_contenu_balise() {
    global $acte; 
    $html =
    '<section>
        <h4>CONTENU BALISÉ'
        . html_button('', 'btn-section-title', 'id="btn-copy-xml"', 'Copier XML') 
        // <button class="btn-section-title" id="btn-copy-xml">Copier XML</button>' 
            . html_actions_acte('fusion') . 
        '</h4>
        <div>
            <div class="acte-contenu xmlselect-edit">
                <form method="post" id="form-raw-xml">
                    <input type="hidden" name="source_id" value="'.$acte->source_id.'">
                    <textarea style="display: none;" id="raw-xml" name="raw_xml">'
                        . $acte->contenu . 
                    '</textarea>
                </form>
            </div>
        </div>
        </section>';
    

    return $html;
}

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

//  *** pour affichage général 
function html_affichage_acte() {
    global $acte;

    $result = $acte->from_db(TRUE); 

    if(!isset($result)){
        $html = 
            '<div>
                Aucun acte enregistré avec cet id
            </div>';
    }else{
        if(isset($ARGS["export"])){
            if($ARGS["export"] == "xml"){
                $export = new XMLExport([$acte->id]);
                $export->export();
            }else if($ARGS["export"] == "gdf"){

            }
        }else{
            echo html_div_actions(
                html_actions_acte("export") . 
                html_actions_acte("supprimer")
            );

            $html = 
            '<input id="acte_source_id" type="hidden" value="$acte->source_id">'
            . html_section('ID', 'acte-id', $acte->id) 
            . html_section('EPOUX', '', html_personne(personne_memory($acte->epoux->id), TRUE, TRUE, FALSE)) 
            . html_section('EPOUSE', '', html_personne(personne_memory($acte->epouse->id), TRUE, TRUE, FALSE)) 
            . html_section('DATE', '', html_date($acte->date_start, $acte->date_end)) 
            . html_section('CONDITIONS', '', html_conditions($acte->conditions)) 
            . html_section('RELATIONS', '', html_relations($acte->relations))
            . html_section('CONTENU BRUT', '', $acte->contenu)
            . html_affichage_contenu_balise();
        }
    }
    return $html; 
}



echo html_affichage_acte();

?>
