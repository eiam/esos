<?php
	$page = "eltern_ausgabe";

    require('./includes/head.inc.php');
	require('./includes/mysql.inc.php');
	require('./includes/eltern_auth.inc.php');
	$ID = $_SESSION["id"];

	function termine($id) {
		$cmd = "SELECT LName, LVorname, TIME_FORMAT(TIME(Zeit), '%H:%i') AS Zeit, Raum
			FROM Lehrer, VTermine
			WHERE schuelerID=$id AND lehrerID = Lehrer.id ORDER BY Zeit";
		$result = mysql_query($cmd);
		echo "<table><tr><th>Zeit</th><th>Lehrer</th><th>Raum</th></tr>\n";
		while($row = mysql_fetch_array($result)) {
			echo "<tr><td>$row[Zeit]</td><td>$row[LVorname] $row[LName]</td><td>$row[Raum]</td></tr>\n";
		}
		echo "</table>\n";
	}
?>
<h2>Terminübersicht</h2>
<?php termine($ID); ?>
<p id="print"><a href="javascript:window.print()">Tabelle drucken</a></p>
<?php require('./includes/foot.inc.php'); ?>

	
