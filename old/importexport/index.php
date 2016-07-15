<?php 
include("../includes/header.php"); 
include("../includes/fonctions_all.php"); 
change_title('import/export');
?>
<h1>IMPORT / EXPORT</h1>

<!--
<a href="compiler.php">>> Compiler le fichier de base <<</a><br /><br />
-->
<a href="ajouter_acte.php">>> Ajouter un ou des actes <<</a><br /><br />
<a href="restauration.php">>> Restaurer la base &agrave; partir d'un fichier de sauvegarde <<</a><br /><br />
<?php if (isset ($compte) and $compte->rang >= 2) {
	echo '<a href="../data/logSql.bsql">>> T&eacute;l&eacute;charger le fichier de sauvegarde (bsql) <<</a> (faire clique droit et enregistrer sous)<br /><br />';
	echo '<a href="../data/logSqlBak.bsql">>> T&eacute;l&eacute;charger l\'avant dernier fichier de sauvegarde (bsql) <<</a> (faire clique droit et enregistrer sous)<br /><br />'; 
}
?>
<a href="../gestion/gdf.php?p=all">>> T&eacute;l&eacute;charger le fichier GDF g&eacute;n&eacute;ral <<</a> (faire clique droit et enregistrer sous)<br /><br />
<a href="../importexport/xml.php">>> T&eacute;l&eacute;charger le fichier XML g&eacute;n&eacute;ral <<</a> (faire clique droit et enregistrer sous)<br /><br />
<a href="../importexport/csv.php">>> T&eacute;l&eacute;charger les fichiers CSV <<</a><br /><br />


<?php 
include("../includes/footer.php"); 
?>