<?php

    include_once(ROOT."src/XMLActeReader.php");

    function error_message_receive_file($error_code){
        $log = "Erreur ($error_code) inconnue lors du téléchargement du fichier";
        $alert = "Erreur lors du téléchargement du fichier vers le serveur";

        switch($error_code){
            case 1:
                $message = "La taille du fichier téléchargé excède la valeur maximale supportée par le serveur";
                $log = $message;
                $alert = $message;
                break;
            case 2:
                $message = "La taille du dichier téléchargé excède la valeur MAX_FILE_SIZE, qui a été spécifiée dans le formulaire HTML";
                $log = $message;
                $alert = $message;
                break;
            case 3:
                $message = "Le fichier n'a été que partiellement téléchargé";
                $log = $message;
                $alert = $message;
                break;
            case 4:
                $message = "Aucun fichier n'a été téléchargé";
                $log = $message;
                $alert = $message;
                break;
            case 6:
                $log = "Un fichier temporaire est manquant";
                break;
            case 7:
                $log = "Echec de l'écriture du fichier sur le disque";
                break;
            case 8:
                $log = "Une extension PHP a arrêté l'envoi de fichier";
                break;
        }
        return [
            "log" => $log,
            "alert" => $alert
        ];
    }

    function receive_file(){
        global $alert, $log;

        if($_FILES["import_file"]["error"] > 0){
            $tabs = error_message_receive_file($_FILES["import_file"]["error"]);
            $log->e($tabs["log"]);
            $alert->error($tabs["alert"]);
            return NULL;
        }

        $infos = pathinfo($_FILES["import_file"]["name"]);
        if($infos["extension"] === "xml"){
            $uploadfile = TMP_DIRECTORY . "/" . basename($_FILES["import_file"]["name"]);
            if(@move_uploaded_file($_FILES["import_file"]["tmp_name"], $uploadfile) === FALSE){
                $log->e("Erreur fct move_uploaded_file({$_FILES['import_file']['tmp_name']}, $uploadfile)");
                $alert->error("Erreur interne du serveur lors du téléchargement du fichier");
                return NULL;
            }
            return $uploadfile;
        }else{
            $alert->error("Le fichier doit être au format XML");
            return NULL;
        }
    }

    function receive_text(){
        global $alert, $log;

        $sources = '<?xml version="1.0" encoding="UTF-8"?>';
        $sources = "<document><ACTES>\n".stripslashes($_POST['import_text'])."\n</ACTES></document>";

        $filename = TMP_DIRECTORY . "/new_actes.xml";
        $tmp_file = @fopen($filename, "w");
        if($tmp_file === FALSE){
            $log->e("Erreur de la fct fopen($filename, 'w')");
            $alert->error("Erreur interne du serveur lors de l'import");
            return NULL;
        }
        if(fwrite($tmp_file, $sources) === FALSE){
            $log->e("Erreur de la fct fwrite($tmp_file, $sources)");
            $alert->error("Erreur interne du serveur lors de l'import");
            return NULL;
        }
        return $filename;
    }

    function all_sources_available(){
        global $mysqli, $alert;

        $rep = $mysqli->select("source", ["*"], "");
        if($rep === FALSE){
            $alert->error("Impossible de récupérer les sources");
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
        $source_id;

        if($_POST["form_type"] === "file"){
            $filename = receive_file();
            $only_new = isset($_POST["import_file_only_new"]);
            $source_id = $_POST["import_file_source"];
        }else if($_POST["form_type"] === "text"){
            $filename = receive_text();
            $only_new = isset($_POST["import_text_only_new"]);
            $source_id = $_POST["import_text_source"];
        }

        if($filename != NULL){
            $reader = new XMLActeReader($source_id);
            $reader->use_xml_file($filename);
            $reader->read_actes($only_new);
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
