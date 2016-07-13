DROP TABLE IF EXISTS actes;
DROP TABLE IF EXISTS personnes;
DROP TABLE IF EXISTS relations;
DROP TABLE IF EXISTS mentions;
DROP TABLE IF EXISTS periodes;
DROP TABLE IF EXISTS cond;
DROP TABLE IF EXISTS statuts;
DROP TABLE IF EXISTS sources;
DROP TABLE IF EXISTS actes_contenu;
DROP TABLE IF EXISTS utilisateurs;


CREATE TABLE actes (
		id_acte int(11) NOT NULL,
		epoux text COLLATE utf8_unicode_ci NOT NULL,
		epouse text COLLATE utf8_unicode_ci NOT NULL,
		periode int(11) NOT NULL,
		PRIMARY KEY (id_acte)
);

CREATE TABLE personnes (
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
		INDEX (nom1noaccent,nom2noaccent,nom3noaccent,prenom1noaccent,prenom2noaccent)
);

CREATE TABLE relations (
		id int(11) NOT NULL auto_increment,
		personne1 int(11) NOT NULL,
		personne2 int(11) NOT NULL,
		type int(11) NOT NULL,
		periode int(11) NOT NULL,
		PRIMARY KEY (id),
		INDEX (personne1,personne2)
);

CREATE TABLE mentions (
		id int(11) NOT NULL auto_increment,
		relation int(11) NOT NULL,
		acte int(11) NOT NULL,
		PRIMARY KEY (id)
);

CREATE TABLE periodes (
		id int(11) NOT NULL auto_increment,
		minDebut text COLLATE utf8_unicode_ci NOT NULL,
		maxDebut text COLLATE utf8_unicode_ci NOT NULL,
		minFin text COLLATE utf8_unicode_ci NOT NULL,
		maxFin text COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (id)
);

CREATE TABLE cond (
		id int(11) NOT NULL auto_increment,
		id_personne int(11) NOT NULL,
		cond text COLLATE utf8_unicode_ci NOT NULL,
		source int(11) NOT NULL,
		periode int(11) NOT NULL,
		acte int(11) NOT NULL,
		PRIMARY KEY (id)
);

CREATE TABLE statuts (
		id int(11) NOT NULL,
		statut text COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (id)
);

INSERT INTO statuts (id, statut) VALUES ('1', 'Epoux');
INSERT INTO statuts (id, statut) VALUES ('2', 'Epouse');
INSERT INTO statuts (id, statut) VALUES ('3', 'Pere');
INSERT INTO statuts (id, statut) VALUES ('4', 'Mere');
INSERT INTO statuts (id, statut) VALUES ('5', 'Temoin');

CREATE TABLE sources (
		id int(11) NOT NULL,
		source text COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (id)
);

INSERT INTO sources (id, source) VALUES ('1', 'Matrimonios');

CREATE TABLE actes_contenu (
		id_acte int(11) NOT NULL,
		contenu text COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (id_acte)
);

CREATE TABLE utilisateurs (
    id int(11) NOT NULL auto_increment,
    rang int(11) NOT NULL,
    nom text COLLATE utf8_unicode_ci NOT NULL,
    prenom text COLLATE utf8_unicode_ci NOT NULL,
    pwd text COLLATE utf8_unicode_ci NOT NULL,
    amail char(80) NOT NULL,
    date_inscr date NOT NULL,
    valid text COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (id)
);
