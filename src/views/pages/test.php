<?php

echo "<p>";
echo "hello, world<br>";

$personne = new Personne(1);
$bob = new Prenom(10000, "Bob", "Bob");
$toto = new Prenom(10001, "Toto", "Toto");

$personne->add_prenom($bob);
$p0 = $personne->prenoms[0]->to_string();
echo "to_string = $p0<br>";
echo "_str = $personne->prenoms_str<br>";

$personne->add_prenom($toto);
$p1 = $personne->prenoms[1]->to_string();
echo "to_string = $p1<br>";
echo "_str = $personne->prenoms_str<br>";
echo "</p>\n";

echo "<h2>Acte</h2>";
$acte = new Acte(6813);
$date = $acte->get_date();
echo "<p>\n";
echo "date : $date<br>";

function affiche_xml($xml)
{
  echo '<textarea>';
  echo $xml;
  echo '</textarea>';
  echo '<p>';
  echo '<code>';
  echo $xml;
  echo '</code>';
  echo '</p>';
}

$contenu = $acte->get_contenu();

echo '<h3>Unique</h3>';
$arr = array($bob, $toto, $bob);
$unique = array_unique_by_id($arr);
var_dump($unique);

echo '<h3>Suppression à la main</h3>';

/*
Calderon + Basav
$personne = new Personne(402);
$personne->remove_from_db(TRUE);
$acte = new Acte(5982);
$acte->remove_from_db();
$acte = new Acte(4789);
$acte->remove_from_db();

Florencia
$personne = new Personne(533);
$personne->remove_from_db(TRUE);
$acte = new Acte(5598);
$acte->remove_from_db();
*/

echo "<p>passed</p>\n";

?>
