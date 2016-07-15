<?php

function affiche_table($sql, $table) {
	$nbLigne = mysql_num_rows($sql);
	$nbColonne = mysql_num_fields($sql);
	echo '<table border="1" cellspacing="0" class="mes_tables" align="center">';
	print("<tr><td colspan=\"$nbColonne\" class=\"big\">TABLE DES ".strtoupper($table)." ($nbLigne)</td></tr>");
	$premiereLigne = "";
	for ($i=0;$i<$nbColonne;$i++){
		$nom_col = mysql_field_name($sql, $i);
		$premiereLigne .= "".unTDbig($nom_col);
	}
	echo ligne($premiereLigne);
	while ($line = mysql_fetch_row($sql)){
		$uneLigne = "";
		for ($i=0;$i<$nbColonne;$i++){
			$n_col = mysql_field_name($sql, $i);
			if ($n_col == "id_acte" || $n_col == "acte") $uneLigne .= "".unTD($line[$i], "../gestion/viewActes.php?i=".$line[$i]);
			else if ($n_col == "epoux" || $n_col == "epouse" || $n_col == "personne1" || $n_col == "personne2") $uneLigne .= "".unTD($line[$i], "../gestion/viewPersonne.php?id=".$line[$i]);
			else if (($n_col == "id" && $table == "personnes") || $n_col == "id_personne") $uneLigne .= "".unTD($line[$i], "../gestion/viewPersonne.php?id=".$line[$i]);
			else if (($n_col == "nom1" || $n_col == "nom2" || $n_col == "nom3" || $n_col == "prenom1" || $n_col == "prenom2") && $line[$i] != "") $uneLigne .= "".unTD($line[$i], "../gestion/viewPersonnes.php?n=".$line[$i]);
			else $uneLigne .= "".unTD($line[$i], "");
		}
		echo ligne($uneLigne);
	}
	echo '</table>';
}			

function ligne($texte){
	return '<tr>'.$texte.'</tr>';
}
function unTDbig($texte){
	return '<td class="big">'.$texte.'</td>';	
}
function unTD($texte, $lien){
	if ($lien != "") return '<td class="tdcolor"><a href="'.$lien.'" class="a_display_block">'.$texte.'</a></td>';
	return '<td>'.$texte.'</td>';	
}

?>