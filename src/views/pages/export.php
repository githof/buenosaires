<?php

include_once(ROOT."src/class/io/XMLExport.php");
include_once(ROOT."src/class/io/CSVExport.php");


function html_section($title, $href, $label) {
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

function appel_export($class, $method, $names, $dates) {
    return $class::$method($names, $dates);
}

function page_export() {
    global $ARGS;

    if(isset($ARGS["export"])){

        switch($ARGS["what"]){
            case "all_actes":
                if($ARGS["export"] == "xml"){
                    echo appel_export('XMLExport', 'export_all', '', '');
                }
                break;
            case "all_personnes":
                if($ARGS["export"] == "csv"){
                    echo appel_export('CSVExport', 'export_personnes', '', '');
                }
                break;
            case "all_relations":
                if($ARGS["export"] == "csv"){
                    echo appel_export('CSVExport', 'export_relations', TRUE, TRUE);
                }
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

        echo html_section('Tous les actes', 'export?export=xml&what=all_actes', 'XML');
        echo html_section('Toutes les personnes', 'export?export=csv&what=all_personnes', 'CSV');
        echo html_section('Toutes les relations', 'export?export=csv&what=all_relations', 'CSV');

    }
}

echo page_export(); 

?>


