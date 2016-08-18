<?php

    function html_personne_fusion($personne){
        $html = "<div class='fusion-id'>$personne->id</div>";

        foreach($personne->noms as $nom)
            $html .= "<div class='fusion-nom'>$nom->to_String()</div>";

        foreach($personne->prenoms as $prenom)
            $html .= "<div class='fusion-prenom'>$prenom->prenom</div>";

        $html .= "<div class='fusion-condition'>".html_condition($personne->get_conditions, FALSE, FALSE)."</div>";

        $html .= "<div class='fusion-relations'>".html_personne_relations($personne, FALSE)."</div>";
    }

    $html = "";

    if(isset($ARGS["id"])){
        $personne = new Personne($ARGS["id"]);
        if($mysqli->from_db($personne) != NULL){
            $html = html_personne_fusion($personne);
        }
    }

    echo $html;

?>
