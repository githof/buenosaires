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

function html_form_group($contents)
{
  return '
    <div class="form-group">
      '."$contents".'
    </div>
  ';
}

function html_import_file()
{
  return html_form_group('
    <label for="import_file">Fichier</label>
    <input type="file" id="import_file" name="import_file">
  ');
}

function html_import_text()
{
  return html_form_group('
    <textarea class="form-control" rows="6" name="import_text">
    </textarea>
  ');
}

function html_check_ignore($file_or_text)
{
  $attr = 'import_'.$file_or_text.'_only_new';
  $input = '<input type="checkbox" id="'.$attr.'" name="'.$attr.'"/>';
  $label = '<label for="'.$attr.'">Ignorer les actes déjà balisés</label>';
  return html_form_group("$input\n  $label");
}

function html_submit()
{
  $button = '<button class="import-submit btn btn-primary">Envoyer</button>';
  return html_form_group($button);
}

function html_hidden_type($file_or_text)
{
  return '
    <input type="hidden" name="form_type" value="'
      . $file_or_text
      . '" />';
}

if(isset($_POST["form_type"])){
    $filename;
    $only_new;
    $source_id;

    $file_or_text = $_POST["form_type"]; // 'file' or 'text' :)
    $receive_method = "receive_$file_or_text";
    $str_import = 'import_'.$file_or_text;
    $filename = $receive_method($str_import);
    // NB : pour text, le texte est copié dans un fichier temporaire
    $only_new = isset($_POST[$str_import.'_only_new']);
    $source_id = $_POST[$str_import.'_source'];

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
            <?php
              echo html_import_file();
              echo html_check_ignore('file');
              echo html_submit();
              echo html_hidden_type('file');
              ?>
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
            <?php
              echo html_import_text();
              echo html_check_ignore('text');
              echo html_submit();
              echo html_hidden_type('text');
              ?>
        </form>
    </div>
</section>
