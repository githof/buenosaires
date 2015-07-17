ù<?php
$string = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<document>
  <ACTES>
    <ACTE num="235">235) <date>3-3-1668</date>: <epoux><prenom>Juan</prenom> de CACERES,</epoux> con <epouse>Da. Juana de ALEMAN, vecinos de ésta. </epouse>. Ts.: <temoins><temoin> el Sargento Mayor Dn. Martín de Segura</temoin>,<temoin> y Da. Lucía Velázquez Meléndez. </temoin></temoins>(f. 35).</ACTE>

    <ACTE num="236">236) <date>1-3-1668</date>: Juan de MONTEMAYOR, y Da. Isabel HUMANES. Ts.: <temoins><temoin> Hipólito Ramírez</temoin>,<temoin> Bernardo Pereira</temoin>,<temoin> y otros. Al margen: murió el dicho</temoin>,</temoins>(f. 35v).</ACTE>

    <ACTE num="237">237) <date>9-4-1668</date>: <epoux>Manuel de MATUS, soldado del presidio, natural de Angola,</epoux> con <epouse>María de GUZMAN, natural de ésta, hija legítima de Juan de Guzmán y de María de Toledo. </epouse>. Ts.: <temoins><temoin> el Licenciado Pascual de Fuentes</temoin>,<temoin> y el Maestro Juan de Oramas</temoin>,<temoin> Curas de ésta. </temoin></temoins>(f. 35v).</ACTE>
  </ACTES>
</document>
XML;

$string2 = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<document><ACTES>
<ACTE num="5"> 1)<date>4-2-1656</date> :     <epoux id="8"><condition>Capitán</condition><prenom>Juan</prenom><nom>ZACARIAS</nom> de la <nom de="true" la="true">SIERRA</nom> y     <nom>MORALES</nom></epoux>, con <epouse don="true" id="2">Da.     <prenom>María</prenom> de <nom de="true">CARAVAJAL</nom>, <veuve id="3">viuda del     <condition>Capitán</condition><prenom>Cristóbal</prenom><nom>Cabral</nom></veuve></epouse>.<temoins>Ts.:     el<temoin id="4"><condition>Capitán</condition><prenom>Diego</prenom><nom>Gutiérrez</nom>de     <nom de="true">Humanes</nom></temoin><temoin id="5"><prenom>Juan</prenom>de<nom>Ibarra     o Izarra</nom></temoin>,<temoin id="7">el<condition>Alférez</condition><prenom>Juan</prenom><nom>Rodríguez</nom><nom>Estela</nom></temoin>, y     otros muchos</temoins>, (f. 1).</ACTE>
 </ACTES>
</document>
XML;
// FONCTION QUI COMPTE LE NOMBRE ACTE DE NAISSANCE D UN FICHIER XML
// EX
function compteurActes($fichier){
  return $fichier->ACTES->ACTE->count();
}

$xml = new SimpleXMLElement($string);
$xml2 = new SimpleXMLElement($string2);


// FONCTION QUI RETOUR LE TABLEAU DES NUMEROS DES ACTES
// EX: print_r(um_actes($xml))
function num_actes($xml){
  $tableau = array();
  for($i=0 ; $i <$xml->ACTES->ACTE->count() ; $i++){
    $tableau[]= $xml->ACTES->ACTE[$i]->attributes(); 
  }
  return $tableau;
}

// FONCTION QUI RETOURNE UN TABLEAU DES ID D UN ACTE PAR ODRE ( RECURSIVEMENT)
// EX: print_r(ids_acte($xml2->ACTES));
function ids_acte($xml){
 $tableau = array();
 foreach($xml->children() as $fils){
   if(isset($fils['id'])){ 
    $valeur = $fils['id'];
    $tableau[]=intval($valeur);
   }

   $tableau= array_merge($tableau,ids_acte($fils));
 }
 return $tableau;
 }



//print_r(ids_acte($xml2->ACTES->ACTE[0]));

// FONCTION QUI ASSOCIE A CHAQUE NUMERO ACTES SONT TABLEAU IDS ( FONCTION PRECEDANTE)
// EX:print_r(ids_actes($xml2));
function ids_actes($xml){
  $tableau= array();
  foreach($xml->ACTES->ACTE as $acte){
    $indice=$acte->attributes();
    $tableau["$indice"]= ids_acte($acte);
  }
  return $tableau;
 }
//print_r(ids_actes($xml2));

// FONCTION QUI PREND DEUX TABLEAUX DE NUMEROS D ACTES ET LES COMPARE AINSI QUE LEIR IDS
// EX :pas demander pas important je crois
function diff_ids_acte_autre($ids1,$ids2){
  if(! isset($ids)  || ! isset($ids2) )
    return (!isset($ids2))?( (!isset($ids2)) ? 0:2 ) : 1;
  if(count($ids1) != count($ids2))
    return ( count($ids1)>count($ids2) ) ? 1 : 2;

  for($i=0 ; $i< cout($ids1) ; $i++){
    if($ids1["$i"] != $ids2["$i"]) break;
    if( count($ids1["$i"])!= count($ids2["$i"]) ) break;
    for($j=0 ; $j< cout($ids1["$i"]) ; $j++)
      if($ids1["$i"]["$j"] != $ids1["$i"]["$j"]) break;

    return 0;
  }
  return 3;
}

// FONCTION QUI TESTE DIFFENRENCE  DE DEUX TABLEAUX  D'ID 
// EX : pas tester
function diff_ids_acte($ids1,$ids2){
  if(!isset($ids1)){
      if(!isset($ids2)) return 0;
      else return 2; 
  }else{
    if(!isset($ids2)){return 1;}
    else{
          if(count($ids1) != count($ids2))
	    return ( count($ids1)>count($ids2) ) ? 1 : 2;
	  else{
	    for($i=0 ; $i< count($ids1) ; $i++){
	      if($ids1["$i"] != $ids2["$i"]) return 3;
	      if( count($ids1["$i"])!= count($ids2["$i"]) ) return 3;

	      for($j=0 ; $j< count($ids1["$i"]) ; $j++)
	         if($ids1["$i"]["$j"] != $ids1["$i"]["$j"]) return 3;

	    return 0;
	  }
	}
     }
  }
}

// FONCTION QUI PENDS DEUX TABLEAUX DE NUMERO ACTE(AVEC LE TABLEAU DE ID) ET LES COPARES
// EX :
function diff_ids_actes($ids_actes1, $ids_actes2){
  // en supposant que les numeros de actes sont identiques
  $tableau = array();
  // recperation des ids des 2 tableau, fusion et union
  $tableau_ids= array_unique( array_merge(
					  array_keys($ids_actes1),array_keys($ids_actes2) ));
			      //inversion k, v
  foreach($tableau_ids as $v => $k){
    if(isset($ids_actes1["$k"]) && isset($ids_actes2["$k"])){
      $tableau["$k"] = diff_ids_acte($ids_actes1["$k"],$ids_actes2["$k"]);
    }else{
      if(isset($ids_actes1["$k"])) $tableau["$k"] = diff_ids_acte($ids_actes1["$k"],null);
      else $tableau["$k"] = diff_ids_acte(null,$ids_actes2["$k"]);
    }
  }
  return $tableau;
}
// FONCTIO QUI PREND UN FICHIER XML ET CONSTRUIS LE TABLEAU DE NUMERO ASOCIER A CELUI DES ID
// EX:print_r(tab_ids_fichier('buenosaires-base.xml'));
function tab_ids_fichier($fichier){
  if(file_exists($fichier))
    $monfichier = fopen($fichier,'r');
  else {
    echo "Le fichier $fichier n existe pas.";
    return ;
  }
  $xml = simplexml_load_file($fichier);
  return ids_actes($xml);
}

//print_r(tab_ids_fichier('buenosaires-base.xml'));


// FONCTION QUI PREND DEUX FICHIER
// EX :
function compare_fichier($fichier1,$fichier2){
  $resultat1 = tab_ids_fichier($fichier1);
  $resultat2 = tab_ids_fichier($fichier2);
  return diff_ids_actes($resultat1, $resultat2);
}

// FONCTION QUI UN TABLEAU DE DIFFEENCE DE FICHIER( NUMERO D ACTE)
// EX: 
function affiche_diff_ids($diff_ids){
  $nombre_actes =0;

  $nombre_difference = 0;
  $nombre_un = 0;
  $nombre_deux =0;
  $nombre_trois =0;
  foreach($diff_ids as $v){
    if($v != 0) $nombre_difference ++;
    if($v == 1) $nombre_un ++;
    if($v == 2) $nombre_deux ++;
    if($v == 3) $nombre_trois ++;
    $nombre_actes ++;
  }
  echo 'IL ya <strong>'.$nombre_actes.'</strong> numero dans le tableau contenant le regroupement des actes par id <br />';
  echo 'IL ya <strong>'.$nombre_difference.'</strong> mumero different dans le tableau <br >';
  echo 'IL ya <strong>'.$nombre_un.'</strong> mumero de plus dans le premier tableau que le deuxieme<br >';
  echo 'IL ya <strong>'.$nombre_deux.'</strong> mumero de plus dans le deuxieme tableau  que le premiere<br >';
  echo 'IL ya <strong>'.$nombre_trois.'</strong> mumero dans le tableau qui ont le meme id mais de contenue different <br >';
  
}
//FONCTION QUI AFFICHE SOUS FORME DE TABLEAU LA DIFFERENCE DES FICHIERS AVEC LES IDS
//
function afficheTableau($tab){
  echo '
  <table BORDER="1" 
    <caption> liste des numeros desidentifiants qui ont u probleme</caption>
    <thead>
      <tr>
         <th>fichier 1</th>
         <th>fichier 2</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
         <th>fichier 1</th>
         <th>fichier 2</th>
      </tr>
    </tfoot>
    <tbody>    
    ';
  foreach($tab as $i =>$v ){
    if($v==0){
      echo '
		<tr> 
			<td> '.$i.' </td>
			<td> '.$i.' </td>
		</tr> 
         ';
    }
   if($v==1){
      echo '
		<tr> 
			<td> <font color="red">'.$i.' </font></td>
			<td> <font color="red">**** </font></td>
		</tr> 
         ';
    }
  if($v==2){
      echo '
		<tr> 
			<td> <font color="red">*** </font></td>
			<td> <font color="red">'.$i.' </font></td>
		<tr> 
         ';
    }
  if($v==3){
      echo '
		<tr> 
			<td> '.$i.' !!!</td>
			<td> '.$i.' !!!</td>
		<tr> 
         ';
    }
  }  
echo '
      
    </tbody>
  </table>
   ';

}


?>
