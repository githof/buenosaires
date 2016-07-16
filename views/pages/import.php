<?php

    include_once("src/XMLDecoder.php");
    include_once("src/database/Acte.php");

    function receive_file(){
        $infos = pathinfo($_FILES["import_file"]["name"]);
        if($infos["extension"] === "xml"){
            $uploadfile = TMP_DIRECTORY . "/" . basename($_FILE["import_file"]["name"]);
            move_uploaded_file($_FILE["import_file"]["tmp_name"], $uploadfile);
            return $uploadfile;
        }else{

        }
    }

    function receive_text(){
        $sources = '<?xml version="1.0" encoding="UTF-8"?>';
        $sources = "<document><ACTES>\n".stripslashes($_POST['import_text'])."\n</ACTES></document>";

        $filename = TMP_DIRECTORY . "/new_actes.xml";
        $tmp_file = fopen($filename, "w");
        fwrite($tmp_file, $sources);
        return $filename;
    }

    function add_actes($actes, $only_new){
        foreach($actes as $acte){
            $do_it = true;

            if($only_new){
                $num = $acte->attributes()["num"];
                if(isset($num) && $num != FALSE)
                    $do_it = db_has_acte($num);
            }

            if($do_it){
                $obj = new Acte($acte);
                $obj->into_db();
            }
        }
    }



    if(isset($_POST["form_type"])){
        $filename;
        $only_new;

        if($_POST["form_type"] === "file"){
            $filename = receive_file();
            $only_new = isset($_POST["import_file_only_new"]);
        }else if($_POST["form_type"] === "text"){
            $filename = receive_text();
            $only_new = isset($_POST["import_text_only_new"]);
        }

        $decoder = new XMLDecoder($filename);
        $actes = $decoder->get_actes();
        if($actes != FALSE){
            add_actes($actes, $only_new);
        }else{

        }
    }

?>

<h1 class="page-header">
    Import
</h1>
<div>
    <h3>
        Ajouter un ou des actes
    </h3>
    <div>
        <h4>
            Avec un fichier
        </h4>
        <form method="post" enctype="multipart/form-data" action="">
            <div class="form-group">
                <label for="import_file">Fichier</label>
                <input type="file" id="import_file" name="import_file">
            </div>
            <div class="form-group">
                <input type="checkbox" id="import_file_only_new" checked="checked" name="import_file_only_new">
                <label for="import_file_only_new">Ignorer les actes déjà balisés</label>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Envoyer">
            </div>
            <input type="hidden" name="form_type" value="file">
        </form>
    </div>
    <div>
        <h4>
            En le(s) copiant ici
        </h4>
        <form method="post" action="">
            <div class="form-group">
                <textarea class="form-control" name="import_text"></textarea>
            </div>
            <div class="form-group">
                <input type="checkbox" id="import_text_only_new" checked="checked" name="import_text_only_new">
                <label for="import_text_only_new">Ignorer les actes déjà balisés</label>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Envoyer">
            </div>
            <input type="hidden" name="form_type" value="text">
        </form>
    </div>
</div>
