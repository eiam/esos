<?php

$page = "passwort-vergessen";

require("./includes/head.inc.php");
require("./includes/mysql.inc.php");

if($_SERVER["REQUEST_METHOD"] == "POST") {
	include_once ('./securimage/securimage.php');

	$securimage = new Securimage();

	$valid = true;

	if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) { 
    	echo '<p><strong>Bitte geben Sie eine gültige E-Mail-Adresse an.</strong></p>';
		$valid = false;
	}

	$cmd = "SELECT 1 FROM Schueler WHERE SEmail='".mysql_real_escape_string($_POST["email"])."' LIMIT 1";
	$result = mysql_query($cmd);
	if(!$row = mysql_fetch_array($result)) {
		echo '<p><strong>Bitte geben Sie die E-Mail-Adresse an, die Sie der Schule genannt haben.</strong></p>';
		$valid = false;
	}

	if ($securimage->check($_POST['captcha_code']) == false) {
		echo "<p><strong>Der eingegebene Sicherheitscode war falsch.</strong></p>";
		$valid = false;
	}

	if($valid) {
		$cmd = "SELECT SLogin, SPasswort FROM `Schueler` WHERE SEmail = '".mysql_real_escape_string($_POST["email"])."'";
		$result = mysql_query($cmd);
		if($row = mysql_fetch_array($result)) {
			$username = $row["SLogin"];
			$password = $row["SPasswort"];
		}

		$empfaenger = $_POST["email"];

	    $betreff = "Anforderung für neue Zugangsdaten für das Elternsprechtagsportal";

	    $header  = "MIME-Version: 1.0\n";
	    $header .= "Content-type: text/html; charset=utf-8";
		$header .= "X-IP:".$_SERVER["REMOTE_ADDR"]."\n";

	    $nachricht = "Klicken Sie auf folgenden Link, um sich Ihre neuen Zugangsdaten generieren und anzeigen zu lassen.\n\n";
	    $nachricht .= 'http://'.$hostname.($path == '/' ? '' : $path).'/passwort-anzeigen.php?challenge=001\n';
		$nachricht .= "\n\nFalls Sie keine neuen Zugangsdaten angefordert haben sollten, ignorieren Sie bitte einfach diese E-Mail.";

	    if (@$send = mail($empfaenger, $betreff, $nachricht, $header)) {
			echo "<p><strong>Ihre Zugangsdaten wurden versendet.</strong></p>";
		} else {
			echo "<p><strong>Ihre Zugangsdaten konnten aufgrund eines technischen Fehlers leider nicht versendet werden.</strong></p>";
		}
	 }

}


?>
<h2>Neue Zugangsdaten anfordern</h2>
<p>Falls Sie Ihren Benutzernamen und Ihr Passwort vergessen haben, können Sie sich neue Zugangsdaten erstellen lassen.</p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<label for="email">E-Mail-Adresse</label><input type="email" name="email" value="<?php
if($_SERVER["REQUEST_METHOD"] == "POST") { echo strip_tags($_POST["email"]); }
?>" required="required" />
<p><img id="captcha" src="./securimage/securimage_show.php" alt="[CAPTCHA]" onclick="document.getElementById('captcha').src = './securimage/securimage_show.php?' + Math.random(); return false" /></p>
<p><label for="captcha_code">Sicherheitscode</label><input type="text" name="captcha_code" size="10" maxlength="6" required="required" /></p>
<input type="submit" value="Zugangsdaten anfordern" />
</form>
<?php
require("./includes/foot.inc.php");

?>
