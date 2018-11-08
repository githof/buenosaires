<?php

echo "hello, world";

include_once(ROOT."src/class/model/Personne.php");
include_once(ROOT."src/html_entities.php");

$personne = new Personne(1);
$prenom = new Prenom(10000, "Bob", "Bob");
$personne->add_prenom($prenom);
echo "<p>";
$p0 = $personne->prenoms[0]->to_string();
echo "to_string = $p0<br>";
echo "_str = $personne->prenoms_str<br>";
echo "</p>";

?>