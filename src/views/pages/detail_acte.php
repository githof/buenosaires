<?php


    include_once(ROOT."src/class/model/Acte.php");
    include_once(ROOT."src/html_entities.php");
    include_once(ROOT."src/class/io/XMLExport.php");
    include_once(ROOT."src/utils.php");
    include_once(ROOT."src/class/io/XMLActeReader.php");

    if(can_access($access_pages["acte"]) &&
        isset($_POST["raw_xml"], $_POST["source_id"])){
        $only_new = FALSE;
        $source_id = $_POST["source_id"];

        $reader = new XMLActeReader($source_id);
        $reader->use_xml_text($_POST["raw_xml"]);
        $reader->read_actes($only_new);

        $alert->info("Acte mis à jour");
    }

    $page_title = "Acte {$url_parsed["id"]}";
    $acte = new Acte($url_parsed["id"]);
    $result = $mysqli->from_db($acte, TRUE);
    if(!isset($result)){
?>
<div>
    Aucun acte enregistré avec cet id
<?php
    }else{
        if(isset($ARGS["export"])){
            if($ARGS["export"] == "xml"){
                $export = new XMLExport([$acte->id]);
                $export->export();
            }else if($ARGS["export"] == "gdf"){

            }
        }else{
?>
<div class="detail_options">
<?php
            if(can_access($access_pages["export"])){ ?>
                <a class="btn btn-info btn-sm" href="acte/<?php echo $acte->id; ?>?export=xml">XML</a>
                <a class="btn btn-info btn-sm" href="acte/<?php echo $acte->id; ?>?export=gdf">GDF</a>
<?php       }
            if(can_access($access_pages["supprimer"])){ ?>
                <button class="btn btn-danger btn-sm" id="acte-suppr-1">Supprimer l'acte</button>
                <button class="btn btn-danger btn-sm" id="acte-suppr-2">Vous êtes sûr ?</button>
                <button class="btn btn-danger btn-sm" id="acte-suppr-3">Parce que vous allez vraiment le faire</button>
                <button class="btn btn-danger btn-sm" id="acte-suppr-4">Dernière chance ?</button>
                <a class="btn btn-danger btn-sm" id="acte-suppr-5" href="supprimer/acte/<?php echo $acte->id; ?>">Okay, okay</a>
<?php       }?>
</div>
<input id='acte_source_id' type="hidden" value="<?php echo $acte->source_id; ?>">
<section>
    <h4>ID</h4>
    <div id="acte-id">
        <?php echo $acte->id ?>
    </div>
</section>
<section>
    <h4>EPOUX</h4>
    <div>
        <?php if(isset($acte->epoux))
                echo html_personne(personne_memory($acte->epoux->id), TRUE, TRUE, FALSE);
        ?>
    </div>
</section>
<section>
    <h4>EPOUSE</h4>
    <div>
        <?php if(isset($acte->epouse))
                echo html_personne(personne_memory($acte->epouse->id), TRUE, TRUE, FALSE);
        ?>
    </div>
</section>
<section>
    <h4>DATE</h4>
    <div>
        <?php echo html_date($acte->date_start, $acte->date_end); ?>
    </div>
</section>
<section>
    <h4>CONDITIONS</h4>
    <div>
        <?php echo html_conditions($acte->conditions); ?>
    </div>
</section>
<section>
    <h4>RELATIONS</h4>
    <div>
        <?php echo html_relations($acte->relations); ?>
    </div>
</section>
<section>
    <h4>CONTENU BRUT</h4>
    <div>
        <?php echo $acte->get_contenu(); ?>
    </div>
</section>
<section>
    <h4>CONTENU BALISÉ
        <button class='btn-section-title' id='btn-copy-xml'>Copier XML</button>
        <?php if(can_access($access_pages["fusion"])){ ?>
            <button class='btn-section-title' id='btn-edit-xml'>Editer XML</button>
            <button class='btn-section-title' id='btn-cancel-edit-xml'>Annuler l'édition</button>
            <button class='btn-section-title' id='btn-save-edit-xml'>Sauvegarder les changements</button>
        <?php } ?>
    </h4>
    <div>
        <div class='acte-contenu xmlselect-edit'>
            <form method='post' id='form-raw-xml'>
                <input type='hidden' name='source_id' value='<?php echo $acte->source_id; ?>'>
                <textarea style='display: none;' id='raw-xml' name='raw_xml'>
                    <?php echo $acte->get_contenu(); ?>
                </textarea>
            </form>
        </div>
    </div>
</section>
<?php
        }
    }
?>
