<?php

    include_once(ROOT."src/html_entities.php");

    function all_personnes(){
        global $mysqli;
        $str = "";

        $result = $mysqli->select("personne", ["*"]);
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $personne = new Personne($row["id"]);
                $mysqli->from_db($personne);
                $html = "";

                $html .= " [$personne->id]";
                foreach($personne->prenoms as $prenom)
                    $html .= " $prenom->prenom";

                foreach($personne->noms as $nom)
                    $html .= " " . $nom->to_String();

                $str .= "<option value='$personne->id'>$html</option>";
            }
        }
        return $str;
    }

    echo all_personnes();

?>
