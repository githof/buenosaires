<?php

include_once(ROOT."src/class/io/XMLExport.php");
include_once(ROOT."src/class/io/CSVExport.php");
//  *** Pour fonctions d'affichage :
include_once(ROOT."src/html_entities.php");
//  *** Pour fonctions d'export :
include_once(ROOT."src/utils.php");


//  *** onglets des tabs // 
function html_tab_titles(){
    return '<ul class="nav nav-tabs" role="tablist">'
                . html_tab_title('export#actes', 'active', 'Actes')
                . html_tab_title('export#personnes', '', 'Personnes')
                . html_tab_title('export#relations', '', 'Relations')
            . '</ul>';
}

//  OPTIONS  // 
function html_radio_export($option, $value, $label) {
    return '
        <label for="' . $option . '">' . $label . '</label>
        <input type="radio" id="' . $option . '" name="' . $option . '" value="' . $value . '">';
}

function html_form_group_export($contents) {
    return '
      <div class="form-group col-md-4">
        ' . "$contents" . '
      </div>
    ';
}

function html_export_actes() {
    return '<h4>Tous les actes</h4>';
}

//  *** rewrite-noms-export
//  par défaut : $attr = false, $no_accent = true 
function html_export_personnes() {
    $contents = '<h4>Toutes les personnes</h4>'
                . html_form_group_export(html_radio_export('attr', '1', 'Avec attributs'))
                . html_form_group_export(html_radio_export('no_accent', '0', 'Avec accents'));
    
    return $contents;
}


function html_export_relations() {
    $contents = '<h4>Toutes les relations</h4>
                
                <p>Section Export relations en travaux, les résultats ne seront pas systématiquement ceux que vous attendez. Merci de votre compréhension :)</p> 

                <p>Par défaut : export avec les noms, sans les dates, et dans les 2 sens. Cocher les cases pour changer ce comportement.<br> 
                Attention : les dates risquent de provoquer un dépassement du timout, ce problème n\'est pas encore réglé.</p>
                
                <div class="row">
                    <div class="col-md-12">'
                . html_form_group_export(html_radio_export('dates', 1, 'Avec les dates')) 
                . html_form_group_export(html_radio_export('deux_sens', 0, 'Dans 1 seul sens'))
                . '</div>
                <div class="col-md-12">'
                . html_form_group_export(html_radio_export('attr', '1', 'Avec attributs'))
                . html_form_group_export(html_radio_export('no_accent', '0', 'Avec accents'))
                . '</div>
                </div>'
                . html_hidden_type('names', TRUE);

    return $contents;
}

function html_form_export($objet, $data_export) { 
    $export = "html_export_$objet";
    
    //  export (export seul ne marche pas avec $ARGS cf Dropbox/buenosaires/todoM.txt)    
    return '<form action="export?export=true" method="POST">'  
            . $export()
            . html_submit('', 'Exporter') 
            . html_hidden_type('data_export', $data_export) . 
            '</form>';
}


//  *** tabs Actes / Personnes / Relations 

//  *** $objet à la place de $acte_or_personne // 
function html_tabpanel($class, $objet, $data_export) { 
    return '<div role="tabpanel" class="tab-pane '
        . $class . '" id="' . $objet . '">  
                <section>
                    <div>
                        ' . html_form_export($objet, $data_export) . '
                    </div>
                </section>
            </div>';
}

//  *** remplace html_section // 
function html_tab_contents() {
    return '<div class="tab-content">'
                . html_tabpanel('active', 'actes', 'all_actes')
                . html_tabpanel('', 'personnes', 'all_personnes')
                . html_tabpanel('', 'relations', 'all_relations')
            . '</div>';
}

function page_export() {
    if(isset($_POST["data_export"])){
        switch($_POST["data_export"]){
            case "all_actes":
                echo appel_export_actes('XMLExport', 'export_all');  //  export, '4968',
                break;
            case "all_personnes":
                $attr = (isset($_POST["attr"]) && $_POST["attr"] == '1') ? TRUE : FALSE;
                $no_accent = (isset($_POST["no_accent"]) && $_POST["no_accent"] == '0') ? FALSE : TRUE; 
                echo appel_export_personnes('CSVExport', 'export_personnes', $attr, $no_accent);
                break;
            case "all_relations":
                $names = isset($_POST["names"]) ? $_POST["names"] : TRUE;
                $dates = isset($_POST["dates"]) ? $_POST["dates"] : FALSE;
                $deux_sens = isset($_POST["deux_sens"]) ? $_POST["deux_sens"] : TRUE;
                $attr = (isset($_POST["attr"]) && $_POST["attr"] == '1') ? TRUE : FALSE;
                $no_accent = (isset($_POST["no_accent"]) && $_POST["no_accent"] == '0') ? FALSE : TRUE; 
                echo appel_export_relations('CSVExport', 'export_relations', $names, $dates, $deux_sens, $attr, $no_accent);    //   1, 50,
                    
            /*  break;
                *** mettre index:define(ROOT...) et $view + if... dans html_entities ou URLRewriter
                pour renvoyer (ici) vers 404 en default case ? 
            */
            // default:
            //  *** voir si on met ça ou autre chose
                // $view = ROOT."src/views/pages/404.php";
                // $page_title = "Page introuvable";
        }
    } else {
        echo html_tab_titles();
        echo html_tab_contents();
    }
}

echo page_export(); 


?>



