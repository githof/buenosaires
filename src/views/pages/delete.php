<?php

$type = $url_parsed["type"];
$id = $url_parsed["id"];

$page_title = $type == "acte" ? 
    "Suppression de l'acte $id" : 
    "Suppression de la personne $id";

//  *** rewrite-noms-export 

function delete_type($type, $id) {

    if($type == "acte") {
        $acte = new Acte($id);
        if($acte->from_db() == NULL) 
            $html = "L'acte $id n'existe pas";
        else {
            $acte->remove_from_db();
            if($acte->from_db() == NULL) 
                $html = "Suppression de l'acte $id réalisée avec succès";
            else 
                $html = "L'acte $id n'a pas pu être supprimé";
        }
    } elseif($type == "personne") {
        $personne = new Personne($id);

        if($personne->from_db() == NULL) { 
            $html = "La personne $id n'existe pas";
        }
        else {
            $personne->from_db();
            $personne->remove_from_db(); 
            if($personne->from_db() == NULL) 
                $html = "Suppression de la personne $id réalisée avec succès";
            else 
                $html = "La personne $id n'a pas pu être supprimée";
        }
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

