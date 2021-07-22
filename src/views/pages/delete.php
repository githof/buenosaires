<?php

$type = $url_parsed["type"];
$id = $url_parsed["id"];

$html = "Impossible de réaliser la suppression";
$page_title = "Suppression de l'acte $id";

if($type == "acte"){
    $acte = new Acte($id);
    if($acte->from_db() == NULL)
        $html = "L'acte n'existe pas";
    else
    {
        $acte->remove_from_db();
        $html = "Suppression de l'acte $id réalisée avec succès";
    }
}

?>
<section>
    <h4>
        <?php echo $html; ?>
    </h4>
</section>
