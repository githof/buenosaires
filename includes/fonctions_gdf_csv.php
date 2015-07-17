<?php
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
	

?>