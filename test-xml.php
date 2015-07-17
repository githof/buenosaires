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

echo $xml->ACTES->ACTE[0]->asXML();

echo "______________________________\n";

$premieracte = $xml->ACTES->ACTE[0];
echo $premieracte->asXML();
echo "---\n";

echo "prenom : "; print_r($premieracte->epoux->prenom);
echo "\n______________________________\n";

echo $premieracte->epoux->asXML() . "\n";

function modif_epoux($acte, $id)
{
  $acte->epoux->addAttribute('id', $id);
}

modif_epoux($premieracte, 1024);
echo "---\n";
echo $premieracte->asXML();
echo "---\n";
echo $xml->asXML();

echo "\n______________________________\n";

if(! isSet($premieracte->bob)) echo "bob not set\n";
if(isSet($premieracte->epoux)) echo "epoux set\n";

?>
