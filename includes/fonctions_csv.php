<?php
    
    
    
    function csv_fields($fichier, $fields)
    {
        for($i = 0; $i < count($fields); $i++)
        {
            fwrite($fichier, "\"$fields[$i]\"");
            if($i < count($fields)-1)
                fwrite($fichier, ';');
        }
        fwrite($fichier, "\n");
    }
    
	function get_name_pers($p){		
		$prenom1 = ucname(stripAccentsLower($p['prenom1']));
		$prenom2 = ucname(stripAccentsLower($p['prenom2']));
		$nom1 = ucname(stripAccentsLower($p['nom1']));
		$nom2 = ucname(stripAccentsLower($p['nom2']));
		$nom3 = ucname(stripAccentsLower($p['nom3']));
		$r[] = preg_replace("/(\r\n|\n|\r)/", " ", $prenom1);
		$r[] = preg_replace("/(\r\n|\n|\r)/", " ", $prenom2);
		$r[] = preg_replace("/(\r\n|\n|\r)/", " ", $nom1);
		$r[] = preg_replace("/(\r\n|\n|\r)/", " ", $nom2);
		$r[] = preg_replace("/(\r\n|\n|\r)/", " ", $nom3);
		return $r;
	}
	
	function csv_get_prenomsnoms($id)
	{
		$p = mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id'"));
		return get_name_pers($p);
	}
	
	function csv_relation($fichier, $relation)
    {
        $fields[] = 'p'.$relation[0];
		$p1 = csv_get_prenomsnoms($relation[0]);
		$fields[] = trim($p1[0]." ".$p1[1]);
		$fields[] = trim($p1[2]." ".trim($p1[3]." ".$p1[4]));
        $fields[] = 'p'.$relation[1];
		$p2 = csv_get_prenomsnoms($relation[1]);
		$fields[] = trim($p2[0]." ".$p2[1]);
		$fields[] = trim($p2[2]." ".trim($p2[3]." ".$p2[4]));
        $fields[] = $relation[2]; // type
		$fields[] = $relation[3]; // periode_debut
		$fields[] = $relation[4]; // periode_fin
        csv_fields($fichier, $fields);
    }
    
	function csv_get_relations()
    {
		$entries = mysql_query("SELECT * FROM relations");
		while ($entry = mysql_fetch_assoc($entries))
        {
            $relations[] = $entry;
        }
        return undup_relations($relations);
    }
	
    function csv_relations($fichier)
    {
        // ici je mets les relations entre les personnes concerncées
        $rn = csv_get_relations();
        foreach($rn as $relation)
            csv_relation($fichier, $relation);
    }
	

?>