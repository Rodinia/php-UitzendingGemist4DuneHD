CREATE  TABLE IF NOT EXISTS `dunehd_player` (
  `duneSerial` CHAR(39) NOT NULL ,
  `ipAddress` INT NOT NULL ,
  `lastSeen` TIMESTAMP NOT NULL ,
  `firstSeen` DATETIME NOT NULL ,
  `lang` VARCHAR(15) NULL ,
  `userAgent` VARCHAR(512) NULL ,
  PRIMARY KEY (`duneSerial`) )
ENGINE = InnoDB;

CREATE  TABLE IF NOT EXISTS `favorite` (
  `duneSerial` CHAR(39) NOT NULL ,
  `provider` VARCHAR(25) NOT NULL ,
  `type` VARCHAR(20) NOT NULL ,
  `refid` VARCHAR(80) NOT NULL ,
  `title` VARCHAR(80) NULL ,
  `img` VARCHAR(255) NULL ,
  PRIMARY KEY (`duneSerial`, `provider`, `type`, `refid`) )
ENGINE = InnoDB;