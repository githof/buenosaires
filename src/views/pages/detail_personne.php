<?php

include_once(ROOT."src/class/model/Personne.php");
include_once(ROOT."src/html_entities.php");

//  *** [rewrite_requete]
//  stocker l'id entrée dans l'url pour pouvoir le comparer avec $personne->id 
//  et arrêter la boucle from_db 
$post_id = $url_parsed["id"];

// $obj = new Personne($url_parsed["id"]);
$personne = new Personne($url_parsed["id"]);
$result = $personne->from_db(TRUE); 
if($result == NULL){
?>
<div>
    Aucune personne enregistrée avec cet id
</div>
<?php
}else{
    $name = "";
    foreach($personne->prenoms as $prenom)
    // foreach($obj->prenoms as $prenom)
        $name .= $prenom->to_string() . " ";

    foreach($personne->noms as $nom)
    // foreach($obj->noms as $nom)
        $name .= $nom->to_string() . " ";

    $page_title = "$name";
if(can_access($access_pages["dissocier"])){?>
<div class="detail_options">
    <a href="dissocier?personne-A=<?php echo $personne->id; ?>">
    <!-- <a href="dissocier?personne-A=<?php// echo $obj->id; ?>"> -->
        <button class="btn btn-info btn-sm">Dissocier</button>
    </a>
</div>
<?php } ?>
<section>
    <?php echo html_personne($personne, FALSE, FALSE); ?>
    <?php //    echo html_personne($obj, FALSE, FALSE); ?>
</section>
<section>
    <h4>ID</h4>
    <div>
        <?php echo $personne->id; ?>
        <?php // echo $obj->id; ?>
    </div>
</section>
<section>
    <h4>PERIODE</h4>
    <?php echo html_personne_periode($personne->id); 
    ?>
</section>
<section>
    <h4>CONDITIONS</h4>
    <div>
        <?php // *** contourné re-création new personne() dans html_entities::has_memory 
            echo html_conditions($personne->conditions, FALSE);
            // echo html_conditions($obj->conditions, FALSE);
            
        ?>
    </div>
</section>
<section>
    <h4>RELATIONS</h4>
    <div>
        <?php echo html_personne_relations($personne); ?><!-- Ne re-crée pas new personne() pour la même personne, mais pour l'autre de la relation --> 
        <?php // echo html_personne_relations($obj); ?>
    </div>
</section>
<?php
}
?>
