<?php

    include_once(ROOT."src/XMLDecoder.php");
    include_once(ROOT."src/database/Acte.php");

    function receive_file(){
        global $alert;

        $infos = pathinfo($_FILES["import_file"]["name"]);
        if($infos["extension"] === "xml"){
            $uploadfile = TMP_DIRECTORY . "/" . basename($_FILES["import_file"]["name"]);
            move_uploaded_file($_FILES["import_file"]["tmp_name"], $uploadfile);
	    // TODO : test erreur (la fonction renvoie FALSE)
            return $uploadfile;
        }else{
            $alert->add_error("Le fichier doit être au format XML");
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

    function add_actes($actesXML, $only_new, $id_source){
        global $alert;

        $success_nb = 0;
        $i = 0;
        $num;
        foreach($actesXML as $acteXML){
            $do_it = true;
	    $num = $acteXML->attributes()["num"];

            if($only_new){
                if(isset($num) && $num != FALSE)
                    $do_it = db_has_acte($num);
            }

            if(!isset($num)){
                $alert->e("L'acte en position $i n'a pas de num");
                continue;
            }

            if($do_it){
                $acte = new Acte($num);
                $acte->id_source = $id_source;
                $acte->set_xml($acteXML);
                if($acte->into_db())
                    $success_nb++;
                else
                    $alert->add_error("Erreur lors de l'ajout de l'acte en position $i");
            }

            $i++;
        }

        if($success_nb > 0)
            $alert->add_success("$success_nb acte(s) ajouté(s)");
    }

    function all_sources_available(){
        global $mysqli, $alert;

        $rep = $mysqli->select("source", ["*"]);
        if($rep === FALSE){
            $alert->e("Impossible de récupérer les sources");
            return;
        }

        if($rep->num_rows > 0){
            while($row = $rep->fetch_assoc()){
                echo "<option value='{$row["id"]}'>{$row["source"]}</option>";
            }
        }
    }



    if(isset($_POST["form_type"])){
        $filename;
        $only_new;
        $id_source;

        if($_POST["form_type"] === "file"){
            $filename = receive_file();
            $only_new = isset($_POST["import_file_only_new"]);
            $id_source = $_POST["import_file_source"];
        }else if($_POST["form_type"] === "text"){
            $filename = receive_text();
            $only_new = isset($_POST["import_text_only_new"]);
            $id_source = $_POST["import_text_source"];
        }

        $decoder = new XMLDecoder($filename);
        $actes = $decoder->get_actes();
        if($actes != FALSE){
            add_actes($actes, $only_new, $id_source);
        }else{
            $alert->add_error("Impossible de lire la source envoyée");
        }
    }

?>

<h3>
    Ajouter un ou des actes
</h3>
<div class="import_form">
    <h4>
        Avec un fichier
    </h4>
    <form method="post" enctype="multipart/form-data" action="">
        <div class="form-group">
            <label for="import_file_source">Source du/des actes(s) : </label>
            <select name="import_file_source" id="import_file_source">
                <?php all_sources_available(); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="import_file">Fichier</label>
            <input type="file" id="import_file" name="import_file">
        </div>
        <div class="form-group">
            <input type="checkbox" id="import_file_only_new" name="import_file_only_new">
            <label for="import_file_only_new">Ignorer les actes déjà balisés</label>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Envoyer">
        </div>
        <input type="hidden" name="form_type" value="file">
    </form>
</div>
<div class="import_form">
    <h4>
        En le(s) copiant ici
    </h4>
    <form method="post" action="">
        <div class="form-group">
            <label for="import_text_source">Source du/des actes(s) : </label>
            <select name="import_text_source" id="import_text_source">
                <?php all_sources_available(); ?>
            </select>
        </div>
        <div class="form-group">
            <textarea class="form-control" rows="6" name="import_text"></textarea>
        </div>
        <div class="form-group">
            <input type="checkbox" id="import_text_only_new" name="import_text_only_new">
            <label for="import_text_only_new">Ignorer les actes déjà balisés</label>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Envoyer">
        </div>
        <input type="hidden" name="form_type" value="text">
    </form>
</div>
