<?php

$type = $url_parsed["type"];
$id = $url_parsed["id"];

$html = "Impossible de réaliser la suppression";
$page_title = "Suppression de l'acte $id";

//  *** test-personne-suppr 

if($type == "acte"){
    $acte = new Acte($id);
    // if($mysqli->from_db($acte) == NULL)
    if($acte->from_db($acte) == NULL)
        $html = "L'acte n'existe pas";
    // $mysqli->delete_acte($acte);
    $acte->remove_from_db();
    $html = "Suppression de l'acte $id réalisée avec succès";
} elseif($type == "personne") {
    $personne = new Personne($id);
    if($personne->from_db($personne) == NULL)
        $html = "La personne n'existe pas";
    $personne->remove_from_db();
    $html = "Suppression de la personne $id réalisée avec succès";
}

?>
<section>
    <h4>
        <?php echo $html; ?>
    </h4>
</section>
