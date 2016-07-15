<?php

    function safe($string){
        global $mysqli;
        return htmlspecialchars($mysqli->real_escape_string(trim($string)));
    }

?>
