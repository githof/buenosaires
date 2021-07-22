<?php

$type = $url_parsed["type"];
$id = $url_parsed["id"];

// $html = "Impossible de réaliser la suppression";
$page_title = $type == "acte" ? "Suppression de l'acte $id" : "Suppression de la personne $id";

//  *** test-personne-suppr 
// echo '<br>'.__METHOD__.' $type : ';
// var_dump($type);
//  fin test 

function delete_type($type, $id) {

    if($type == "acte") {
        // $obj = new Acte($id);
        $acte = new Acte($id);
        if($acte->from_db($acte) == NULL) 
            $html = "L'acte $id n'existe pas";
        else {
            $acte->remove_from_db();
            if($acte->from_db($acte) == NULL) 
                $html = "Suppression de l'acte $id réalisée avec succès";
            else 
                $html = "L'acte $id n'a pas pu être supprimé";
        }
    } elseif($type == "personne") {
        // $obj = new Acte($id);
        $personne = new Personne($id);

        // //  *** test-personne-suppr 
        // echo '<br>'.__METHOD__.' $personne->from_db($personne) : ';
        // var_dump($personne);
        // //  fin test 

        // if($personne->from_db($personne) == NULL) 
        if($personne == NULL) {
            // //  *** test-personne-suppr 
            echo '<br>'.__METHOD__.' if($personne == NULL) : ';
            var_dump($personne);
            // //  fin test 
            $html = "La personne $id n'existe pas";
        }
        else {
            // //  *** test-personne-suppr 
            echo '<br>'.__METHOD__.' else($personne) : ';
            var_dump($personne);
            // //  fin test 
            $personne->remove_from_db(TRUE);
            if($personne->from_db($personne) == NULL) 
                $html = "Suppression de la personne $id réalisée avec succès";
            else 
                $html = "La personne $id n'a pas pu être supprimée";
        }
    }
    // elseif($type == "personne") 
    //     // $obj = new Personne($id);
    //     $personne = new Personne($id);

    // // if($mysqli->from_db($acte) == NULL)
    // // if($obj->from_db($obj) == NULL) {
    // if(($acte->from_db($acte) == NULL) || ($personne->from_db($personne) == NULL)) {
    //     $html = $type == "personne" ? "La personne $id n'existe pas" : "L'acte $id n'existe pas";
    // } else {
    //     $obj->remove_from_db();
    //     if($obj->from_db($obj) == NULL) 
    //         $html = $type == "acte" ? "Suppression de l'acte $id réalisée avec succès" : "Suppression de la personne $id réalisée avec succès";
    //     else 
    //         $html = $type == "acte" ? "L'acte $id n'a pas pu être supprimé" : "La personne $id n'a pas pu être supprimée";
    // }

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
