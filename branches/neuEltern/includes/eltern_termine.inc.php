<?php
	require_once("config.inc.php");
	require_once("termine.inc.php");

	function elternTermine($id) {
		echo '<p><strong>Elternsprechtag am '.readConfig('TAG').', '.readConfig('SCHULNAME').'</strong></p>';
		$cmd = "SELECT LName, LVorname, TIME_FORMAT(TIME(Zeit), '%H:%i') AS Zeit, Raum,
					   Schueler.id, SVorname, SName, SKlasse
			FROM Lehrer, VTermine, Schueler
			WHERE schuelerID = Schueler.id
				AND (Schueler.elternID = (SELECT elternID FROM Schueler WHERE id='$id') OR Schueler.id='$id')
				AND lehrerID = Lehrer.id ORDER BY Zeit";
		$result = mysql_query($cmd);
		$kindh = (count(geschwister($id))>1) ? "<th>Kind</th>" : "";
		echo "<table><tr><th>Zeit</th><th>Lehrer</th><th>Raum</th>$kindh</tr>\n";
		$i = 0;
		$doppeltermin = false;
		$termine = array();
		while($row = mysql_fetch_array($result)) {
			$kindr = (count(geschwister($id))>1)
					? "<td>$row[SVorname] $row[SName] ($row[SKlasse])</td>" : "";
			echo "<tr><td>$row[Zeit] – ".naechste_zeit($row['Zeit'])."</td><td>$row[LVorname] $row[LName]</td><td>$row[Raum]</td>$kindr</tr>\n";
			if(in_array($row['Zeit'], $termine)) { $doppeltermin = true; }
			$termine[] = $row['Zeit'];
			$i++;
		}
		if ($i == 0) echo '<tr><td colspan="4"><i>Keine Termine vereinbart.</i></td></tr>';
		echo "</table>\n";
		if($doppeltermin) { echo '<p><b>Achtung!</b> Sie haben bei der <a href="eltern_termin.php">manuellen Terminwahl</a> zur selben Zeit verschiedene Termine vereinbart.</p>'; }
	}
?>
