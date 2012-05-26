<?php
	$page = "lehrer_ausgabe";

	require("includes/head.inc.php");
	require("includes/mysql.inc.php");
	require("includes/lehrer_auth.inc.php");
	require("./includes/termine.inc.php");
	$ID = $_SESSION["id"];

	function raum($id) {
		$cmd = "SELECT Raum FROM Lehrer WHERE id = $id";
		$result = mysql_query($cmd);
		$row = mysql_fetch_array($result);
		return $row["Raum"];
	}

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$cmd = "UPDATE Lehrer SET Krank=1 WHERE id = $ID";
	}
?>

	<h2>Raum: <?php echo raum($ID) ?></h2>
		<form method="post" action="">
			<input type="submit" value="Krank melden" />
		</form>
                <table>
                        <?php
                                echo "<tr><th>Zeit</th><th>Schüler</th></tr>\n";
                               
                                $akt_minute = $startzeit['minuten'];
                                $akt_stunde = $startzeit['stunde'];
                                for ($i = 0; $i<$anzahl_termine; $i++) {
                                $fuellnull = (strlen((string)$akt_minute)==1?'0':'');
                                        echo "<tr>";
                                        echo "<td>".$akt_stunde.":".$fuellnull.$akt_minute."</td>";
                                        $abfrage = mysql_query("SELECT Zeit, SName, SVorname, SKlasse FROM `VTermine`, Schueler WHERE lehrerID=".$ID." AND id=schuelerID AND Zeit='".$akt_stunde.":".$akt_minute.":00';");
                                        if (($row = mysql_fetch_array($abfrage))===false) {
                                          echo "<td><span class=\"frei\">frei</span></td>";
                                        } else {
                                          echo "<td>";
										  echo "<span class=\"vorname\">$row[SVorname]</span>";
										  echo "<span class=\"name\">$row[SName]</span>";
										  echo "<span class=\"klasse\">($row[SKlasse])</span>";
										  echo "</td>";
                                        }
                                        echo "</tr>";
                                        $akt_minute += $gespraechsdauer;
                                       
                                        if ($akt_minute >= 60) {
                                                $akt_minute -= 60;
                                                $akt_stunde += 1;
                                        }
                                       
                                }
                               
                               
         
                        ?>
</table>

    <p id="print"><a href="javascript:window.print()">Tabelle drucken</a></p>
<?php require("includes/foot.inc.php"); ?>
