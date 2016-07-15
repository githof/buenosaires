<?php 
include("../includes/header.php"); 
include("../info/parametre.php");
include("../includes/fonctions_all.php");
include("groupes.php");

?>

<h1>VOIR UNE PERSONNE</h1>
<div class="form_form">
	<?php 
		$flag_exist = false;
		$id = htmlspecialchars(mysql_real_escape_string($_GET['id']));
		if (strlen($id)<=6 and is_numeric($id)){
			$sql_pers_aff = mysql_query("SELECT * FROM personnes WHERE id='$id'");
			if (mysql_num_rows($sql_pers_aff) > 0) $flag_exist = true; 
			$pers = mysql_fetch_assoc($sql_pers_aff);
			change_title(nom_court($pers));
			
			if ($pers['de1'] == 1) $de1 = "de "; else $de1 = "";
			if ($pers['la1'] == 1) $la1 = "la "; else $la1 = "";
			if ($pers['de2'] == 1) $de2 = "de "; else $de2 = "";
			if ($pers['la2'] == 1) $la2 = "la "; else $la2 = "";
			if ($pers['de3'] == 1) $de3 = "de "; else $de3 = "";
			if ($pers['la3'] == 1) $la3 = "la "; else $la3 = "";
			$affich_date = affiche_date($pers['periode']);
			echo $pers['id'].' | '.$pers['prenom1'].' '.$pers['prenom2'].' <span class="en_maj">'.$de1.$la1.$pers['nom1'].' '.$de2.$la2.$pers['nom2'].' '.$de3.$la3.$pers['nom3'].'</span> | '.$affich_date;
		}
		$pile = acte_of_pers($id);
		asort($pile);
		echo '<br />Actes : - ';
		foreach ($pile as $ac){
			echo '<a href="viewActes.php?i='.$ac.'">'.$ac.'</a> - ';	
		}
		echo '<br /><a href="dissocier.php?p='.$id.'">>> Dissocier cette personne <<</a>';
	?>
</div>

<div class="affiche_table">
	<form action="selectPersonnes.php" method="post">
		<?php
			if (strlen($id)<=6 and is_numeric($id) and $flag_exist){
				// on affiche ses conditions
				$sql_cond = mysql_query("SELECT * FROM cond WHERE id_personne='$id'");
				echo '<h2>SES CONDITIONS :</h2>';
				echo '<table cellspacing="0" align="center">';

				while($sql_cond_row = mysql_fetch_assoc($sql_cond)){
					$id_source = $sql_cond_row['source'];
					$id_date = $sql_cond_row['periode'];
					$source = mysql_fetch_assoc(mysql_query("SELECT source FROM sources WHERE id='$id_source'"));
					$conditio['source'] = $source['source'];
					$conditio['per'] = affiche_date($id_date);
					$ddate = mysql_fetch_assoc(mysql_query("SELECT minDebut FROM periodes WHERE id='$id_date'"));
					$conditio['intper'] = transforme_periode($ddate['minDebut']);
					$conditio['cond'] = $sql_cond_row['cond'];
					$conditio['acte'] = $sql_cond_row['acte'];
					$condit[] = $conditio;
				}
				
				if (isset($condit)){
				foreach ($condit as $key => $row) {
					$intper[$key]  = $row['intper'];
				}
				
				array_multisort($intper, SORT_ASC, $condit);
				
				foreach($condit as $k => $va){
					$id_source = $va['source'];
					echo '<tr>';
					echo '<td>'.$va['cond'].'</td><td>'.$va['source'].'</td><td>'.$va['per'].'</td><td><a href="viewActes.php?i='.$va['acte'].'">a'.$va['acte'].'</a></td>';
					echo '</tr>';
				}			
				}
				echo '</table>';
				
				// on affiche ses relations
				$sql = mysql_query("SELECT * FROM relations WHERE personne1='$id' or personne2='$id'");
				if (mysql_num_rows($sql) > 0) echo '<h2>SES RELATIONS :</h2><p class="p_center"><a href="gdf.php?p='.$id.'" target="_blank">&gt;&gt; TELECHARGER LE FICHIER GDF DE CETTE PERSONNE &lt;&lt;</a></p>';
				while ($line = mysql_fetch_assoc($sql)){
					$line['personne1'] == $id ? $id_pers = $line['personne2'] : $id_pers = $line['personne1'];
					$pers = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_pers'"));
					$data[] = array("tt"=>$line['type'],"ide"=>$pers['id'], "nom1"=>$pers['nom1'], "prenom1"=>$pers['prenom1'], "line"=>$line);
				}
				foreach ($data as $key => $row) {
					$tt[$key] = $row['tt'];
					$ide[$key]  = $row['ide'];
					$nom1[$key] = $row['nom1'];
					$prenom1[$key] = $row['prenom1'];
				}
				if (isset($_GET['t']) and isset($_GET['o'])){
					$t = htmlspecialchars(mysql_real_escape_string($_GET['t']));
					$o = htmlspecialchars(mysql_real_escape_string($_GET['o']));	
					if ($t == "t" and $o == "asc") array_multisort($tt, SORT_ASC,$data);
					if ($t == "t" and $o == "desc") array_multisort($tt, SORT_DESC,$data);
					if ($t == "ide" and $o == "asc") array_multisort($ide, SORT_ASC,$data);
					if ($t == "ide" and $o == "desc") array_multisort($ide, SORT_DESC,$data);
					if ($t == "nom1" and $o == "asc") array_multisort($nom1, SORT_ASC,$data);
					if ($t == "nom1" and $o == "desc") array_multisort($nom1, SORT_DESC,$data);
					if ($t == "prenom1" and $o == "asc") array_multisort($prenom1, SORT_ASC,$data);
					if ($t == "prenom1" and $o == "desc") array_multisort($prenom1, SORT_DESC,$data);
				}
				echo '<table cellspacing="0" align="center">';
				echo '<tr>
				<td class="big"><a href="viewPersonne.php?id='.$id.'&t=t&o=desc"><img src="../images/fleche_haut.png" alt="Trier en ordre d&eacute;croissant"/><a><a href="viewPersonne.php?id='.$id.'&t=t&o=asc"><img src="../images/fleche_bas.png" alt="Trier en ordre croissant"/></a></td>
				<td class="big"><a href="viewPersonne.php?id='.$id.'&t=ide&o=desc"><img src="../images/fleche_haut.png" alt="Trier en ordre d&eacute;croissant"/><a><a href="viewPersonne.php?id='.$id.'&t=ide&o=asc"><img src="../images/fleche_bas.png" alt="Trier en ordre croissant"/></a></td>
				<td class="big"><a href="viewPersonne.php?id='.$id.'&t=prenom1&o=desc"><img src="../images/fleche_haut.png" alt="Trier en ordre d&eacute;croissant"/><a><a href="viewPersonne.php?id='.$id.'&t=prenom1&o=asc"><img src="../images/fleche_bas.png" alt="Trier en ordre croissant"/></a></td>
				<td class="big"><a href="viewPersonne.php?id='.$id.'&t=nom1&o=desc"><img src="../images/fleche_haut.png" alt="Trier en ordre d&eacute;croissant"/><a><a href="viewPersonne.php?id='.$id.'&t=nom1&o=asc"><img src="../images/fleche_bas.png" alt="Trier en ordre croissant"/></a></td>
				<td colspan="2" class="big"></td>
				</tr>';
				foreach($data as $cle => $value){
					affiche_pers2($value["line"], $id);
				}
				echo '</table>';
			}

/* Alpha		
			input_select_groupe();
*/
			
			?>
			<label for="choix" >Veuillez Selectionner un Choix  </label><br />
			<select id="choix" class="liste">
			   <option value="creer"> Créer </option>   
			   <option value="afficher"> Afficher </option>		        
			   <option value="supprimer"> Supprimer </option>     
			   <option value="exporter"> Exporter </option>       
			</select><br /><br />
<!-- Alpha		   
		  <input type="checkbox" name="voisins" value="true" id="pers_liee"><label for="pers_liee">ajouter également les personnes liées (parents, enfants, témoins, époux)</label><br /><br />
		  <input type="hidden" name="personnes[]"  value="$id" />
		  <input type="submit" class="bouton" value="ajouter au groupe sélectionné" />
-->
  </form>
</div>



<?php affiche_legend(); ?>
<?php 
mysql_close();
include("../includes/footer.php"); 
?>
