<?php

	include("../info/parametre.php");
	include("../includes/fonctions_ecrire.php");
	include("../includes/fonctions_all.php");

    function convert_type($relation)
    {
        switch($relation['type'])
        {
            case 1:
            case 2:
                return "E";
                break;
            case 3:
            case 4:
                return "P";
                break;
            case 5:
                return "T";
        }
    }
	    
    function undup_relations($relations)
    {
        for($i = 0; $i<count($relations); $i++)
        {
			if ($relations[$i] != NULL)
			{
				$id_periode = $relations[$i]['periode'];
				$per = mysql_fetch_assoc(mysql_query("SELECT minDebut, maxFin FROM periodes WHERE id='$id_periode'"));
				$per_debut = $per['minDebut'];
				$per_fin = $per['maxFin'];
				$pers = array(0 => $relations[$i]['personne1'], 1 => $relations[$i]['personne2'], 2 => convert_type($relations[$i]), 3 => $per_debut, 4 => $per_fin);
				$dico[$i] = $pers;
				for($j = $i + 1; $j<count($relations); $j++)
				{
					if ($relations[$i]['personne1'] == $relations[$j]['personne1'] and $relations[$i]['personne2'] == $relations[$j]['personne2'])
					{
						$pers[2] .= convert_type($relations[$j]);
						$dico[$i] = $pers;
						$relations[$j] = NULL;
					}				
				}
			}
        }
        return $dico;
    }
    
// Attention, si $flag est faux, $id n'est pas renseigné
    function get_relations($id, $flag)
    {
      if ($flag) $entries = mysql_query("SELECT * FROM relations");
      else $entries = mysql_query("SELECT * FROM relations WHERE personne1='$id' or personne2='$id'");
      while ($entry = mysql_fetch_assoc($entries))
        {
	  $relations[] = $entry;
        }
      return undup_relations($relations);
    }
    
    function gdf_fields($fichier, $fields)
    {
        for($i = 0; $i < count($fields); $i++)
        {
            fwrite($fichier, $fields[$i]);
            if($i < count($fields)-1)
                fwrite($fichier, ',');
        }
        fwrite($fichier, "\n");
    }
    
	function gdf_relation($fichier, $relation)
    {
        $fields[] = 'p'.$relation[0];
        $fields[] = 'p'.$relation[1];
        $fields[] = 'true'; // oriented
        $fields[] = $relation[2];
		$fields[] = $relation[3];
		$fields[] = $relation[4];
        gdf_fields($fichier, $fields);
    }
    
    function gdf_relations($fichier, $id, $flag)
    {
        // ici je mets les relations entre les personnes concerncées
        $rn = get_relations($id, $flag);
        foreach($rn as $relation)
            gdf_relation($fichier, $relation);
    }
	
	function ouvrir($path_fichier){
		// d'abord j'ouvre le fichier en mode écriture
		$fichier = fopen($path_fichier,"w");
		// je vide le fichier
		ftruncate($fichier,0);
	
		// je commence la première partie du fichier - les Personnes	
		fwrite($fichier, "nodedef>name VARCHAR, label VARCHAR, periode VARCHAR\n");
		// ici je mets toutes les personnes concernées
		
		return $fichier;
	}

	function ecrire_fichier($fichier, $p){
		$date_p = affiche_date($p['periode']);
		$prenom1 = ucname(stripAccentsLower($p['prenom1']));
		$prenom2 = ucname(stripAccentsLower($p['prenom2']));
		$nom1 = ucname(stripAccentsLower($p['nom1']));
		$nom2 = ucname(stripAccentsLower($p['nom2']));
		$nom3 = ucname(stripAccentsLower($p['nom3']));
		$prenom1 = preg_replace("/(\r\n|\n|\r)/", " ", $prenom1);
		$prenom2 = preg_replace("/(\r\n|\n|\r)/", " ", $prenom2);
		$nom1 = preg_replace("/(\r\n|\n|\r)/", " ", $nom1);
		$nom2 = preg_replace("/(\r\n|\n|\r)/", " ", $nom2);
		$nom3 = preg_replace("/(\r\n|\n|\r)/", " ", $nom3);
		$label = trim(trim($prenom1)." ".trim($prenom2)." ".trim($nom1)." ".trim($nom2)." ".trim($nom3));
		if($label != "")
		  fwrite($fichier,
			 "p".$p['id'].",'"
			 .$label
			 ."','$date_p'\n");
	}
	


	$var = htmlspecialchars(mysql_real_escape_string($_GET['p']));
	
	$path = "";
	if ($var == "all"){
		$fichier = ouvrir("../gdf/all.gdf");
		$path = "../gdf/all.gdf";
		$rel = mysql_query("SELECT * FROM personnes");
		$flag = true;
		$pile = array();
		while ($p = mysql_fetch_assoc($rel)){
		  $id = ecrire_fichier($fichier, $p);
		}
	}
	else {
		$id = $var;
		$rel = mysql_query("SELECT * FROM relations WHERE personne1='$id' or personne2='$id'");
		$p = personne($id);
		$nom_pers = stripAccentsLower($p['nom1']).'.gdf';
		$fichier = ouvrir("/tmp/".$nom_pers);				// a modifier si on utilise sous windows 	pas de fichier /tmp sous 
		$path = '/tmp/'.$nom_pers;							// a modifier si on utilise sous windows	windowsd
		$date_p = affiche_date($p['periode']);
		ecrire_fichier($fichier, $p);
		$pile = array();
		while ($line = mysql_fetch_assoc($rel)){
			$line['personne1'] == $id ? $p_id = $line['personne2'] : $p_id = $line['personne1'];
			if (!in_array($p_id, $pile)){		
				array_push($pile, $p_id);
				$p = personne($p_id);
				ecrire_fichier($fichier, $p);
			}
		}
		$flag = false;
	}
		
	
	// puis la deuxième partie du fichier - les RELATIONS
	fwrite($fichier, "edgedef>node1 VARCHAR,node2 VARCHAR,directed BOOLEAN,nature VARCHAR,periode_debut VARCHAR, periode_fin VARCHAR\n");
    gdf_relations($fichier, $id, $flag);
	
	mysql_close();
	
	fclose($fichier);
	
	// on redirige la page web
	header('Location: '.$path);

	
	  

?>