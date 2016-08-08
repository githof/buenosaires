<?php

// base URL for all relative URLs
// define("BASE_URL", "https://buenosaires.telecom-paristech.fr/");

define("BASE_URL", "http://localhost/buenosaires/");


//  Server MYSQL
// define("SQL_SERVER", "mysql-projet.enst.fr");
// define("SQL_DATABASE_NAME", "buenosaires_TPT");
// define("SQL_USER", "buenosaires.TPT");
// define("SQL_PASS", "UvPsQ8RrB3OuDApImf5Y");

define("SQL_SERVER", "localhost");
define("SQL_DATABASE_NAME", "buenosaires_TPT");
define("SQL_USER", "root");
define("SQL_PASS", "root");

//  log default output file
define("LOG_DEFAULT_OUTPUT", "log.txt");

//  log defaut level
//  0: none, 1: error, 2: warning, 3:info, 4:debug
define("LOG_DEFAULT_LEVEL", 3);


// tmp Directory
define("TMP_DIRECTORY", "./tmp");


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

 ?>
