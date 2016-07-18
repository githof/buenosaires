<?php

    function safe($string){
        global $mysqli;
        return htmlspecialchars($mysqli->real_escape_string(trim($string)));
    }

    function no_accent($string){
        $string = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $string);
        $string = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $string); // pour les ligatures e.g. '&oelig;'
        $string = preg_replace('#&[^;]+;#', '', $string); // supprime les autres caractères
        return $string;
    }

?>
