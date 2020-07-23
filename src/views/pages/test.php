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

$contenu = $acte->get_contenu();
// $contenu = $mysqli->get_contenu_acte(6813);
echo "contenu :<br>\n";
echo "<code>\n[$contenu]\n<br>\n</code>";

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
      $attr = $node->attributes();
      $id = $attr['id'];
      echo "id : $id, old_id : $old_id<br>";
      echo ".{$node['id']}.<br>";
      if(isset($node['id']) && $node['id'] == $old_id)
      {
        echo "$old_id<br>";
        $attr['id'] = $new_id;
      }
    }
    change_id_personne_xml($node, $old_id, $new_id, "..$pretty");
  }
}
// _________________________________________________________

change_id_personne_xml($xml, 413, 99001, '');
$new_contenu = $xml->asXML();
echo "new contenu :<br>\n";
echo "<code>\n[$new_contenu]\n</code>";
echo "</p>\n"

?>
