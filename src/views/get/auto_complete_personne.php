<?php

    function list_personne(){
        global $mysqli;
        $personnes = [];

        $results = $mysqli->select("personne", ["*"]);
        if($results != FALSE && $results->num_rows > 0){
            while($row = $results->fetch_assoc()){
                $personne = new Personne($row["id"]);
                $mysqli->from_db($personne);
                $str = "";

                $str .= " [$personne->id]";
                foreach($personne->prenoms as $prenom)
                    $str .= " $prenom->prenom";

                foreach($personne->noms as $nom)
                    $str .= " " . $nom->to_String();

                $personnes[] = [$row["id"], $str];
            }
        }

        return $personnes;
    }

    function search($findme, $personnes){
        $results = [];
        foreach($personnes as $personne){
            if(strpos(strtoupper($personne[1]), strtoupper($findme)))
                $results[] = "
                    <div>
                        <span class='personne-id' style='display:none;'>".$personne[0]."</span>
                        ".$personne[1]."
                    </div>";
        }
        return $results;
    }

    if(isset($ARGS["str"])){
        $results = search($ARGS["str"], list_personne());
        foreach($results as $result)
            echo $result;
    }

?>
