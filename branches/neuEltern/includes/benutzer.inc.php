<?php

function logout() {
	// Die Session darf nicht zurückgesetzt werden, da diese für das CAPTCHA notwendig ist.
	unset($_SESSION["eltern"]);
	unset($_SESSION["lehrer"]);
	unset($_SESSION["admin"]);

	unset($_SESSION["id"]);
	unset($_SESSION["vorname"]);
	unset($_SESSION["name"]);
	unset($_SESSION["login"]);
}

function pruefe() {
	require_once("frist.inc.php");

	if (readConfig("OFFEN") != "true") {
		exit();
	}

	if(eintragungsfrist()) exit();

	if(!isset($_SESSION['eltern'])) exit();
	if(!isset($_SESSION['id'])) trigger_error("Schüler-ID nicht gesetzt!");

	$eintragenerlaubt = false;

	if (!isset($_POST['sid'])) $_POST['sid'] = $_SESSION['id'];

	if ($_SESSION['id']==$_POST['sid']) {
		$eintragenerlaubt = true;
	} else {
		$elternID = ElternIDdesKindes($_SESSION["id"]);
		$geschwisterabfrage = "SELECT id FROM Schueler WHERE elternID = '$elternID'";
		$ergebnis = mysql_query($geschwisterabfrage);
		while ( ($kind = mysql_fetch_row($ergebnis)) and !$eintragenerlaubt) {
			if ($kind[0]==$_POST['sid']) $eintragenerlaubt=true;
		}
	}

	if (!$eintragenerlaubt) exit();
}

// Dient dazu die ElternID des eigenen oder eines fremden Kindes zu bestimmen
function ElternIDdesKindes($ID) {
	// Setze ElternID, falls noch nicht vorhanden
	$cmd  = "UPDATE Schueler SET elternID = UUID() WHERE id = '$ID' and isNull(elternID)";
	mysql_query($cmd);

	// Lese ElternID
	$cmd = "SELECT elternID FROM Schueler WHERE id = '$ID'";
	$row = mysql_fetch_array(mysql_query($cmd));
	return $row["elternID"];
}

function geschwister($ID) {
			$schueler = array();
			$query = mysql_query("
SELECT id, SVorname, SName, SKlasse, SEmail, SLogin FROM Schueler
  WHERE elternID = (SELECT elternID FROM Schueler WHERE id = $ID) OR id = $ID
  ORDER BY SVorname");
  echo mysql_error();
			while ($row = mysql_fetch_array($query)) {
				$schueler[] = $row;
 			}
			return $schueler;
}

function kind($sid) {
	$sid = mysql_real_escape_string($sid);
	$schueler = geschwister($_SESSION["id"]);
	if (count($schueler)>1) {
		echo '<select id="sid">';
		foreach ($schueler as $kind) {
			$selected = ($kind["id"]==$sid) ? ' selected="selected"' : '';
			echo '<option value="'.$kind["id"].'"'.$selected.'>'.$kind["SVorname"].' '.$kind["SName"].' ('.$kind["SKlasse"].')</option>';
		}
		echo '</select>';
	} else {
		echo $_SESSION["vorname"]." ".$_SESSION["name"];
	}
}
function kindt($sid)
{
	$sid = mysql_real_escape_string($sid);
	$schueler = geschwister($_SESSION["id"]);
	if (count($schueler)>1) {
		$kt = '<select id="sid">';
		foreach ($schueler as $kind) {
			$selected = ($kind["id"]==$sid) ? ' selected="selected"' : '';
			$kt .= '<option value="'.$kind["id"].'"'.$selected.'>'.$kind["SVorname"].' '.$kind["SName"].' ('.$kind["SKlasse"].')</option>';
		}
		$kt .= '</select>';
		
				
		return  $kt;		
		
	
	
	}
}

function KindAuswahl() {
	$sid = $_SESSION["id"];
	$schueler = geschwister($sid);
	if (count($schueler)>1) {
		echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		echo '<p>';
		echo '<label for="sid">Kind:</label>';
		echo kind($sid);
		echo '</p>';
		echo "</form>";
	} else {
		echo '<input id="sid" type="hidden" value="'.$sid.'"></input>';
	}
}

function KindWechseln($ID) {
	$kindID = mysql_real_escape_string($ID);
	// Hier wird aus Sicherheitsgründen überprüft,
	// ob $_POST["kind"] tatsächlich ein Geschwister von $_SESSION["id"] ist.
	$cmd  = "SELECT SLogin, id, SName, SVorname, elternID FROM Schueler WHERE id = '$kindID'";
	$cmd .= " AND elternID = (SELECT elternID FROM Schueler WHERE id = '$_SESSION[id]')";
	$result = mysql_query($cmd);
	if ($row = mysql_fetch_array($result)) {
		$_SESSION["id"] = $row["id"];
		$_SESSION["login"] = $row["SLogin"];
		$_SESSION["vorname"] = $row["SVorname"];
		$_SESSION["name"] = $row["SName"];
	}
}

?>