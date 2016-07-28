CREATE TABLE utilisateurs (
    id int(11) NOT NULL auto_increment,
    rang int(11) NOT NULL,
    pseudo char(50) NOT NULL,
    nom text COLLATE utf8_unicode_ci NOT NULL,
    prenom text COLLATE utf8_unicode_ci NOT NULL,
    pwd text COLLATE utf8_unicode_ci NOT NULL,
    amail char(80) NOT NULL,
    date_inscr int(11) NOT NULL,
    valid text COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (id),
    INDEX (pseudo,amail))
;
INSERT INTO utilisateurs (rang, pseudo, nom, prenom, pwd, amail, date_inscr, valid)
  VALUES
  (2, 'Axelle', 'Piot', 'Axelle','3ab9071536d62f29aa8b3fd39141f6ad',
  'axelle.piot@lyart.fr',1372655717, 'in')
;
INSERT INTO utilisateurs (rang, pseudo, nom, prenom, pwd, amail, date_inscr, valid)
  VALUES
  (2, 'Prieur', 'Prieur', 'Christophe','19854811c214c4f8226e2d6ee9008015',
  'prieur@liafa.fr',1372655717, 'in')
;
