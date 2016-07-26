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
        <?php echo html_periode(new Periode($personne->values["periode_id"])); ?>
    </div>
    <div class="detail_div">
        <div class="detail_div_title">
            CONDITIONS
        </div>
        <?php
            $conditions = $personne->get_conditions();
            foreach($conditions as $condition)
                echo html_personne_condition($condition);
         ?>
    </div>
    <div class="detail_div">
        <div class="detail_div_title">
            RELATIONS
        </div>
        <?php
            $relations = $personne->get_relations();
            foreach($relations as $relation)
                echo html_personne_relation($relation);
        ?>
    </div>
</div>
<?php
    }
?>
