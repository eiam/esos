<?php

require("./includes/main.inc.php");
require("./includes/lehrer_auth.inc.php");

head();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$cmd = 'SELECT * FROM `Faecher` ORDER BY id';
	$result = mysql_query($cmd);
	while(@$row = mysql_fetch_array($result)) {
		if (isset($_POST[$row['id']])) {
			$cmd = 'SELECT 1 FROM `VLehrerFach` WHERE lehrerID="'.$_SESSION['id'].'" AND fachID="'.$row['id'].'"';
			if(!mysql_fetch_array(mysql_query($cmd))) {
				$cmd = 'INSERT INTO `VLehrerFach` (fachID, lehrerID) VALUES ("'.$row['id'].'", "'.$_SESSION['id'].'")';
				mysql_query($cmd);
			}
		} else {
			$cmd = 'DELETE FROM `VLehrerFach` WHERE fachID="'.$row['id'].'" AND lehrerID="'.$_SESSION['id'].'"';
			mysql_query($cmd);
		}
	}
}

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>Bitte wählen Sie die Fächer aus, welche Sie unterrichten.</p><p>Klicken Sie danach bitte auf „Speichern“.</p>
<table>
<?php
$cmd = 'SELECT * FROM `Faecher` ORDER BY Fach';
$result = mysql_query($cmd);
while($row = mysql_fetch_array($result)) {
	$cmd = 'SELECT 1 FROM `VLehrerFach` WHERE lehrerID="'.$_SESSION['id'].'" AND fachID="'.$row['id'].'"';
	if(mysql_fetch_array(mysql_query($cmd))) { $checked = ' checked="checked"'; }
	else { $checked = ''; }	
	echo '<tr> <td><input type="checkbox"'.$checked.' name="'.$row['id'].'" /></td>';
	echo '<td>'.$row['Fach'].'</td>';
	echo '</tr>';
}
?>
</table>
<p><input type="submit" value="Speichern" /></p>
</form>
<?php
foot();

?>
