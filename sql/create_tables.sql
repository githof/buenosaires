
-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`periode`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`periode` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`periode` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `debut_min` DATE NOT NULL,
  `debut_max` DATE NOT NULL,
  `fin_min` DATE NOT NULL,
  `fin_max` DATE NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`personne`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`personne` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`personne` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `periode_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_personne_periode1`
    FOREIGN KEY (`periode_id`)
    REFERENCES `buenosaires_TPT`.`periode` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_personne_periode1_idx` ON `buenosaires_TPT`.`personne` (`periode_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`attribut`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`attribut` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`attribut` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`nom`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`nom` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`nom` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(50) NOT NULL,
  `no_accent` VARCHAR(50) NOT NULL,
  `attribut_id` INT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_nom_attribut1`
    FOREIGN KEY (`attribut_id`)
    REFERENCES `buenosaires_TPT`.`attribut` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_nom_attribut1_idx` ON `buenosaires_TPT`.`nom` (`attribut_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`prenom`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`prenom` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`prenom` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `prenom` VARCHAR(50) NOT NULL,
  `no_accent` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`prenom_personne`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`prenom_personne` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`prenom_personne` (
  `personne_id` INT NOT NULL,
  `prenom_id` INT NOT NULL,
  `ordre` INT NOT NULL,
  PRIMARY KEY (`personne_id`, `prenom_id`),
  CONSTRAINT `fk_personne_has_prenom_personne`
    FOREIGN KEY (`personne_id`)
    REFERENCES `buenosaires_TPT`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_personne_has_prenom_prenom1`
    FOREIGN KEY (`prenom_id`)
    REFERENCES `buenosaires_TPT`.`prenom` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_personne_has_prenom_prenom1_idx` ON `buenosaires_TPT`.`prenom_personne` (`prenom_id` ASC);

CREATE INDEX `fk_personne_has_prenom_personne_idx` ON `buenosaires_TPT`.`prenom_personne` (`personne_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`nom_personne`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`nom_personne` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`nom_personne` (
  `personne_id` INT NOT NULL,
  `nom_id` INT NOT NULL,
  `ordre` INT NOT NULL,
  PRIMARY KEY (`personne_id`, `nom_id`),
  CONSTRAINT `fk_personne_has_nom_personne1`
    FOREIGN KEY (`personne_id`)
    REFERENCES `buenosaires_TPT`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_personne_has_nom_nom1`
    FOREIGN KEY (`nom_id`)
    REFERENCES `buenosaires_TPT`.`nom` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_personne_has_nom_nom1_idx` ON `buenosaires_TPT`.`nom_personne` (`nom_id` ASC);

CREATE INDEX `fk_personne_has_nom_personne1_idx` ON `buenosaires_TPT`.`nom_personne` (`personne_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`acte`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`acte` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`acte` (
  `id` INT NOT NULL,
  `periode_id` INT NULL,
  `epoux` INT NULL,
  `epouse` INT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_acte_periode1`
    FOREIGN KEY (`periode_id`)
    REFERENCES `buenosaires_TPT`.`periode` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_personne1`
    FOREIGN KEY (`epoux`)
    REFERENCES `buenosaires_TPT`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_personne2`
    FOREIGN KEY (`epouse`)
    REFERENCES `buenosaires_TPT`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_acte_periode1_idx` ON `buenosaires_TPT`.`acte` (`periode_id` ASC);

CREATE INDEX `fk_acte_personne1_idx` ON `buenosaires_TPT`.`acte` (`epoux` ASC);

CREATE INDEX `fk_acte_personne2_idx` ON `buenosaires_TPT`.`acte` (`epouse` ASC);


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`statut`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`statut` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`statut` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE UNIQUE INDEX `value_UNIQUE` ON `buenosaires_TPT`.`statut` (`value` ASC);


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`relation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`relation` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`relation` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `source` INT NOT NULL,
  `destination` INT NOT NULL,
  `statut_id` INT NOT NULL,
  `periode_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_relation_personne1`
    FOREIGN KEY (`source`)
    REFERENCES `buenosaires_TPT`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relation_personne2`
    FOREIGN KEY (`destination`)
    REFERENCES `buenosaires_TPT`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relation_status1`
    FOREIGN KEY (`statut_id`)
    REFERENCES `buenosaires_TPT`.`statut` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relation_periode1`
    FOREIGN KEY (`periode_id`)
    REFERENCES `buenosaires_TPT`.`periode` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_relation_personne1_idx` ON `buenosaires_TPT`.`relation` (`source` ASC);

CREATE INDEX `fk_relation_personne2_idx` ON `buenosaires_TPT`.`relation` (`destination` ASC);

CREATE INDEX `fk_relation_status1_idx` ON `buenosaires_TPT`.`relation` (`statut_id` ASC);

CREATE INDEX `fk_relation_periode1_idx` ON `buenosaires_TPT`.`relation` (`periode_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`acte_has_relation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`acte_has_relation` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`acte_has_relation` (
  `acte_id` INT NOT NULL,
  `relation_id` INT NOT NULL,
  PRIMARY KEY (`acte_id`, `relation_id`),
  CONSTRAINT `fk_acte_has_relation_acte1`
    FOREIGN KEY (`acte_id`)
    REFERENCES `buenosaires_TPT`.`acte` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_acte_has_relation_relation1`
    FOREIGN KEY (`relation_id`)
    REFERENCES `buenosaires_TPT`.`relation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_acte_has_relation_relation1_idx` ON `buenosaires_TPT`.`acte_has_relation` (`relation_id` ASC);

CREATE INDEX `fk_acte_has_relation_acte1_idx` ON `buenosaires_TPT`.`acte_has_relation` (`acte_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`source`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`source` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`source` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `source` TEXT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`cond`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`cond` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`cond` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `text` TEXT NOT NULL,
  `source_id` INT NOT NULL,
  `periode_id` INT NOT NULL,
  `personne_id` INT NOT NULL,
  `acte_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_cond_source1`
    FOREIGN KEY (`source_id`)
    REFERENCES `buenosaires_TPT`.`source` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cond_periode1`
    FOREIGN KEY (`periode_id`)
    REFERENCES `buenosaires_TPT`.`periode` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cond_personne1`
    FOREIGN KEY (`personne_id`)
    REFERENCES `buenosaires_TPT`.`personne` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cond_acte1`
    FOREIGN KEY (`acte_id`)
    REFERENCES `buenosaires_TPT`.`acte` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_cond_source1_idx` ON `buenosaires_TPT`.`cond` (`source_id` ASC);

CREATE INDEX `fk_cond_periode1_idx` ON `buenosaires_TPT`.`cond` (`periode_id` ASC);

CREATE INDEX `fk_cond_personne1_idx` ON `buenosaires_TPT`.`cond` (`personne_id` ASC);

CREATE INDEX `fk_cond_acte1_idx` ON `buenosaires_TPT`.`cond` (`acte_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`utilisateurs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`utilisateurs` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`utilisateurs` (
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
-- Table `buenosaires_TPT`.`categorie`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`categorie` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`categorie` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE UNIQUE INDEX `value_UNIQUE` ON `buenosaires_TPT`.`categorie` (`value` ASC);


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`tag` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`tag` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(45) NOT NULL,
  `categorie_id` INT NOT NULL,
  `parent_tag` INT NOT NULL,
  `attribut_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_tag_categorie1`
    FOREIGN KEY (`categorie_id`)
    REFERENCES `buenosaires_TPT`.`categorie` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_tag1`
    FOREIGN KEY (`parent_tag`)
    REFERENCES `buenosaires_TPT`.`tag` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_attribut1`
    FOREIGN KEY (`attribut_id`)
    REFERENCES `buenosaires_TPT`.`attribut` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_tag_categorie1_idx` ON `buenosaires_TPT`.`tag` (`categorie_id` ASC);

CREATE INDEX `fk_tag_tag1_idx` ON `buenosaires_TPT`.`tag` (`parent_tag` ASC);

CREATE INDEX `fk_tag_attribut1_idx` ON `buenosaires_TPT`.`tag` (`attribut_id` ASC);


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`acte_contenu`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`acte_contenu` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`acte_contenu` (
  `contenu` TEXT NOT NULL,
  `acte_id` INT NOT NULL,
  PRIMARY KEY (`acte_id`),
  CONSTRAINT `fk_acte_contenu_acte1`
    FOREIGN KEY (`acte_id`)
    REFERENCES `buenosaires_TPT`.`acte` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `buenosaires_TPT`.`variable`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenosaires_TPT`.`variable` ;

CREATE TABLE IF NOT EXISTS `buenosaires_TPT`.`variable` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `valeur` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
