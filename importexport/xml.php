<?php

	include("../info/parametre.php");

	$path = "../data/buenosaires.xml";
	$fichier = fopen($path, "w");
	function ajouter_fichier($string_sql){
		$string_sql = preg_replace("/(\r\n|\n|\r)/", " ", $string_sql);
		fwrite($GLOBALS['fichier'], $string_sql."\n");	
	}
	
	
	ajouter_fichier("<?xml version=\"1.0\" encoding=\"UTF-8\"?><document><ACTES>");
	$sql = mysql_query("SELECT * FROM actes_contenu");
	while ($row = mysql_fetch_assoc($sql)){
		ajouter_fichier($row['contenu']);
	}
	ajouter_fichier("</ACTES></document>");
	
	fclose($fichier);
	mysql_close();
	// on redirige la page web
	header('Location: '.$path);

	
	  

?>
