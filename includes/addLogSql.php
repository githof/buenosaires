<?php

$fichier_log_sql = fopen("../data/logSql.bsql", "a");

function ajouter_fichier_log($fichier, $string_sql, $type){
	$date_heure = date_heure();
	$compte = $GLOBALS['compte'];
	$string_sql = preg_replace("/(\r\n|\n|\r)/", " ", $date_heure." ".$compte->pseudo." ".$type." ".$string_sql);
	fwrite($GLOBALS['fichier_log_sql'], $string_sql."\n");	
}

// ajouter_fichier_log($fichier_log_sql, );


/*
	Retourne la date et l'heure
*/
function date_heure(){
	return date("d-m-Y H:i:s");	
}

?>