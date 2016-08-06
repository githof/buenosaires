<?php

    include_once(ROOT."src/database/Personne.php");
    include_once(ROOT."src/html_entities.php");


    $personne = new Personne($url_parsed["id"]);
    $result = $mysqli->from_db($personne);
    $personne->result_from_db($result);
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
    <div class="detail_div detail_div_horizontal">
        <div class="detail_div_title">
            ID: <?php echo $personne->id; ?>
        </div>
    </div>
    <div class="detail_div detail_div_horizontal">
        <div class="detail_div_title">
            PERIODE
        </div>
    </div>
    <div class="detail_div">
        <div class="detail_div_title">
            CONDITIONS
        </div>
        <?php echo html_conditions($personne->get_conditions()); ?>
    </div>
    <div class="detail_div">
        <div class="detail_div_title">
            RELATIONS
        </div>
        <?php echo html_personne_relations($personne); ?>
    </div>
</div>
<?php
    }
?>
