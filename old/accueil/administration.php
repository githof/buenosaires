<?php 
include("../includes/header.php"); 
include("../includes/restriction.php");
restriction($compte, 2);
include("../info/parametre.php");
?>

<h1>ADMINISTRATION</h1>

<h2>Ajouter un utilisateur</h2>
<form action="administration.php" method="post">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td>Pseudo</td><td><input name="pseudo" type="text" value="" /></td>
<td>Mot de Passe</td><td><input name="pwd" type="password" value="" /></td>
<td>Rang</td><td><select name="rang">
<option value="0" selected>Invit&eacute;-e</option>
<option value="1">Utilisateur</option>
<option value="2">Administrateur</option></select></td>
<td><input type="submit" value="Ajouter" /></td></tr>
</table>
</form>

<h2>G&eacute;rer les utilisateurs</h2>

<?php
if (isset($_POST['pseudo'])){
	$pseudo = trim(htmlspecialchars(mysql_real_escape_string($_POST['pseudo'])));
	$pwd = md5(trim(htmlspecialchars(mysql_real_escape_string($_POST['pwd']))));
	$rang = trim(htmlspecialchars(mysql_real_escape_string($_POST['rang'])));
	$now = time();
	mysql_query("INSERT INTO utilisateurs (id, rang, pseudo, nom, prenom, pwd, amail, date_inscr, valid) VALUES (NULL, '$rang', '$pseudo', '', '', '$pwd', '', '$now', 'in')");
}

if (isset($_GET['s']) and $_GET['s'] == "yes" and !isset($_POST['pseudo'])){
	$id_suppr = $_GET['id']; $id_suppr = htmlspecialchars(mysql_real_escape_string($id_suppr));	
	mysql_query("DELETE FROM utilisateurs WHERE id='$id_suppr'");
}
?>

<?php afficher_all_utilisateurs(); ?>

<?php 
function afficher_all_utilisateurs(){
	$sql = mysql_query("SELECT * FROM utilisateurs WHERE valid='in'");
	while ($line = mysql_fetch_assoc($sql)){
		echo "Pseudo : <strong>".$line['pseudo']."</strong> Rang : <strong>".affiche_rang($line['rang'])."</strong> - <a href=\"administration.php?id=".$line['id']."&s=yes\">Supprimer</a><br />";	
	}
}

function affiche_rang($rang){
	if ($rang == 0) return "Invit&eacute;-e";
	else if ($rang == 1) return "Utilisateur";
	else if ($rang == 2)return "Administrateur";
	else return "Pas de rang";
}

?>


<?php 
mysql_close();
include("../includes/footer.php"); 
?>