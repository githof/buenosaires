<?php

    include_once(ROOT."src/database/Personne.php");
    include_once(ROOT."src/html_entities.php");


    $personne = new Personne($url_parsed["id"]);
    $result = $mysqli->from_db($personne, TRUE);
    if($result == NULL){
?>
<div>
    Aucune personne enregistrée avec cet id
</div>
<?php
    }else{
?>
<div class="detail_container">
    <div class="detail_div personne_title">
        <?php echo html_personne_full_name($personne); ?>
    </div>
    <div class="detail_div flex_horizontal">
        <div class="detail_div_title">
            ID
        </div>
        <div class="detail_div_contenu">
            <?php echo $personne->id; ?>
        </div>
    </div>
    <div class="detail_div flex_horizontal">
        <div class="detail_div_title">
            PERIODE
        </div>
        <div class="detail_div_contenu">
        </div>
    </div>
    <div class="detail_div flex_vertical">
        <div class="detail_div_title">
            CONDITIONS
        </div>
        <div class="detail_div_contenu">
            <?php echo html_conditions($personne->get_conditions()); ?>
        </div>
    </div>
    <div class="detail_div flex_vertical">
        <div class="detail_div_title">
            RELATIONS
        </div>
        <div class="detail_div_contenu">
            <?php echo html_personne_relations($personne); ?>
        </div>
    </div>
</div>
<?php
    }
?>
