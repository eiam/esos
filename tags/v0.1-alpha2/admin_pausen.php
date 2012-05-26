<?php

require_once("./includes/main.inc.php");
require_once("./includes/admin_auth.inc.php");
require_once("./includes/mysql.inc.php");
require_once("./includes/termine.inc.php");

head();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$zeit = mysql_real_escape_string($_POST['zeit']).":00";
	if($_POST['typ'] == 'gespraech' && isset($_POST['change'])) {
		mysql_query("DELETE FROM VTermine WHERE Zeit = '$zeit';");
		mysql_query("INSERT INTO `Pausen` (`Zeit`) VALUES ('$zeit');");
	}
	if($_POST['typ'] == 'pause' && isset($_POST['change'])) {
		mysql_query("DELETE FROM `Pausen` WHERE Zeit = '$zeit';");
	}
}

?>
<h2>Pausen</h2>
<p><i>Hier können Sie Pausen für Lehrkräfte einrichten oder Zeiten freihalten, bei denen die Anmeldung auf die herkömmliche Weise auf Papier erfolgen soll. Pausen für Eltern, um den Raum zu wechseln, werden unabhängig von diesen Einstellungen bereits automatisch gesetzt.</i></p>
<table>
	<tr><th>Zeit</th><th>Nutzung</th></tr>
<?php
	$zeit = $startzeitR;
	for ($i = 0; $i<$anzahl_termine; $i++) {
		echo '<tr>';
		echo '<td>'.$zeit.'</td>';
		echo '<td>';
		echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		echo '<input type="hidden" name="zeit" value="'.$zeit.'" />';
		$abfrage = mysql_query("SELECT * FROM `Pausen` WHERE Zeit='$zeit:00';");
		if (!($row = mysql_fetch_array($abfrage))) {
			echo '<input type="hidden" name="typ" value="gespraech" />';
			echo '<span style="color:green;">Elterngespräch</span>';
		} else {
			echo '<input type="hidden" name="typ" value="pause" />';
			echo '<span style="color:grey;">Pause</span>';
		}
		echo '<input type="submit" name="change" value="↻" /></form></td>';
		echo '</tr>';
		$zeit = naechste_zeit($zeit);
	}
?>
</table>
<?php
foot();

?>
