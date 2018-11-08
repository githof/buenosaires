<?php

echo "hello, world";

include_once(ROOT."src/class/model/Personne.php");
include_once(ROOT."src/html_entities.php");

$personne = new Personne(1);
$personne->add_prenom("Bob");
echo "<p>";
echo "to_string = $personne->prenoms->to_string()<br>";
echo "_str = $personne->prenoms_str<br>";
echo "</p>";

?>