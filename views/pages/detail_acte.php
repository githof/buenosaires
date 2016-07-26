<?php


    include_once(ROOT."src/database/Acte.php");
    include_once(ROOT."src/html_entities.php");


    $acte = new Acte($url_parsed["id"]);
    if(!$acte->is_in_db){
?>
<div>
    Aucun acte enregistré avec cet id
<?php
    }else{
?>
<div class="detail_container">
    <div class="detail_div">
        <div class="detail_div_title">
            ID
        </div>
        <?php echo html_acte_small($acte); ?>
    </div>
    <div class="detail_div">
        <div class="detail_div_title">
            EPOUX
        </div>
        <?php if(isset($acte->values["epoux"]))
                echo html_personne_small(personne_memory($acte->values["epoux"]));
        ?>
    </div>
    <div class="detail_div">
        <div class="detail_div_title">
            EPOUSE
        </div>
        <?php if(isset($acte->values["epouse"]))
                echo html_personne_small(personne_memory($acte->values["epouse"]));
        ?>
    </div>
    <div class="detail_div">
        <div class="detail_div_title">
            CONDITIONS
        </div>
        <?php echo html_conditions($acte->get_conditions()); ?>
    </div>
    <div class="detail_div">
        <div class="detail_div_title">
            RELATIONS
        </div>
        <?php echo html_relations($acte->get_relations()); ?>
    </div>
</div>
<?php
    }
?>
