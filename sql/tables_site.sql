SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
--
-- Tables pour le site
--
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `buenosaires`.`utilisateurs`   ok  16/03/21 
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`utilisateurs` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`utilisateurs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(50) NOT NULL,
  `prenom` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `pwd` VARCHAR(100) NOT NULL,
  `date_inscr` DATE NOT NULL,
  `rang` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
INSERT INTO `utilisateurs` VALUES (1,'Des','Ced','aa@aa.com','440ac85892ca43ad26d44c7ad9d47d3e','2016-09-20',3),(2,'Prieur','Christophe','prieur@didiode.fr','f0f9c00c80dd888f66c3020b61e04e1e','2016-09-20',3),(3,'Moutoukias','Zacarias','zacarias.moutoukias@univ-paris-diderot.fr','f59484ac49e4281cf6d8e17ebd8d997e','2017-04-06',3),(4,'Prieur','Morgan','ba-tp@chatnoir.lautre.net','5dd23b6d061e802a863d7e50af3e4a22','2021-01-25',3);
UNLOCK TABLES;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

