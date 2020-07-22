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
echo "date : $date\n";

include_once(ROOT."src/class/io/Database.php");

$contenu = $mysqli->get_contenu_acte($acte);
echo "contenu :\n";
echo "$contenu \n";
$xml = new SimpleXMLElement($contenu);
change_id_personne_xml($xml, $old_id, $new_id);
$new_contenu = $xml->asXML();
echo "new contenu :\n";
echo "$new_contenu \n";
echo "</p>\n"

?>
