<?php

    function safe($string){
        return htmlspecialchars(mysql_real_escape_string(trim($string)));
    }

?>
