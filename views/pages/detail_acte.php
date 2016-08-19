<?php


    include_once(ROOT."src/database/Acte.php");
    include_once(ROOT."src/html_entities.php");


    $acte = new Acte($url_parsed["id"]);
    $result = $mysqli->from_db($acte, TRUE);
    if(!isset($result)){
?>
<div>
    Aucun acte enregistré avec cet id
<?php
    }else{
?>
<div class="detail_options">
    <a class="btn btn-danger btn-sm" href="supprimer/acte/<?php echo $acte->id; ?>">Supprimer l'acte</a>
</div>
<section>
    <h4>ID</h4>
    <div>
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
        <?php echo html_conditions($acte->get_conditions()); ?>
    </div>
</section>
<section>
    <h4>RELATIONS</h4>
    <div>
        <?php echo html_relations($acte->get_relations()); ?>
    </div>
</section>
<section>
    <h4>CONTENU</h4>
    <div>
        <?php echo html_acte_contenu($acte->get_contenu()); ?>
    </div>
</section>
<?php
    }
?>
