<?php

    include_once(ROOT."src/class/io/XMLExport.php");
    include_once(ROOT."src/class/io/CSVExport.php");

    if(isset($ARGS["export"])){
        switch($ARGS["what"]){
            case "all_actes":
                if($ARGS["export"] == "xml"){
                    $export = new XMLExport();
                    $export->export_all();
                }
                break;
            case "all_personnes":
                if($ARGS["export"] == "csv"){
                    $export = new CSVExport();
                    $export->export_personnes();
                }
                break;
        }
    }else{
?>
<section>
    <h4>Tous les actes</h4>
    <div>
        <a class="btn btn-info btn-sm bold" href="export?export=xml&what=all_actes">
            XML
        </a>
    </div>
</section>
<section>
    <h4>Toutes les personnes</h4>
    <div>
        <a class="btn btn-info btn-sm bold" href="export?export=csv&what=all_personnes">
            CSV
        </a>
    </div>
</section>
<section>
    <h4>Toutes les relations</h4>
    <div>
        <a class="btn btn-info btn-sm bold" href="export?export=csv&what=all_relations">
            CSV
        </a>
    </div>
</section>
<?php
    }
?>
