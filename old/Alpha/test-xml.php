<?php
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

$xml = new SimpleXMLElement($string);
/*foreach($xml->ACTES->ACTE->attributes() as $a =>$b){
	echo $a, '="',$b, "\"\n";
}*/
//echo $xml->ACTES->ACTE->count();
/*for ($i = 0; $i < $xml->ACTES->ACTE->count(); $i++) {
    echo "\n";
    echo $xml->ACTES->ACTE[$i]->attributes();
    echo "\n";
}*/
// definition de la fonction qui prend en parametre un fichier
function compteurActes($fichier){
	 for ($i = 0; $i < $fichier->ACTES->ACTE->count(); $i++) {
  	      echo "\n";
 	      echo $fichier->ACTES->ACTE[$i]->attributes();
 	      echo "\n";
	  }
	  return $fichier->ACTES->ACTE->count();
}
// fonction de comparaison 
function differenceActes($fichier){
	 $dif=0;
	 for ($i = 0; $i < $fichier->ACTES->ACTE->count(); $i++) {
  	      $num_acte =  $fichier->ACTES->ACTE[$i]->attributes();
	      //echo $num_acte;
	      //echo "\n";
	      $num_texte_reel= explode(')',$fichier->ACTES->ACTE[$i])[0];
	      $num_texte = explode(')',$fichier->ACTES->ACTE[$i])[0];	      
	      $num_acte =  $num_acte + $dif;
	      $dif = 0;
	     //echo $num_texte;
 	     // echo "\n";
	     if ($num_acte != $num_texte){
	     	echo $num_acte;
	        echo "---->";
		echo $num_texte;
		echo '::';
		echo $num_texte_reel;
 	     	echo "\n  ****  ";
		$dif=$num_texte-$num_acte ;
		echo $dif;
		echo "\n";
	     }
	     
	  }

}
//differenceActes($xml);

if (file_exists('matrimonios.num.xml')) {
    $xml2 = simplexml_load_file('matrimonios.num.xml');
    /*
    $reponse = compteurActes($xml2);
    echo $reponse;*/
    differenceActes($xml2);
} else {
    exit('Echec lors de l\'ouverture du fichier test.xml.');
}


?>