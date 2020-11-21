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
-- Table `iis`.`Patient`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Patient` ;

CREATE TABLE IF NOT EXISTS `iis`.`Patient` (
  `idPatient` INT NOT NULL,
  `Full_name` VARCHAR(45) NULL,
  `Date_of_birht` DATE NULL,
  PRIMARY KEY (`idPatient`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iis`.`Doctor`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Doctor` ;

CREATE TABLE IF NOT EXISTS `iis`.`Doctor` (
  `idDoctor` INT NOT NULL,
  `Full_name` VARCHAR(45) NULL,
  `Function` VARCHAR(45) NULL,
  PRIMARY KEY (`idDoctor`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iis`.`Health_problem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Health_problem` ;

CREATE TABLE IF NOT EXISTS `iis`.`Health_problem` (
  `idHealth_problem` INT NOT NULL,
  `Name` VARCHAR(100) NULL,
  `Description` VARCHAR(45) NULL,
  `Patient_idPatient` INT NOT NULL,
  `Doctor_idDoctor` INT NOT NULL,
  PRIMARY KEY (`idHealth_problem`),
  CONSTRAINT `fk_Health_problem_Patient`
    FOREIGN KEY (`Patient_idPatient`)
    REFERENCES `iis`.`Patient` (`idPatient`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Health_problem_Doctor1`
    FOREIGN KEY (`Doctor_idDoctor`)
    REFERENCES `iis`.`Doctor` (`idDoctor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Health_problem_Patient_idx` ON `iis`.`Health_problem` (`Patient_idPatient` ASC);

CREATE INDEX `fk_Health_problem_Doctor1_idx` ON `iis`.`Health_problem` (`Doctor_idDoctor` ASC);


-- -----------------------------------------------------
-- Table `iis`.`Health_report`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Health_report` ;

CREATE TABLE IF NOT EXISTS `iis`.`Health_report` (
  `Health_problem_idHealth_problem` INT NOT NULL,
  `Text` VARCHAR(1000) NULL,
  `Picture` BLOB NULL,
  PRIMARY KEY (`Health_problem_idHealth_problem`),
  CONSTRAINT `fk_Health_report_Health_problem1`
    FOREIGN KEY (`Health_problem_idHealth_problem`)
    REFERENCES `iis`.`Health_problem` (`idHealth_problem`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iis`.`Examination_request`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Examination_request` ;

CREATE TABLE IF NOT EXISTS `iis`.`Examination_request` (
  `Health_problem_idHealth_problem` INT NOT NULL,
  `idExamination_request` INT NOT NULL,
  `State` VARCHAR(45) NULL,
  `Doctor_idDoctor` INT NOT NULL,
  PRIMARY KEY (`Health_problem_idHealth_problem`, `idExamination_request`),
  CONSTRAINT `fk_Examination_request_Health_problem1`
    FOREIGN KEY (`Health_problem_idHealth_problem`)
    REFERENCES `iis`.`Health_problem` (`idHealth_problem`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Examination_request_Doctor1`
    FOREIGN KEY (`Doctor_idDoctor`)
    REFERENCES `iis`.`Doctor` (`idDoctor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Examination_request_Health_problem1_idx` ON `iis`.`Examination_request` (`Health_problem_idHealth_problem` ASC);

CREATE INDEX `fk_Examination_request_Doctor1_idx` ON `iis`.`Examination_request` (`Doctor_idDoctor` ASC);


-- -----------------------------------------------------
-- Table `iis`.`Medical_procedure`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Medical_procedure` ;

CREATE TABLE IF NOT EXISTS `iis`.`Medical_procedure` (
  `idMedical_procedure` INT NOT NULL,
  `Type_of_procedure` VARCHAR(100) NULL,
  `Execution_date` DATETIME NULL,
  `Examination_request_Health_problem_idHealth_problem` INT NOT NULL,
  `Examination_request_idExamination_request` INT NOT NULL,
  `Doctor_idDoctor` INT NOT NULL,
  PRIMARY KEY (`idMedical_procedure`),
  CONSTRAINT `fk_Medical_procedure_Examination_request1`
    FOREIGN KEY (`Examination_request_Health_problem_idHealth_problem` , `Examination_request_idExamination_request`)
    REFERENCES `iis`.`Examination_request` (`Health_problem_idHealth_problem` , `idExamination_request`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Medical_procedure_Doctor1`
    FOREIGN KEY (`Doctor_idDoctor`)
    REFERENCES `iis`.`Doctor` (`idDoctor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Medical_procedure_Examination_request1_idx` ON `iis`.`Medical_procedure` (`Examination_request_Health_problem_idHealth_problem` ASC, `Examination_request_idExamination_request` ASC);

CREATE INDEX `fk_Medical_procedure_Doctor1_idx` ON `iis`.`Medical_procedure` (`Doctor_idDoctor` ASC);


-- -----------------------------------------------------
-- Table `iis`.`Insurance_worker`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Insurance_worker` ;

CREATE TABLE IF NOT EXISTS `iis`.`Insurance_worker` (
  `idInsurance_worker` INT NOT NULL,
  `Full_name` VARCHAR(45) NULL,
  PRIMARY KEY (`idInsurance_worker`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iis`.`Payment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `iis`.`Payment` ;

CREATE TABLE IF NOT EXISTS `iis`.`Payment` (
  `Medical_procedure_idMedical_procedure` INT NOT NULL,
  `Insurance_worker_idInsurance_worker` INT NOT NULL,
  `Amount` INT NULL,
  `Doctor_idDoctor` INT NOT NULL,
  PRIMARY KEY (`Medical_procedure_idMedical_procedure`),
  CONSTRAINT `fk_Payment_Medical_procedure1`
    FOREIGN KEY (`Medical_procedure_idMedical_procedure`)
    REFERENCES `iis`.`Medical_procedure` (`idMedical_procedure`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Payment_Insurance_worker1`
    FOREIGN KEY (`Insurance_worker_idInsurance_worker`)
    REFERENCES `iis`.`Insurance_worker` (`idInsurance_worker`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Payment_Doctor1`
    FOREIGN KEY (`Doctor_idDoctor`)
    REFERENCES `iis`.`Doctor` (`idDoctor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Payment_Insurance_worker1_idx` ON `iis`.`Payment` (`Insurance_worker_idInsurance_worker` ASC);

CREATE INDEX `fk_Payment_Doctor1_idx` ON `iis`.`Payment` (`Doctor_idDoctor` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
