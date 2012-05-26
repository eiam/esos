<?php

require_once('./includes/main.inc.php');
require_once('./includes/termine.inc.php');

head();

echo "<h2>Teilzeit-Lehrer</h2>";

function teilzeit_select_start($teilzeit_start) {
	global $startzeitR, $anzahl_termine;

	$o = '<select>';

	$zeit = $startzeitR;

	for ($i = 0; $i<$anzahl_termine; $i++) {
		$selected='';

		if ($zeit==substr($teilzeit_start,0,5)) $selected=' selected="selected"';

		$o .= '<option'.$selected.'>'.$zeit.'</option>';
		
		$zeit = naechste_zeit($zeit);
	}
	
	$o .= "</select>";

	return $o;

}

echo "<table><tr><th>Lehrername</th><th>Startzeit</th><th>Endzeit</th></tr>";

$result = mysql_query("SELECT * FROM Lehrer");
while($row = mysql_fetch_array($result)) {
	$name = $row["LVorname"] . " " . $row["LName"];
	echo "<tr><td>$name</td><td>".teilzeit_select_start($row["Startzeit"])."</td><td><select><option>$row[Endzeit]</option></select></td></tr>";
}
echo "</table>";

foot();

?>
