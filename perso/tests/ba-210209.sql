-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 09 fév. 2021 à 12:11
-- Version du serveur :  8.0.23-0ubuntu0.20.04.1
-- Version de PHP : 7.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `buenosaires`
--

-- --------------------------------------------------------

--
-- Structure de la table `acte`
--

CREATE TABLE `acte` (
  `id` int NOT NULL,
  `epoux` int DEFAULT NULL,
  `epouse` int DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `acte`
--

INSERT INTO `acte` (`id`, `epoux`, `epouse`, `date_start`, `date_end`) VALUES
(4227, 404, 515, '1757-11-04', '1757-11-04'),
(4789, 459, 401, '1785-06-13', '1785-06-13'),
(6813, 413, 414, '1808-05-09', '1808-05-09');

-- --------------------------------------------------------

--
-- Structure de la table `acte_contenu`
--

CREATE TABLE `acte_contenu` (
  `acte_id` int NOT NULL,
  `contenu` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `acte_contenu`
--

INSERT INTO `acte_contenu` (`acte_id`, `contenu`) VALUES
(4227, '\n<ACTE id=\"4227\">4253) <date>4-11-1757</date>: <epoux id=\"404\" don=\"true\">Dn. <prenom>Domingo</prenom> de <nom attr=\"de\">BELGRANO</nom>, natural de la ciudad de <naissance-lieu>Oneglia, Italia, Estado de G&#xE9;nova, Reino de Cerde&#xF1;a</naissance-lieu>, hijo leg&#xED;timo de <pere id=\"433\" don=\"true\"><prenom>Carlos</prenom> de <nom attr=\"de\">Belgrano</nom></pere> y de <mere id=\"434\" don=\"true\">Da. <prenom>Mar&#xED;a</prenom> <nom>Peri</nom></mere></epoux>, con <epouse id=\"515\" don=\"true\">Da. <prenom>Mar&#xED;a</prenom> <prenom>Josefa</prenom> <nom>GONZALEZ</nom>, natural de <naissance-lieu>&#xE9;sta</naissance-lieu>, hija leg&#xED;tima de <pere id=\"435\"> <prenom>Juan</prenom> <prenom>Manuel</prenom> <nom>Gonz&#xE1;lez </nom> </pere>y de <mere id=\"436\" don=\"true\">Da. <prenom>In&#xE9;s</prenom> <nom>Casero</nom></mere></epouse>. Ts.: <temoins><temoin id=\"437\" don=\"true\">Dn. <prenom>Jos&#xE9;</prenom> <nom>Molina</nom>, <condition>Secretario de este Gobierno</condition> y <condition>Capitan&#xED;a General</condition> y <condition>Capit&#xE1;n de este Real Presidio</condition> </temoin>y <temoin id=\"190\" don=\"true\">Da. <prenom>Gregoria</prenom> de <nom attr=\"de\">Salas</nom></temoin></temoins>. Al margen: \"copiada a pedimento de dicho Dn. Domingo Belgrano en 27-11-1765\". (f. 86).</ACTE>\n'),
(4789, '\n<ACTE id=\"4789\">4811) <date>13-6-1785</date>: Dn. <epoux id=\"459\" don=\"true\"><prenom>Jos&#xE9; </prenom><prenom>Mar&#xED;a</prenom> <nom>CALDERON</nom> de la <nom attr=\"de la\">BARCA</nom>, natural de la ciudad de <naissance-lieu>Sevilla</naissance-lieu>, hijo leg&#xED;timo de <pere id=\"402\" don=\"true\">Dn. <prenom>Antonio </prenom><prenom>Francisco</prenom> <nom>Calder&#xF3;n</nom> de la <nom attr=\"de la\">Barca</nom></pere>, y de <mere id=\"403\" don=\"true\">Da. <prenom>Isabel </prenom><prenom>Ana</prenom> de <nom attr=\"de\">Vera</nom></mere></epoux>, con <epouse id=\"401\" don=\"true\">Da. <prenom>Mar&#xED;a </prenom><prenom>Josefa</prenom> <nom>BELGRANO</nom> <nom>PEREZ</nom>, natural de <naissance-lieu>&#xE9;sta</naissance-lieu>, <pere id=\"404\" don=\"true\">hija leg&#xED;tima del r (Dn. <prenom>Domingo</prenom> Belgrano P&#xE9;rez) [<nom>Belgrano</nom> <condition>Capit&#xE1;n de Milicias Urbanas</condition> y <condition>Alf&#xE9;rez Real que ha sido de &#xE9;sta</condition> y <condition>Regidor</condition> y <condition>su S&#xED;ndico Procurador</condition><nom>Peri</nom>] </pere>y de<mere id=\"515\" don=\"true\"> Da. <prenom>Josefa </prenom><nom>Gonz&#xE1;lez</nom></mere>.</epouse> Ts.: <temoins><temoin id=\"404\" don=\"true\">Dn. <prenom>Domingo </prenom><nom>Belgrano </nom> <nom>Peri</nom> <condition>Capit&#xE1;n de Milicias Urbanas </condition>y <condition>Alf&#xE9;rez Real </condition>que ha sido de &#xE9;sta y <condition>Regidor </condition>y su <condition>S&#xED;ndico Procurador </condition></temoin>y <temoin id=\"405\" don=\"true\">Da. <prenom>Josefa </prenom><nom>Gonz&#xE1;lez</nom></temoin></temoins>, padres de la contrayente. Al margen: \"saqu&#xE9; copia a 30-6-1786\". (f. 430).</ACTE>\n'),
(6813, '\n<ACTE id=\"6813\">6849) <date>9-5-1808</date>: <epoux don=\"true\" id=\"413\">Dn. <prenom>Joaqu&#xED;n </prenom><nom>BELGRANO</nom> <nom>GONZALEZ</nom>, natural de <naissance-lieu>&#xE9;sta</naissance-lieu>, <condition>Oficial Real Honorario</condition>, hijo leg&#xED;timo de Dn. <pere id=\"404\"> <prenom>Domingo </prenom> <nom>Belgrano</nom> <nom>Peri</nom> </pere>y de Da. <mere id=\"515\"> <prenom>Mar&#xED;a</prenom> <prenom>Josefa </prenom> <nom>Gonz&#xE1;lez</nom> </mere></epoux>, con <epouse don=\"true\" id=\"414\">Da. <prenom>Catalina </prenom><nom>MELIAN</nom>, natural de <naissance-lieu>&#xE9;sta</naissance-lieu>, hija leg&#xED;tima de <pere don=\"true\" id=\"416\">Dn. <prenom>Antonio </prenom><nom>Meli&#xE1;n</nom> </pere>y de Da. <mere don=\"true\" id=\"417\"> <prenom>Mar&#xED;a </prenom> <prenom>Josefa</prenom> <nom>Correa</nom> (viuda de <veuve don=\"true\">Dn. <prenom>Juan</prenom> <prenom>Tom&#xE1;s</prenom> <nom>Mart&#xED;nez</nom></veuve>)</mere></epouse>. Ts.: <temoins>el <temoin don=\"true\" id=\"418\"><condition>Cl&#xE9;rigo Presb&#xED;tero</condition> Dn. <prenom>Francisco</prenom> <nom>Robles</nom></temoin> y Dn. <temoin id=\"419\"> <prenom>Francisco </prenom> <nom>Belgrano</nom> </temoin></temoins>. (f. 558).</ACTE>\n');

-- --------------------------------------------------------

--
-- Structure de la table `acte_has_condition`
--

CREATE TABLE `acte_has_condition` (
  `acte_id` int NOT NULL,
  `condition_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `acte_has_relation`
--

CREATE TABLE `acte_has_relation` (
  `acte_id` int NOT NULL,
  `relation_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `attribut`
--

CREATE TABLE `attribut` (
  `id` int NOT NULL,
  `value` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id` int NOT NULL,
  `value` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `condition`
--

CREATE TABLE `condition` (
  `id` int NOT NULL,
  `text` text NOT NULL,
  `source_id` int NOT NULL,
  `personne_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `nom`
--

CREATE TABLE `nom` (
  `id` int NOT NULL,
  `nom` varchar(50) NOT NULL,
  `no_accent` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `nom`
--

INSERT INTO `nom` (`id`, `nom`, `no_accent`) VALUES
(775, 'BELGRANO', 'BELGRANO'),
(776, 'Abastos', 'Abastos');

-- --------------------------------------------------------

--
-- Structure de la table `nom_personne`
--

CREATE TABLE `nom_personne` (
  `personne_id` int NOT NULL,
  `nom_id` int NOT NULL,
  `ordre` int NOT NULL,
  `attribut` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `nom_personne`
--

INSERT INTO `nom_personne` (`personne_id`, `nom_id`, `ordre`, `attribut`) VALUES
(401, 775, 1, NULL),
(404, 775, 1, 'de'),
(413, 775, 1, NULL),
(419, 775, 1, NULL),
(433, 775, 1, 'de');

-- --------------------------------------------------------

--
-- Structure de la table `periode`
--

CREATE TABLE `periode` (
  `id` int NOT NULL,
  `debut_min` date NOT NULL,
  `debut_max` date NOT NULL,
  `fin_min` date NOT NULL,
  `fin_max` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `personne`
--

CREATE TABLE `personne` (
  `id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `personne`
--

INSERT INTO `personne` (`id`) VALUES
(190),
(401),
(402),
(403),
(404),
(405),
(413),
(414),
(416),
(417),
(418),
(419),
(433),
(434),
(435),
(436),
(437),
(459),
(515);

-- --------------------------------------------------------

--
-- Structure de la table `prenom`
--

CREATE TABLE `prenom` (
  `id` int NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `no_accent` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `prenom`
--

INSERT INTO `prenom` (`id`, `prenom`, `no_accent`) VALUES
(300, 'mendoza', 'mendoza'),
(301, 'sancho', 'sancho');

-- --------------------------------------------------------

--
-- Structure de la table `prenom_personne`
--

CREATE TABLE `prenom_personne` (
  `personne_id` int NOT NULL,
  `prenom_id` int NOT NULL,
  `ordre` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `relation`
--

CREATE TABLE `relation` (
  `id` int NOT NULL,
  `pers_source_id` int NOT NULL,
  `pers_destination_id` int NOT NULL,
  `statut_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `source`
--

CREATE TABLE `source` (
  `id` int NOT NULL,
  `valeur` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `statut`
--

CREATE TABLE `statut` (
  `id` int NOT NULL,
  `valeur` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tag`
--

CREATE TABLE `tag` (
  `id` int NOT NULL,
  `value` varchar(45) NOT NULL,
  `categorie_id` int NOT NULL,
  `parent_tag` int NOT NULL,
  `attribut_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pwd` varchar(100) NOT NULL,
  `date_inscr` date NOT NULL,
  `rang` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `pwd`, `date_inscr`, `rang`) VALUES
(4, 'prieur', 'morgan', 'ba-tp@chatnoir.lautre.net', '5dd23b6d061e802a863d7e50af3e4a22', '2021-02-02', 3),
(5, 'pri', 'morg', 'morg@prieur.net', '0b4e7a0e5fe84ad35fb5f95b9ceeac79', '2021-02-04', 2);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `acte`
--
ALTER TABLE `acte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_acte_personne1_idx` (`epoux`),
  ADD KEY `fk_acte_personne2_idx` (`epouse`);

--
-- Index pour la table `acte_contenu`
--
ALTER TABLE `acte_contenu`
  ADD PRIMARY KEY (`acte_id`);

--
-- Index pour la table `acte_has_condition`
--
ALTER TABLE `acte_has_condition`
  ADD PRIMARY KEY (`acte_id`,`condition_id`),
  ADD KEY `fk_acte_has_condition_condition1_idx` (`condition_id`),
  ADD KEY `fk_acte_has_condition_acte1_idx` (`acte_id`);

--
-- Index pour la table `acte_has_relation`
--
ALTER TABLE `acte_has_relation`
  ADD PRIMARY KEY (`acte_id`,`relation_id`),
  ADD KEY `fk_acte_has_relation_relation1_idx` (`relation_id`),
  ADD KEY `fk_acte_has_relation_acte1_idx` (`acte_id`);

--
-- Index pour la table `attribut`
--
ALTER TABLE `attribut`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `condition`
--
ALTER TABLE `condition`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cond_source1_idx` (`source_id`),
  ADD KEY `fk_cond_personne1_idx` (`personne_id`);

--
-- Index pour la table `nom`
--
ALTER TABLE `nom`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `nom_personne`
--
ALTER TABLE `nom_personne`
  ADD PRIMARY KEY (`personne_id`,`nom_id`),
  ADD KEY `fk_personne_has_nom_nom1_idx` (`nom_id`),
  ADD KEY `fk_personne_has_nom_personne1_idx` (`personne_id`);

--
-- Index pour la table `periode`
--
ALTER TABLE `periode`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `personne`
--
ALTER TABLE `personne`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `prenom`
--
ALTER TABLE `prenom`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `prenom_personne`
--
ALTER TABLE `prenom_personne`
  ADD PRIMARY KEY (`personne_id`,`prenom_id`),
  ADD KEY `fk_personne_has_prenom_prenom1_idx` (`prenom_id`),
  ADD KEY `fk_personne_has_prenom_personne_idx` (`personne_id`);

--
-- Index pour la table `relation`
--
ALTER TABLE `relation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_relation_personne1_idx` (`pers_source_id`),
  ADD KEY `fk_relation_personne2_idx` (`pers_destination_id`),
  ADD KEY `fk_relation_status1_idx` (`statut_id`);

--
-- Index pour la table `source`
--
ALTER TABLE `source`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `statut`
--
ALTER TABLE `statut`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tag_categorie1_idx` (`categorie_id`),
  ADD KEY `fk_tag_tag1_idx` (`parent_tag`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `attribut`
--
ALTER TABLE `attribut`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `condition`
--
ALTER TABLE `condition`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1423;

--
-- AUTO_INCREMENT pour la table `nom`
--
ALTER TABLE `nom`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=777;

--
-- AUTO_INCREMENT pour la table `periode`
--
ALTER TABLE `periode`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1473;

--
-- AUTO_INCREMENT pour la table `personne`
--
ALTER TABLE `personne`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10065;

--
-- AUTO_INCREMENT pour la table `prenom`
--
ALTER TABLE `prenom`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=302;

--
-- AUTO_INCREMENT pour la table `relation`
--
ALTER TABLE `relation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2814;

--
-- AUTO_INCREMENT pour la table `source`
--
ALTER TABLE `source`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `statut`
--
ALTER TABLE `statut`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `acte`
--
ALTER TABLE `acte`
  ADD CONSTRAINT `fk_acte_personne1` FOREIGN KEY (`epoux`) REFERENCES `personne` (`id`),
  ADD CONSTRAINT `fk_acte_personne2` FOREIGN KEY (`epouse`) REFERENCES `personne` (`id`);

--
-- Contraintes pour la table `acte_contenu`
--
ALTER TABLE `acte_contenu`
  ADD CONSTRAINT `fk_acte_contenu_acte1` FOREIGN KEY (`acte_id`) REFERENCES `acte` (`id`);

--
-- Contraintes pour la table `acte_has_condition`
--
ALTER TABLE `acte_has_condition`
  ADD CONSTRAINT `fk_acte_has_condition_acte1` FOREIGN KEY (`acte_id`) REFERENCES `acte` (`id`),
  ADD CONSTRAINT `fk_acte_has_condition_condition1` FOREIGN KEY (`condition_id`) REFERENCES `condition` (`id`);

--
-- Contraintes pour la table `acte_has_relation`
--
ALTER TABLE `acte_has_relation`
  ADD CONSTRAINT `fk_acte_has_relation_acte1` FOREIGN KEY (`acte_id`) REFERENCES `acte` (`id`),
  ADD CONSTRAINT `fk_acte_has_relation_relation1` FOREIGN KEY (`relation_id`) REFERENCES `relation` (`id`);

--
-- Contraintes pour la table `condition`
--
ALTER TABLE `condition`
  ADD CONSTRAINT `fk_cond_personne1` FOREIGN KEY (`personne_id`) REFERENCES `personne` (`id`),
  ADD CONSTRAINT `fk_cond_source1` FOREIGN KEY (`source_id`) REFERENCES `source` (`id`);

--
-- Contraintes pour la table `nom_personne`
--
ALTER TABLE `nom_personne`
  ADD CONSTRAINT `fk_personne_has_nom_nom1` FOREIGN KEY (`nom_id`) REFERENCES `nom` (`id`),
  ADD CONSTRAINT `fk_personne_has_nom_personne1` FOREIGN KEY (`personne_id`) REFERENCES `personne` (`id`);

--
-- Contraintes pour la table `prenom_personne`
--
ALTER TABLE `prenom_personne`
  ADD CONSTRAINT `fk_personne_has_prenom_personne` FOREIGN KEY (`personne_id`) REFERENCES `personne` (`id`),
  ADD CONSTRAINT `fk_personne_has_prenom_prenom1` FOREIGN KEY (`prenom_id`) REFERENCES `prenom` (`id`);

--
-- Contraintes pour la table `relation`
--
ALTER TABLE `relation`
  ADD CONSTRAINT `fk_relation_personne1` FOREIGN KEY (`pers_source_id`) REFERENCES `personne` (`id`),
  ADD CONSTRAINT `fk_relation_personne2` FOREIGN KEY (`pers_destination_id`) REFERENCES `personne` (`id`),
  ADD CONSTRAINT `fk_relation_status1` FOREIGN KEY (`statut_id`) REFERENCES `statut` (`id`);

--
-- Contraintes pour la table `tag`
--
ALTER TABLE `tag`
  ADD CONSTRAINT `fk_tag_categorie1` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`),
  ADD CONSTRAINT `fk_tag_tag1` FOREIGN KEY (`parent_tag`) REFERENCES `tag` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
