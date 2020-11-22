-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema iis
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema iis
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `iis` DEFAULT CHARACTER SET utf8 ;
USE `iis` ;

-- -----------------------------------------------------
-- Table `iis`.`User`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`User` ;

CREATE TABLE IF NOT EXISTS `iis`.`User` (
  `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
  `username` VARCHAR(45) NULL,
  `password` VARCHAR(45) NULL,
  `role` ENUM('admin', 'insuranceWorker', 'doctor', 'patient') NULL,
  `Full_name` VARCHAR(45) NULL,
  `Date_of_birth` DATE NULL,
  `Function` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iis`.`Health_problem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Health_problem` ;

CREATE TABLE IF NOT EXISTS `iis`.`Health_problem` (
  `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
  `Name` VARCHAR(100) NULL,
  `Description` VARCHAR(300) NULL,
  `User_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_Health_problem_User1`
    FOREIGN KEY (`User_id`)
    REFERENCES `iis`.`User` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Health_problem_User1_idx` ON `iis`.`Health_problem` (`User_id` ASC);


-- -----------------------------------------------------
-- Table `iis`.`Health_report`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Health_report` ;

CREATE TABLE IF NOT EXISTS `iis`.`Health_report` (
  `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
  `Text` VARCHAR(1000) NULL,
  `Picture` BLOB NULL,
  `Health_problem_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_Health_report_Health_problem1`
    FOREIGN KEY (`Health_problem_id`)
    REFERENCES `iis`.`Health_problem` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Health_report_Health_problem1_idx` ON `iis`.`Health_report` (`Health_problem_id` ASC);


-- -----------------------------------------------------
-- Table `iis`.`Examination_request`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Examination_request` ;

CREATE TABLE IF NOT EXISTS `iis`.`Examination_request` (
  `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
  `State` VARCHAR(45) NULL,
  `Health_problem_id` INT NOT NULL,
  `User_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_Examination_request_Health_problem1`
    FOREIGN KEY (`Health_problem_id`)
    REFERENCES `iis`.`Health_problem` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Examination_request_User1`
    FOREIGN KEY (`User_id`)
    REFERENCES `iis`.`User` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Examination_request_Health_problem1_idx` ON `iis`.`Examination_request` (`Health_problem_id` ASC);

CREATE INDEX `fk_Examination_request_User1_idx` ON `iis`.`Examination_request` (`User_id` ASC);


-- -----------------------------------------------------
-- Table `iis`.`Medical_procedure`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Medical_procedure` ;

CREATE TABLE IF NOT EXISTS `iis`.`Medical_procedure` (
  `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
  `Type_of_procedure` VARCHAR(100) NULL,
  `Execution_date` DATETIME NULL,
  `Examination_request_idExamination_request` INT NOT NULL,
  `User_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_Medical_procedure_Examination_request1`
    FOREIGN KEY (`Examination_request_idExamination_request`)
    REFERENCES `iis`.`Examination_request` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Medical_procedure_User1`
    FOREIGN KEY (`User_id`)
    REFERENCES `iis`.`User` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Medical_procedure_Examination_request1_idx` ON `iis`.`Medical_procedure` (`Examination_request_idExamination_request` ASC);

CREATE INDEX `fk_Medical_procedure_User1_idx` ON `iis`.`Medical_procedure` (`User_id` ASC);


-- -----------------------------------------------------
-- Table `iis`.`Payment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Payment` ;

CREATE TABLE IF NOT EXISTS `iis`.`Payment` (
  `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
  `Amount` INT NULL,
  `User_id` INT NOT NULL,
  `Medical_procedure_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_Payment_User1`
    FOREIGN KEY (`User_id`)
    REFERENCES `iis`.`User` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Payment_Medical_procedure1`
    FOREIGN KEY (`Medical_procedure_id`)
    REFERENCES `iis`.`Medical_procedure` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Payment_User1_idx` ON `iis`.`Payment` (`User_id` ASC);

CREATE INDEX `fk_Payment_Medical_procedure1_idx` ON `iis`.`Payment` (`Medical_procedure_id` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
