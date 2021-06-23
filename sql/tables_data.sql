SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
--
-- Tables pour les données
--
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `buenosaires`.`personne`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`personne` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`personne` (
  `id` INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires`.`nom`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`nom` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`nom` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(50) NOT NULL,
  `no_accent` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires`.`prenom`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`prenom` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`prenom` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `prenom` VARCHAR(50) NOT NULL,
  `no_accent` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires`.`prenom_personne`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`prenom_personne` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`prenom_personne` (
  `personne_id` INT NOT NULL,
  `prenom_id` INT NOT NULL,
  `ordre` INT NOT NULL,
  PRIMARY KEY (`personne_id`, `prenom_id`),
  CONSTRAINT `fk_personne_has_prenom_personne`
    FOREIGN KEY (`personne_id`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_personne_has_prenom_prenom1`
    FOREIGN KEY (`prenom_id`)
    REFERENCES `buenosaires`.`prenom` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_personne_has_prenom_prenom1_idx` ON `buenosaires`.`prenom_personne` (`prenom_id` ASC);

CREATE INDEX `fk_personne_has_prenom_personne_idx` ON `buenosaires`.`prenom_personne` (`personne_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`nom_personne`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`nom_personne` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`nom_personne` (
  `personne_id` INT NOT NULL,
  `nom_id` INT NOT NULL,
  `ordre` INT NOT NULL,
  `attribut` VARCHAR(45) NULL,
  PRIMARY KEY (`personne_id`, `nom_id`),
  CONSTRAINT `fk_personne_has_nom_personne1`
    FOREIGN KEY (`personne_id`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_personne_has_nom_nom1`
    FOREIGN KEY (`nom_id`)
    REFERENCES `buenosaires`.`nom` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_personne_has_nom_nom1_idx` ON `buenosaires`.`nom_personne` (`nom_id` ASC);

CREATE INDEX `fk_personne_has_nom_personne1_idx` ON `buenosaires`.`nom_personne` (`personne_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`acte`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`acte` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`acte` (
  `id` INT NOT NULL,
  `epoux` INT NULL,
  `epouse` INT NULL,
  `date_start` DATE NULL,
  `date_end` DATE NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_acte_personne1`
    FOREIGN KEY (`epoux`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_personne2`
    FOREIGN KEY (`epouse`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_acte_personne1_idx` ON `buenosaires`.`acte` (`epoux` ASC);

CREATE INDEX `fk_acte_personne2_idx` ON `buenosaires`.`acte` (`epouse` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`acte_contenu`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`acte_contenu` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`acte_contenu` (
  `acte_id` INT NOT NULL,
  `contenu` TEXT NOT NULL,
  PRIMARY KEY (`acte_id`),
  CONSTRAINT `fk_acte_contenu_acte1`
    FOREIGN KEY (`acte_id`)
    REFERENCES `buenosaires`.`acte` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires`.`relation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`relation` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`relation` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pers_source_id` INT NOT NULL,
  `pers_destination_id` INT NOT NULL,
  `statut_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_relation_personne1`
    FOREIGN KEY (`pers_source_id`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relation_personne2`
    FOREIGN KEY (`pers_destination_id`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relation_status1`
    FOREIGN KEY (`statut_id`)
    REFERENCES `buenosaires`.`statut` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_relation_personne1_idx` ON `buenosaires`.`relation` (`pers_source_id` ASC);

CREATE INDEX `fk_relation_personne2_idx` ON `buenosaires`.`relation` (`pers_destination_id` ASC);

CREATE INDEX `fk_relation_status1_idx` ON `buenosaires`.`relation` (`statut_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`acte_has_relation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`acte_has_relation` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`acte_has_relation` (
  `acte_id` INT NOT NULL,
  `relation_id` INT NOT NULL,
  PRIMARY KEY (`acte_id`, `relation_id`),
  CONSTRAINT `fk_acte_has_relation_acte1`
    FOREIGN KEY (`acte_id`)
    REFERENCES `buenosaires`.`acte` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_has_relation_relation1`
    FOREIGN KEY (`relation_id`)
    REFERENCES `buenosaires`.`relation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_acte_has_relation_relation1_idx` ON `buenosaires`.`acte_has_relation` (`relation_id` ASC);

CREATE INDEX `fk_acte_has_relation_acte1_idx` ON `buenosaires`.`acte_has_relation` (`acte_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`condition`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`condition` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`condition` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `text` TEXT NOT NULL,
  `source_id` INT NOT NULL,
  `personne_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_cond_source1`
    FOREIGN KEY (`source_id`)
    REFERENCES `buenosaires`.`source` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cond_personne1`
    FOREIGN KEY (`personne_id`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_cond_source1_idx` ON `buenosaires`.`condition` (`source_id` ASC);

CREATE INDEX `fk_cond_personne1_idx` ON `buenosaires`.`condition` (`personne_id` ASC);



-- -----------------------------------------------------
-- Table `buenosaires`.`acte_has_condition`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`acte_has_condition` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`acte_has_condition` (
  `acte_id` INT NOT NULL,
  `condition_id` INT NOT NULL,
  PRIMARY KEY (`acte_id`, `condition_id`),
  CONSTRAINT `fk_acte_has_condition_acte1`
    FOREIGN KEY (`acte_id`)
    REFERENCES `buenosaires`.`acte` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_has_condition_condition1`
    FOREIGN KEY (`condition_id`)
    REFERENCES `buenosaires`.`condition` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_acte_has_condition_condition1_idx` ON `buenosaires`.`acte_has_condition` (`condition_id` ASC);

CREATE INDEX `fk_acte_has_condition_acte1_idx` ON `buenosaires`.`acte_has_condition` (`acte_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`variable`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`variable` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`variable` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `valeur` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Data for table `buenosaires`.`variable`
-- -----------------------------------------------------
START TRANSACTION;
USE `buenosaires`;
INSERT INTO `buenosaires`.`variable` (`id`, `nom`, `valeur`) VALUES (NULL, 'PERSONNE_ID_MAX', '15267');

COMMIT;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
