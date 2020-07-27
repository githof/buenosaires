<?php

    include_once(ROOT."src/class/io/XMLActeReader.php");
    include_once(ROOT."src/utils.php");

    function all_sources_available(){
        global $mysqli, $alert;

        $rep = $mysqli->select("source", ["*"], "");
        if($rep === FALSE){
            $alert->error("Impossible de récupérer les sources");
            return;
        }

        if($rep->num_rows > 0){
            while($row = $rep->fetch_assoc()){
                echo "<option value='{$row["id"]}'>{$row["valeur"]}</option>";
            }
        }
    }


    if(isset($_POST["form_type"])){
        $filename;
        $only_new;
        $source_id;

        if($_POST["form_type"] === "file"){
            $filename = receive_file("import_file");
            $only_new = isset($_POST["import_file_only_new"]);
            $source_id = $_POST["import_file_source"];
        }else if($_POST["form_type"] === "text"){
            $filename = receive_text($_POST['import_text']);
	    // le texte est copié dans un fichier temporaire
            $only_new = isset($_POST["import_text_only_new"]);
            $source_id = $_POST["import_text_source"];
        }

	/*
	  dans les instructions ci-dessous, le traitement des noeuds xml
	  des actes sera fait dans la fonction
	  XMLActeReader->read_acte_node
	  (via l'appel $reader->read_actes)
	 */

        if($filename != NULL){
            chmod($filename, 0776);
            $reader = new XMLActeReader($source_id);
            $reader->use_xml_file($filename);
            $reader->read_actes($only_new);
            unlink($filename);
        }
    }

?>

<section>
    <h4>
        Avec un fichier
    </h4>
    <div>
        <form method="post" enctype="multipart/form-data" action="" class="import-form">
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
                <button class="import-submit btn btn-primary">Envoyer</button>
            </div>
            <input type="hidden" name="form_type" value="file">
        </form>
    </div>
</section>
<section>
    <h4>
        En le(s) copiant ici
    </h4>
    <div>
        <form method="post" action="" class="import-form">
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
                <button class="import-submit btn btn-primary">Envoyer</button>
            </div>
            <input type="hidden" name="form_type" value="text">
        </form>
    </div>
</section>
