<?php

echo "<p>";
echo "hello, world<br>";

include_once(ROOT."src/class/model/Personne.php");
include_once(ROOT."src/html_entities.php");

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

echo "<h2>Acte</h2>";
$acte = new Acte(6813);
$date = $acte->get_date();
echo "date : $date";

echo "</p>\n";

?>