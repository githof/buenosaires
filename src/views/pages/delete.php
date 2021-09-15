<?php

$type = $url_parsed["type"];
$id = $url_parsed["id"];

// $html = "Impossible de réaliser la suppression";
$page_title = $type == "acte" ? "Suppression de l'acte $id" : "Suppression de la personne $id";

//  *** rewrite-noms-export 

function delete_type($type, $id) {

    if($type == "acte") {
        // $obj = new Acte($id);
        $acte = new Acte($id);
        if($acte->from_db() == NULL) 
        // if($acte->from_db(FALSE, TRUE, TRUE, FALSE) == NULL) 
            $html = "L'acte $id n'existe pas";
        else {
            $acte->remove_from_db();
            if($acte->from_db() == NULL) 
            // if($acte->from_db(FALSE, TRUE, TRUE, FALSE) == NULL) 
                $html = "Suppression de l'acte $id réalisée avec succès";
            else 
                $html = "L'acte $id n'a pas pu être supprimé";
        }
    } elseif($type == "personne") {
        // $obj = new Acte($id);
        $personne = new Personne($id);

        // //  *** test-personne-suppr 
        // echo '<br>'.__METHOD__.' $personne->from_db($personne) : '';
        // var_dump($personne);
        // //  fin test 

        if($personne->from_db() == NULL) { 
            // if($personne->from_db(FALSE, TRUE, TRUE, FALSE) == NULL) { 
        // if($personne == NULL) {
            // //  *** test-personne-suppr 
            // echo '<br>'.__METHOD__.' if($personne == NULL) : '';
            // var_dump($personne);
            // //  fin test 
            $html = "La personne $id n'existe pas";
        }
        else {
            $personne->from_db();
            // $personne->from_db(FALSE, TRUE, TRUE, FALSE);
            //  *** test-personne-suppr 
            // $personne->remove_from_db(FALSE) teste si la personne est présente dans un acte ou relations, conditions  
            // $personne->remove_from_db(TRUE);
            $personne->remove_from_db(); 
            if($personne->from_db() == NULL) 
            // if($personne->from_db(FALSE, TRUE, TRUE, FALSE) == NULL) 
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

