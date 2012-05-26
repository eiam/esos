<?php

function generatePassword($length = 8) {
	$password = "";
	$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
	$maxlength = strlen($possible);
	if ($length > $maxlength) {
		$length = $maxlength;
	}
	$i = 0; 
	while ($i < $length) { 
		$char = substr($possible, mt_rand(0, $maxlength-1), 1);
		if (!strstr($password, $char)) { 
			$password .= $char;
			$i++;
		}
	}
	return $password;
}

// Für Eltern und Lehrer
function PasswordMail($username, $password, $email) {
    
    global $hostname, $path;
    
	$betreff = "Ihre Zugangsdaten für das Elternsprechtagsportal";

	$header  = "MIME-Version: 1.0\n";
	$header .= "Content-type: text/plain; charset=utf-8\n";
	$header .= "X-IP: ".$_SERVER["REMOTE_ADDR"]."\n";
	$header .= "X-Timestamp: ".time()."\n";
	$header .= "From: Elternsprechtag ".readConfig("SCHULNAME")." <".readConfig("E-MAIL").">\n";

	$nachricht =
"Da Sie im Elternsprechtagsportal neue Zugangsdaten angefordert haben, senden wir Ihnen diese hiermit zu.

	Benutzername: $username
	Passwort: $password

";
	$nachricht .= 'http://'.$hostname.($path == '/' ? '' : $path)."/ Elternsprechtaganmeldung ".readConfig("SCHULNAME")."\r\n\r\n";

	if (@$send = mail($email, $betreff, $nachricht, $header)) {
		echo "<p><strong>Ihre Zugangsdaten wurden Ihnen zusätzlich per E-Mail zugesendet.</strong></p>";
	} else {
		echo "<p><strong>Ihre Zugangsdaten konnten Ihnen aufgrund eines technischen Fehlers leider nicht per E-Mail zugeschickt werden.</strong></p>";
	}
}

function passwordForm() {
?>
<p>Falls Ihr Passwort in falsche Hände geraten sein sollte, können Sie sich hier ein neues erstellen lassen.</p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p><label>Altes Passwort</label><input name="altespasswort" type="password" autocomplete="off" /></p>
<p><input type="submit" value="Neue Zugangsdaten anfordern" /></p>
</form>
<?php
}

?>
