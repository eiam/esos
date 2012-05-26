<?php
	require_once('./includes/mysql.inc.php');
	require_once('./includes/lehrer.inc.php');
	require_once('./includes/termine.inc.php');
	require_once('./includes/config.inc.php');

	session_start();

	pruefe();

	if(!isset($_POST['step'])) {
		trigger_error("Fehler: AJAX-Parameter 'step' fehlt.");
		exit();
	}

	// Anzahl Lehrer für den ausgesuchten Schüler
	$result = mysql_query("SELECT COUNT(*) FROM VSchuelerLehrer WHERE schuelerID = '$_SESSION[id]'");
	$row = mysql_fetch_array($result);
	$anzahl_meiner_lehrer = $row[0];

	switch($_POST['step']) {
		case "check":
			if ($_POST['checked']=="checked") {
				if ($anzahl_meiner_lehrer>=$anzahl_termine) {
					echo "<strong>Achtung!</strong> Sie können maximal nur $anzahl_termine Lehrer für ein Kind auswählen.";
				} else {
					$result=mysql_query("SELECT schuelerID, lehrerID FROM VSchuelerLehrer WHERE schuelerID=".$_SESSION['id']." AND lehrerID=".mysql_real_escape_string($_POST['id']));
					if(!$row = mysql_fetch_array($result)) {
						$result=mysql_query("INSERT INTO VSchuelerLehrer(schuelerID, lehrerID) VALUES ('".$_SESSION['id']."', '".mysql_real_escape_string($_POST['id'])."');");
						$success = insert($_POST['id'], $_SESSION['id'], true); // Automatische Terminzusammenstellung
						if(!$success) {
							echo "<strong>Achtung!</strong>
							Es konnte kein weiterer Termin vereinbart werden. Möglicherweise ist Ihr Terminplan voll.";
						}
					}
				}
			}
			else {
				// Lehrer aus "Meine Lehrer" entfernen
				mysql_query("DELETE FROM VSchuelerLehrer WHERE schuelerID=".$_SESSION['id']." AND lehrerID=".mysql_real_escape_string($_POST['id']));
				// Termine löschen
				mysql_query("DELETE FROM VTermine WHERE schuelerID=".$_SESSION['id']." AND lehrerID=".mysql_real_escape_string($_POST['id']));
			}
			break;
		case 'kindwechseln':
			if(isset($_POST['sid'])) KindWechseln($_POST['sid']);
			break;
		case 'mein_lehrer':
			$_SESSION['mein_lehrer_init'] = (int) $_POST['id'];
			break;
		case "meine":
			meine_lehrer();
			break;
		case "andere":
			andere_lehrer();
			break;
	}
?>
