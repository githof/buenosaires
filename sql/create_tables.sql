
-- -----------------------------------------------------
-- Schema buenosaires
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `buenosaires` ;

-- -----------------------------------------------------
-- Schema buenosaires
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `buenosaires` DEFAULT CHARACTER SET utf8 ;
USE `buenosaires` ;

-- -----------------------------------------------------
-- Table `buenosaires`.`periode`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`periode` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`periode` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `debut_min` DATE NOT NULL,
  `debut_max` DATE NOT NULL,
  `fin_min` DATE NOT NULL,
  `fin_max` DATE NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires`.`personne`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`personne` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`personne` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `periode_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_personne_periode1`
    FOREIGN KEY (`periode_id`)
    REFERENCES `buenosaires`.`periode` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_personne_periode1_idx` ON `buenosaires`.`personne` (`periode_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`attribut`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`attribut` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`attribut` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires`.`nom`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`nom` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`nom` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(50) NOT NULL,
  `no_accent` VARCHAR(50) NOT NULL,
  `attribut_id` INT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_nom_attribut1`
    FOREIGN KEY (`attribut_id`)
    REFERENCES `buenosaires`.`attribut` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_nom_attribut1_idx` ON `buenosaires`.`nom` (`attribut_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`prenom`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`prenom` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`prenom` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(50) NOT NULL,
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
-- Table `buenosaires`.`source`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`source` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`source` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires`.`cond`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`cond` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`cond` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` TEXT NOT NULL,
  `source_id` INT NOT NULL,
  `periode_id` INT NOT NULL,
  `personne_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_cond_source1`
    FOREIGN KEY (`source_id`)
    REFERENCES `buenosaires`.`source` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cond_periode1`
    FOREIGN KEY (`periode_id`)
    REFERENCES `buenosaires`.`periode` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cond_personne1`
    FOREIGN KEY (`personne_id`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_cond_source1_idx` ON `buenosaires`.`cond` (`source_id` ASC);

CREATE INDEX `fk_cond_periode1_idx` ON `buenosaires`.`cond` (`periode_id` ASC);

CREATE INDEX `fk_cond_personne1_idx` ON `buenosaires`.`cond` (`personne_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`acte`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`acte` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`acte` (
  `id` INT NOT NULL,
  `periode_id` INT NULL,
  `epoux` INT NULL,
  `epouse` INT NULL,
  `cond_id` INT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_acte_periode1`
    FOREIGN KEY (`periode_id`)
    REFERENCES `buenosaires`.`periode` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_personne1`
    FOREIGN KEY (`epoux`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_personne2`
    FOREIGN KEY (`epouse`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_cond1`
    FOREIGN KEY (`cond_id`)
    REFERENCES `buenosaires`.`cond` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_acte_periode1_idx` ON `buenosaires`.`acte` (`periode_id` ASC);

CREATE INDEX `fk_acte_personne1_idx` ON `buenosaires`.`acte` (`epoux` ASC);

CREATE INDEX `fk_acte_personne2_idx` ON `buenosaires`.`acte` (`epouse` ASC);

CREATE INDEX `fk_acte_cond1_idx` ON `buenosaires`.`acte` (`cond_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`statut`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`statut` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`statut` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE UNIQUE INDEX `value_UNIQUE` ON `buenosaires`.`statut` (`value` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`relation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`relation` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`relation` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `source` INT NOT NULL,
  `destination` INT NOT NULL,
  `statut_id` INT NOT NULL,
  `periode_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_relation_personne1`
    FOREIGN KEY (`source`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relation_personne2`
    FOREIGN KEY (`destination`)
    REFERENCES `buenosaires`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relation_status1`
    FOREIGN KEY (`statut_id`)
    REFERENCES `buenosaires`.`statut` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relation_periode1`
    FOREIGN KEY (`periode_id`)
    REFERENCES `buenosaires`.`periode` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_relation_personne1_idx` ON `buenosaires`.`relation` (`source` ASC);

CREATE INDEX `fk_relation_personne2_idx` ON `buenosaires`.`relation` (`destination` ASC);

CREATE INDEX `fk_relation_status1_idx` ON `buenosaires`.`relation` (`statut_id` ASC);

CREATE INDEX `fk_relation_periode1_idx` ON `buenosaires`.`relation` (`periode_id` ASC);


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
-- Table `buenosaires`.`utilisateurs`
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


-- -----------------------------------------------------
-- Table `buenosaires`.`categorie`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`categorie` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`categorie` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE UNIQUE INDEX `value_UNIQUE` ON `buenosaires`.`categorie` (`value` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`tag`
-- -----------------------------------------------------
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
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_attribut1`
    FOREIGN KEY (`attribut_id`)
    REFERENCES `buenosaires`.`attribut` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_tag_categorie1_idx` ON `buenosaires`.`tag` (`categorie_id` ASC);

CREATE INDEX `fk_tag_tag1_idx` ON `buenosaires`.`tag` (`parent_tag` ASC);

CREATE INDEX `fk_tag_attribut1_idx` ON `buenosaires`.`tag` (`attribut_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires`.`acte_contenu`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires`.`acte_contenu` ;

CREATE TABLE IF NOT EXISTS `buenosaires`.`acte_contenu` (
  `contenu` TEXT NOT NULL,
  `acte_id` INT NOT NULL,
  PRIMARY KEY (`acte_id`),
  CONSTRAINT `fk_acte_contenu_acte1`
    FOREIGN KEY (`acte_id`)
    REFERENCES `buenosaires`.`acte` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
