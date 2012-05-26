<?php

require_once("config.inc.php");

// Gibt die Startzeit des Elternsprechtags als Zeitstempel aus
function elternsprechtag_timestamp() {
	setlocale(LC_TIME, 'de_DE.UTF8');
	$datum = readConfig("TAG");
	$datum_array = explode(".", $datum);
	$day = $datum_array[0];
	$month = $datum_array[1];
	$year = $datum_array[2];
	$zeit = readConfig("STARTZEIT");
	$zeit_array = explode(":", $zeit);
	$hour = $zeit_array[0];
	$minute = $zeit_array[1];
	$second = 0;
	$time = mktime((int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year);
	// echo strftime("%c", $time);
	return $time;
}

// Gibt wahr aus, wenn Eintragungssperre kurz vor dem Elternsprechtag in Kraft treten soll
function eintragungsfrist() {
	$time = elternsprechtag_timestamp();
	
	$frist = readConfig("FRIST")*60*60;
	$now = time();
	if($now>$time-$frist) return true;
	else return false;
}

// Gibt wahr aus, wenn der Elternsprechtag bereits begonnen hat
//function elternsprechtag_hat_begonnen() {
//	if(time()>elternsprechtag_timestamp()) return true;
//	else return false;
//}

?>
