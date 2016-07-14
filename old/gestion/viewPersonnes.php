<?php 
include("../includes/header.php"); 
include("../info/parametre.php");
include("../includes/fonctions_all.php");
?>

<h1>RECHERCHER UNE PERSONNE</h1>

<div class="form_form">
<form method="get" name="form_table" id="form_table" action="">
   	<select name="i">
       	<option value="" selected class="italique">Sélectionnez un identifiant</option>
       	<?php
			
			$sql = mysql_query("SELECT * FROM personnes");
			while ($line = mysql_fetch_assoc($sql)){
				echo '<option value="'.$line['id'].'" onClick="allerVers(\'viewPersonnes.php?i='.$line['id'].'\');">'.$line['id'];
				if (personneVide($line)) echo ' (empty)';
				echo '</option>';	
			}
			
		?>
	</select>
    <input type="submit" value="ok" />
</form>


<form method="get" name="form_nom" action="">
	<label for="nom">Rechercher par nom :</label>
	<input name="n" id="nom" value="" maxlength="50" type="text" />
    <input type="submit" value="ok" />
</form>

<form method="get" name="form_periode" action="">
	<label for="datepicker">Début période :</label>
	<input name="dp" id="dperiode" value="" maxlength="10" type="text" />
	<label for="fperiode">Fin période :</label>
	<input name="fp" id="fperiode" value="" maxlength="10" type="text" />
    <input type="submit" value="ok" /> (format JJ-MM-AAAA de 1655 &agrave; 1830)
</form>

<form method="get" name="voirtous" action="">
	<input type="hidden" name="h" value="all" />
	<input type="submit" value="Voir tout" />
</form>
</div>

<div class="affiche_table">
	
  <form action="selectPersonnes.php" method="post">
	<?php 
	
		// cest dans la variable pers que les resultats sont stocké
		if (isset($_GET['i']) or isset($_GET['n']) or (isset($_GET['dp']) and isset($_GET['fp'])) or isset($_GET['h'])) {
			echo '<h2>RESULTATS : ';
			if (isset($_GET['n'])) {
				$n = stripAccentsLower(htmlspecialchars(mysql_real_escape_string($_GET['n'])));
				if (strlen($n)<=50 and strlen($n)>2){
					echo $n;
					change_title($n);
				}
			}
			echo '</h2>';
		}
		// pour la recherche par identifiant
		if (isset($_GET['i'])){
			$i = htmlspecialchars(mysql_real_escape_string($_GET['i']));
			if (strlen($i)<=6 and is_numeric($i)){
				$pers = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$i'"));
				$nom = affiche_pers($pers);
				if($nom != " ")
				  change_title($nom);
			}			
		}
		// pour la recherche par nom
		if (isset($_GET['n'])){
			$n = stripAccentsLower(htmlspecialchars(mysql_real_escape_string($_GET['n'])));
			if (strlen($n)<=50 and strlen($n)>2){
				$res = mysql_query("SELECT * FROM personnes");
				while ($pers = mysql_fetch_assoc($res)){
					if (rechercherPersonne($n,$pers)) {
						affiche_pers_with_check($pers);
					}
				}
			}
		}
		// pour la recherche par période
		if (isset($_GET['dp']) and isset($_GET['fp'])){
			$dp = stripAccentsLower(htmlspecialchars(mysql_real_escape_string($_GET['dp'])));
			$fp = stripAccentsLower(htmlspecialchars(mysql_real_escape_string($_GET['fp'])));
			if ((strlen($fp) == 10 or strlen($fp) == 4) and (strlen($dp) == 10 or strlen($dp) == 4)){				
				$sql = mysql_query("SELECT * FROM personnes");
				while ($pers = mysql_fetch_assoc($sql)){
					if (strlen($fp)==4) $fp = "31-12-".$fp;
					if (strlen($dp)==4) $dp = "01-01-".$dp;
					$periode_id = $pers['periode'];
					if (bonnePeriode($periode_id, $dp, $fp)) {
						affiche_pers_with_check($pers);	
					}
				}
			}
		}
		// voir tout
		if (isset($_GET['h'])) {
			$sql = mysql_query("SELECT * FROM personnes");
			while ($pers = mysql_fetch_assoc($sql)){
				affiche_pers_with_check($pers);
			}
		}
	?>
	<!-- En attente deja enregistre dans une fontion select_input dans groupe.php -->
	
	<label for="groupe" >Dans quelle categorie souhaitez-vous les enegistrer ? ?</label><br />
       <select id="groupe" class="liste">
           <option value=" "> EN ATTENTE</option>     
       </select><br /><br />
       
      <input type="checkbox" name="voisins" value="true" id="pers_liee"><label for="pers_liee">ajouter également les personnes liées (parents, enfants, témoins, époux)</label><br /><br />
      <input type="submit" class="bouton" value="ajouter au groupe sélectionné" />
      
  </form>
</div>

<?php 
mysql_close();
include("../includes/footer.php"); 
?>
