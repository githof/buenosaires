<?php

    include_once(ROOT."src/html_entities.php");


    function html_personne_fusion($personne){
        $html = "<div class='id'>$personne->id</div>";

        foreach($personne->noms as $nom){
            $attr = "";
            if(isset($nom->attribut))
                $attr = "<div class='nom-attribut'>$nom->attribut</div>";
            $html .= "
                <div id='nom-$nom->id' class='nom'>
                    $attr
                    <div class='nom-nom'>$nom->nom</div>
                </div>";
        }

        foreach($personne->prenoms as $prenom)
            $html .= "<div id='prenom-$prenom->id' class='prenom'>".$prenom->to_string()."</div>";

        $conditions = $personne->conditions;
        foreach($conditions as $condition)
            $html .= "<div id='condition-$condition->id' class='condition'>".html_condition($condition)."</div>";

        $relations = $personne->relations;
        foreach($relations as $relation)
            $html .= "<div id='relation-$relation->id' class='relation'>".html_relation($relation)."</div>";

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
