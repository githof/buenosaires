<?php 
include("../includes/header.php"); 
include("../includes/restriction.php");
restriction($compte, 2);
include("../info/parametre.php");
?>
<h1>RESTAURATION DE LA BASE</h1>

Uploader le fichier de sauvegarde : 
<form name="restor_save" method="post" action="" enctype="multipart/form-data">
<input name="fichier_upload" value="" type="file" />
<input type="hidden" name="MAX_FILE_SIZE" value="20000000">
<input type="hidden" name="hide" value="yes" />
<input type="submit" value="Valider" />
</form>

<?php 

if (isset($_POST['hide']) and $_POST['hide'] == "yes"){
	$name_fichier = basename($_FILES['fichier_upload']['name']);
	$tab_ext = preg_split("/\./", $name_fichier);
	$ext = $tab_ext[count($tab_ext)-1];
	if ($ext == "bsql") {
		
		// on fait la sauvegarde du fichier bsql dans un deuxieme fichier
		include_once("../includes/fonctions_copy_fichier.php");
		copy_fichier_bsql();
		
		move_uploaded_file($_FILES['fichier_upload']['tmp_name'], "../data/logSql.bsql");
		maj_sql_fichier("../data/logSql.bsql");	
		echo "<br /><br />Op&eacute;ration r&eacute;ussie.";
	}
}

function maj_sql_fichier($fichier){
	include("../includes/creer_tables.php"); // on créer les tables dans la base de données
	$file = fopen($fichier, "r");
	while (($ligne = fgets($file, 4096)) !== false){
		$tab = preg_split("/\s/", $ligne);
		$res = "";
		foreach($tab as $key => $val){
			if ($key != 0 and $key != 1 and $key != 2 and $key != 3) $res .= $val." "; 	
		}
		mysql_query($res);	
	}
	fclose($file);
}

?>




<?php 
mysql_close();
include("../includes/footer.php"); 
?>