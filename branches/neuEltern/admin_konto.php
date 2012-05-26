<?php

require_once("./includes/main.inc.php");
require_once("./includes/admin_auth.inc.php");
require_once("./includes/termine.inc.php");
require_once("./includes/form.inc.php");
require_once("./includes/konto.inc.php");


head();

$fields = array();
$fields[] = array( 'name' => 'admin', 'sql_name' => 'ALogin', 'required' => "true");
$fields[] = array( 'name' => 'passwort', 'sql_name' => 'APasswort', 'required' => "true");
$fields[] = array( 'name' => 'vorname', 'sql_name' => 'AVorname', 'required' => "true");
$fields[] = array( 'name' => 'nachname', 'sql_name' => 'AName', 'required' => "true");
$fields[] = array( 'name' => 'email', 'sql_name' => 'AEMail');

$saveOnPost_success = saveOnPost($fields, 'Administratoren', 'adminID');

deleteOnPost('adminID', 'Administratoren');

?>

<h3>Alle Administratoren</h3>
<table>
	<thead>
	<tr><th>Benutzername</th><th>Nachname</th><th>Vorname</th><th>E-Mail</th></tr>
	</thead>
	<?php
	$cmd = "SELECT id, ALogin, AVorname, AName, AEMail FROM Administratoren";
	$result = mysql_query($cmd);
	while($row = mysql_fetch_array($result)) {
		$row['ALogin'] = htmlspecialchars($row['ALogin'], ENT_QUOTES, "UTF-8");
		$row['AName'] = htmlspecialchars($row['AName'], ENT_QUOTES, "UTF-8");
		$row['AVorname'] = htmlspecialchars($row['AVorname'], ENT_QUOTES, "UTF-8");
		$row['AEMail'] = htmlspecialchars($row['AEMail'], ENT_QUOTES, "UTF-8");
		echo "<tr><td>$row[ALogin]</td><td>$row[AName]</td><td>$row[AVorname]</td><td>$row[AEMail]</td>";
		echo '<td><form method="post" action="'.$_SERVER['PHP_SELF'].'">
<input type="hidden" name="adminID" value="'.$row["id"].'" />
<input type="hidden" name="edit" value="true" />
<input type="image" src="design/edit.png" width="16" height="16" alt="[Bearbeiten]" /></form></td>';
		echo '<td><form method="post" action="'.$_SERVER['PHP_SELF'].'">
<input type="hidden" name="adminID" value="'.$row["id"].'" />
<input type="hidden" name="delete" value="true" />';
		if (mysql_num_rows($result)>1) {
			echo '<input type="image" src="design/trash.png" width="16" height="16" alt="[Löschen]" />';
		}
		echo '</form></td></tr>';
	}
	?>
</table>
<?php

if(!isset($_POST['neueradmin'])) {
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">
	<p>
	<input type="hidden" name="neueradmin" value="true" />
	<input type="submit" value="Neuen Administrator hinzufügen" class="bigaddbutton" />
	</p></form>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
	echo '<h3>Administrator bearbeiten</h3>';
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['neueradmin']))  {
	echo '<h3>Neuen Administrator hinzufügen</h3>';
}

$admin = array();
$admin['ALogin'] = "";
$admin['APasswort'] = generatePassword();
$admin['AVorname'] = "";
$admin['AName'] = "";
$admin['AEMail'] = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
	$id = mysql_real_escape_string($_POST['adminID']);
	$cmd = "SELECT * FROM Administratoren WHERE id = '$id'";
	$result = mysql_query($cmd);
	$row = mysql_fetch_array($result);
	$admin = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['neu']) && !$saveOnPost_success) {
	$admin['ALogin'] = $_POST['admin'];
	$admin['APasswort'] = $_POST['passwort'];
	$admin['AVorname'] = $_POST['vorname'];
	$admin['AName'] = $_POST['nachname'];
	$admin['AEMail'] = $_POST['email'];
}

$fields = array();
$fields[] = array(	'label' => 'Benutzername', 'name' => 'admin',
					'attr' => 'autocomplete="off"', 'value' => $admin['ALogin']);

$fields[] = array(	'label' => 'Passwort', 'name' => 'passwort', 'type' => 'text',
					'attr' => 'autocomplete="off"', 'value' => $admin['APasswort']);

$fields[] = array(	'label' => 'Vorname', 'name' => 'vorname',
					'value' => $admin['AVorname']);

$fields[] = array(	'label' => 'Nachname', 'name' => 'nachname',
					'value' => $admin['AName']);

$fields[] = array(	'label' => 'E-Mail', 'name' => 'email', 'type' => 'email',
					'value' => $admin['AEMail']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['neueradmin']) or isset($_POST['edit']))) formular($fields, 'adminID');

foot();
?>
