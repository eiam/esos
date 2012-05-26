<?php

require("./includes/main.inc.php");
require("./includes/admin_auth.inc.php");
require("./includes/mysql.inc.php");

head();

// Gemerkten Eintrag anzeigen
if (isset($_SESSION['schueler_entry'])) {
	$_SERVER["REQUEST_METHOD"] = "POST";
	$_POST['entry'] = $_SESSION['schueler_entry'];
}

$entries_per_page = 13;

$entry = 0;

$cmd = "SELECT COUNT(*) FROM Schueler";
$result = mysql_query($cmd);
$row = mysql_fetch_array($result);
$all_entries = $row[0];

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['entry'])) {
	$entry = (int) $_POST['entry'];
	if (isset($_POST['next'])) {
		$entry += $entries_per_page;
	} elseif (isset($_POST['back'])) {
		$entry -= $entries_per_page;
	} elseif (isset($_POST['nextfast'])) {
		$entry += $entries_per_page*10;
	} elseif (isset($_POST['backfast'])) {
		$entry -= $entries_per_page*10;
	}
	if ($entry>$all_entries-1) $entry=$all_entries-1;
	if ($entry<0) $entry=0;
}

// Angezeigte Seite bzw. angezeigten Eintrag für eine Session speichern
$_SESSION['schueler_entry'] = $entry;

?>
<h2>Schüler</h2>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table>
<tr><th>Nachname</th><th>Vorname</th><th>Klasse</th><th>E-Mail-Adresse der Eltern</th><!--<th></th>--></tr>
<p>
<input type="submit" value="&lt;&lt;" name="backfast" />
<input type="submit" value="Vorherige Seite" name="back" />
<input type="submit" value="Nächste Seite" name="next" />
<input type="submit" value="&gt;&gt;" name="nextfast" />
<?php
	for ($first_entry=0;$first_entry <= $entry-$entries_per_page;$first_entry+=$entries_per_page);

	$page = ($first_entry+$entries_per_page)/$entries_per_page;
	$all_pages = ceil(($all_entries)/$entries_per_page);

	echo "Seite ".$page." / ".$all_pages;
?>
</p>
<?php
$result=mysql_query("SELECT id, SName, SVorname, SKlasse, SEmail FROM Schueler ORDER BY SName LIMIT $first_entry, $entries_per_page");
		while ($row=mysql_fetch_array($result)) {
		    echo '<tr>';
			echo '<td>'.$row['SName'].'</td>';
			echo '<td>'.$row['SVorname'].'</td>';
			echo '<td>'.$row['SKlasse'].'</td>';
			echo '<td>'.$row['SEmail'].'</td>';
			/* echo '<td><input type="button" value="X"/></td>'; */
			echo '</tr>';
		}
?>
</table>
<input type="hidden" value="<?php echo $entry; ?>" name="entry" />
</form>
<!--
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<h3>Neuen Schüler kurzfristig hinzufügen</h3>
<p>Das Portal verwendet die aus der Schuldatenbank importierten Schüler. Beachten Sie, dass wenn Sie hier einen Schüler manuell hinzufügen, dass dieser mit dem nächsten Datenabgleich wieder aus der Datenbank entfernt wird.</p>
<p><label for="vorname">Vorname:</label> <input type="text" name="vorname" /></p>
<p><label for="name">Nachname:</label> <input type="text" name="name" /></p>
<p><label for="klasse">Klasse:</label> <select name="klasse"><option>5a</option></select></p>
<p><label for="email">E-Mail-Adresse der Eltern:</label> <input type="email" name="email" /></p>
<input type="submit" value="Neuen Schüler hinzufügen" />
</form>
-->
<?php
foot();
?>
