<?php

function dbInit($dbname, $dbuser, $dbpwd) {
	mysql_query('CREATE USER "'.$dbuser.'" IDENTIFIED BY "'.$dbpwd.'";');
echo mysql_error();
	mysql_query('GRANT USAGE ON * . * TO "'.$dbuser.'" IDENTIFIED BY "'.$dbpwd.'"
	  WITH MAX_QUERIES_PER_HOUR 0
	  MAX_CONNECTIONS_PER_HOUR 0
	  MAX_UPDATES_PER_HOUR 0 
	  MAX_USER_CONNECTIONS 0 ;');
echo mysql_error();
	mysql_query('CREATE DATABASE IF NOT EXISTS `'.$dbname.'` ;');
echo mysql_error();
	mysql_query('GRANT ALL PRIVILEGES ON `'.$dbname.'` . * TO "'.$dbname.'";');
	echo mysql_error();
}

// Sicherstellen, dass die benötigten Tabellen existieren
// Achtung: Pro mysql_query-Funktion ist nur ein SQL-Befehl erlaubt.

function tableInit($dbname) {
	require_once('includes/mysql.inc.php');
	
	mysql_query('
	CREATE TABLE IF NOT EXISTS `Administratoren` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `ALogin` varchar(30) NOT NULL,
	  `APasswort` varchar(28) NOT NULL,
	  `AVorname` text NOT NULL,
	  `AName` text NOT NULL,
	  `AEMail` text NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
	');

	mysql_query('
	CREATE TABLE IF NOT EXISTS `Config` (
	  `Schluessel` varchar(60) NOT NULL,
	  `Wert` text NOT NULL,
	  PRIMARY KEY (`Schluessel`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;
	');

	mysql_query('
	CREATE TABLE IF NOT EXISTS `Einladungen` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `Zeit` datetime DEFAULT NULL,
	  `Anzahl` int(5) DEFAULT NULL,
	  `Text` text,
	  `Betreff` varchar(50) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	');

	mysql_query('
	CREATE TABLE IF NOT EXISTS `LEinladungen` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `Zeit` datetime DEFAULT NULL,
	  `Anzahl` int(5) DEFAULT NULL,
	  `Text` text,
	  `Betreff` varchar(50) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	');

	mysql_query('
	CREATE TABLE IF NOT EXISTS `Faecher` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
  	`Fach` varchar(20) NOT NULL,
  	`Kuerzel` varchar(5) NOT NULL,
  	PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
	');

	mysql_query('
	CREATE TABLE IF NOT EXISTS `Lehrer` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `LLogin` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
	  `LPasswort` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
	  `LVorname` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
	  `LName` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
	  `LEmail` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	  `Krank` tinyint(1) NOT NULL,
	  `Raum` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
	  `Vollzeit` tinyint(1) NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `LLogin` (`LLogin`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
	');

	mysql_query('
	CREATE TABLE IF NOT EXISTS `Pausen` (
	  `Zeit` time NOT NULL,
	  PRIMARY KEY (`Zeit`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;
	');

	mysql_query('
	CREATE TABLE IF NOT EXISTS `Schueler` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `SName` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	  `SVorname` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	  `SKlasse` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	  `SEmail` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	  `SPasswort` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	  `SLogin` varchar(30) NOT NULL,
	  `elternID` varchar(34) DEFAULT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `SLogin` (`SLogin`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;
	');

	mysql_query('
	CREATE TABLE IF NOT EXISTS `VLehrerFach` (
	  `lehrerID` int(11) NOT NULL,
	  `fachID` int(11) NOT NULL
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;
	');
	
	mysql_query('
	CREATE TABLE IF NOT EXISTS `VSchuelerLehrer` (
 	 `schuelerID` int(11) NOT NULL,
 	 `lehrerID` int(11) NOT NULL
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;
	');
	
	mysql_query('
	CREATE TABLE IF NOT EXISTS `VTermine` (
	  `schuelerID` int(11) NOT NULL,
	  `lehrerID` int(11) NOT NULL,
	  `Zeit` time NOT NULL,
	  `Tid` int(11) NOT NULL AUTO_INCREMENT,
	  PRIMARY KEY (`Tid`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
	');
	
	mysql_query('
	INSERT INTO `Config` (`Schluessel`, `Wert`) VALUES
	("STARTZEIT", "17:00"),
	("ENDZEIT", "19:00"),
	("GESPRAECHSDAUER", "10"),
	("E-MAIL", "admin@localhost"),
	("TAG", "31.12.2012"),
	("SCHULNAME", "Schule"),
	("OFFEN", "false"),
	("FRIST", "24"),
	("TITEL", "Anmeldung für den Elternsprechtag");
	');

	mysql_query('
	INSERT INTO `Faecher` (`id`, `Fach`, `Kuerzel`) VALUES
	(1, "Informatik", "Inf"),
	(2, "Mathematik", "M"),
	(3, "Deutsch", "D"),
	(4, "Physik", "Ph"),
	(5, "Chemie", "Ch"),
	(6, "Kunst", "Ku"),
	(7, "Musik", "Mu"),
	(8, "Geschichte", "G"),
	(9, "Sozialkunde", "Sk"),
	(10, "Englisch", "E"),
	(11, "Französisch", "F"),
	(12, "Latein", "L"),
	(13, "Italienisch", "I"),
	(14, "Religion katholisch", "Kath"),
	(15, "Religion evangelisch", "Ev"),
	(16, "Wirtschaft und Recht", "WR"),
	(17, "Biologie", "Bio"),
	(18, "Geographie", "Geo"),
	(19, "Ethik", "Eth"),
	(20, "Sport", "Spo"),
	(21, "Astronomie", "Astro"),
	(22, "Polnisch", "Pl"),
	(23, "Spanisch", "Esp");
	');
}
?>
