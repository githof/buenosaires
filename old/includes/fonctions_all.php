<?php

/*
  CP 01/11/14
  J'ai mis ici cette fonction et l'ouverture du fichier, qui avant
  étaient dans fonctions_compilation
 */
$fichier_log_sql = fopen("../data/logSql.bsql", "a");
function ajouter_fichier_log($string_sql){
	$date_heure = date_heure();
	$compte = $GLOBALS['compte'];
	$string_sql = preg_replace("/(\r\n|\n|\r)/", " ", $date_heure." ".$compte->pseudo." A ".$string_sql);
	fwrite($GLOBALS['fichier_log_sql'], $string_sql."\n");	
}
function date_heure(){
	return date("d-m-Y H:i:s");	
}

/*
  CP 01/11/14
*/
function requete_lecture($req, $msg_operation)
{
  if(! (($res = mysql_query($req))))
    {
      echo "<p>échec $msg_operation</p>";
      echo "<code><pre>" . $req . "</pre></code>";
      throw new Exception("mysql_query") ;
    }
  return $res;
}

function requete_ecriture($req, $msg_operation)
{
  $res = requete_lecture($req, $msg_operation);
  ajouter_fichier_log($req);
  return $res;
}

function test_bd()
{
  $query = "SELECT nom1 FROM personnes WHERE id='1'";
  $res = requete_lecture($query, "SELECT");
  $res = mysql_fetch_assoc($res);
  echo "<h3>Test</h3><p>".$res['nom1']."</p>";
}

function change_title($title)
{
  $js = 'document.title = "'
    .$title
    .'";';
  echo "<script>$js</script>\n";
}

/*
	Affiche la date sous forme human_readable
*/
function affiche_date($id_date){
	$res = mysql_fetch_assoc(mysql_query("SELECT * FROM periodes WHERE id='$id_date'"));
	if ($res['minDebut'] == $res['maxDebut'] and $res['maxDebut'] == $res['minFin'] and $res['minFin'] == $res['maxFin']){
		$laDate = preg_split("/-/",$res['minDebut']);
		$jour = $laDate[2];
		$mois = $laDate[1];
		$annee = $laDate[0];
		return ''.$jour.'-'.$mois.'-'.$annee.'';		
	}
	else if ($res['minDebut'] == $res['minFin'] and $res['maxDebut'] == $res['maxFin']) {
		$debut = preg_split("/-/",$res['minDebut']);
			$dj = $debut[2];
			$dm = $debut[1];
			$da = $debut[0];
		$fin = preg_split("/-/",$res['maxDebut']);
			$fj = $fin[2];
			$fm = $fin[1];
			$fa = $fin[0];
		return ''.$dj.'-'.$dm.'-'.$da.' to '.$fj.'-'.$fm.'-'.$fa;
	}
	else if ($res['minDebut'] == $res['maxDebut'] and $res['minFin'] == $res['maxFin']) {
		$debut = preg_split("/-/",$res['minDebut']);
			$dj = $debut[2];
			$dm = $debut[1];
			$da = $debut[0];
		$fin = preg_split("/-/",$res['minFin']);
			$fj = $fin[2];
			$fm = $fin[1];
			$fa = $fin[0];
		return ''.$dj.'-'.$dm.'-'.$da.' to '.$fj.'-'.$fm.'-'.$fa;
	}	
	else {
		$d1 = preg_split("/-/",$res['minDebut']);
		$d2 = preg_split("/-/",$res['maxDebut']);
		$d3 = preg_split("/-/",$res['minFin']);
		$d4 = preg_split("/-/",$res['maxFin']);
		$anneDebut = floor(($d1[0] + $d2[0])/2);
		$anneFin = ceil(($d3[0] + $d4[0])/2);
		return ''.$anneeDebut.' to '.$anneeFin;
	}
}

function stripAccentsLower($string){
	return strtolower(suppr_accents($string));
}
function suppr_accents($str, $encoding='utf-8'){
  // transformer les caractères accentués en entités HTML
  $str = htmlentities($str, ENT_NOQUOTES, $encoding);
  // remplacer les entités HTML pour avoir juste le premier caractères non accentués
  // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
  $str = preg_replace('#&([A-Za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
  // Remplacer les ligatures tel que : Œ, Æ ...
  // Exemple "Å"" => "oe"
  $str = preg_replace('#&([A-Za-z]{2})(?:lig);#', '\1', $str);
  // Supprimer tout le reste
  $str = preg_replace('#&[^;]+;#', '', $str);
  return $str;
}

function ucname($texte){
	$resultat = "";
	$tab = preg_split('/ /', $texte);
	foreach($tab as $val){
		$resultat .= "".ucfirst(strtolower($val))." ";
	}
	return trim($resultat);
}

/*
	transforme une date en valeur numérique pour pouvoir la comparer avec une autre date
*/
function transforme_periode($periode){
	$p = preg_split("/-/",$periode);
	return ($p[0]*10000 + $p[1]*100 + $p[2]);	
}

function personneVide($sql_pers){
	if ($sql_pers['nom1'] == "" and $sql_pers['prenom1'] == "") return true;
	return false;	
}

function rechercherPersonne($rech, $pers){
	if (strpos("_".stripAccentsLower($pers['prenom1']), stripAccentsLower($rech)) >= 1 or strpos("_".stripAccentsLower($pers['prenom2']), stripAccentsLower($rech)) >= 1 or strpos("_".stripAccentsLower($pers['nom1']), stripAccentsLower($rech)) >= 1 or strpos("_".stripAccentsLower($pers['nom2']), stripAccentsLower($rech)) >= 1 or strpos("_".stripAccentsLower($pers['nom3']), stripAccentsLower($rech)) >= 1 ) return true;
	return false;	
}

function nom_court($personne){
  if(! (isset($personne['prenom1']) && isset($personne['nom1'])))
    return '';
  $nom = $personne['prenom1'].' '.$personne['nom1'];
  return $nom == ' ' ? '' : $nom;
}

function affiche_pers($pers){
	$affich_date = affiche_date($pers['periode']);
	if ($pers['de1'] == 1) $de1 = "de "; else $de1 = "";
	if ($pers['la1'] == 1) $la1 = "la "; else $la1 = "";
	if ($pers['de2'] == 1) $de2 = "de "; else $de2 = "";
	if ($pers['la2'] == 1) $la2 = "la "; else $la2 = "";
	if ($pers['de3'] == 1) $de3 = "de "; else $de3 = "";
	if ($pers['la3'] == 1) $la3 = "la "; else $la3 = "";
	echo '<a class="p_pers" href="viewPersonne.php?id='.$pers['id'].'">'.$pers['id'].' | '.$pers['prenom1'].' '.$pers['prenom2'].' <span class="en_maj">'.$de1.$la1.$pers['nom1'].' '.$de2.$la2.$pers['nom2'].' '.$de3.$la3.$pers['nom3'].'</span> | '.$affich_date.'</a>';	
	return nom_court($pers);
}

function affiche_pers2($line, $id){
	$flag = false;
	if ($line['personne1'] == $id){
		if ($line['type'] == EPOUSE or $line['type'] == EPOUX) $t = "E";
		else if ($line['type'] == PERE) $t = "P";
		else if ($line['type'] == MERE) $t = "M";
		else if ($line['type'] == TEMOIN) $t = "T";
		$line['personne1'] == $id ? $id_pers = $line['personne2'] : $id_pers = $line['personne1'];
		$pers = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_pers'"));
		$flag = true;
	}
	if ($line['personne2'] == $id and $line['type'] != EPOUX and $line['type'] != EPOUSE){
		if ($line['type'] == PERE) $t = "C";
		else if ($line['type'] == MERE) $t = "C";
		else if ($line['type'] == TEMOIN) $t = "t";
		$line['personne1'] == $id ? $id_pers = $line['personne2'] : $id_pers = $line['personne1'];
		$pers = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_pers'"));
		$flag = true;
	}
	if ($flag){
		if ($pers['de1'] == 1) $de1 = "de "; else $de1 = "";
		if ($pers['la1'] == 1) $la1 = "la "; else $la1 = "";
		if ($pers['de2'] == 1) $de2 = "de "; else $de2 = "";
		if ($pers['la2'] == 1) $la2 = "la "; else $la2 = "";
		if ($pers['de3'] == 1) $de3 = "de "; else $de3 = "";
		if ($pers['la3'] == 1) $la3 = "la "; else $la3 = "";
		$affich_date = affiche_date($pers['periode']);
		$id_rel = $line['id'];
		$sq = mysql_fetch_assoc(mysql_query("SELECT acte FROM mentions WHERE relation='$id_rel'"));
		$num_rel = $line['id'];
		echo '<tr>
		<td>';
		if ($t == "C") echo '&#8659; ';//echo '<img src="./images/fleche_bas2.gif" alt="Child" class="img_perso" />';
		else if ($t == "P" or $t == "M") echo '&#8657; '; //echo '<img src="./images/fleche_haut2.gif" alt="Parent" class="img_perso" />';
		else if ($t == "E") echo '&#8658; '; //echo '<img src="./images/fleche_droit2.gif" alt="Epoux" class="img_perso" />';
		else echo $t;
		echo '</td>
		<td>p'.$pers['id'].'</td>
		<td><a href="../gestion/viewPersonne.php?id='.$pers['id'].'">'.$pers['prenom1'].' '.$pers['prenom2'].'</a></td>
		<td class="en_maj"><a href="../gestion/viewPersonne.php?id='.$pers['id'].'">'.$de1.$la1.$pers['nom1'].' '.$de2.$la2.$pers['nom2'].' '.$de3.$la3.$pers['nom3'].'</a></td>
		<td>'.$affich_date.'</td>
		</tr>
		<tr>
		<td colspan="5" class="gris padding_left">Relation n° r'.$num_rel.' | '.affiche_date($line['periode']).' | Actes : ';
		$acte_sql = mysql_query("SELECT acte FROM mentions WHERE relation='$num_rel'");
		while ($acte_line = mysql_fetch_assoc($acte_sql)){
			echo '<input type="checkbox" name="'.$pers['id'].'" id="'.$pers['id'].'" value="'.$pers['prenom1'].' "/>  <label for="'.$pers['id'].'">';
			echo '<a href="../gestion/viewActes.php?i='.$acte_line['acte'].'"><span class="span_gris">a'.$acte_line['acte'].'</span></a> </label>: ';
		}
		echo '</td</tr>';
	}
}


function affiche_pers3($line, $id, $id_rel){
	$flag = false;
	if ($line['personne1'] == $id){
		if ($line['type'] == EPOUSE or $line['type'] == EPOUX) $t = "E";
		else if ($line['type'] == PERE) $t = "P";
		else if ($line['type'] == MERE) $t = "M";
		else if ($line['type'] == TEMOIN) $t = "T";
		$line['personne1'] == $id ? $id_pers = $line['personne2'] : $id_pers = $line['personne1'];
		$pers = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_pers'"));
		$flag = true;
	}
	if ($line['personne2'] == $id and $line['type'] != EPOUX and $line['type'] != EPOUSE){
		if ($line['type'] == PERE) $t = "C";
		else if ($line['type'] == MERE) $t = "C";
		else if ($line['type'] == TEMOIN) $t = "t";
		$line['personne1'] == $id ? $id_pers = $line['personne2'] : $id_pers = $line['personne1'];
		$pers = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id_pers'"));
		$flag = true;
	}
	if ($flag){
		if ($pers['de1'] == 1) $de1 = "de "; else $de1 = "";
		if ($pers['la1'] == 1) $la1 = "la "; else $la1 = "";
		if ($pers['de2'] == 1) $de2 = "de "; else $de2 = "";
		if ($pers['la2'] == 1) $la2 = "la "; else $la2 = "";
		if ($pers['de3'] == 1) $de3 = "de "; else $de3 = "";
		if ($pers['la3'] == 1) $la3 = "la "; else $la3 = "";
		$affich_date = affiche_date($pers['periode']);
		$id_rel = $line['id'];
		$sq = mysql_fetch_assoc(mysql_query("SELECT acte FROM mentions WHERE relation='$id_rel'"));
		$num_rel = $line['id'];
		echo '<tr><td>';
		if ($pers['id'] != $id) echo '<input type="checkbox" name="r[]" value="'.$id_rel.'" />';	
		echo '</td><td>';
		if ($t == "C") echo '&#8659; ';//echo '<img src="./images/fleche_bas2.gif" alt="Child" class="img_perso" />';
		else if ($t == "P" or $t == "M") echo '&#8657; '; //echo '<img src="./images/fleche_haut2.gif" alt="Parent" class="img_perso" />';
		else if ($t == "E") echo '&#8658; '; //echo '<img src="./images/fleche_droit2.gif" alt="Epoux" class="img_perso" />';
		else echo $t;
		echo '</td>
		<td>p'.$pers['id'].'</td>
		<td><a href="../gestion/viewPersonne.php?id='.$pers['id'].'">'.$pers['prenom1'].' '.$pers['prenom2'].'</a></td>
		<td class="en_maj"><a href="../gestion/viewPersonne.php?id='.$pers['id'].'">'.$de1.$la1.$pers['nom1'].' '.$de2.$la2.$pers['nom2'].' '.$de3.$la3.$pers['nom3'].'</a></td>
		<td>'.$affich_date.'</td>
		</tr><tr>
		<td colspan="7" class="gris marge_bas padding_bas_pers">Relation n° r'.$num_rel.' | '.affiche_date($line['periode']).' | Actes ';
		$acte_sql = mysql_query("SELECT acte FROM mentions WHERE relation='$num_rel'");
		while ($acte_line = mysql_fetch_assoc($acte_sql)){
			echo ': <a href="../gestion/viewActes.php?i='.$acte_line['acte'].'"><span class="span_gris">a'.$acte_line['acte'].'</span></a>';
		}
		echo '</td</tr>';
	}
}


function bonnePeriode($periode_id, $dp, $fp){
	$d = preg_split("/-/",$dp);
	$f = preg_split("/-/",$fp);	
	$td = $d[2]*10000 + $d[1]*100 + $d[0];
	$tf = $f[2]*10000 + $f[1]*100 + $f[0];
	$periode = mysql_fetch_assoc(mysql_query("SELECT * FROM periodes WHERE id='$periode_id'"));
	$p_mind = transforme_periode($periode['minDebut']);
	$p_maxd = transforme_periode($periode['maxDebut']);
	$p_minf = transforme_periode($periode['minFin']);
	$p_maxf = transforme_periode($periode['maxFin']);
	if (($td <= $p_maxd and $p_maxd <= $tf) or ($td <= $p_mind and $p_mind <= $tf) or ($td <= $p_maxf and $p_maxf <= $tf) or ($td <= $p_minf and $p_minf <= $tf)) return true;
	return false;	
}

function maj_periode2($id_per_maj, $id_periode){
		$per_maj = mysql_fetch_assoc(mysql_query("SELECT * FROM periodes WHERE id='$id_per_maj'"));
		$sql = mysql_fetch_assoc(mysql_query("SELECT * FROM periodes WHERE id='$id_periode'"));
		$maj_minDebut = $sql['minDebut'];
		$maj_maxDebut = $sql['maxDebut'];
		$maj_minFin = $sql['minFin'];
		$maj_maxFin = $sql['maxFin'];
		if ($maj_minDebut != "00-00-0000"){
			if (transforme_periode($per_maj['minDebut']) < transforme_periode($maj_minDebut)) $maj_minDebut = $per_maj['minDebut'];
			if (transforme_periode($per_maj['maxDebut']) < transforme_periode($maj_maxDebut)) $maj_maxDebut = $per_maj['maxDebut'];
			if (transforme_periode($per_maj['minFin']) > transforme_periode($maj_minFin)) $maj_minFin = $per_maj['minFin'];
			if (transforme_periode($per_maj['maxFin']) > transforme_periode($maj_maxFin)) $maj_maxFin = $per_maj['maxFin'];
			mysql_query("UPDATE periodes SET minDebut='$maj_minDebut',maxDebut='$maj_maxDebut',minFin='$maj_minFin',maxFin='$maj_maxFin' WHERE id='$id_periode'");
		}
	}	

function affiche_legend(){
	echo '<div class="legend_person">
	<p>L&eacute;gende</p>
	<ul>
    	<li>&#8658;<!--<img src="./images/fleche_droit2.gif" alt="Epoux" class="img_perso" />--> : Epoux-se </li>
        <li>&#8657;<!--<img src="./images/fleche_haut2.gif" alt="Pere" class="img_perso" />--> : P&eacute;re</li>
        <li>&#8657;<!--<img src="./images/fleche_haut2.gif" alt="Mere" class="img_perso" />--> : M&eacute;re</li>
        <li>&#8659;<!-- <img src="./images/fleche_bas2.gif" alt="Child" class="img_perso" />--> : Enfant (children)</li>
        <li>T : T&eacute;moin</li>
        <li>t : Married person<br />La personne<br />principale est<br />témoin</li>
    </ul>
	</div>';	
}

function acte_of_pers($id){
	$pile = array();
	$sql_p_a = mysql_query("SELECT id FROM relations WHERE personne1='$id' or personne2='$id'");
	while ($r_p_a = mysql_fetch_assoc($sql_p_a)){
		$rel_r_a = $r_p_a['id'];
		$sql_m_a = mysql_fetch_assoc(mysql_query("SELECT acte FROM mentions WHERE relation='$rel_r_a'"));
		if (!in_array($sql_m_a['acte'], $pile)) array_push($pile, $sql_m_a['acte']);
	}
	return $pile;	
}

function update_if($node, $old, $new){
    if(isset($node)) {
        $attr = $node->attributes();
        if (isset($attr["id"]) and $attr["id"] == $old){
            $attr["id"] = $new;
		}
        return true;
    }
    return false;
}

/*
	Fonction qui met à jour les bout de texte XML dans la bdd 
	Met à jour les identifiants des personnes lors de l'opération de fusion
*/
function maj_id_xml_diss($id_old, $id_new, $id_autre_rel, $id_acte, $type_pers){
	$sql = mysql_query("SELECT contenu FROM actes_contenu WHERE id_acte='$id_acte'");
	$c = mysql_fetch_assoc($sql);
	$xml = simplexml_load_string("<?xml version=\"1.0\" encoding=\"UTF-8\"?><document><ACTES>".$c['contenu']."</ACTES></document>");
	$acte_xml = $xml->ACTES->ACTE;
	// si type = 5 (témoin) je n'en prends qu'un des deux
	// si type = 1 ou 2 je modifie l'époux ou l'épouse
	// si type = 3 ou 4, je modifie le parent
	$compt = 0;
	if ($type_pers == 1 or $type_pers == 2){
		update_if($acte_xml->epoux, $id_old, $id_new);
		update_if($acte_xml->epouse, $id_old, $id_new);
	}
	else if ($type_pers == 3 or $type_pers == 4 or $type_pers == 6){
		update_if($acte_xml->epoux->pere, $id_old, $id_new);
		update_if($acte_xml->epoux->mere, $id_old, $id_new);
		update_if($acte_xml->epouse->pere, $id_old, $id_new);
		update_if($acte_xml->epouse->mere, $id_old, $id_new);
	}
	else if ($type_pers == 5){
		foreach($acte_xml->temoins as $temoin){
			if ($compt == 0){ // on ne le fait qu'une fois.
        		if (update_if($temoin, $id_old, $id_new)) $compt++;
			}
		}
	}
	$req_sql = "UPDATE actes_contenu SET contenu='".$acte_xml->asXML()."' WHERE id_acte='$id_acte'";
	mysql_query($req_sql);		
	ajouter_fichier_log("", $req_sql, "D");
}
    
/*
	Fonction qui met à jour les bout de texte XML dans la bdd 
	Met à jour les identifiants des personnes lors de l'opération de fusion
*/
function maj_id_xml($id_old, $id_new){
	$actes = acte_of_pers($id_old);
	foreach($actes as $acte){
		$sql = mysql_query("SELECT contenu FROM actes_contenu WHERE id_acte='$acte'");
		$c = mysql_fetch_assoc($sql);
		$xml = simplexml_load_string("<?xml version=\"1.0\" encoding=\"UTF-8\"?><document><ACTES>".$c['contenu']."</ACTES></document>");
		$acte_xml = $xml->ACTES->ACTE;
		
        if (update_if($acte_xml->epoux, $id_old, $id_new)){
            update_if($acte_xml->epoux->pere, $id_old, $id_new);
            update_if($acte_xml->epoux->mere, $id_old, $id_new);
		}
        if (update_if($acte_xml->epouse, $id_old, $id_new)){
            update_if($acte_xml->epouse->pere, $id_old, $id_new);
            update_if($acte_xml->epouse->mere, $id_old, $id_new);
        }
        if (isset($acte_xml->temoins)){
			foreach($acte_xml->temoins as $temoin){
                update_if($temoin, $id_old, $id_new);
			}
		}
		
		$req_sql = "UPDATE actes_contenu SET contenu='".$acte_xml->asXML()."' WHERE id_acte='$acte'";
		mysql_query($req_sql);		
		ajouter_fichier_log("", $req_sql, "F");
	}
}
/*
  Alpha
	la fonction affiche_pers_with_check appelle la fonction affiche_pers mais en affichant au préalable un
					<input type="checkbox" name="personnes[]" value=l'id de la personne>
								'id de la personne se trouve dans $pers['id']
 */
function affiche_pers_with_check($pers){
  /*
	echo '<input type="checkbox" name="'.$pers['id'].'" id="'.$pers['id'].'" value="'.$pers['nom1'].' '. $pers['prenom1'].' "/> 
						<label for="'.$pers['id'].'">'.affiche_pers($pers).'</label><br />';
  */
  affiche_pers($pers);
}

function fully_named($personne){
  return isset($personne->nom) and isset($personne->prenom);
}

/* 
   CP 09/08/2014
   isSet($attr["num"]) uniquement
 */
function ok_pour_prendre_acte($acte){
  $attr = $acte->attributes();
  return isSet($attr["num"]);
  /*
  return
    (
     count($acte->children()) > 0
     and isset($acte->epoux)
     and isset($acte->epouse)
     and isset($acte->date)
     and (fully_named($acte->epoux)
	  or fully_named($acte->epouse))
     );
  */
}

?>
