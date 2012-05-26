<?php

require_once("mysql.inc.php");
require_once("termine.inc.php");
require_once("frist.inc.php");

// Allgemein
function faecher($ID) {
	$ID = mysql_real_escape_string($ID);
	$result=mysql_query("SELECT Fach, Kuerzel FROM Faecher, VLehrerFach WHERE Faecher.id=fachID AND lehrerID=".$ID);
	while ($row=mysql_fetch_array($result))
	{
		if (isset($faecher)) $faecher.=" / ".'<abbr title="'.$row['Fach'].'">'.$row['Kuerzel'].'</abbr>';
		else $faecher='<abbr title="'.$row['Fach'].'">'.$row['Kuerzel'].'</abbr>';
	}
	if (isset($faecher)) return $faecher;
}

// Für Admin und Lehrer selber
function krank_melden($ID) {
	$ID = mysql_real_escape_string($ID);

	// Lehrer in Datenbank als "krank" markieren
	$cmd = "UPDATE Lehrer SET Krank=1 WHERE id = '$ID'";
	mysql_query($cmd);

	// Alle Termine dieses Lehrers löschen
	$cmd = "DELETE FROM VTermine WHERE lehrerID = '$ID'";
	mysql_query($cmd);

	// Vorname und Nachnamen des Lehrern für E-Mail ermitteln
	$cmd = "SELECT LVorname, LName FROM Lehrer WHERE id='$ID'";
	$result = mysql_query($cmd);
	$row = mysql_fetch_array($result);
	$vorname = $row["LVorname"];
	$nachname = $row["LName"];

	// Alle betroffenen Eltern per E-Mail über die Absage der Termine informieren
	$cmd = "SELECT DISTINCT SEMail FROM Schueler, VTermine WHERE (schuelerID=id and lehrerID='$ID') or id IN (SELECT id FROM Schueler WHERE elternID IN (SELECT elternID FROM Schueler, VTermine WHERE schuelerID=id and lehrerID='$ID'))";
	$result = mysql_query($cmd);
	while($row = mysql_fetch_array($result)) {
		if (filter_var($row["SEmail"], FILTER_VALIDATE_EMAIL)) {
			$nachricht = "Hiermit werden Sie darüber informiert, dass der Lehrer $nachname, $vorname am kommenden Elternsprechtag leider nicht erscheinen kann.";
			//echo "<p><strong>Sende Mail an $row[SEmail]...</strong></p>";
		    $header  = "MIME-Version: 1.0\n";
		    $header .= "Content-type: text/plain; charset=utf-8\n";
			$header .= "From: Elternsprechtag ".readConfig("SCHULNAME")." <".readConfig("E-MAIL").">";
			$betreff = "Elternsprechtag ".readConfig("SCHULNAME").": Absage eines Termins";
			@mail($row["SEmail"], $betreff, $nachricht, $header);
		}
	}
}

function gesund_melden($ID) {
	$ID = mysql_real_escape_string($ID);
	$cmd = "UPDATE Lehrer SET Krank='' WHERE id = '$ID'";
	mysql_query($cmd);
}

// Für Eltern
function meine_lehrer() {
	echo '<tr>
		<th></th><th>Name</th> <th>Fächer</th> <th>E-Mail</th> <th>Termin</th>
		</tr>';
	$result=mysql_query("SELECT id, LName, LVorname, LEmail, Krank FROM Lehrer, VSchuelerLehrer WHERE schuelerID=".$_SESSION['id']." AND lehrerID=id ORDER BY LName");
	$i = 0;
	while ($row=mysql_fetch_array($result)) {
		echo '<tr>';
		if (!eintragungsfrist())
			echo '<td><input type="checkbox" class="lehrer" id="'.$row['id'].'" checked="checked" /></td>';
		else echo '<td></td>';
		echo '<td>'.$row['LName'].', '.$row['LVorname'].'</td>';
		echo '<td>'.faecher($row['id']).'</td><td>'.$row['LEmail'].'</td>';
		if ($row["Krank"]!=1) {
			$termine = lehrer_termine($_SESSION['id'], $row['id']);
			$terminetxt = ($termine>1) ? $termine.' Termine' : '<img src="./design/haekchen.png" alt="✓" />';
			$class = ($termine>0) ? '✓ terminlink' : '× xterminlink';
			$text = ($termine>0) ? $terminetxt : '<img src="./design/cross.png" alt="×" />';
			echo '<td><a href="#" ref="'.$row['id'].'" class="'.$class.'">'.$text.'</a></td>';
		} else {
			echo '<td><i>Kein Termin möglich.</i></td>';
		}
		echo '</tr>';
		$i++;
	}
	if ($i == 0) echo '<tr><td colspan="5"><i>Sie haben keine Lehrer ausgewählt. Bitte wählen Sie Ihre Lehrer aus der unteren Liste mit einem Klick auf das jeweilige Kästchen aus.</i></td></tr>';
}

function andere_lehrer() {
	echo '<tr>
		<th></th><th>Name</th> <th>Fächer</th> <th>E-Mail</th> <th>Info</th>
		</tr>';
	$result=mysql_query("SELECT id, LName, LVorname, LEmail, Krank FROM Lehrer WHERE id NOT IN (SELECT lehrerID FROM VSchuelerLehrer WHERE schuelerID=$_SESSION[id]) ORDER BY LName");
	$i = 0;
	while ($row=mysql_fetch_array($result)) {
		$termin_moeglich = (($row["Krank"]!=1&&!lehrer_ausgebucht($row["id"])));
		$disabled = ($termin_moeglich) ? '' : 'disabled="disabled"';
		echo '<tr>';
		if (!eintragungsfrist())
			echo '<td><input type="checkbox" '.$disabled.' class="lehrer" id="'.$row['id'].'" /></td>';
		else echo '<td></td>';
		echo '<td>'.$row['LName'].', '.$row['LVorname'].'</td>';
		echo '<td>'.faecher($row['id']).'</td><td>'.$row['LEmail'].'</td>';
		if ($termin_moeglich) echo '<td></td>';
		else echo '<td><i>Kein Termin möglich.</i></td>';
		echo '</tr>';
		$i++;
	}
	if ($i == 0) echo '<script>$("#andere").fadeOut();$("#andereh").fadeOut();</script>';
	else echo '<script>$("#andere").fadeIn();$("#andereh").fadeIn();</script>';
}

function lehrer_termine($sid, $lid) {
	$sid = mysql_real_escape_string($sid);
	$lid = mysql_real_escape_string($lid);
	$result = mysql_query("SELECT COUNT(*) FROM VTermine WHERE lehrerID='$lid' AND schuelerID='$sid'");
	$row = mysql_fetch_row($result);
	return $row[0];
}

function termin_tabelle() {
	echo '<p><strong>Elternsprechtag am '.readConfig('TAG').', '.readConfig('SCHULNAME').'</strong></p>';
	echo '<table><tr><th class="smallwidth">Zeit</th><th>Schüler</th></tr>'."\n";
	$zeit = $GLOBALS['startzeitR'];
	for ($i = 0; $i<$GLOBALS['anzahl_termine']; $i++) {
		echo "<tr>";
		echo "<td>".$zeit."</td>";
		$abfrage = mysql_query("SELECT TIME_FORMAT(TIME(Zeit), '%H:%i') AS Zeit FROM Pausen WHERE Zeit = '$zeit'");
		if ($row = mysql_fetch_array($abfrage)) {
			echo '<td><span class="pause">Pause</span></td>';
		} else {
			$abfrage = mysql_query("SELECT Zeit, SName, SVorname, SKlasse FROM `VTermine`, Schueler WHERE lehrerID=".$GLOBALS['ID']." AND id=schuelerID AND Zeit='".$zeit.":00';");
			if (($row = mysql_fetch_array($abfrage))===false) {
				echo "<td><span class=\"frei\">frei</span></td>";
			} else {
				echo "<td>";
				echo "<span class=\"vorname\">$row[SVorname]</span> ";
				echo "<span class=\"name\">$row[SName]</span> ";
				echo "<span class=\"klasse\">($row[SKlasse])</span>";
				if ($row = mysql_fetch_array($abfrage)) {
					echo "<strong>Doppelbelegung!</strong>";
				}
				echo "</td>";
			}
		}
		echo "</tr>";
		$zeit = naechste_zeit($zeit);
	}
	echo '</table>';
}

?>
