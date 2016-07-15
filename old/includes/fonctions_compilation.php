<?php 
include_once("fonctions_all.php");
include_once("periode.class.php");

	class Personne{

		var $prenom;
		var $nom;
		var $de;
		var $la;
		var $prenomnoaccent;
		var $nomnoaccent;
		
		function Personne($pers){
			for ($i = 0; $i < 3; $i++) {
				$this->de[$i] = 0; $this->la[$i] = 0;
				if (isset($pers->nom[$i])){
					$this->nom[$i] = ucname($pers->nom[$i]);
					$this->nomnoaccent[$i] = stripAccentsLower($pers->nom[$i]);
					$attributs = $pers->nom[$i]->attributes();
					if (isset($attributs['de'])) $this->de[$i] = 1; 
					if (isset($attributs['la'])) $this->la[$i] = 1; 
				}
				else {$this->nom[$i] = "";$this->nomnoaccent[$i] = "";}
				if (isset($pers->prenom[$i])){
					$this->prenom[$i] = ucname($pers->prenom[$i]);
					$this->prenomnoaccent[$i] = stripAccentsLower($pers->prenom[$i]);
				}
				else {$this->prenom[$i] = "";$this->prenomnoaccent[$i] = "";}

			}
		}
		
	}

function acte_connu($num)
{
  $req = "SELECT id_acte FROM actes WHERE id_acte = '$num'";
  $res = requete_lecture($req, "recherche acte");
  return (mysql_num_rows($res) > 0);
}
	
/*
  Cette fonction ajoute
  - le texte de l'acte dans actes_contenu
  dans tous les cas
  - les époux et l'acte dans actes
  SI l'epoux *et* l'epouse sont complètement nommés
  Si ($only_new),
  l'acte n'est ajouté que s'il n'est pas déjà dans la table actes
*/
function add_acte($acte, $only_new = false){
  /*
    CP 09/08/14
    acte non balisé
    CP 01/11/14
    correction bug add_texteActe
  */
  $acte_attr = $acte->attributes();
  $acte_num = $acte_attr['num'];

  if (! isSet($acte_num))
    throw new Exception('Un acte ou des actes'
			.'n\'ont pas de numéro. '
			.'Ils n\'ont pas été pris en compte.');

  if($only_new and acte_connu($acte_num))
    return;

  if(isSet($acte->epoux)
     and isSet($acte->epouse)
     and (fully_named($acte->epoux)
	  or fully_named($acte->epouse)) )
    {
      $id_epoux = existe_pers($acte->epoux, $acte);
      $id_epouse = existe_pers($acte->epouse, $acte);
      if ($id_epoux == 0 and $id_epouse == 0){
	$id_epoux = add_pers($acte->epoux, $acte);
	$id_epouse = add_pers($acte->epouse, $acte);	
	add_acte_date($acte, $acte_num, $id_epoux, $id_epouse);
	// puis on rajoute les relations entre les époux/épouse
	add_mention(add_relation($id_epoux, $id_epouse, EPOUSE, $acte), $acte_num, $acte);
	add_mention(add_relation($id_epouse, $id_epoux, EPOUX, $acte), $acte_num, $acte);	
      }
      // puis les relations avec la famille
      add_familles($acte, $acte_num, $id_epoux, $id_epouse);
    }
  add_texteActe($acte, $acte_num, true);
}
 	
	/*
		Vérifie si l'attribut id d'une personne existe, et si oui return cet id, sinon retourne 0
		Et si la personne existe déjà, met à jour cette personne avec les éléments du XML (qui sont
		a priori plus récents)
	*/
	function existe_pers($pers, $acte){
		$attri = $pers->attributes();
		$id = $attri["id"];
		if(! isSet($id))
		  return 0;
		maj_pers($pers, $id, $acte);
		return $id;
	}
	
	/*
		Fonction qui met à jour une personne
	*/
	function maj_pers($pers, $id, $acte){
		$objDate = new LirePeriode($acte);
		add_condition($id, $pers, $acte, $objDate);
		$p = new Personne($pers);
		$req_sql = "UPDATE personnes SET de1='".$p->de[0]."', la1='".$p->la[0]."', nom1='".mysql_real_escape_string($p->nom[0])."', de2='".$p->de[1]."', la2='".$p->la[1]."', nom2='".mysql_real_escape_string($p->nom[1])."', de3='".$p->de[2]."', la3='".$p->la[2]."', nom3='".mysql_real_escape_string($p->nom[2])."', prenom1='".mysql_real_escape_string($p->prenom[0])."', prenom2='".mysql_real_escape_string($p->prenom[1])."', nom1noaccent='".mysql_real_escape_string($p->nomnoaccent[0])."', nom2noaccent='".mysql_real_escape_string($p->nomnoaccent[1])."', nom3noaccent='".mysql_real_escape_string($p->nomnoaccent[2])."', prenom1noaccent='".mysql_real_escape_string($p->prenomnoaccent[0])."', prenom2noaccent='".mysql_real_escape_string($p->prenomnoaccent[1])."' WHERE id='$id'";
		mysql_query($req_sql);		
		ajouter_fichier_log($req_sql);		
	}
	
	/*
		Ajoute le texte de l'acte brut dans la base de donnée
	*/
	function add_texteActe($acte, $acte_num, $flag_new){
	  /*
	    CP 09/08/2014
	    - Le flag n'est plus utilisé, je le laisse en param
	    pour éviter les problèmes pour le moment
	    CP 01/11/2014
	    Acte test ok
    <ACTE num="14006">14024) 1801: Antonio ANDRADE, con Pilar CABREL. (f. 174v).</ACTE>
	   */
		$texte = mysql_real_escape_string($acte->asXML());
		$req_sql = "INSERT INTO actes_contenu (id_acte, contenu)"
		  . " VALUES ('$acte_num', '$texte')"
		  . " ON DUPLICATE KEY UPDATE contenu='$texte'";
		/*
		if ($flag_new){
			$req_sql = "INSERT INTO actes_contenu (id_acte, contenu) VALUES ('$acte_num', '$texte')";
		}
		else $req_sql = "UPDATE actes_contenu SET contenu='$texte' WHERE id_acte='$acte_num'";
		*/
		requete_ecriture($req_sql, "INSERT/UPDATE acte");
	}
	
	/*
		Ces fonctions ajoutent les membres de la famille (pere, mere, veuf, veuve)
		et les témoins
	*/
	function add_familles($acte, $acte_num, $id_epoux, $id_epouse){
		add_veuf($acte, $acte_num, $id_epoux);
		add_veuve($acte, $acte_num, $id_epouse);
		add_parents($acte, $acte->epoux, $acte_num, $id_epoux);
		add_parents($acte, $acte->epouse, $acte_num, $id_epouse);	
		add_temoins_all($acte, $acte_num, $id_epoux, $id_epouse);	
	}
	// ajoute les anciens mariages de l'époux
	function add_veuf($acte, $acte_num, $id_epoux){
		if (isset($acte->epoux->veuf)){
			foreach($acte->epoux->veuf as $veuf){
				$id_pers = existe_pers($veuf, $acte);
				if ($id_pers == 0){
					$id_pers = add_pers($veuf, $acte);
					add_mention(add_relation($id_epoux, $id_pers, EPOUSE, $acte), $acte_num, $acte);
					add_mention(add_relation($id_pers, $id_epoux, EPOUX, $acte), $acte_num, $acte);
				}
			}
		}
	}
	// ajoute les anciens mariages de l'épouse
	function add_veuve($acte, $acte_num, $id_epouse){
		if (isset($acte->epouse->veuve)){
			foreach($acte->epouse->veuve as $veuve){
				$id_pers = existe_pers($veuve, $acte);
				if ($id_pers == 0){
					$id_pers = add_pers($veuve, $acte, $acte);
					add_mention(add_relation($id_epouse, $id_pers, EPOUX, $acte), $acte_num, $acte);
					add_mention(add_relation($id_pers, $id_epouse, EPOUSE, $acte), $acte_num, $acte);
				}
			}
		}
	}	
	// ajoute les parents de l'epoux/se
	function add_parents($acte, $pers, $acte_num, $id_epoux){
		$flag = false;
		if (isset($pers->pere)){
			$id_pere = existe_pers($pers->pere, $acte);
			if ($id_pere == 0){
				$flag = true;
				$id_pere = add_pers($pers->pere, $acte);
				add_mention(add_relation($id_epoux, $id_pere, PERE, $acte), $acte_num, $acte);
			}
		}
		if (isset($pers->mere)){
			$id_mere = existe_pers($pers->mere, $acte);
			if ($id_mere == 0){
				$flag = true;
				$id_mere = add_pers($pers->mere, $acte);
				add_mention(add_relation($id_epoux, $id_mere, MERE, $acte), $acte_num, $acte);
			}
		}
		if (isset($pers->pere) and isset($pers->mere) and $flag) add_rel_parents_epoux($acte, $pers, $acte_num, $id_pere, $id_mere);
	}
	// ajoute relation entre les parents de l'époux ou de l'épouse
	function add_rel_parents_epoux($acte, $acte_epoux, $acte_num, $id_pere, $id_mere){
		$attri = $acte_epoux->pere->attributes();
		if (!isset($attri["legitime"]) or $attri["legitime"] == "true"){
			add_mention(add_relation($id_pere, $id_mere, EPOUSE, $acte), $acte_num, $acte);
			add_mention(add_relation($id_mere, $id_pere, EPOUX, $acte), $acte_num, $acte);
		}	
	}
	// ajoute les témoins
	function add_temoins_all($acte, $acte_num, $id_epoux, $id_epouse){
		if (isset($acte->temoins->temoin)){ 
			foreach($acte->temoins->temoin as $temoin){
				$id_pers = existe_pers($temoin, $acte);
				if ($id_pers == 0){
					$id_pers = add_pers($temoin, $acte);
					add_mention(add_relation($id_epoux, $id_pers, TEMOIN, $acte), $acte_num, $acte);
					add_mention(add_relation($id_epouse, $id_pers, TEMOIN, $acte), $acte_num, $acte);
				}
			}
		}
	}	

	/*
		Cette fonction sert à ajouter une personne dans la base de donnée
		ainsi que d'ajouter les différents relations qu'il peut avoir
		Et return l'id de la personne ajoutée
	*/
	
	function add_pers($pers, $acte){
		$objDate = new LirePeriode($acte);
		$p = new Personne($pers);		
		if ($p->nom[0] != "" and $p->prenom[0] != ""){
			$sql = mysql_query("SELECT id, periode FROM personnes WHERE nom1noaccent='".mysql_real_escape_string($p->nomnoaccent[0])."' and nom2noaccent='".mysql_real_escape_string($p->nomnoaccent[1])."' and nom3noaccent='".mysql_real_escape_string($p->nomnoaccent[2])."' and prenom1noaccent='".mysql_real_escape_string($p->prenomnoaccent[0])."' and prenom2noaccent='".mysql_real_escape_string($p->prenomnoaccent[1])."'");
			if (mysql_num_rows($sql) > 0){
				$sql_r = mysql_fetch_assoc($sql); // je prends le premier résultat, qui n'est pas meilleur qu'un autre
				add_condition($sql_r['id'], $pers, $acte, $objDate);
				if ($pers->getName()!= "veuf" and $pers->getName()!= "veuve") {
					maj_periode($objDate, $sql_r['periode']);	
				}
				@$pers->addAttribute("id", $sql_r['id']);
				return $sql_r['id'];
			}
		}
		$id_periode = add_periode($objDate->minDebut, $objDate->maxDebut, $objDate->minFin, $objDate->maxFin);
		$req_sql = "INSERT INTO personnes (id, de1, la1, nom1, de2, la2, nom2, de3, la3, nom3, prenom1, prenom2, nom1noaccent, nom2noaccent, nom3noaccent, prenom1noaccent, prenom2noaccent, periode) VALUES (NULL, '".$p->de[0]."','".$p->la[0]."', '".mysql_real_escape_string($p->nom[0])."', '".$p->de[1]."','".$p->la[1]."', '".mysql_real_escape_string($p->nom[1])."', '".$p->de[2]."','".$p->la[2]."', '".mysql_real_escape_string($p->nom[2])."', '".mysql_real_escape_string($p->prenom[0])."', '".mysql_real_escape_string($p->prenom[1])."', '".mysql_real_escape_string($p->nomnoaccent[0])."', '".mysql_real_escape_string($p->nomnoaccent[1])."', '".mysql_real_escape_string($p->nomnoaccent[2])."', '".mysql_real_escape_string($p->prenomnoaccent[0])."', '".mysql_real_escape_string($p->prenomnoaccent[1])."', '$id_periode')";
		mysql_query($req_sql);	
		ajouter_fichier_log($req_sql);
		$id_pers = last_id_personnes();
		add_condition($id_pers, $pers, $acte, $objDate);
		@$pers->addAttribute("id", $id_pers);
		return $id_pers;
	}
	
	/*
		Cette fonction met à jour une période existante avec une nouvelle période
		Attention, il y a un maj_periode2 dans fonctions_all.php
	*/
	function maj_periode($objDate, $id_periode){
		$sql = mysql_fetch_assoc(mysql_query("SELECT * FROM periodes WHERE id='$id_periode'"));
		$maj_minDebut = $sql['minDebut'];
		$maj_maxDebut = $sql['maxDebut'];
		$maj_minFin = $sql['minFin'];
		$maj_maxFin = $sql['maxFin'];
		if ($maj_minDebut != "00-00-0000"){
			if (transforme_periode($objDate->minDebut) < transforme_periode($maj_minDebut)) $maj_minDebut = $objDate->minDebut;
			if (transforme_periode($objDate->maxDebut) < transforme_periode($maj_maxDebut)) $maj_maxDebut = $objDate->maxDebut;
			if (transforme_periode($objDate->minFin) > transforme_periode($maj_minFin)) $maj_minFin = $objDate->minFin;
			if (transforme_periode($objDate->maxFin) > transforme_periode($maj_maxFin)) $maj_maxFin = $objDate->maxFin;
			$req_sql = "UPDATE periodes SET minDebut='$maj_minDebut',maxDebut='$maj_maxDebut',minFin='$maj_minFin',maxFin='$maj_maxFin' WHERE id='$id_periode'";
			mysql_query($req_sql);
			ajouter_fichier_log($req_sql);
		}
	}

	function add_condition($id_pers, $pers, $acte, $objDate){
		$acte_num = $acte->attributes();
		$id_periode = add_periode($objDate->minDebut, $objDate->maxDebut, $objDate->minFin, $objDate->maxFin);
		if (isset($pers->condition)){	
			foreach($pers->condition as $val){
				if (!verif_existe_cond($id_pers, $val, $objDate)){
				mysql_query("INSERT INTO cond (id, id_personne, cond, source, periode, acte) VALUES (NULL,'$id_pers','$val',1, '$id_periode', '$acte_num')");
				ajouter_fichier_log("INSERT INTO cond (id, id_personne, cond, source, periode, acte) VALUES (NULL,'$id_pers','$val',1, '$id_periode', '$acte_num')");
				}
			}
		}
		// puis on ajoute les éléments en attributs s'ils existent
		$id_periode2 = add_periode($objDate->minDebut, $objDate->maxDebut, $objDate->minFin, $objDate->maxFin);
		if ($pers->attributes() != NULL) @$tab_attr = (array)$pers->attributes();
		if (isset($tab_attr) and count($tab_attr) > 0) {
			foreach($pers->attributes() as $key => $val){
				if (!verif_existe_cond($id_pers, $key, $objDate)){
					if ($key != "id"){
						mysql_query("INSERT INTO cond (id, id_personne, cond, source, periode, acte) VALUES (NULL,'$id_pers','$key',1,'$id_periode2', '$acte_num')");	
						ajouter_fichier_log("INSERT INTO cond (id, id_personne, cond, source, periode, acte) VALUES (NULL,'$id_pers','$key',1,'$id_periode2', '$acte_num')");
					}
				}
			}
		}
		// si c'est un veuf ou veuve : on ajoute la condition "décès"
		if ($pers->getName() == "veuf" or $pers->getName() == "veuve"){
			$deces = "Décédé-e";
			if (!verif_existe_cond($id_pers, $deces, $objDate)){
			mysql_query("INSERT INTO cond (id, id_personne, cond, source, periode, acte) VALUES (NULL,'$id_pers','$deces',1,'$id_periode2', '$acte_num')");	
			ajouter_fichier_log("INSERT INTO cond (id, id_personne, cond, source, periode, acte) VALUES (NULL,'$id_pers','$deces',1,'$id_periode2', '$acte_num')");
			}
		}
	}
	function verif_existe_cond($id_pers, $val, $objDate){
		$flag = false;
		$sql = mysql_query("SELECT * FROM cond WHERE id_personne='$id_pers' and cond='$val'");
		while ($line = mysql_fetch_assoc($sql)){
			$id_per = $line['periode'];	
			$periode = mysql_fetch_assoc(mysql_query("SELECT * FROM periodes WHERE id='$id_per'"));
			if ($objDate->minDebut == $periode['minDebut'] and $objDate->maxDebut == $periode['maxDebut'] and $objDate->minFin == $periode['minFin'] and $objDate->maxFin == $periode['maxFin']){
				$flag = true;
			}
		}
		return $flag;
	}
	
	/*
		Cette fonction ajoute une relation (type) entre personne A et personne B 
		B est époux de A ou B est témoin de A
		Et return l'id de la relation
	*/
	function add_relation($persA, $persB, $type, $acte){
		$objDate = new LirePeriode($acte);
		$line = mysql_query("SELECT id, periode FROM relations WHERE personne1='$persA' and personne2='$persB' and type='$type'");
		if (mysql_num_rows($line)>0) {
			$id = mysql_fetch_assoc($line);
			maj_periode($objDate, $id['periode']);
			return $id['id'];
		}
		else {
			$id_periode = add_periode($objDate->minDebut, $objDate->maxDebut, $objDate->minFin, $objDate->maxFin);
			mysql_query("INSERT INTO relations (id, personne1, personne2, type, periode) VALUES (NULL, '$persA', '$persB', '$type', '$id_periode')");
			ajouter_fichier_log("INSERT INTO relations (id, personne1, personne2, type, periode) VALUES (NULL, '$persA', '$persB', '$type', '$id_periode')");
			return last_id_relations();
		}
	}
	
	/*
		Cette fonction ajoute une mention d'une relation par rapport à son acte
	*/
	function add_mention($id_relation, $acte_num){
		mysql_query("INSERT INTO mentions (id, relation, acte) VALUES (NULL, '$id_relation', '$acte_num')");	
		ajouter_fichier_log("INSERT INTO mentions (id, relation, acte) VALUES (NULL, '$id_relation', '$acte_num')");
	}
	
	/*
		Cette fonction ajoute une ligne dans la table acte
	*/
	function add_acte_date($acte, $acte_num, $id_epoux, $id_epouse){
		$objDate = new LirePeriode($acte);
		$id_periode = add_periode($objDate->minDebut, $objDate->maxDebut, $objDate->minFin, $objDate->maxFin);
		mysql_query("INSERT INTO actes (id_acte, epoux, epouse, periode) VALUES ('$acte_num', '$id_epoux', '$id_epouse', '$id_periode')");	
		ajouter_fichier_log("INSERT INTO actes (id_acte, epoux, epouse, periode) VALUES ('$acte_num', '$id_epoux', '$id_epouse', '$id_periode')");
	}
	
	/*
		Cette fonction ajoute une periode
	*/
	function add_periode($minD, $maxD, $minF, $maxF){
		mysql_query("INSERT INTO periodes (id, minDebut, maxDebut, minFin, maxFin) VALUES (NULL, '$minD', '$maxD', '$minF', '$maxF')");	
		ajouter_fichier_log("INSERT INTO periodes (id, minDebut, maxDebut, minFin, maxFin) VALUES (NULL, '$minD', '$maxD', '$minF', '$maxF')");
		return last_id_periodes();
	}
	
	/*
		Cette fonction sert à return le id de la dernière entrée
		de la table désignée
	*/
	function last_id_personnes(){
		$id_sql = mysql_fetch_row(mysql_query("SELECT id FROM personnes order by id desc limit 0,1"));
		return $id_sql[0];
	}
	function last_id_relations(){
		$id_sql = mysql_fetch_row(mysql_query("SELECT id FROM relations order by id desc limit 0,1"));
		return $id_sql[0];
	}
	function last_id_periodes(){
		$id_sql = mysql_fetch_row(mysql_query("SELECT id FROM periodes order by id desc limit 0,1"));
		return $id_sql[0];
	}
 ?>
