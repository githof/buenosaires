<?php

    include_once(ROOT."src/database/Personne.php");
    include_once(ROOT."src/html_entities.php");


    $personne = new Personne($url_parsed["id"]);
    if(!$personne->is_in_db){
?>
<div>
    Aucune personne enregistrée avec cet id
</div>
<?php
    }else{
?>
<div class="detail_container">
    <div class="detail_div">
        <div class="detail_div_title">
            PRENOM(S)
        </div>
        <?php echo html_personne_prenoms($personne); ?>
    </div>
    <div class="detail_div">
        <div class="detail_div_title">
            NOM(S)
        </div>
        <?php echo html_personne_noms($personne); ?>
    </div>
    <div class="detail_div">
        <div class="detail_div_title">
            PERIODE
        </div>
        <?php echo html_periode(periode_memory($personne->values["periode_id"])); ?>
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
        <?php echo html_relations($personne->get_relations()); ?>
    </div>
</div>
<?php
    }
?>
