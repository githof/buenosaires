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


function appel_export($class, $method, $params) {
    return $class::$method($params);
}

/*
  TODO :
  passer en POST pour pouvoir ajouter des options en checkbox
  (par ex. pour l'export des relations : avec ou sans les noms, avec
  ou sans la date)
  Voir comment c'est fait dans import avec import_file_only_new par ex.
 */
if(isset($ARGS["export"])){
    switch($ARGS["what"]){
        case "all_actes":
            if($ARGS["export"] == "xml"){
                //  ***  test export static 
                // $export = new XMLExport();
            /* on a vraiment besoin de ces new ?
                Les fonctions pourraient être statiques, c'est
                juste un espace de nom dont on a besoin
            */
                // XMLExport::export_all();

                echo appel_export('XMLExport', 'export_all', '');
            }
            break;
        case "all_personnes":
            if($ARGS["export"] == "csv"){
                //  *** test export 
                // $export = new CSVExport();
                // $export->export_personnes();
                // CSVExport::export_personnes();

                echo appel_export('CSVExport', 'export_personnes', '');
            }
            break;
        case "all_relations":
            if($ARGS["export"] == "csv"){
                // $export = new CSVExport();
                // $export->export_relations(TRUE, TRUE);
                // CSVExport::export_relations(TRUE, TRUE);

                echo appel_export('CSVExport', 'export_relations', 'FALSE, FALSE');
            }
            break;
    }
}else{

    //  *** appel fonctions
    echo html_section('Tous les actes', 'export?export=xml&what=all_actes', 'XML');
    echo html_section('Toutes les personnes', 'export?export=csv&what=all_personnes', 'CSV');
    echo html_section('Toutes les relations', 'export?export=csv&what=all_relations', 'CSV');

}
?>


