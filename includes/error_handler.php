<?php
/*
  CP 01/11/14
  récupéré dans la doc php.net pour la fonction set_error_handler
 */
function error_handler($errno, $errstr, $errfile, $errline)
{
   if (!(error_reporting() & $errno)) {
        // Ce code d'erreur n'est pas inclus dans error_reporting()
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
      echo "<h3>ERREUR [$errno] $errstr</h3>\n";
      echo "<p>\n";
      echo "  Erreur fatale sur la ligne $errline dans le fichier $errfile\n";
      echo "</p>\n";
      break;

    case E_USER_WARNING:
        echo "<h3>ALERTE [$errno] $errstr</h3>\n";
        break;

    case E_USER_NOTICE:
        echo "<h3>NOTE : [$errno] $errstr</h3>\n";
        break;

    default:
        echo "<p>Type d'erreur inconnu : [$errno] $errstr</p>\n";
        break;
    }

    return false; // on exécute quand même le gestionnaire interne
}

set_error_handler("error_handler");

?>
