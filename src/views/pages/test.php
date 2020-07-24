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
// $contenu = $mysqli->get_contenu_acte(6813);
echo "<h3>contenu</h3>\n";
affiche_xml($contenu);

$xml = new SimpleXMLElement($contenu);

// _________________________________________________________
$balises_personnes = ['epoux', 'epouse', 'pere', 'mere',
  'temoin', 'parrain', 'veuf-de', 'veuf', 'veuve-de', 'veuve'];

function change_id_personne_xml($xml, $old_id, $new_id, $pretty)
{
  global $balises_personnes;

  foreach($xml->children() as $node)
  {
    $name = $node->getName();
    echo $pretty.$name."<br>";
    if(in_array($node->getName(), $balises_personnes))
    {
      if(isset($node['id']) && $node['id'] == $old_id)
      {
        echo "$old_id<br>";
        $node['id'] = $new_id;
      }
    }
    change_id_personne_xml($node, $old_id, $new_id, "..$pretty");
  }
}
// _________________________________________________________

echo '<h3>Trace</h3>';
echo "<p>\n";
change_id_personne_xml($xml, 418, 99418, '');
change_id_personne_xml($xml, 515, 99515, '');
echo "</p>\n";
$new_contenu = $xml->asXML();
echo "<h3>new contenu</h3>\n";
affiche_xml($new_contenu);

echo '<h3>Unique</h3>';
$arr = array($bob, $toto, $bob);
$unique = array_unique_by_id($arr);
var_dump($unique);

$acte = new Acte(15000);
$acte->remove_from_db();

?>
