<?php

require_once("config.inc.php");
require_once("benutzer.inc.php");
require_once("frist.inc.php");

$startzeitR = readConfig("STARTZEIT"); // im format hh:mm
$endzeitR = readConfig("ENDZEIT");// im format hh:mm
$gespraechsdauer = (int) readConfig("GESPRAECHSDAUER"); // im format mm

$startzeit['stunde'] = (int)substr($startzeitR, 0, 2);
$startzeit['minuten'] = (int)substr($startzeitR, 3, 2);

$endzeit['stunde'] = (int)substr($endzeitR, 0, 2);
$endzeit['minuten'] = (int)substr($endzeitR, 3, 2);

$anzahl_termine = ($endzeit['minuten']+($endzeit['stunde']-$startzeit['stunde'])*60-$startzeit['minuten'])/$gespraechsdauer;

if ($anzahl_termine <= 0) {
	trigger_error("Es sind rechnerisch keine oder weniger als Null Termine möglich. Bitte überprüfen Sie die Einstellungen Startzeit und Endzeit.");
}

// Gibt immer die nächste bzw. vorherige Zeit aus, ohne Berücksichtigung von Startzeit und Endzeit
function naechste_zeit($zeit, $step=1) {
	global $gespraechsdauer;

	$akt_stunde = substr($zeit,0,2);
	$akt_minute = substr($zeit,3,2);

	if ($step==1) {
		$akt_minute+=$gespraechsdauer;
		if ($akt_minute >= 60) {
			$akt_stunde+=1;
			$akt_minute-=60;
		}
	} elseif ($step==-1) {
		$akt_minute-=$gespraechsdauer;
		if ($akt_minute < 0) {
			$akt_stunde-=1;
			$akt_minute+=60;
		}
	}

	$akt_stunde=(strlen((string)$akt_stunde)==1?'0':'').$akt_stunde;
	$akt_minute=(strlen((string)$akt_minute)==1?'0':'').$akt_minute;
	$zeit=$akt_stunde.":".$akt_minute;

	return $zeit;
}

// Gebe ein Array von belegten Terminen aus
function belegt($lehrer, $schueler="NULL", $auto=false) {
	$belegt = array();

	// Belegung bei Lehrern
	$cmd = "SELECT DISTINCT Zeit
			FROM VTermine
			WHERE lehrerID=".$lehrer." ORDER BY Zeit";
	$result = mysql_query($cmd);
	while($row = mysql_fetch_array($result)) {
		$belegt[] = substr($row["Zeit"],0,5);
	}

	// Belegung bei Eltern
	if ($auto) {
		$cmd = "SELECT DISTINCT Zeit
				FROM VTermine
				WHERE schuelerID IN (SELECT id FROM Schueler WHERE elternID = '".ElternIDdesKindes($schueler)."')
				AND lehrerID IN (SELECT id FROM Lehrer)
				ORDER BY Zeit";
		$result = mysql_query($cmd);
		while($row = mysql_fetch_array($result)) {
			// Belegung bei einem Kind
			$belegt[] = substr($row["Zeit"],0,5);
			// Zeit um von Raum zu Raum zu wechseln
			$belegt[] = naechste_zeit($row["Zeit"],-1);
			$belegt[] = naechste_zeit($row["Zeit"]);
		}
	}

	// Pausen
	$cmd = "SELECT Zeit FROM Pausen ORDER BY Zeit";
	$result = mysql_query($cmd);
	while($row = mysql_fetch_array($result)) {
		$belegt[] = substr($row["Zeit"],0,5);
	}

	return $belegt;
}

function zeit($zeitTermin, $lehrer, $id, $sid) {
	$sid = mysql_real_escape_string($sid);
	$lehrer = mysql_real_escape_string($lehrer);

	$belegt = belegt($lehrer, $sid);
	
	global $startzeitR, $anzahl_termine;
	
	if ($anzahl_termine <= 0) return $anzahl_termine;

	$o = '<select class="zeit" id="zeit'.$id.'">';
	
	$zeit = $startzeitR;

	for ($i = 0; $i<$anzahl_termine; $i++) {
		$selected='';
		if ($zeit==substr($zeitTermin,0,5)) $selected=' selected="selected"';
		
		if (!in_array($zeit, $belegt) || $zeit==substr($zeitTermin,0,5))
			$o.='<option'.$selected.'>'.$zeit.'</option>';
		
		$zeit = naechste_zeit($zeit);
	}
	
	$o .= "</select>";
	return $o;
}

// Wieviel Termine wurden eingetragen?
function eingetragene_termine_berechnen() {
	$cmd = "SELECT COUNT(*) FROM VTermine";
	$result = mysql_query($cmd);
	$row = mysql_fetch_array($result);
	return $eingetragene_termine = $row[0];
}

// Prüft, ob das Elternteil zur gleichen Zeit auch bei einem anderen Lehrer einen Termin vereinbart hat
function auf_doppeltermin_pruefen($zeit, $schueler) {
	$cmd = "SELECT COUNT(*)
			FROM VTermine
			WHERE schuelerID IN (SELECT id FROM Schueler WHERE elternID = '".ElternIDdesKindes($schueler)."')
			AND Zeit = '$zeit'";
	$result = mysql_query($cmd);
	$row = mysql_fetch_array($result);
	if($row[0]>1) { return true; }
	else return false;
}

function table($sid) {
	$sid = mysql_real_escape_string($sid);

	echo "<tr><th>Lehrer</th><th>Raum</th><th>Zeit</th><th></th><th></th><th></th></tr>\n";
    $cmd = "SELECT LName, LVorname, id, Raum
            FROM Lehrer, VSchuelerLehrer
            WHERE schuelerID=".$sid." AND lehrerID = Lehrer.id ORDER BY LName";
	$result = mysql_query($cmd);
	$i = 0;
	while ($row = mysql_fetch_array($result)) {
		$lehrer = $row["id"];
		$termin_id = 0;
		echo "<tr><td>$row[LName], $row[LVorname]</td>
              <td>$row[Raum]</td>";

        // Überprüfung, ob der Schüler bereits einen Termin hat:
        $cmdTermin = "SELECT Zeit, Tid FROM VTermine WHERE schuelerID=".$sid." AND lehrerID=".$lehrer;
        $rowTermin = mysql_fetch_array(mysql_query($cmdTermin));
        if($rowTermin != FALSE) {
            $tid = $rowTermin["Tid"];
            $zeit = $rowTermin["Zeit"];
            echo "<td>".zeit($zeit, $lehrer, $tid, $sid)."</td>";
		    echo '<td><img class="delete clickable" id="termin'.$tid.'" alt="Termin absagen" title="Termin absagen" src="design/trash.png" width="16" height="16" /></td>';
    		echo '<td class="ajaxstatus"><img class="hidden" id="working'.$tid.'" alt="in Arbeit" title="in Arbeit" src="design/ajax-loader.gif" width="16" height="16" /><img class="hidden" id="success'.$tid.'" alt="&Auml;nderung erfolgreich" src="design/haekchen.png" width="16" height="16" /></td>';
            if(auf_doppeltermin_pruefen($zeit, $sid)) {
                echo '<td><b>Doppeltermin!</b></td>';
            } else {
                echo '<td></td>';
            } 
        } else {
            echo "<td>Noch kein Termin</td><td></td><td></td><td></td>";
        }

		echo '</tr>';
		$i++;
	}
	if ($i==0) echo '<tr><td colspan="5"><i>Keine Termine vereinbart.</i></td></tr>';
}

function lehrer($sid) {
	$sid = mysql_real_escape_string($sid);

	$cmd = "SELECT id FROM Lehrer, VSchuelerLehrer WHERE id=lehrerID AND Krank != 1 AND schuelerID=$sid";
	$result = mysql_query($cmd);
	$lehrerIDs = array();
	while ($row = mysql_fetch_array($result)) {
			$lehrerIDs[] = $row['id'];
	}

	foreach($lehrerIDs as $lehrer)
	{
		$result2 = mysql_query("SELECT * FROM VTermine WHERE schuelerID=".$sid." AND lehrerID=".$lehrer);
		if (!mysql_fetch_array($result2)) {
			$result3 = mysql_query("SELECT LName, LVorname FROM Lehrer WHERE id=".$lehrer);
			$name = mysql_fetch_array($result3);
			$selected = '';
			if(isset($_SESSION['mein_lehrer_init'])) {
				$_SESSION['mein_lehrer'] = $_SESSION['mein_lehrer_init'];
				unset($_SESSION['mein_lehrer_init']);
			}
			if(isset($_SESSION['mein_lehrer'])) {
				if ($_SESSION['mein_lehrer']==$lehrer) {
					$selected = ' selected="selected"';
				}
			}
			$options[]= '<option value="'.$lehrer.'"'.$selected.'>'.$name['LName'].', '.$name['LVorname'].'</option>';
		}
	}
	if (!empty($options)) {
		echo '<select id="lehrer">';
		foreach ($options as $option) echo $option;
		echo '<option value="other">Weitere Lehrer</option>
			</select>
			<input type="button" id="terminhinzufuegen" value="Termin mit Lehrer hinzufügen" />';
	}
	elseif (count($lehrerIDs)==0) echo '<input type="button" id="other" value="Lehrer auswählen" />';
	else echo '<input type="button" id="other" value="Weitere Lehrer" />';
}

function kein_termin() {
	echo '<script>alert("Leider ist bei diesem Lehrer kein Termin mehr möglich.");</script>';
}

// Termin einfügen; gibt true bei Erfolg und false bei Misserfolg aus
function insert($lehrer, $schueler, $auto=false) { 
	$lehrer = mysql_real_escape_string($lehrer);
	$schueler = mysql_real_escape_string($schueler);

	// Prüfe, ob dieser Lehrer überhaupt existiert
	$cmd = "SELECT 1 FROM Lehrer WHERE id = '$lehrer' AND Krank != 1";
	$result = mysql_query($cmd);
	if (!$row = mysql_fetch_array($result)) return;

	// Prüfe, ob bereits für diesen Schüler der eine maximal zugelassene Termin pro Lehrer erreicht ist
	$cmd = "SELECT 1 FROM VTermine WHERE lehrerID = '$lehrer' AND schuelerID = '$schueler'";
	$result = mysql_query($cmd);
	if ($row = mysql_fetch_array($result)) { kein_termin(); return; }

	$belegt = belegt($lehrer, $schueler, $auto);

	global $startzeitR, $anzahl_termine;

	$zeit = $startzeitR;

	$termin_gefunden = false;

	for ($i = 0; $i<$anzahl_termine; $i++) {
		if(!in_array($zeit, $belegt)) { $termin_gefunden = true; break; }

		$zeit = naechste_zeit($zeit);
	}
	
	if ($termin_gefunden) {
		/* $cmd  = "INSERT INTO VTermine (schuelerID, lehrerID, Zeit)
				VALUES('".$schueler."', '".$lehrer."', '".$zeit."')"; */
        $cmd = "INSERT INTO VTermine (schuelerID, lehrerID, Zeit) (SELECT '$schueler' AS schuelerID, '$lehrer' AS lehrerID, '$zeit' AS Zeit FROM (SELECT DISTINCT 1 FROM (SELECT 1) AS _1 WHERE TIME_FORMAT(TIME('$zeit'), '%H:%i:00') NOT IN (SELECT DISTINCT Zeit FROM VTermine WHERE lehrerID='$lehrer')) AS _t)";
		// Doppelbelegungen werden auf jeden Fall verhindert! :)
		mysql_query($cmd);
		//echo mysql_error();
		return true;
	} else {
		return false;
	}
}

?>
