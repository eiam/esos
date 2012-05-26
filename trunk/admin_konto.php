<?php

require_once("./includes/main.inc.php");
require_once("./includes/admin_auth.inc.php");
require_once("./includes/termine.inc.php");
require_once("./includes/form.inc.php");
require_once("./includes/konto.inc.php");

// Wenn ein Admin seinen eigenen Namen ändert, soll sich das sofort auf die Namensanzeige über dem Menü auswirken
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save']) ) {
	if ($_POST['adminID']==$_SESSION["id"]) {
		if(isset($_POST['vorname']))
			$_SESSION["vorname"] = htmlspecialchars($_POST['vorname'], ENT_QUOTES, "UTF-8");
		if(isset($_POST['nachname']))
			$_SESSION["name"] = htmlspecialchars($_POST['nachname'], ENT_QUOTES, "UTF-8");
	}
}

// AdminID setzen
if(!isset($_POST['adminID'])) {
	if(!isset($_POST['neu'])) {
		$_POST['adminID'] = $_SESSION['id'];
	} else { $_POST['adminID'] = ''; }
}

head();

/* Daten speichern */
$fields = array();
if($_POST['adminID'] != $_SESSION['id']) {
	$fields[] = array( 'name' => 'admin', 'sql_name' => 'ALogin', 'required' => "true");
	$fields[] = array( 'name' => 'passwort', 'sql_name' => 'APasswort', 'required' => "true");
	$fields[] = array( 'name' => 'vorname', 'sql_name' => 'AVorname', 'required' => "true");
	$fields[] = array( 'name' => 'nachname', 'sql_name' => 'AName', 'required' => "true");
	$fields[] = array( 'name' => 'email', 'sql_name' => 'AEMail');
} else {
	//$fields[] = array( 'name' => 'vorname', 'sql_name' => 'AVorname', 'required' => "true");
	//$fields[] = array( 'name' => 'nachname', 'sql_name' => 'AName', 'required' => "true");
	$fields[] = array( 'name' => 'email', 'sql_name' => 'AEMail');
	$fields[] = array( 'name' => 'altespw', 'required' => "true");
	$fields[] = array( 'name' => 'neuespw', 'required' => "true");
	$fields[] = array( 'name' => 'neuespw-wh', 'required' => "true");
}

$saveOnPost_success = saveOnPost($fields, 'Administratoren', 'adminID');

/* Admin löschen */
deleteOnPost('adminID', 'Administratoren');

/* Überschrift für Aktion */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit']) && $_POST['adminID'] != $_SESSION['id']) {
	echo '<h3>Administrator bearbeiten</h3>';
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['neu'] ))  {
	echo '<h3>Neuen Administrator hinzufügen</h3>';
} elseif ($_POST['adminID'] == $_SESSION['id']) {
	echo '<h3>Eigene Daten bearbeiten</h3>';
}


$fields = array();
$edit_modus = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['adminID'] != $_SESSION['id'] && ( isset($_POST['neu']) || isset($_POST['edit']))) {
	/* Felder für Formular */
	$fields[] = array('label' => 'Benutzername', 'name' => 'admin', 'sql_name' => 'ALogin', 'attr' => 'autocomplete="off"');
	$fields[] = array('label' => 'Passwort', 'name' => 'passwort', 'sql_name' => 'APasswort', 'type' => 'password','attr' => 'autocomplete="off"', 'value' => generatePassword());
	$fields[] = array('label' => 'Vorname', 'name' => 'vorname', 'sql_name' => 'AVorname');
	$fields[] = array('label' => 'Nachname', 'name' => 'nachname', 'sql_name' => 'AName');
	$fields[] = array('label' => 'E-Mail', 'name' => 'email', 'sql_name' => 'AEMail', 'type' => 'email');
} else {
	/* Formular für eigene Daten */
	//$fields[] = array('label' => 'Vorname', 'name' => 'vorname', 'sql_name' => 'AVorname');
	//$fields[] = array('label' => 'Nachname', 'name' => 'nachname', 'sql_name' => 'AName');
	$fields[] = array('label' => 'E-Mail', 'name' => 'email', 'sql_name' => 'AEMail');
	$fields[] = array('label' => 'Altes Passwort', 'name' => 'altespw', 'value' => '');
	$fields[] = array('label' => 'Neues Passwort', 'name' => 'neuespw', 'value' => '');
	$fields[] = array('label' => 'Neues Passwort wiederholen', 'name' => 'neuespw-wh', 'value' => '');
	$edit_modus = true;
}

if($saveOnPost_success) {
	echo '<p class="success">Ihre Daten wurden gespeichert.</p>';
} else {
	formular($fields, 'adminID', 'Administratoren', $saveOnPost_success, $edit_modus);
}

echo '<h3>Alle Administratoren</h3>';

$fields = array();
$fields[] = array('label' => "Benutzername", "sql_name" => "ALogin");
$fields[] = array('label' => "Nachname", "sql_name" => "AName");
$fields[] = array('label' => "Vorname", "sql_name" => "AVorname");
$fields[] = array('label' => "E-Mail", "sql_name" => "AEMail");
data_table($fields, 'Administratoren', 'adminID');

bigaddbutton("Neuen Administrator hinzufügen");

// Hier werden die Emails an die Administratoren verschickt, wenn ihre Accountdaten geändert werden. Die email enthält den Anmeldenamen und das Passwort
// allerdings nur, wenn eine email adresse angegeben wurde ;)

$id = mysql_real_escape_string($_POST['adminID']);
$cmd = "SELECT AEmail FROM Administratoren WHERE id = '$id'";
$result = mysql_query($cmd);
$row = mysql_fetch_array($result);
$admin = $row;

if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['neu'])||isset($_POST['save'])) && isset($admin['AEmail'])) {
	/*$betreff = "Anderung ihrer Accountdaten auf ESOS";
	$text = "Guten Tag ".$admin[AVorname].$admin[AName].",\n"
	."ihre Accountdaten auf wurden geändert\n"
	."TEST"
	;
	mail($admin[AVorname].$admin[AName]."<".$admin['AEmail'].">", $betreff, $text);*/
	mail($admin['AEmail'], "Elternsprechtsanmeldung [ADMIN-Accountdaten]", "Ihre Accountdaten wurden geändert.");
	echo '<p class="success">Der Administrator wurde per E-Mail über die Änderung informiert.</p>';
}


foot();
?>
