<?php

function copy_fichier_bsql(){
	$fichier = "../data/logSql.bsql";
	$fichier_bak = "../data/logSqlBak.bsql";
	$file = fopen($fichier, "r");
	$bak = fopen($fichier_bak, "w");
	while (($ligne = fgets($file, 4096)) !== false){
		fwrite($bak, $ligne);
	}
	fclose($file);
	fclose($bak);
}

?>