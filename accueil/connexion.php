<?php

	include("../info/parametre.php");
	include ("../includes/account_log.php");
	
	$pseudo = $_POST['pseudo'];
	$pass = $_POST['pass'];
	$path = $_POST['url_courant'];
	
	$compte = connect($pseudo, $pass);
	if ($compte == NULL) $path = "../accueil/error.php";
	
	mysql_close();
	// on redirige la page web
	header('Location: '.$path);

	
	  

?>
