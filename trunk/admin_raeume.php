<?php

require_once('./includes/main.inc.php');

head();


echo "<h2>Räume</h2>";

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$add_raeume = explode(",", $_POST["add_raeume"]);
	$result = mysql_query("SELECT LName, LVorname, id FROM Lehrer WHERE RAUM = ''");
	$i=0;
	while($row = mysql_fetch_array($result)) {
		$id = $row["id"];
		$raum = $add_raeume[0];
		$name = $row['LVorname'].' '.$row["LName"];
		unset($add_raeume[0]); sort($add_raeume);
		mysql_query("UPDATE Lehrer SET Raum = '$raum' WHERE id = '$id'");
		echo "<p>$name ist nun für den Raum $raum eingetragen.</p>";
		$i++;
	}
	if($i==0) { echo "<p><strong>Es konnten keine Räume hinzugefügt werden. Alle Lehrer haben bereits ihren Raum.</strong></p>"; }
}

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<?php
echo '<p>Bitte geben Sie in das untere Textfeld eine kommaseparierte Liste der Räume an. (ohne Leerzeichen nach dem Komma)</p>
<textarea name="add_raeume"></textarea>';

echo '<p><input type="submit" value="Räume zuteilen" /></p>';

echo "<h3>Zugeteilte Räume</h3>";

echo '<table><tr><th>Raumname</th><th>Lehrer</th></tr>';
$result = mysql_query("SELECT DISTINCT Raum FROM Lehrer");
while($row = mysql_fetch_array($result)) {
	echo '<tr><td>'.$row["Raum"].'</td>';
	$result2 = mysql_query("SELECT LVorname, LName FROM `Lehrer` WHERE Raum = '$row[Raum]'");
	$raumlehrer = array();
	while($row2 = mysql_fetch_array($result2)) {
		$raumlehrer[] = $row2['LVorname'].' '.$row2["LName"];
	}
	echo "<td>".implode(", ", $raumlehrer)."</td></tr>";
}
echo '</table>';

echo '<h3>Lehrer ohne Raum</h3>';

$result = mysql_query("SELECT LName, LVorname, id FROM Lehrer WHERE RAUM = ''");
$i = 0;
$raumlos = array();
while($row = mysql_fetch_array($result)) {
	$i++;
	$raumlos[] = $row['LVorname'].' '.$row["LName"];	
}
echo implode(", ", $raumlos);
if($i==0) { echo "<p>Es gibt keine Lehrer ohne Raum.</p>"; }

echo '</form>';

foot();

?>
