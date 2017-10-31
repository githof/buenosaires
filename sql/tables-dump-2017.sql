-- MySQL dump 10.15  Distrib 10.0.32-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: mysql-projet.enst.fr    Database: buenosaires
-- ------------------------------------------------------
-- Server version	10.0.32-MariaDB-0+deb8u1

--
-- Table structure for table `acte`
--

DROP TABLE IF EXISTS `acte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acte` (
  `id` int(11) NOT NULL,
  `epoux` int(11) DEFAULT NULL,
  `epouse` int(11) DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_acte_personne1_idx` (`epoux`),
  KEY `fk_acte_personne2_idx` (`epouse`),
  CONSTRAINT `fk_acte_personne1` FOREIGN KEY (`epoux`) REFERENCES `personne` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_personne2` FOREIGN KEY (`epouse`) REFERENCES `personne` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acte_contenu`
--

DROP TABLE IF EXISTS `acte_contenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acte_contenu` (
  `acte_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  PRIMARY KEY (`acte_id`),
  CONSTRAINT `fk_acte_contenu_acte1` FOREIGN KEY (`acte_id`) REFERENCES `acte` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acte_has_condition`
--

DROP TABLE IF EXISTS `acte_has_condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acte_has_condition` (
  `acte_id` int(11) NOT NULL,
  `condition_id` int(11) NOT NULL,
  PRIMARY KEY (`acte_id`,`condition_id`),
  KEY `fk_acte_has_condition_condition1_idx` (`condition_id`),
  KEY `fk_acte_has_condition_acte1_idx` (`acte_id`),
  CONSTRAINT `fk_acte_has_condition_acte1` FOREIGN KEY (`acte_id`) REFERENCES `acte` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_has_condition_condition1` FOREIGN KEY (`condition_id`) REFERENCES `condition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acte_has_relation`
--

DROP TABLE IF EXISTS `acte_has_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acte_has_relation` (
  `acte_id` int(11) NOT NULL,
  `relation_id` int(11) NOT NULL,
  PRIMARY KEY (`acte_id`,`relation_id`),
  KEY `fk_acte_has_relation_relation1_idx` (`relation_id`),
  KEY `fk_acte_has_relation_acte1_idx` (`acte_id`),
  CONSTRAINT `fk_acte_has_relation_acte1` FOREIGN KEY (`acte_id`) REFERENCES `acte` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_has_relation_relation1` FOREIGN KEY (`relation_id`) REFERENCES `relation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attribut`
--

DROP TABLE IF EXISTS `attribut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attribut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attribut`
--

LOCK TABLES `attribut` WRITE;
/*!40000 ALTER TABLE `attribut` DISABLE KEYS */;
INSERT INTO `attribut` VALUES (1,'de la'),(2,'de'),(3,'y');
/*!40000 ALTER TABLE `attribut` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `condition`
--

DROP TABLE IF EXISTS `condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `source_id` int(11) NOT NULL,
  `personne_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cond_source1_idx` (`source_id`),
  KEY `fk_cond_personne1_idx` (`personne_id`),
  CONSTRAINT `fk_cond_personne1` FOREIGN KEY (`personne_id`) REFERENCES `personne` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cond_source1` FOREIGN KEY (`source_id`) REFERENCES `source` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1423;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nom`
--

DROP TABLE IF EXISTS `nom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `no_accent` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=775;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nom_personne`
--

DROP TABLE IF EXISTS `nom_personne`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nom_personne` (
  `personne_id` int(11) NOT NULL,
  `nom_id` int(11) NOT NULL,
  `ordre` int(11) NOT NULL,
  `attribut` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`personne_id`,`nom_id`),
  KEY `fk_personne_has_nom_nom1_idx` (`nom_id`),
  KEY `fk_personne_has_nom_personne1_idx` (`personne_id`),
  CONSTRAINT `fk_personne_has_nom_nom1` FOREIGN KEY (`nom_id`) REFERENCES `nom` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_personne_has_nom_personne1` FOREIGN KEY (`personne_id`) REFERENCES `personne` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `periode`
--
-- vieux truc de la version d'Axelle, on garde pas

DROP TABLE IF EXISTS `periode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `debut_min` date NOT NULL,
  `debut_max` date NOT NULL,
  `fin_min` date NOT NULL,
  `fin_max` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1473;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personne`
--

DROP TABLE IF EXISTS `personne`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personne` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10065;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prenom`
--

DROP TABLE IF EXISTS `prenom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prenom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prenom` varchar(50) NOT NULL,
  `no_accent` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=300;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prenom_personne`
--

DROP TABLE IF EXISTS `prenom_personne`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prenom_personne` (
  `personne_id` int(11) NOT NULL,
  `prenom_id` int(11) NOT NULL,
  `ordre` int(11) NOT NULL,
  PRIMARY KEY (`personne_id`,`prenom_id`),
  KEY `fk_personne_has_prenom_prenom1_idx` (`prenom_id`),
  KEY `fk_personne_has_prenom_personne_idx` (`personne_id`),
  CONSTRAINT `fk_personne_has_prenom_personne` FOREIGN KEY (`personne_id`) REFERENCES `personne` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_personne_has_prenom_prenom1` FOREIGN KEY (`prenom_id`) REFERENCES `prenom` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relation`
--

DROP TABLE IF EXISTS `relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pers_source_id` int(11) NOT NULL,
  `pers_destination_id` int(11) NOT NULL,
  `statut_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_relation_personne1_idx` (`pers_source_id`),
  KEY `fk_relation_personne2_idx` (`pers_destination_id`),
  KEY `fk_relation_status1_idx` (`statut_id`),
  CONSTRAINT `fk_relation_personne1` FOREIGN KEY (`pers_source_id`) REFERENCES `personne` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_relation_personne2` FOREIGN KEY (`pers_destination_id`) REFERENCES `personne` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_relation_status1` FOREIGN KEY (`statut_id`) REFERENCES `statut` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2814;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `source`
--

DROP TABLE IF EXISTS `source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valeur` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `statut`
--

DROP TABLE IF EXISTS `statut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valeur` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(45) NOT NULL,
  `categorie_id` int(11) NOT NULL,
  `parent_tag` int(11) NOT NULL,
  `attribut_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tag_categorie1_idx` (`categorie_id`),
  KEY `fk_tag_tag1_idx` (`parent_tag`),
  CONSTRAINT `fk_tag_categorie1` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_tag1` FOREIGN KEY (`parent_tag`) REFERENCES `tag` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pwd` varchar(100) NOT NULL,
  `date_inscr` date NOT NULL,
  `rang` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `variable`
--

DROP TABLE IF EXISTS `variable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `valeur` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variable`
--

LOCK TABLES `variable` WRITE;
/*!40000 ALTER TABLE `variable` DISABLE KEYS */;

/*!40000 ALTER TABLE `variable` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-10-25 11:28:32
