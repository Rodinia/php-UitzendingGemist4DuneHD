delimiter ;

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


CREATE TABLE `favorite` (
  `duneSerial` char(39) NOT NULL,
  `provider` varchar(25) NOT NULL,
  `type` varchar(20) NOT NULL,
  `refid` varchar(80) NOT NULL,
  `title` varchar(80) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`duneSerial`,`provider`,`type`,`refid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

