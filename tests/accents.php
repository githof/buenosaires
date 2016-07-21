<?php

function stripAccents($str) {
    return strtr(utf8_decode($str),
		 utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
		 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}


echo stripAccents('María') . "\n";
echo get_include_path() . "<br>";
echo $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo __DIR__ . "<br>";
?>
