SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `buenosaires` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `buenosaires` ;

-- -----------------------------------------------------
--
-- Tables pour les métadonnées 
--
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `buenosaires`.`statut`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`statut` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`statut` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `valeur` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Data for table `buenosaires`.`statut`
-- -----------------------------------------------------
START TRANSACTION;
USE `buenosaires`;
INSERT INTO `buenosaires`.`statut` (`id`, `valeur`) VALUES (1, 'epoux');
INSERT INTO `buenosaires`.`statut` (`id`, `valeur`) VALUES (2, 'epouse');
INSERT INTO `buenosaires`.`statut` (`id`, `valeur`) VALUES (3, 'pere');
INSERT INTO `buenosaires`.`statut` (`id`, `valeur`) VALUES (4, 'mere');
INSERT INTO `buenosaires`.`statut` (`id`, `valeur`) VALUES (5, 'temoin');
INSERT INTO `buenosaires`.`statut` (`id`, `valeur`) VALUES (6, 'parrain');

COMMIT;


-- -----------------------------------------------------
-- Table `buenosaires`.`source`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`source` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`source` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `valeur` TEXT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Data for table `buenosaires`.`source`
-- -----------------------------------------------------
START TRANSACTION;
USE `buenosaires`;
INSERT INTO `buenosaires`.`source` (`id`, `valeur`) VALUES (1, 'Matrimonios');

COMMIT;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;



