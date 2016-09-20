<?php

    include_once(ROOT."src/class/io/XMLExport.php");

    if(isset($ARGS["export"])){
        switch($ARGS["what"]){
            case "all_actes":
                $export = new XMLExport();
                $export->export_all();
                break;

        }
    }else{
?>
<section>
    <h4>Tous les actes</h4>
    <div>
        <a class="btn btn-info btn-sm" href="export?export=xml&what=all_actes">
            XML
        </a>
    </div>
</section>
<?php
    }
?>
