<?php 
include("../includes/header.php"); 
include("../includes/restriction.php");
restriction($compte, 1);
include("../info/parametre.php");
?>

<h1>CONSOLE</h1>

(Entrer un String)
<form action="" method="post">
<textarea cols="70" rows="6" name="requete"></textarea>
<input type="submit" value="Valider" />
</form>

<?php 
if (isset($_POST['requete'])){
	$req = trim($_POST['requete']);
	$tab = preg_split("/\s/",$req);
	if ($tab[0] == "SELECT" or $tab[0] == "select"){
		try {
			$res = mysql_query($req);
			while($line = mysql_fetch_assoc($res)){
				foreach ($line as $key => $val){
					echo $val." ";	
				}
				echo "<br />";
			}
		}
		catch(Exception $error){
			echo "ERREUR dans la syntaxe de la requete";	
		}
	}
}
?>

<?php 
mysql_close();
include("../includes/footer.php"); 
?>