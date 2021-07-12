<?php

$type = $url_parsed["type"];
$id = $url_parsed["id"];

// $html = "Impossible de réaliser la suppression";
$page_title = $type == "acte" ? "Suppression de l'acte $id" : "Suppression de la personne $id";

//  *** test-personne-suppr 

function delete_type($type, $id) {

    if($type == "acte") 
        $obj = new Acte($id);
    elseif($type == "personne") 
        $obj = new Personne($id);

    // if($mysqli->from_db($acte) == NULL)
    if($obj->from_db($obj) == NULL) {
        $html = $type == "personne" ? "La personne $id n'existe pas" : "L'acte $id n'existe pas";
    } else {
        $obj->remove_from_db();
        if($obj->from_db($obj) == NULL) 
            $html = $type == "acte" ? "Suppression de l'acte $id réalisée avec succès" : "Suppression de la personne $id réalisée avec succès";
        else 
            $html = $type == "acte" ? "L'acte $id n'a pas pu être supprimé" : "La personne $id n'a pas pu être supprimée";
    }

    $contents = "<section>
                <h4>
                    $html
                </h4>
            </section>";
    return $contents;

}    

echo delete_type($type, $id);

?>
<!-- <section>
    <h4>
        <?php  //   echo $html; ?>
    </h4>
</section> -->
