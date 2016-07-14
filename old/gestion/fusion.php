<?php 
include("../includes/header.php"); 
include("../includes/restriction.php");
restriction($compte, 1);
include("../info/parametre.php");
include("../includes/fonctions_all.php");

change_title("Fusion");
?>
<h1>FUSIONNER DEUX PERSONNES</h1>

<div class="form_form">
<form method="get" name="form_table" id="form_table" action="">
   	<select name="p1">
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
    <select name="p2">
       	<option value="" selected class="italique">Sélectionnez un identifiant</option>
       	<?php
			
			$sql2 = mysql_query("SELECT * FROM personnes");
			while ($line2 = mysql_fetch_assoc($sql2)){
				echo '<option value="'.$line2['id'].'">'.$line2['id'];
				if (personneVide($line2)) echo ' - (empty)';
				else echo ' - '.$line2['prenom1'].' <span class="en_maj">'.$line2['nom1'].'<span>';
				echo '</option>';	
			}
			
		?>
	</select>
    <input type="submit" value="ok" />
</form>
</div>


<div class="affiche_table">
	<?php
		if (isset($_REQUEST['p1']) and isset($_REQUEST['p2'])){
			$id_p1 = htmlspecialchars(mysql_real_escape_string($_REQUEST['p1']));
			$id_p2 = htmlspecialchars(mysql_real_escape_string($_REQUEST['p2']));
			if ($id_p1 != $id_p2){
				echo '<h2>RESULTATS :</h2> ';
				$pers1 = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_p1'"));
				affiche_pers($pers1);
				$pers2 = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_p2'"));
				affiche_pers($pers2);
	
	?>
            <h2>FUSIONNER EN :</h2>
            <form name="form_fusion" method="post" action="">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>Id</td>
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
                        <td><input name="id" type="text" value="<?php echo $pers1['id'] ?>" readonly size="8"/></td>
                        <td><input name="prenom1" type="text" value="<?php echo $pers1['prenom1'] ?>" maxlength="100" /></td>
                        <td><input name="prenom2" type="text" value="<?php echo $pers1['prenom2'] ?>" maxlength="100" /></td>
                        <td><input name="de1" type="checkbox" <?php if ($pers1['de1']==1) echo "checked"; ?> value=1></td>
                        <td><input name="la1" type="checkbox" <?php if ($pers1['la1']==1) echo "checked"; ?> value=1></td>
                        <td><input name="nom1" type="text" value="<?php echo $pers1['nom1'] ?>" maxlength="100" /></td>
                        <td><input name="de2" type="checkbox" <?php if ($pers1['de2']==1) echo "checked"; ?> value=1></td>
                        <td><input name="la2" type="checkbox" <?php if ($pers1['la2']==1) echo "checked"; ?> value=1></td>
                        <td><input name="nom2" type="text" value="<?php echo $pers1['nom2'] ?>" maxlength="100" /></td>
                        <td><input name="de3" type="checkbox" <?php if ($pers1['de3']==1) echo "checked"; ?> value=1></td>
                        <td><input name="la3" type="checkbox" <?php if ($pers1['la3']==1) echo "checked"; ?> value=1></td>
                        <td><input name="nom3" type="text" value="<?php echo $pers1['nom3'] ?>" maxlength="100" /></td>
                    </tr>
                    <tr>
                        <td colspan="11"><input type="hidden" name="hide" value="yes" />
                        <input type="hidden" name="id_other" value="<?php echo $id_p2; ?>" />
                        <input type="hidden" name="id_periode" value="<?php echo $pers1['periode']; ?>" />
                        <input type="hidden" name="id_per_maj" value="<?php echo $pers2['periode']; ?>" />
                        </td>
                        <td align="right"><input type="submit" value="Valider"/></td>
                    </tr>
                </table>
            </form>
    <?php	
			}
		}
	?>
    <?php
		if (isset($_REQUEST['hide'])){
			$hide = htmlspecialchars(mysql_real_escape_string($_REQUEST['hide']));
			if ($hide == "yes"){
				echo "<h2>RESULTAT DE LA FUSION :</h2>";
				change_title('Fusion ' . nom_court($_REQUEST));
				$id_f = $_REQUEST['id'];
				$prenom1_f = $_REQUEST['prenom1']; $prenom1_f = htmlspecialchars($prenom1_f); $prenom1_f_noaccent = stripAccentsLower(htmlspecialchars($prenom1_f));
				$prenom2_f = $_REQUEST['prenom2'];  $prenom2_f = htmlspecialchars($prenom2_f); $prenom2_f_noaccent = stripAccentsLower(htmlspecialchars($prenom2_f));
				$nom1_f = $_REQUEST['nom1']; $nom1_f = htmlspecialchars($nom1_f); $nom1_f_noaccent = stripAccentsLower(htmlspecialchars($nom1_f));
				$nom2_f = $_REQUEST['nom2']; $nom2_f = htmlspecialchars($nom2_f); $nom2_f_noaccent = stripAccentsLower(htmlspecialchars($nom2_f));
				$nom3_f = $_REQUEST['nom3']; $nom3_f = htmlspecialchars($nom3_f); $nom3_f_noaccent = stripAccentsLower(htmlspecialchars($nom3_f));
				if (isset($_REQUEST['de1'])) {$de1_f = $_REQUEST['de1'];} else $de1_f = 0;
				if (isset($_REQUEST['de2'])) {$de2_f = $_REQUEST['de2'];} else $de2_f = 0;
				if (isset($_REQUEST['de3'])) {$de3_f = $_REQUEST['de3'];} else $de3_f = 0;
				if (isset($_REQUEST['la1'])) {$la1_f = $_REQUEST['la1'];} else $la1_f = 0;
				if (isset($_REQUEST['la2'])) {$la2_f = $_REQUEST['la2'];} else $la2_f = 0;
				if (isset($_REQUEST['la3'])) {$la3_f = $_REQUEST['la3'];} else $la3_f = 0;
				$id_other = $_REQUEST['id_other'];
				$id_periode = $_REQUEST['id_periode'];
				$id_per_maj = $_REQUEST['id_per_maj'];
				// je mets à jour l'id dans le xml pour chacun des actes concernés
				maj_id_xml($id_other, $id_f);				
				// je mets à jour la personne que je garde
				$req_sql = "UPDATE personnes SET de1='$de1_f', la1='$la1_f', nom1='$nom1_f', de2='$de2_f', la2='$la2_f', nom2='$nom2_f', de3='$de3_f', la3='$la3_f', nom3='$nom3_f', prenom1='$prenom1_f', prenom2='$prenom2_f', nom1noaccent='$nom1_f_noaccent', nom2noaccent='$nom2_f_noaccent', nom3noaccent='$nom3_f_noaccent', prenom1noaccent='$prenom1_f_noaccent', prenom2noaccent='$prenom1_f_noaccent' WHERE id='$id_f'";
				mysql_query($req_sql);
				ajouter_fichier_log($fichier_log_sql, $req_sql, "F");
				// je supprime le "doublon"
				mysql_query("DELETE FROM personnes WHERE id='$id_other'");
				ajouter_fichier_log($fichier_log_sql, "DELETE FROM personnes WHERE id='$id_other'", "F");
				
				// je mets à jour les actes, relations, les conditions, (mentions sur relation effacée) et les periodes (au passage)
				// les actes
				$sql_maj_actes = mysql_query("SELECT * FROM actes WHERE epoux='$id_other' or epouse='$id_other'");
				while($r1 = mysql_fetch_assoc($sql_maj_actes)){
					$id_acte = $r1['id_acte'];
					if ($r1['epoux'] == $id_other) {
						$req_sql = "UPDATE actes SET epoux='$id_f' WHERE id_acte='$id_acte'";
						mysql_query($req_sql);
						ajouter_fichier_log($fichier_log_sql, $req_sql, "F");
					}
					if ($r1['epouse'] == $id_other) {
						$req_sql = "UPDATE actes SET epouse='$id_f' WHERE id_acte='$id_acte'";
						mysql_query($req_sql);
						ajouter_fichier_log($fichier_log_sql, $req_sql, "F");
					}

				}
				// les conditions
				$sql_maj_cond = mysql_query("SELECT id,id_personne FROM cond WHERE id_personne='$id_other'");
				while($r2 = mysql_fetch_assoc($sql_maj_cond)){
					$id_cond = $r2['id'];
					mysql_query("UPDATE cond SET id_personne='$id_f' WHERE id='$id_cond'");
					ajouter_fichier_log($fichier_log_sql, "UPDATE cond SET id_personne='$id_f' WHERE id='$id_cond'", "F");
				}
				// les relations
				$sql_maj_rel = mysql_query("SELECT * FROM relations WHERE personne1='$id_other' or personne2='$id_other'");
				while($r3 = mysql_fetch_assoc($sql_maj_rel)){
					$id_rel = $r3['id'];
					$type_autre = $r3['type'];
					if ($r3['personne1'] == $id_other) {
						mysql_query("UPDATE relations SET personne1='$id_f' WHERE id='$id_rel'");
						ajouter_fichier_log($fichier_log_sql, "UPDATE relations SET personne1='$id_f' WHERE id='$id_rel'", "F");
						// faut supprimer les relations doublons
						$id_autre = $r3['personne2'];
						$r4_sql = mysql_query("SELECT * FROM relations WHERE personne1='$id_f' and personne2='$id_autre' and id!='$id_rel' and type='$type_autre'");
						if (mysql_num_rows($r4_sql)>0){
							while ($r4 = mysql_fetch_assoc($r4_sql)){
								maj_periode2($r4['periode'],$r3['periode']);
								$id_rel_suppr = $r4['id'];
								mysql_query("DELETE FROM relations WHERE id='$id_rel_suppr'");
								ajouter_fichier_log($fichier_log_sql, "DELETE FROM relations WHERE id='$id_rel_suppr'", "F");
								mysql_query("UPDATE cond SET relation='$id_rel' WHERE relation='$id_rel_suppr'");
								ajouter_fichier_log($fichier_log_sql, "UPDATE cond SET relation='$id_rel' WHERE relation='$id_rel_suppr'", "F");
							}
						}
					}
					if ($r3['personne2'] == $id_other) {
						mysql_query("UPDATE relations SET personne2='$id_f' WHERE id='$id_rel'");
						ajouter_fichier_log($fichier_log_sql, "UPDATE relations SET personne2='$id_f' WHERE id='$id_rel'", "F");
						// faut supprimer les relations doublons
						$id_autre = $r3['personne1'];
						$r5_sql = mysql_query("SELECT * FROM relations WHERE personne1='$id_autre' and personne2='$id_f' and id!='$id_rel' and type='$type_autre'");
						if (mysql_num_rows($r5_sql)>0){
							while ($r5 = mysql_fetch_assoc($r5_sql)){
								maj_periode2($r5['periode'],$r3['periode']);
								$id_rel_suppr = $r5['id'];
								mysql_query("DELETE FROM relations WHERE id='$id_rel_suppr'");
								ajouter_fichier_log($fichier_log_sql, "DELETE FROM relations WHERE id='$id_rel_suppr'", "F");
								mysql_query("UPDATE cond SET relation='$id_rel' WHERE relation='$id_rel_suppr'");
								ajouter_fichier_log($fichier_log_sql, "UPDATE cond SET relation='$id_rel' WHERE relation='$id_rel_suppr'", "F");
							}
						}
					}
				}
				
				//maj periode de la personne
				maj_periode2($id_per_maj, $id_periode);
				
				// puis j'affiche le résultat
				$pers3 = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_f'"));
				affiche_pers($pers3);
			}
		}
	?>

</div>

<?php 
fclose($fichier_log_sql);
mysql_close();
include("../includes/footer.php"); 
?>