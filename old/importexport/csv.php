<?php 
include("../includes/header.php"); 
?>

<h1>FICHIERS CSV</h1>

<?php

	include("../info/parametre.php");
	include("../includes/fonctions_ecrire.php");
	include("../includes/fonctions_all.php");
	include("../includes/fonctions_gdf_csv.php");
	include("../includes/fonctions_csv.php");

	
	// on fait d'abord le fichier csv des personnes

	$fichier = ouvrir("../data/csv_personnes.csv");
	$rel = mysql_query("SELECT * FROM personnes");
	fwrite($fichier, "identifiant;prenoms;noms;periode\n");
	while ($p = mysql_fetch_assoc($rel)){	
		ecrire_fichier($fichier, $p);
	}
	fclose($fichier);
	
	// puis on fait le fichier csv des relations
	$fichier = ouvrir("../data/csv_relations.csv");
	fwrite($fichier, "identifiant1;prenoms1;noms1;identifiant2;prenoms2;noms2;type;periode_debut;periode_fin\n");
    csv_relations($fichier);
	fclose($fichier);
	
	mysql_close();
	
	function ouvrir($path_fichier){
		$fichier = fopen($path_fichier,"w");
		ftruncate($fichier,0);	
		return $fichier;
	}
		
	function ecrire_fichier($fichier, $p){
		$date_p = affiche_date($p['periode']);
		$tab = get_name_pers($p);
		fwrite($fichier, "p".$p['id'].";\"".trim(trim($tab[0])." ".trim($tab[1])."\";\"".trim($tab[2])." ".trim($tab[3])." ".trim($tab[4]))."\";\"$date_p\"\n");
	}
	
	
	

?>

<a href="../data/csv_personnes.csv"> >> T&eacute;l&eacute;charger le fichier CSV des personnes << </a><br /><br />
<a href="../data/csv_relations.csv"> >> T&eacute;l&eacute;charger le fichier CSV des relations << </a>

<?php 
include("../includes/footer.php"); 
?>