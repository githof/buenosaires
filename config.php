<?php

include('local-config.php');

//  log default output file
define("LOG_DEFAULT_OUTPUT", "log.txt");

//  log defaut level
//  0: none, 1: error, 2: warning, 3:info, 4:debug
define("LOG_DEFAULT_LEVEL", 4);

//  log default max lines
define("LOG_LINES_MAX", 100000);


// tmp Directory
define("TMP_DIRECTORY", "tmp");


// default periode
// 0000-00-00 n'est pas une date valide pour MySQL
define("PERIODE_DEFAULT", "0001-01-01");


// statut relation
define("STATUT_EPOUX", 1);
define("STATUT_EPOUSE", 2);
define("STATUT_PERE", 3);
define("STATUT_MERE", 4);
define("STATUT_TEMOIN", 5);
define("STATUT_PARRAIN", 6);


// default id source
define("SOURCE_DEFAULT_ID", 1);

// level access name
$level_access_name = [
    0 => "Visiteur",
    1 => "Lecteur",
    2 => "Editeur",
    3 => "Administrateur"
];

// RIGHT LEVEL ACCESS PAGES
$access_pages  = [
    "" => 0,
    "/" => 0,
    "import" => 2,
    "export" => 2,
    "acte" => 1,
    "personne" => 1,
    // je désactive fusion et dissoc en attendant le bug fix
    "fusion" => 3,
    "dissocier" => 3,
    //
    "administration" => 3,
    "logs" => 3,
    "table" => 1,
    "recherche" => 1,
    "supprimer" => 2,
    "auto_complete_personne" => 1,
    "test" => 3
]

 ?>
