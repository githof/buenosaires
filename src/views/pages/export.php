<?php

include_once(ROOT."src/class/io/XMLExport.php");
include_once(ROOT."src/class/io/CSVExport.php");

/*
    TODO :
    passer en POST pour pouvoir ajouter des options en checkbox
    (par ex. pour l'export des relations : avec ou sans les noms, avec
    ou sans la date)
    Voir comment c'est fait dans import avec import_file_only_new par ex.
*/
//  *** Est-ce qu'on met une option pour fractionner les exports ? (les 100 premiers, ou de XX à XX pour les id...) 

//  form-balise-a 
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
    //  Voir comment factoriser ce morceau avant d'ajouter des options 
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
//  fin form-balise-a 


//  *** form GET 
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


function html_form_wrap($action, $method) { 
    return '<form  action="' . $action . '" method="' . $method . '">'
            . html_form_group(html_select_export(''))   
            . html_form_group(html_submit('', 'Exporter')) . 
            '</form>';
}

function html_section() {   
    return '
        <section>
            <h4>'. 'Données à exporter' . '</h4>'    
            . html_form_wrap('export?export=xml&what=all_actes', 'POST') .    
        '</section>
    ';   
}
//  test qui marche pas : ?export=xml&what=all_actes (exporte mais ne suit pas l'uri, 
//  alors qu'avec <a> il suit l'uri d'abord, exporte ensuite et raffiche le html en dernier)


function appel_export_statique($class, $method, $names, $dates) {   //   $start, $end, //   $actes_id,
    return $class::$method($names, $dates);     //  $start, $end, // $actes_id, $
}


function page_export() {
    if(isset($_REQUEST["data_export"])){

        switch($_REQUEST["data_export"]){
            case "all_actes":
                // if($ARGS["export"] == "xml"){
                    // XMLExport::export_all(); 
                    echo appel_export_statique('XMLExport', 'export_all', '', '');  //  export, '4968',
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

    }
    // else{

        echo html_section();
    // }
}


// echo page_export_lien(); 
echo page_export(); 




?>





