<?php
	
	mysql_query("DROP TABLE actes");
	mysql_query("DROP TABLE personnes");
	mysql_query("DROP TABLE relations");
	mysql_query("DROP TABLE mentions");
	mysql_query("DROP TABLE periodes");
	mysql_query("DROP TABLE cond");
	mysql_query("DROP TABLE statuts");
	mysql_query("DROP TABLE sources");
	mysql_query("DROP TABLE actes_contenu");
	
	mysql_query("CREATE TABLE actes (
		id_acte int(11) NOT NULL,
		epoux text COLLATE utf8_unicode_ci NOT NULL,
		epouse text COLLATE utf8_unicode_ci NOT NULL,
		periode int(11) NOT NULL,
		PRIMARY KEY (id_acte))"
	);

	mysql_query("CREATE TABLE personnes (
		id int(11) NOT NULL auto_increment,
		de1 int(1) NOT NULL,
		la1 int(1) NOT NULL,
		nom1 char(50) NOT NULL,
		de2 int(1) NOT NULL,
		la2 int(1) NOT NULL,
		nom2 char(50) NOT NULL,
		de3 int(1) NOT NULL,
		la3 int(1) NOT NULL,
		nom3 char(50) NOT NULL,
		prenom1 char(50) NOT NULL,
		prenom2 char(50) NOT NULL,
		nom1noaccent char(50) NOT NULL,
		nom2noaccent char(50) NOT NULL,
		nom3noaccent char(50) NOT NULL,
		prenom1noaccent char(50) NOT NULL,
		prenom2noaccent char(50) NOT NULL,
		periode int(11) NOT NULL,
		PRIMARY KEY (id),
		INDEX (nom1noaccent,nom2noaccent,nom3noaccent,prenom1noaccent,prenom2noaccent))"
	);

	mysql_query("CREATE TABLE relations (
		id int(11) NOT NULL auto_increment,
		personne1 int(11) NOT NULL,
		personne2 int(11) NOT NULL,
		type int(11) NOT NULL,
		periode int(11) NOT NULL,
		PRIMARY KEY (id),
		INDEX (personne1,personne2))"
	);

	mysql_query("CREATE TABLE mentions (
		id int(11) NOT NULL auto_increment,
		relation int(11) NOT NULL,
		acte int(11) NOT NULL,
		PRIMARY KEY (id))"
	);
	
	mysql_query("CREATE TABLE periodes (
		id int(11) NOT NULL auto_increment,
		minDebut text COLLATE utf8_unicode_ci NOT NULL,
		maxDebut text COLLATE utf8_unicode_ci NOT NULL,
		minFin text COLLATE utf8_unicode_ci NOT NULL,
		maxFin text COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (id))"
	);
	
	mysql_query("CREATE TABLE cond (
		id int(11) NOT NULL auto_increment,
		id_personne int(11) NOT NULL,
		cond text COLLATE utf8_unicode_ci NOT NULL,
		source int(11) NOT NULL,
		periode int(11) NOT NULL,
		acte int(11) NOT NULL,
		PRIMARY KEY (id))"
	);
	
	mysql_query("CREATE TABLE statuts (
		id int(11) NOT NULL,
		statut text COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (id))"
	);
	$statuts = array(1=>"Epoux",2=>"Epouse",3=>"Pere",4=>"Mere",5=>"Temoin");
	foreach($statuts as $key => $val) mysql_query("INSERT INTO statuts (id, statut) VALUES ('$key', '$val')");

	mysql_query("CREATE TABLE sources (
		id int(11) NOT NULL,
		source text COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (id))"
	);
	$source = array(1=>"Matrimonios");
	foreach($source as $key => $val) mysql_query("INSERT INTO sources (id, source) VALUES ('$key', '$val')");

	mysql_query("CREATE TABLE actes_contenu (
		id_acte int(11) NOT NULL,
		contenu text COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (id_acte))"
	);
	
	/*
	mysql_query("CREATE TABLE utilisateurs (
		id int(11) NOT NULL auto_increment,
		rang int(11) NOT NULL,
		pseudo char(50) NOT NULL,
		nom text COLLATE utf8_unicode_ci NOT NULL,
		prenom text COLLATE utf8_unicode_ci NOT NULL,
		pwd text COLLATE utf8_unicode_ci NOT NULL,
		amail char(80) NOT NULL,
		date_inscr text COLLATE utf8_unicode_ci NOT NULL,
		valid text COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (id),
		INDEX (pseudo,amail))"
	);

	$now = time();
	mysql_query("INSERT INTO utilisateurs (id, rang, pseudo, nom, prenom, pwd, amail, date_inscr, valid) VALUES (NULL, '2', 'Axelle', 'Piot', 'Axelle','3ab9071536d62f29aa8b3fd39141f6ad','axelle.piot@lyart.fr','$now', 'in')");
	mysql_query("INSERT INTO utilisateurs (id, rang, pseudo, nom, prenom, pwd, amail, date_inscr, valid) VALUES (NULL, '2', 'Prieur', 'Prieur', 'Christophe','b4e7fc11c7ce27c282e22d92480c7fa7','prieur@liafa.fr','$now', 'in')");
	*/
?>