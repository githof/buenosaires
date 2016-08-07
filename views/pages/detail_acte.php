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
<div class="detail_container">
    <div class="detail_div flex_horizontal">
        <div class="detail_div_title">
            ID
        </div>
        <div class="detail_div_contenu">
            <?php echo $acte->id ?>
        </div>
    </div>
    <div class="detail_div flex_horizontal">
        <div class="detail_div_title">
            EPOUX
        </div>
        <div class="detail_div_contenu">
            <?php if(isset($acte->epoux))
                    echo html_personne_link(personne_memory($acte->epoux->id));
            ?>
        </div>
    </div>
    <div class="detail_div flex_horizontal">
        <div class="detail_div_title">
            EPOUSE
        </div>
        <div class="detail_div_contenu">
            <?php if(isset($acte->epouse))
                    echo html_personne_link(personne_memory($acte->epouse->id));
            ?>
        </div>
    </div>
    <div class="detail_div flex_horizontal">
        <div class="detail_div_title">
            DATE
        </div>
        <div class="detail_div_contenu">
            <?php echo $acte->date; ?>
        </div>
    </div>
    <div class="detail_div flex_vertical">
        <div class="detail_div_title">
            CONDITIONS
        </div>
        <div class="detail_div_contenu">
            <?php echo html_conditions($acte->get_conditions()); ?>
        </div>
    </div>
    <div class="detail_div flex_vertical">
        <div class="detail_div_title">
            RELATIONS
        </div>
        <div class="detail_div_contenu">
            <?php echo html_relations($acte->get_relations()); ?>
        </div>
    </div>
    <div class="detail_div flex_vertical">
        <div class="detail_div_title">
            CONTENU
        </div>
        <div class="detail_div_contenu">
            <?php echo html_acte_contenu($acte->get_contenu()); ?>
        </div>
    </div>
</div>
<?php
    }
?>
