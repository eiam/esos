<?php

require("./includes/main.inc.php");
require("./includes/admin_auth.inc.php");

head();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add'])) { // Hinzufügen
		$cmd = "INSERT INTO `Faecher` (`Fach`, `Kuerzel`) VALUES ('$_POST[add_fach]', '$_POST[add_kuerzel]')";
		mysql_query($cmd);
	} elseif(isset($_POST['delete'])) {
		$cmd = "DELETE FROM `VLehrerFach` WHERE fachID = '$_POST[delete]'";
		mysql_query($cmd);
		$cmd = "DELETE FROM `Faecher` WHERE id = '$_POST[delete]'";
		mysql_query($cmd);
	} else { // Bearbeiten
		$fachID = '';
		if(isset($_POST['fach'])) {
			foreach ($_POST['fach'] as $key => $value) {
				$fachID = $key; break;
			}
		}
		if(!$fachID=='') {
			$cmd = "UPDATE Faecher SET Fach = '".$_POST['fach'][$fachID]."' WHERE id = '$fachID'";
			mysql_query($cmd);
		} else {
			$kuerzelID = '';
			if(isset($_POST['kuerzel'])) {
				foreach ($_POST['kuerzel'] as $key => $value) {
					$kuerzelID = $key; break;
				}
			}
			if($kuerzelID!='') {
				$cmd = "UPDATE Faecher SET Kuerzel = '".$_POST['kuerzel'][$kuerzelID]."' WHERE id = '$kuerzelID'";
				mysql_query($cmd);
			}
		}
	}

}

$form_start = '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';

?>
<p>Ihre Änderungen werden sofort nach dem Bearbeiten automatisch übernommen.</p>
<table class="faecher">
<tr><th>Fach</th><th>Kürzel</th><th>Lehrer</th><th/></tr>
<?php
$cmd = 'SELECT * FROM `Faecher` ORDER BY Fach';
$result = mysql_query($cmd);
while($row = mysql_fetch_array($result)) {
	echo '<tr>';
	echo '<td>'.$form_start.'<input value="'.$row['Fach'].'" name="fach['.$row['id'].']" onblur="this.form.submit();" type="text" maxlength="20" /></form></td>';
	echo '<td>'.$form_start.'<input value="'.$row['Kuerzel'].'" name="kuerzel['.$row['id'].']" onblur="this.form.submit();" type="text" maxlength="5" /></form></td>';
	echo '<td>';
	$count_result = mysql_query("SELECT COUNT(*) FROM `VLehrerFach` WHERE fachID = '$row[id]'");
	$count_row = mysql_fetch_array($count_result);
	if($count_row[0]!=0) { echo $count_row[0]; }
	echo '</td>';
	echo '<td>'.$form_start.'
		<input type="hidden" name="delete" value="'.$row["id"].'" />
		<input type="image"	src="design/trash.png" alt="[Löschen]" width="16" height="16" title="Fach löschen" />
		</form></td>';
	echo '</tr>';
}
	echo '<tr>';
	echo '<td><input value="'.$row['Fach'].'" onchange="document.getElementById(\'add_fach\').value=this.value" type="text" maxlength="20" /></td>';
	echo '<td><input value="'.$row['Kuerzel'].'" onchange="document.getElementById(\'add_kuerzel\').value=this.value" type="text" maxlength="5" /></td>';
	echo '<td/>';
	echo '<td>'.$form_start.'
			<input type="hidden" name="add_fach" id="add_fach" value="" />
			<input type="hidden" name="add_kuerzel" id="add_kuerzel" value="" />
			<input type="hidden" name="add" value="true" />
			<input type="image" value="'.$row["id"].'"
			src="design/add.png" alt="[Eintragen]" width="16" height="16" title="Fach hinzufügen" /></form></td>';
	echo '</tr>';
?>
</table>
<?php
foot();

?>
