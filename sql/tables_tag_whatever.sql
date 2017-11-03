SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Ci-dessous, tables `attribut`, `categorie` et `tag`,
-- qui me semblent inutilisées, à supprimer prudemment ?
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table structure for table `attribut`
-- -----------------------------------------------------
-- From dump 2017, c'était pas dans le git,
-- je vois pas où c'est utilisé à part dans `tag`,
-- qui lui même ne me semble pas utilisé

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

-- -----------------------------------------------------
-- Table `buenosaires`.`categorie`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`categorie` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`categorie` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `buenosaires`.`tag`
-- -----------------------------------------------------
-- Je vois pas où c'est utilisé, c'est vide dans le dump

DROP TABLE IF EXISTS `buenosaires`.`tag` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`tag` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(45) NOT NULL,
  `categorie_id` INT NOT NULL,
  `parent_tag` INT NOT NULL,
  `attribut_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_tag_categorie1`
    FOREIGN KEY (`categorie_id`)
    REFERENCES `buenosaires`.`categorie` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_tag1`
    FOREIGN KEY (`parent_tag`)
    REFERENCES `buenosaires`.`tag` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_tag_categorie1_idx` ON `buenosaires`.`tag` (`categorie_id` ASC);

CREATE INDEX `fk_tag_tag1_idx` ON `buenosaires`.`tag` (`parent_tag` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

