<?php

    include_once(ROOT."src/html_entities.php");


    function html_personne_fusion($personne){
        $html = "<div class='id'>$personne->id</div>";

        foreach($personne->noms as $nom)
            $html .= "<div class='nom'>".$nom->to_string()."</div>";

        foreach($personne->prenoms as $prenom)
            $html .= "<div class='prenom'>".$prenom->to_string()."</div>";

        $conditions = $personne->conditions;
        foreach($conditions as $condition)
            $html .= "<div class='condition'>".html_condition($condition)."</div>";

        $relations = $personne->relations;
        foreach($relations as $relation)
            $html .= "<div class='relation'>".html_relation($relation)."</div>";

        return $html;
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
