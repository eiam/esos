<?php
    require_once('./includes/mysql.inc.php');
    require_once('./includes/termine.inc.php');
	require_once('./includes/benutzer.inc.php');
	require_once('./includes/config.inc.php');

	session_start();

	pruefe();

	if (isset($_POST['tid'])) $_POST['tid'] = mysql_real_escape_string($_POST['tid']);
	if (isset($_POST['zeit'])) $_POST['zeit'] = mysql_real_escape_string($_POST['zeit']);

	if (isset($_POST['tid'])) {
		// Sicherheitsüberprüfung der Tid (Termin-ID)
		$befehl = "SELECT schuelerID FROM VTermine WHERE Tid = '$_POST[tid]'";
		$result = mysql_query($befehl);
		$row = mysql_fetch_array($result);
		if (!$row) return;
		if ($row[0] != $_POST['sid']) return;
	}

    if (isset($_POST['Lid'], $_POST['sid'], $_POST['insert'])) {
		$ir = insert($_POST['Lid'], $_POST['sid'], true); // Termine erster Wahl
		if(!$ir) $ir = insert($_POST['Lid'], $_POST['sid']); // Termine, die 2. Elternteil erfordern
		if(!$ir) kein_termin();
		// Lehrerauswahl von ×-Terminlink der Lehrerübersicht nicht mehr relevant
		unset($_SESSION['mein_lehrer']);
    } else if (isset($_POST['kindwechseln'], $_POST['sid'])) {
		KindWechseln($_POST['sid']);
	} else if (isset($_POST['terminspeichern'], $_POST['tid'], $_POST['zeit'])) {
		$befehl = "SELECT lehrerID FROM VTermine WHERE Tid = '$_POST[tid]'";
		$result = mysql_query($befehl);
		$row = mysql_fetch_array($result);
		$lehrer = $row[0];

        $befehl  = "UPDATE VTermine SET Zeit='$_POST[zeit]' WHERE Tid='$_POST[tid]'";
		$befehl .= " AND TIME_FORMAT(TIME('$_POST[zeit]'), '%H:%i:00') NOT IN (SELECT * FROM (SELECT DISTINCT Zeit FROM VTermine WHERE lehrerID='$lehrer') AS _t)"; // Doppelbelegungen verhindern
        mysql_query($befehl);
    } else if (isset($_POST['loeschen'], $_POST['tid'])) {
        $befehl = "DELETE FROM VTermine WHERE Tid=".$_POST['tid'];
        mysql_query($befehl);
	}
	else if (isset($_POST['reload'])) {
    	table($_POST['sid']);
	}
	else if (isset($_POST['reloadLehrer'])) {
    	lehrer($_POST['sid']);
	}




?>
