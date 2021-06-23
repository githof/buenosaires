<?php

    include_once(ROOT."src/class/model/Personne.php");
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
        $name = "";
        foreach($personne->prenoms as $prenom)
            $name .= $prenom->to_string() . " ";

        foreach($personne->noms as $nom)
            $name .= $nom->to_string() . " ";

        $page_title = "$name";
    if(can_access($access_pages["dissocier"])){?>
<div class="detail_options">
    <a href="dissocier?personne-A=<?php echo $personne->id; ?>">
        <button class="btn btn-info btn-sm">Dissocier</button>
    </a>
</div>
<?php } ?>
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
    <?php echo html_personne_periode($personne->id); ?>
</section>
<section>
    <h4>CONDITIONS</h4>
    <div>
        <?php echo html_conditions($personne->conditions, FALSE); ?>
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
