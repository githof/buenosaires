<?php 
include("../includes/header.php"); 
include("../info/parametre.php");
include("../includes/fonctions_all.php");
?>

<h1>RECHERCHER UN ACTE</h1>

<div class="form_form">
<form method="get" name="form_table" action="">
    <select name="i">
       	<option value="" selected class="italique">Sélectionnez un identifiant</option>
       	<?php
			$sql = mysql_query("SELECT * FROM actes");
			while ($line = mysql_fetch_assoc($sql)){
				echo '<option value="'.$line['id_acte'].'" onClick="allerVers(\'viewActes.php?i='.$line['id_acte'].'\');">'.$line['id_acte'];
				$id_pers1 = $line['epoux'];
				$id_pers2 = $line['epouse'];
				$pers1 = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_pers1'"));
				$pers2 = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_pers2'"));
				if (personneVide($pers1) and personneVide($pers2)) echo ' (empty)';
				echo '</option>';	
			}
		?>
    </select>
    <input type="submit" value="ok" />
</form>
 

</div>

<div class="affiche_table">
	<?php
		if (isset($_GET['i'])) echo '<h2>RESULTATS :</h2>';
		// pour la recherche par identifiant
		if (isset($_GET['i'])){
			$i = htmlspecialchars(mysql_real_escape_string($_GET['i']));
			if (strlen($i)<=6 and is_numeric($i)){
				$acte = mysql_fetch_assoc(mysql_query("SELECT * FROM actes WHERE id_acte='$i'"));
				$affich_date = affiche_date($acte['periode']);
				echo 'Acte : '.$acte['id_acte'].' &nbsp;&nbsp;le '.$affich_date.'<br />';
				change_title('a' . $acte['id_acte']);
				$id_epoux = $acte['epoux'];
				$id_epouse = $acte['epouse'];
				$epoux = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_epoux'"));
				$epouse = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_epouse'"));
				affiche_pers($epoux);
				affiche_pers($epouse);		
				$texte_acte = mysql_fetch_assoc(mysql_query("SELECT contenu FROM actes_contenu WHERE id_acte='$i'"));
				echo '<p class=".affich_texte">'.$texte_acte['contenu'].'</p>';	
				echo '<p class=".affich_texte">'.htmlspecialchars($texte_acte['contenu']).'</p>';	
			}			
		}

	?>
</div>

<?php 
mysql_close();
include("../includes/footer.php"); 
?>