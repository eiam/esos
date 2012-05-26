<?php

require_once("./includes/main.inc.php");
require_once("./includes/mysql.inc.php");
require_once("./includes/benutzer.inc.php");
require_once("./includes/konto.inc.php");

logout();

head();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	include_once ('./securimage/securimage.php');

	$securimage = new Securimage();

	$valid = true;

	if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    	echo '<p><strong>Bitte geben Sie eine gültige E-Mail-Adresse an.</strong></p>';
		$valid = false;
	}

	$typ = "schueler";
	$cmd = "SELECT 1 FROM Schueler WHERE SEmail='".mysql_real_escape_string($_POST["email"])."' LIMIT 1";
	$result = mysql_query($cmd);
	if(!$row = mysql_fetch_array($result)) {
		$cmd = "SELECT 1 FROM Lehrer WHERE LEmail='".mysql_real_escape_string($_POST["email"])."' LIMIT 1";
		$result = mysql_query($cmd);
		if($row = mysql_fetch_array($result)) {
			$typ = "lehrer";
		} else{
		    $cmd = "SELECT 1 FROM Administratoren WHERE AEmail='".mysql_real_escape_string($_POST["email"])."' LIMIT 1";
            $result = mysql_query($cmd);
            if($row = mysql_fetch_array($result)) {
                $typ = "admin";
            } else {
	   		    echo '<p><strong>Bitte geben Sie die E-Mail-Adresse an, die Sie der Schule genannt haben.</strong></p>';
			    $valid = false;
            }
		}
	}

	if ($securimage->check($_POST['captcha_code']) == false && $valid == true) {
		echo "<p><strong>Der eingegebene Sicherheitscode war falsch.</strong></p>";
		$valid = false;
	}

    
	if($valid) {
       
        if ($typ == "schueler") {
            $cmd = "SELECT SVorname, SLogin FROM `Schueler` WHERE SEmail = '".mysql_real_escape_string($_POST["email"])."'";
            $result = mysql_query($cmd);
            $row = mysql_fetch_array($result);
            $username = $row["SLogin"];
            $password = generatePassword();
            $cmd = "UPDATE Schueler SET SPasswort=SHA1(CONCAT('$password', MD5('".$row['SVorname']."'))) WHERE SLogin='$username'";
            mysql_query($cmd);
            $more_credentials = array();
            while($row = mysql_fetch_array($result)) {
                $password2 = generatePassword();
                $cmd = "UPDATE Schueler SET SPasswort=SHA1(CONCAT('$password2', MD5('${row['SVorname']}'))) WHERE SLogin='${row['SLogin']}'";
                mysql_query($cmd);
                $more_credentials[] = "
        Benutzername: ${row['SLogin']}
        Passwort: $password2";
            }
		} elseif ($typ == "lehrer") {
			$cmd = "SELECT LVorname, LLogin FROM `Lehrer` WHERE LEmail = '".mysql_real_escape_string($_POST["email"])."'";
			$result = mysql_query($cmd);
			$row = mysql_fetch_array($result);
			$username = $row["LLogin"];
			$password = generatePassword();
            $cmd = "UPDATE Lehrer SET LPasswort=SHA1(CONCAT('$password', MD5('${row['LVorname']}'))) WHERE LLogin='$username'";
			mysql_query($cmd);
			$more_credentials = array();
			while($row = mysql_fetch_array($result)) {
			    $password2 = generatePassword();
                $cmd = "UPDATE Lehrer SET LPasswort=SHA1(CONCAT('$password2', MD5('${row['LVorname']}'))) WHERE LLogin='${row['LLogin']}'";
            	mysql_query($cmd);
            	$more_credentials[] = "
	   Benutzername: ${row['LLogin']}
	   Passwort: $password2";
            }
		} elseif ($typ == "admin") {
            $cmd = "SELECT AVorname, ALogin FROM `Administratoren` WHERE AEmail = '".mysql_real_escape_string($_POST["email"])."'";
            $result = mysql_query($cmd);
            $row = mysql_fetch_array($result);
            $username = $row["ALogin"];
            $password = generatePassword();
            $cmd = "UPDATE Administratoren SET APasswort=SHA1(CONCAT('$password', MD5('${row['AVorname']}'))) WHERE ALogin='$username'";
            mysql_query($cmd);
            $more_credentials = array();
            while($row = mysql_fetch_array($result)) {
                $password2 = generatePassword();
                $cmd = "UPDATE Administratoren SET APasswort=SHA1(CONCAT('$password2', MD5('${row['AVorname']}'))) WHERE ALogin='${row['ALogin']}'";
                mysql_query($cmd);
                $more_credentials[] = "
        Benutzername: ${row['ALogin']}
        Passwort: $password2";
            }
        }
    }
		$empfaenger = $_POST["email"];

	    $betreff = "Ihre Zugangsdaten für das Elternsprechtagsportal";

	    $header  = "MIME-Version: 1.0\n";
	    $header .= "Content-type: text/plain; charset=utf-8\n";
		$header .= "X-IP: ".$_SERVER["REMOTE_ADDR"]."\n";
		$header .= "X-Timestamp: ".time()."\n";
		$header .= "From: Elternsprechtag ".readConfig("SCHULNAME")." <".readConfig("E-MAIL").">";

	    $nachricht =
"Ihre Zugangsdaten wurden Ihnen geschickt, weil jemand sie angefordert hat. Falls Sie keine Zugangsdaten angefordert haben sollten, ignorieren bzw. löschen Sie bitte einfach diese E-Mail.

	Benutzername: $username
	Passwort: $password

";
	foreach ($more_credentials as $credential) {
		$nachricht .= $credential;
	}

	$nachricht .= 'http://'.$hostname.($path == '/' ? '' : $path)."/ Elternsprechtaganmeldung ".readConfig("SCHULNAME")."\r\n\r\n";
        
    if (@$send = mail($empfaenger, $betreff, $nachricht, $header)) {
        echo "<p><strong>Ihre Zugangsdaten wurden versendet.</strong></p>";
	} else {
	    echo "<p><strong>Ihre Zugangsdaten konnten aufgrund eines technischen Fehlers leider nicht versendet werden.</strong></p>";
	}
    
}


?>
<h2>Zugangsdaten anfordern</h2>
<p>Falls Sie Ihren Benutzernamen und Ihr Passwort vergessen haben, können Sie sich Ihre Zugangsdaten erneut zuschicken lassen.</p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<label for="email">E-Mail-Adresse</label><input type="email" name="email" value="<?php
if($_SERVER["REQUEST_METHOD"] == "POST") { echo strip_tags($_POST["email"]); }
?>" required="required" />
<p><img id="captcha" src="./securimage/securimage_show.php" alt="[CAPTCHA]" width="215" height="80" onclick="document.getElementById('captcha').src = './securimage/securimage_show.php?' + Math.random(); return false" /></p>
<p><label for="captcha_code">Sicherheitscode</label><input type="text" name="captcha_code" size="10" maxlength="6" required="required" /></p>
<input type="submit" value="Zugangsdaten anfordern" />
</form>
<?php
foot();
?>
