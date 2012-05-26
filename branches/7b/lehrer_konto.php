<?php

require_once('./includes/main.inc.php');
require_once('./includes/mysql.inc.php');
require_once('./includes/lehrer_auth.inc.php');
require_once('./includes/konto.inc.php');

head();
?>
<p><label>Ihre Login-Kennung</label><span class="output"><?php echo $_SESSION["login"]; ?></span></p>
<p><label>Ihre E-Mail-Adresse</label><span class="output"><?php
$result = mysql_query("SELECT LEmail FROM Lehrer WHERE id = '".$_SESSION["id"]."';");
$row = mysql_fetch_row($result);
$email = $row[0];
echo $email;
?></span></p>
<h2>Neues Passwort anfordern</h2>
<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
	$altespasswort = mysql_real_escape_string($_POST['altespasswort']);
	$passwort_richtig = false;
	$result = mysql_query("SELECT id, LPasswort FROM Lehrer WHERE id = '".$_SESSION['id']."' AND LPasswort = '$altespasswort';");
	if ($row = mysql_fetch_array($result)) {
		$passwort_richtig = true;
	}
	if (!$passwort_richtig) {
		echo "<p><strong>Achtung:</strong> Bitte geben Sie Ihr altes Passwort richtig ein!</p>";
	} else {
		$passwort = generatePassword();
		$salt=md5($_SESSION["vorname"]);
		$neuespasswort = sha1($passwort.$salt);
		mysql_query("UPDATE Lehrer SET LPasswort = '".$neuespasswort."' WHERE id = '".$_SESSION['id']."' AND LPasswort = '$altespasswort';");
		echo "<p><label>Neues Passwort</label> $passwort</p>";

		$username = $_SESSION["login"];
		$email = $email;

	    PasswordMail($username, $passwort, $email);
	}
}

passwordForm();
foot();

?>
