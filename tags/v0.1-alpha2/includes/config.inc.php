<?php

require_once("mysql.inc.php");

function readConfig($key) {
	$key = mysql_real_escape_string($key);
	$abfrage = mysql_query("SELECT Wert FROM Config WHERE Schluessel = '$key'");
	$row = mysql_fetch_array($abfrage);
	return $row["Wert"];
}

function writeConfig($key, $value) {
	$key = mysql_real_escape_string($key);
	$value = mysql_real_escape_string($value);
	// Prüfen, ob Einstellungseintrag bereits existiert
	$abfrage = mysql_query("SELECT 1 FROM Config WHERE Schluessel = '$key'");
	if(!mysql_fetch_array($abfrage)) {
		$cmd = mysql_query("INSERT INTO `Config` (`Schluessel`, `Wert`) VALUES ('$key', '$value')");
		mysql_query($cmd);
	}
	// Normaler Aktualisierungsvorgang
	$cmd = mysql_query("UPDATE Config SET Wert='$value' WHERE Schluessel = '$key'");
	mysql_query($cmd);
}

?>
