delimiter ;

CREATE TABLE IF NOT EXISTS `dunehd_player` (
  `duneSerial` CHAR(39) NOT NULL ,
  `ipAddress` INT NOT NULL ,
  `lastSeen` TIMESTAMP NOT NULL ,
  `firstSeen` DATETIME NOT NULL ,
  `lang` VARCHAR(15) NULL ,
  `userAgent` VARCHAR(512) NULL ,
  PRIMARY KEY (`duneSerial`) )
ENGINE = InnoDB;

CREATE TABLE `dunehd_player` (
  `duneSerial` char(39) NOT NULL,
  `ipAddress` int(11) NOT NULL,
  `lastSeen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `firstSeen` timestamp NULL DEFAULT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT '1',
  `lang` varchar(15) DEFAULT NULL,
  `userAgent` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`duneSerial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;