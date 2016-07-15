<?php
include_once("../includes/fonctions_compilation.php");

$texte_acte =  <<<XML
<ACTE num="5553">5577) <date>21-4-1773</date>: <epoux don="true" id="1515"> <condition>Regidor</condition> Dn. <prenom>Juan</prenom> <prenom>José</prenom> de <nom de="true">LEZICA</nom>, natural de <naissance-lieu>ésta</naissance-lieu>, hijo legítimo de <pere don="true" id="1243"> Dn. <prenom>Juan</prenom> de <nom de="true">Lezica</nom> y <nom y="true">Torrezuri</nom> </pere> y de <mere id="1517" don="true">Da. <prenom>Helena</prenom> de <nom de="true">Alquiza</nom></mere> </epoux>, con <epouse don="true">Da. <prenom>María</prenom> <prenom>Rosa</prenom> de <nom de="true">RIGLOS</nom> y <nom y="true">SAN MARTIN</nom>, natural de <naissance-lieu>ésta</naissance-lieu>, hija legítima del <pere don="true" id="1170"> <condition>Capitán</condition> Dn. <prenom>Marcos</prenom> <prenom>José</prenom> de <nom de="true">Riglos</nom> </pere> y de <mere don="true" id="2044"> Da. <prenom>Francisca</prenom> <nom>Javiera</nom> de <nom de="true">San Martín</nom> </mere> </epouse>. <temoins>Ts.: los referidos <temoin don="true" id="1170">Dn. <prenom>Marcos</prenom> <prenom>José</prenom> de <nom de="true">Riglos</nom></temoin> y <temoin don="true" id="1517">Da. <prenom>Helena</prenom> de <nom de="true">Alquiza</nom></temoin></temoins>. (f. 145)</ACTE>
XML;

$acte = new SimpleXMLElement($texte_acte);
$epouse = $acte->epouse;
$nom = $epouse->nom[0];
echo "<p>Nom : $nom</p>";

$id = add_pers($epouse, $acte);
echo "<p>ID : $id</p>";

/*
  23 juil 2015
  Requête d'insersion de la personne envoyée, ajoutée dans le fichier de log, mais pas prise en compte, je sais pas pourquoi.  Je l'ai lancée à la main, c'est passé.
-> id 2045, mais elle n'est reliée à rien.
  Il va falloir relancer add_acte
 */

?>
