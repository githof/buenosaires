<?php 
include("../includes/header.php"); 
include("../includes/restriction.php");
restriction($compte, 1);
include("../info/parametre.php");
include("../includes/fonctions_all.php");

?>
<h1>DISSOCIER UNE PERSONNE</h1>

<div class="form_form">
<form method="get" name="form_table" id="form_table" action="">
   	<select name="p">
       	<option value="" selected class="italique">Sélectionnez un identifiant</option>
       	<?php
			
			$sql = mysql_query("SELECT * FROM personnes");
			while ($line = mysql_fetch_assoc($sql)){
				echo '<option value="'.$line['id'].'">'.$line['id'];
				if (personneVide($line)) echo ' - (empty)';
				else echo ' - '.$line['prenom1'].' <span class="en_maj">'.$line['nom1'].'<span>';
				echo '</option>';	
			}
			
		?>
	</select>
    <input type="submit" value="ok" />
</form>
</div>



<?php 
	if (isset($_GET['p']) and !isset($_POST['hide'])){
		echo '<div class="affiche_table">';
		$id = htmlspecialchars(mysql_real_escape_string($_GET['p']));
		if (strlen($id)<=6 and is_numeric($id)){
			echo '<h2>NOUVELLE PERSONNE :</h2>
			Si relation parent-enfant ou epoux-temoin: la nouvelle personne doit être le parent ou l\'epoux<br /><br />';
			$pers = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id'"));
			if ($pers['de1'] == 1) $de1 = "de "; else $de1 = "";
			if ($pers['la1'] == 1) $la1 = "la "; else $la1 = "";
			if ($pers['de2'] == 1) $de2 = "de "; else $de2 = "";
			if ($pers['la2'] == 1) $la2 = "la "; else $la2 = "";
			if ($pers['de3'] == 1) $de3 = "de "; else $de3 = "";
			if ($pers['la3'] == 1) $la3 = "la "; else $la3 = "";
			$id_periode = $pers['periode'];
			$per_pers = mysql_fetch_assoc(mysql_query("SELECT * FROM periodes WHERE id='$id_periode'"));
		
			?><form name="form_dissocier" method="post" action="">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>Prénom 1</td>
                        <td>Prénom 2</td>
                        <td>De 1</td>
                        <td>La 1</td>
                        <td>Nom 1</td>
                        <td>De 2</td>
                        <td>La 2</td>
                        <td>Nom 2</td>
                        <td>De 3</td>
                        <td>La 3</td>
                        <td>Nom 3</td>
                    </tr>
                    <tr>
                        <td><input name="prenom1" type="text" value="<?php echo $pers['prenom1'] ?>" maxlength="100" /></td>
                        <td><input name="prenom2" type="text" value="<?php echo $pers['prenom2'] ?>" maxlength="100" /></td>
                        <td><input name="de1" type="checkbox" <?php if ($pers['de1']==1) echo "checked"; ?> value=1></td>
                        <td><input name="la1" type="checkbox" <?php if ($pers['la1']==1) echo "checked"; ?> value=1></td>
                        <td><input name="nom1" type="text" value="<?php echo $pers['nom1'] ?>" maxlength="100" /></td>
                        <td><input name="de2" type="checkbox" <?php if ($pers['de2']==1) echo "checked"; ?> value=1></td>
                        <td><input name="la2" type="checkbox" <?php if ($pers['la2']==1) echo "checked"; ?> value=1></td>
                        <td><input name="nom2" type="text" value="<?php echo $pers['nom2'] ?>" maxlength="100" /></td>
                        <td><input name="de3" type="checkbox" <?php if ($pers['de3']==1) echo "checked"; ?> value=1></td>
                        <td><input name="la3" type="checkbox" <?php if ($pers['la3']==1) echo "checked"; ?> value=1></td>
                        <td><input name="nom3" type="text" value="<?php echo $pers['nom3'] ?>" maxlength="100" /></td>
                    </tr>
                </table>
                <br />
                <table border="0" cellpadding="0" cellspacing="0">
                	<tr>
                    	<td>Période Début Min</td>
                        <td>Période Début Max</td>
                        <td>Période Fin Min</td>
                        <td>Période Fin Max</td>
                        <td></td>
                    </tr>
                    <tr>
                    	<td><input name="per_debut_min" type="text" value="<?php echo $per_pers['minDebut']; ?>" maxlength="10" /></td>
                        <td><input name="per_debut_max" type="text" value="<?php echo $per_pers['maxDebut']; ?>" maxlength="10" /></td>
                        <td><input name="per_fin_min" type="text" value="<?php echo $per_pers['minFin']; ?>" maxlength="10" /></td>
                        <td><input name="per_fin_max" type="text" value="<?php echo $per_pers['maxFin']; ?>" maxlength="10" /></td>
                        <td>&nbsp;Au format AAAA-MM-JJ</td>
                    </tr>
                </table>
            
			<?php
		}
		
		if (strlen($id)<=6 and is_numeric($id)){
			// on affiche les actes
			echo '<h2>SES ACTES :</h2>';
			$pile = array();
			$sql_p_a = mysql_query("SELECT id FROM relations WHERE personne1='$id' or personne2='$id'");
			while ($r_p_a = mysql_fetch_assoc($sql_p_a)){
				$rel_r_a = $r_p_a['id'];
				$sql_m_a = mysql_fetch_assoc(mysql_query("SELECT acte FROM mentions WHERE relation='$rel_r_a'"));
				if (!in_array($sql_m_a['acte'], $pile)) array_push($pile, $sql_m_a['acte']);
			}
			asort($pile);
			echo 'Cochez les checkbox pour attribuer l\'acte à la nouvelle personne. (Que si la personne cr&eacute;&eacute;e est l\'epoux ou l\'epouse de l\'acte)<br /><br />';
			echo '<table border="0" cellpadding="0" cellspacing="0" align="center">';
			foreach ($pile as $ac){
				echo '<tr><td><input type="checkbox" name="a[]" value="'.$ac.'"/></td><td>'.$ac.'</td></tr>';	
			}
			echo '</table>';
			
			// on affiche ses conditions
			$sql_cond = mysql_query("SELECT * FROM cond WHERE id_personne='$id'");
			echo '<h2>SES CONDITIONS :</h2>';
			echo 'Cochez les checkbox pour attribuer la condition à la nouvelle personne.<br /><br />';
			echo '<table cellspacing="0" align="center">';

			if (mysql_num_rows($sql_cond) > 0) {
				while($sql_cond_row = mysql_fetch_assoc($sql_cond)){
					$id_source = $sql_cond_row['source'];
					$id_date = $sql_cond_row['periode'];
					$source = mysql_fetch_assoc(mysql_query("SELECT source FROM sources WHERE id='$id_source'"));
					$conditio['id'] = $sql_cond_row['id'];
					$conditio['source'] = $source['source'];
					$conditio['per'] = affiche_date($id_date);
					$ddate = mysql_fetch_assoc(mysql_query("SELECT minDebut FROM periodes WHERE id='$id_date'"));
					$conditio['intper'] = transforme_periode($ddate['minDebut']);
					$conditio['cond'] = $sql_cond_row['cond'];
					$conditio['acte'] = $sql_cond_row['acte'];
					$condit[] = $conditio;
				}
				
				foreach ($condit as $key => $row) {
					$intper[$key]  = $row['intper'];
				}
				
				array_multisort($intper, SORT_ASC, $condit);
				
				foreach($condit as $k => $va){
					$id_source = $va['source'];
					echo '<tr>';
					echo '<td><input type="checkbox" name="c[]" value="'.$va['id'].'" /></td><td>'.$va['cond'].'</td><td>'.$va['source'].'</td><td>'.$va['per'].'</td><td><a href="viewActes.php?i='.$va['acte'].'">a'.$va['acte'].'</a></td>';
					echo '</tr>';
				}			
				echo '</table>';
			}
			
			// on affiche ses relations
			$sql = mysql_query("SELECT * FROM relations WHERE personne1='$id' or personne2='$id'");
			if (mysql_num_rows($sql) > 0) echo '<h2>SES RELATIONS :</h2>';
			while ($line = mysql_fetch_assoc($sql)){
				$line['personne1'] == $id ? $id_pers = $line['personne2'] : $id_pers = $line['personne1'];
				$pers = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_pers'"));
				$data[] = array("tt"=>$line['type'],"ide"=>$pers['id'], "nom1"=>$pers['nom1'], "prenom1"=>$pers['prenom1'], "line"=>$line);
			}
			echo 'Cochez les checkbox pour attribuer la relation à la nouvelle personne.<br /><br />';
			echo '<table cellspacing="0" align="center">
			<tr>
			<td>New person</td>
			<td colspan="5"></td>
			</tr>';	
			foreach($data as $cle => $value){
				affiche_pers3($value["line"], $id, $line['id']);
			}
			echo '</table>';
		}
		?>
        	<input type="hidden" name="hide" value="yes" />
        	<input type="submit" value="Dissocier" />
        	</form>
        <?php
	echo '</div>';
	}
?>


    <?php
		if (isset($_POST['hide']) and isset($_GET['p'])){
			$hide = htmlspecialchars(mysql_real_escape_string($_POST['hide']));
			$id_old = htmlspecialchars(mysql_real_escape_string($_GET['p']));
			if ($hide == "yes"){
				echo "<h2>RESULTAT DE LA DISSOCIATION :</h2>";
				
				$prenom1_f = $_POST['prenom1']; $prenom1_f = htmlspecialchars($prenom1_f);
				$prenom2_f = $_POST['prenom2'];  $prenom2_f = htmlspecialchars($prenom2_f);
				$nom1_f = $_POST['nom1']; $nom1_f = htmlspecialchars($nom1_f);
				$nom2_f = $_POST['nom2']; $nom2_f = htmlspecialchars($nom2_f);
				$nom3_f = $_POST['nom3']; $nom3_f = htmlspecialchars($nom3_f);
				if (isset($_POST['de1'])) {$de1_f = $_POST['de1'];} else $de1_f = 0;
				if (isset($_POST['de2'])) {$de2_f = $_POST['de2'];} else $de2_f = 0;
				if (isset($_POST['de3'])) {$de3_f = $_POST['de3'];} else $de3_f = 0;
				if (isset($_POST['la1'])) {$la1_f = $_POST['la1'];} else $la1_f = 0;
				if (isset($_POST['la2'])) {$la2_f = $_POST['la2'];} else $la2_f = 0;
				if (isset($_POST['la3'])) {$la3_f = $_POST['la3'];} else $la3_f = 0;
				$per_debut_min = $_POST['per_debut_min']; $per_debut_min = htmlspecialchars($per_debut_min);
				$per_debut_max = $_POST['per_debut_max']; $per_debut_max = htmlspecialchars($per_debut_max);
				$per_fin_min = $_POST['per_fin_min']; $per_fin_min = htmlspecialchars($per_fin_min);
				$per_fin_max = $_POST['per_fin_max']; $per_fin_max = htmlspecialchars($per_fin_max);
				if (isset($_POST['a'])) $a = $_POST['a']; else $a = 0;
				if (isset($_POST['c'])) $c = $_POST['c']; else $c = 0;
				if (isset($_POST['d'])) $d = $_POST['d']; else $d = 0;
				if (isset($_POST['r'])) $r = $_POST['r']; else $r = 0;

				// créons la nouvelle periode
				mysql_query("INSERT INTO periodes (id, minDebut, maxDebut, minFin, maxFin) VALUES (NULL,'$per_debut_min','$per_debut_max','$per_fin_min','$per_fin_max')");
				ajouter_fichier_log($fichier_log_sql, "INSERT INTO periodes (id, minDebut, maxDebut, minFin, maxFin) VALUES (NULL,'$per_debut_min','$per_debut_max','$per_fin_min','$per_fin_max')", "D");
				$sql_per = mysql_fetch_assoc(mysql_query("SELECT id FROM periodes order by id desc limit 0,1"));
				$id_per = $sql_per['id'];

				// créons la nouvelle personne
				$nom1noaccent = stripAccentsLower($nom1_f);
				$nom2noaccent = stripAccentsLower($nom2_f);
				$nom3noaccent = stripAccentsLower($nom3_f);
				$prenom1noaccent = stripAccentsLower($prenom1_f);
				$prenom2noaccent = stripAccentsLower($prenom2_f);
				$req_sql = "INSERT INTO personnes (id, de1, la1, nom1, de2, la2, nom2, de3, la3, nom3, prenom1, prenom2, nom1noaccent, nom2noaccent, nom3noaccent, prenom1noaccent, prenom2noaccent, periode) VALUES (NULL, '$de1_f','$la1_f','$nom1_f', '$de2_f','$la2_f','$nom2_f', '$de3_f','$la3_f','$nom3_f','$prenom1_f','$prenom2_f', '$nom1noaccent', '$nom2noaccent', '$nom3noaccent', '$prenom1noaccent', '$prenom2noaccent','$id_per') ";
				mysql_query($req_sql);
				ajouter_fichier_log($fichier_log_sql, $req_sql, "D");
				$sql_pers = mysql_fetch_assoc(mysql_query("SELECT id FROM personnes order by id desc limit 0,1"));
				$id_pers = $sql_pers['id'];
				
				/* je mets à jour les actes, relations, les conditions, (mentions sur relation effacée) et les periodes (au passage) */
				// les actes
				if (!is_numeric($a)){
					foreach($a as $id_acte){
						$sql_acte = mysql_fetch_assoc(mysql_query("SELECT * FROM actes WHERE id_acte='$id_acte'"));
						if ($sql_acte['epoux'] == $id_old) {
							mysql_query("UPDATE actes SET epoux='$id_pers' WHERE id_acte='$id_acte'");
							ajouter_fichier_log($fichier_log_sql, "UPDATE actes SET epoux='$id_pers' WHERE id_acte='$id_acte'", "D");
							maj_id_xml_diss($id_old, $id_pers, $id_acte, 1);
						}
						if ($sql_acte['epouse'] == $id_old) {
							mysql_query("UPDATE actes SET epouse='$id_pers' WHERE id_acte='$id_acte'");					
							ajouter_fichier_log($fichier_log_sql, "UPDATE actes SET epouse='$id_pers' WHERE id_acte='$id_acte'", "D");
							maj_id_xml_diss($id_old, $id_pers, NULL, $id_acte, 2);
						}
					}
				}
				// les conditions
				if (!is_numeric($c)){
					foreach($c as $id_cond){
						mysql_query("UPDATE cond SET id_personne='$id_pers' WHERE id='$id_cond'");	
						ajouter_fichier_log($fichier_log_sql, "UPDATE cond SET id_personne='$id_pers' WHERE id='$id_cond'", "D");	
					}
				}				
				// les relations
				$autre_parent = 0; 
				$flag_parent = false;
				if (!is_numeric($r)){
					foreach($r as $id_r){
							$sql_r = mysql_fetch_assoc(mysql_query("SELECT * FROM relations WHERE id='$id_r'"));
							if ($sql_r['personne1'] == $id_old)	{
								$sql_autre_rel = $sql_r['personne2'];
								mysql_query("UPDATE relations SET personne1='$id_pers' WHERE id='$id_r'");
								ajouter_fichier_log($fichier_log_sql, "UPDATE relations SET personne1='$id_pers' WHERE id='$id_r'", "D");
								if ($sql_r['type'] == 1 or $sql_r['type'] == 2){$autre_parent = $sql_r['personne2']; $flag_parent = true;}
							}
							if ($sql_r['personne2'] == $id_old) {
								$sql_autre_rel = $sql_r['personne1'];
								mysql_query("UPDATE relations SET personne2='$id_pers' WHERE id='$id_r'");					
								ajouter_fichier_log($fichier_log_sql, "UPDATE relations SET personne2='$id_pers' WHERE id='$id_r'", "D");
								if ($sql_r['type'] == 1 or $sql_r['type'] == 2){$autre_parent = $sql_r['personne1']; $flag_parent = true;}
							}
							$id_rer = $sql_r['id'];
							$sql_rel_acte_r = mysql_fetch_assoc(mysql_query("SELECT * FROM mentions WHERE relation='$id_rer'"));
							$sql_acter = $sql_rel_acte_r['acte'];
							$type_r = $sql_r['type'];
							if ($type_r == 1 or $type_r == 2 and ($id_old != $sql_acter['epoux'] and $id_old != $sql_acter['epouse'])) $type_r = 6;
							maj_id_xml_diss($id_old, $id_pers, $sql_autre_rel, $sql_acter, $type_r);
					}
				}
				
				// cas particulier père_fils, mère-fille
				$sql_re_a = mysql_query("SELECT * FROM relations WHERE personne1='$id_old' and personne2='$id_old'");
				while ($sql_re_b = mysql_fetch_assoc($sql_re_a)){
					$id_re_a = $sql_re_b['id'];
					mysql_query("UPDATE relations SET personne2='$id_pers' WHERE id='$id_re_a'");
					ajouter_fichier_log($fichier_log_sql, "UPDATE relations SET personne2='$id_pers' WHERE id='$id_r'", "D");
				}
				if ($flag_parent){
					$sql_upd_parent = mysql_query("SELECT * FROM relations WHERE (personne1='$id_old' or personne2='$id_old') and (personne1='$autre_parent' or personne2='$autre_parent') and (type='1' or type='2')");	
					$sql_upd_parent2 = mysql_fetch_assoc($sql_upd_parent);
					$id_upd_p = $sql_upd_parent2['id'];
					if ($sql_upd_parent2['personne1'] == $id_old) {
						mysql_query("UPDATE relations SET personne1='$id_pers' WHERE id='$id_upd_p'");
						ajouter_fichier_log($fichier_log_sql, "UPDATE relations SET personne1='$id_pers' WHERE id='$id_upd_p'", "D");
					}
					if ($sql_upd_parent2['personne2'] == $id_old) {
						mysql_query("UPDATE relations SET personne2='$id_pers' WHERE id='$id_upd_p'");
						ajouter_fichier_log($fichier_log_sql, "UPDATE relations SET personne2='$id_pers' WHERE id='$id_upd_p'", "D");
					}
				}
				
				// puis j'affiche le résultat
				$pers3 = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_pers'"));
				affiche_pers($pers3);
			}
		}
	?>

</div>

<?php affiche_legend(); ?>
<?php 
fclose($fichier_log_sql);
mysql_close();
include("../includes/footer.php"); 
?>