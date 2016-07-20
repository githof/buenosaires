<?php

    function safe($string){
        global $mysqli;
        return htmlspecialchars($mysqli->real_escape_string(trim($string)));
    }

    function no_accent($string){
      return strtr(
            utf8_decode($string),
            utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }

?>
