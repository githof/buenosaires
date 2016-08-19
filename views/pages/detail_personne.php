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
<section>
    <?php echo html_personne($personne, FALSE, FALSE); ?>
</section>
<section>
    <h4>ID</h4>
    <div>
        <?php echo $personne->id; ?>
    </div>
</section>
<section>
    <h4>PERIODE</h4>
</section>
<section>
    <h4>CONDITIONS</h4>
    <div>
        <?php echo html_conditions($personne->get_conditions(), FALSE); ?>
    </div>
</section>
<section>
    <h4>RELATIONS</h4>
    <div>
        <?php echo html_personne_relations($personne); ?>
    </div>
</section>
<?php
    }
?>
