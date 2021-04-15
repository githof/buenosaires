<?php

include_once(ROOT."src/class/io/XMLExport.php");
include_once(ROOT."src/class/io/CSVExport.php");
//  *** Pour fonctions d'affichage :
include_once(ROOT."src/html_entities.php");
//  *** Pour fonction d'export :
include_once(ROOT."src/utils.php");

/*
    TODO :
    passer en POST pour pouvoir ajouter des options en checkbox
    (par ex. pour l'export des relations : avec ou sans les noms, avec
    ou sans la date)
    Voir comment c'est fait dans import avec import_file_only_new par ex.
*/
//  *** Est-ce qu'on met une option pour fractionner les exports ? (les 100 premiers, ou de XX à XX pour les id...) 
//  on verra comment on gère ça mais oui 

//  balise-a    //  
function html_section_titre($title, $href, $label) {
    return '
        <section>
            <h4>'. $title .'</h4>
            <div>
                <a class="btn btn-info btn-sm bold" href="'. $href .'">'
                    . $label .
                '</a>
            </div>
        </section>
    ';
}

function html_export_lien($href, $label) {
    return '<div>
                <a class="btn btn-info btn-sm bold" href="'. $href .'">'
                    . $label .
                '</a>
            </div>';
}

function page_export_lien() {
    global $ARGS;
    if(isset($ARGS["export"])){

        switch($ARGS["what"]){
            case "all_actes":
                // if($ARGS["export"] == "xml"){
                    echo appel_export_statique('XMLExport', 'export_all', '', '');
                // }
                break;
            case "all_personnes":
                // if($ARGS["export"] == "csv"){
                    echo appel_export_statique('CSVExport', 'export_personnes', '', '');
                // }
                break;
            case "all_relations":
                // if($ARGS["export"] == "csv"){
                    echo appel_export_statique('CSVExport', 'export_relations', TRUE, TRUE);    //   1, 50,
                // }
                // break;
            /*  *** mettre index:define(ROOT...)et $view + if... (à factoriser) dans html_entities ou URLRewriter
                pour renvoyer (ici) vers 404 en default case.
            */
            // default:
            //  *** voir si on met ça ou autre chose
                // $view = ROOT."src/views/pages/404.php";
                // $page_title = "Page introuvable";
        }
    }else{
        echo html_section_titre('Tous les actes', 'export?export=xml&what=all_actes', 'XML');
        echo html_section_titre('Toutes les personnes', 'export?export=csv&what=all_personnes', 'CSV');
        echo html_section_titre('Toutes les relations', 'export?export=csv&what=all_relations', 'CSV');
    }
}
//  fin balise-a  //    


//  fonction d'appel aux méthodes d'export  //  dans utils.php  // 
// function appel_export_statique($class, $method, $start, $end, $names, $dates) {   //    $actes_id   //  pour choix actes à exp. 
//     return $class::$method($start, $end, $names, $dates);      // $actes_id     //  pour choix actes à exp. 
// }


//  form sans options -- remplacé (voir plus bas)   //  
function html_option($data_export, $choice) {
    return '<option value="' . $data_export . '">' . $choice . '</option>';
}
function html_select_export($label) {
    return '<label for="data_export">' . $label . '</label>
            <select class="form-control" name="data_export" id="data_export">'
                . html_option('all_actes', 'Actes') 
                . html_option('all_personnes', 'Personnes')
                . html_option('all_relations', 'Relations') . 
            '</select>';
}
//  fin form sans options    //  

//  *** onglets des tabs // 
function html_tab_titles(){

    return '<ul class="nav nav-tabs" role="tablist">'
                . html_tab_title('export#actes', 'active', 'Actes')
                . html_tab_title('export#personnes', '', 'Personnes')
                . html_tab_title('export#relations', '', 'Relations')
            . '</ul>';

}

//  OPTIONS  // 
//  *** Mettre des <radio> pour chaque option sur le tab du form. 
//     <input type="radio" id="dates" name="dates" value="TRUE">';
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
    $contents = '';
    $contents .= html_radio_export('', '', 'Tous les actes');

    return $contents;
}

function html_export_personnes() {
    $contents = '';
    $contents .= html_radio_export('', '', 'Toutes les personnes');

    return $contents;
}


function html_export_relations() {
    $contents = '<div class="row">';
    $contents .= html_form_group_export(html_radio_export('dates', TRUE, 'Avec les dates')) 
                . html_form_group_export(html_radio_export('names', TRUE, 'Avec les noms')) 
                . html_form_group_export(html_radio_export('deux_sens', TRUE, 'Dans les 2 sens')) ;
                // . html_form_group_export(html_radio_export('dates', TRUE, 'Dans les 2 sens') . '<br>'
                //                                     . html_radio_export('dates', FALSE, 'Sens normal')) ;
    $contents .= '</div>';

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

//  *** test remplacé par html_form_export() // 
// function html_form_wrap($action, $method) { 
//     return '<form  action="' . $action . '" method="' . $method . '">'
//             . html_form_group(html_select_export(''))   
//             . html_form_group(html_submit('', 'Exporter')) . 
//             '</form>';
// }
//  *** test : remplacé par html_tab_contents() // 
// function html_section($action, $method) {   
//     return '
//         <section>
//             <h4>'. 'Données à exporter' . '</h4>'    
//             . html_form_wrap($action, $method) .    
//         '</section>
//     ';   
// }


//  *** tabs Actes / Personnes / Relations 

//  *** $objet à la place de $acte_or_personne // 
function html_tabpanel($class, $objet, $data_export) {      //  id="'.$data_export.'" // 
    return '<div role="tabpanel" class="tab-pane '
        . $class . '" id="' . $objet . '">  
                <section>
                    <div>
                        ' . html_form_export($objet, $data_export) . '
                    </div>
                </section>
            </div>';
}

//  *** test remplace html_section // 
function html_tab_contents() {
    return '<div class="tab-content">'
                . html_tabpanel('active', 'actes', 'all_actes')
                . html_tabpanel('', 'personnes', 'all_personnes')
                . html_tabpanel('', 'relations', 'all_relations')
            . '</div>';
}

function page_export() {
    // if(isset($_REQUEST["data_export"])){     //  $_REQUEST stocke aussi l'URI 
    if(isset($_POST["data_export"])){

        // switch($_REQUEST["data_export"]){
        switch($_POST["data_export"]){
            case "all_actes":
                    echo appel_export_statique('XMLExport', 'export_all', '', '');  //  export, '4968',
                break;
            case "all_personnes":
                    echo appel_export_statique('CSVExport', 'export_personnes', '', '');
                break;
            case "all_relations":
                //  *** envoyer la valeur de $start et de $end 
                $names = isset($_POST["names"]) ? $_POST["names"] : FALSE;
                $dates = isset($_POST["dates"]) ? $_POST["dates"] : FALSE;
                $deux_sens = isset($_POST["deux_sens"]) ? $_POST["deux_sens"] : FALSE;
                    echo appel_export_statique('CSVExport', 'export_relations', '', '', $names, $dates, $deux_sens);    //   1, 50,
                    // echo '<br>'.__METHOD__;
                    // echo '<br>post : ';
                    // var_dump($_POST);
                // break;
            /*  *** mettre index:define(ROOT...)et $view + if... (à factoriser) dans html_entities ou URLRewriter
                pour renvoyer (ici) vers 404 en default case.
            */
            // default:
            //  *** voir si on met ça ou autre chose
                // $view = ROOT."src/views/pages/404.php";
                // $page_title = "Page introuvable";
        }
    } else {
        //  *** remplacé par html_tab_titles et html_tab_contents :
        // echo html_section();
        echo html_tab_titles();
        echo html_tab_contents();

        // echo '<br>'.__METHOD__;
        // echo '<br>post : ';
        // var_dump($_POST);
    }
}

// echo page_export_lien(); 
echo page_export(); 


// var_dump($_POST);

?>



