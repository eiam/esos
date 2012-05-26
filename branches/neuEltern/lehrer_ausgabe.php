<?php

	require("includes/main.inc.php");
	require("includes/mysql.inc.php");
	require("includes/lehrer_auth.inc.php");
	require("./includes/lehrer.inc.php");
	$ID = $_SESSION["id"];

	function raum($id) {
		$cmd = "SELECT Raum FROM Lehrer WHERE id = $id";
		$result = mysql_query($cmd);
		$row = mysql_fetch_array($result);
		return $row["Raum"];
	}

	head();
?>

	<h2>Raum: <?php echo raum($ID) ?></h2>
	<div class="todo">
	<?
		if(eintragungsfrist()) {
			echo '<p><b>Aufgabe:</b></p><ul><li>Bitte drucken Sie Ihren Terminaushang aus.</li></ul>';
		}
		elseif(faecher($ID)=="")
			echo '<p><b>Aufgabe:</b></p><ul><li>Bitte wählen Sie nach einem Klick auf „<a href="lehrer_faecher.php">Fächerauswahl</a>“ alle Fächer aus, welche Sie unterrichten.</li></ul>';
	?>
	</div>
	
<?php
	$row=mysql_fetch_array(mysql_query("SELECT Krank FROM Lehrer WHERE id = $ID"));
	if ($row["Krank"]==1) {
		echo '<p><i>Sie wurden krankgemeldet. Daher werden sich keine Eltern bei Ihnen eintragen k&ouml;nnen. Melden Sie sich bitte beim Administrator dieses Portals, falls Sie doch wieder zur Verf&uuml;gung stehen sollten.</i></p>';			
	}

	termin_tabelle();

	if(!eintragungsfrist()) {
		echo "<p class=\"warning\">Achtung! Dies ist nur der vorläufige, nicht endgültige Terminplan!</p>";
	} else {
		echo '<p id="print"><a href="lehrer_ausdruck.php">Terminaushang</a></p>';
	}

foot();

?>
