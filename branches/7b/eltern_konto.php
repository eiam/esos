<?php

require_once('./includes/main.inc.php');
require_once('./includes/mysql.inc.php');
require_once('./includes/eltern_auth.inc.php');
require_once('./includes/konto.inc.php');

head();
$result = mysql_query("SELECT DISTINCT SEmail FROM Schueler WHERE elternID = '".ElternIDdesKindes($_SESSION['id'])."';");
if (mysql_num_rows($result)>1) {
	echo '<h2>E-Mail-Adressen</h2>
		<p>Folgende E-Mail-Adressen haben wir &uuml;ber Sie gespeichert:</p>';
} else {
	echo '<h2>E-Mail-Adresse</h2>
		<p>Diese E-Mail-Adresse haben wir &uuml;ber Sie gespeichert:</p>';
}
echo '<ul>';
while ($row = mysql_fetch_row($result)) {
        echo "<li>".$row[0]."</li>";
}
?>
</ul>
<p>Falls sich daran etwas ge&auml;ndert haben sollte, wenden Sie sich bitte an das Sekretariat.</p>

<h2>Neues Passwort anfordern</h2>
<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
	$altespasswort = mysql_real_escape_string($_POST['altespasswort']);
	$passwort_richtig = false;
	$result = mysql_query("SELECT id, SPasswort FROM Schueler WHERE elternID = '".ElternIDdesKindes($_SESSION['id'])."' AND SPasswort = '$altespasswort';");
	if ($row = mysql_fetch_array($result)) {
		$passwort_richtig = true;
	}
	if (!$passwort_richtig) {
		echo "<p><strong>Achtung:</strong> Bitte geben Sie Ihr altes Passwort richtig ein!</p>";
	} else {
		$passwort=generatePassword();
		$salt=md5($_SESSION["vorname"]);
		$neuespasswort = sha1($passwort.$salt);
		mysql_query("UPDATE Schueler SET SPasswort = '".$neuespasswort."' WHERE elternID = '".ElternIDdesKindes($_SESSION['id'])."' AND SPasswort = '$altespasswort';");
		echo "<p><label>Neues Passwort</label> $passwort</p>";

		$anfrage = "SELECT SLogin, SEmail FROM Schueler WHERE elternID = '".ElternIDdesKindes($_SESSION['id'])."' AND SPasswort ='".$neuespasswort."';";
		$ergebnis = mysql_query($anfrage);
		$daten = mysql_fetch_object($ergebnis);

		$username = $daten->SLogin;
		$email = $daten->SEmail;

	    PasswordMail($username, $passwort, $email);
	}
}

passwordForm();

foot();

?>
