
<?php

echo '<h1>Test xml</h1>';
if(!($xml = simplexml_load_file('alphaomegatest.xml')))
  echo '<p>Erreur</p>';
else
  {
    echo '<p>';
    echo $xml->ACTES->ACTE[0]->date;
    echo "</p>\n";
  }
?>
