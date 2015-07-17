<?php

include('local.php');

$link = mysql_connect($server, $user, $password) or die ("La connexion au serveur n'a pas réussi !"); 
mysql_select_db($dbb, $link) or die("La connexion à la base de données n'a pas réussi !");

?>