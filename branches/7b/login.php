<?php
	session_start();
	session_destroy();

	require_once("includes/main.inc.php");
	require_once("includes/mysql.inc.php");
	require_once("includes/benutzer.inc.php");
	require_once("includes/config.inc.php");

	head();

	$message = "";

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$login = mysql_real_escape_string($_POST["login"]);
		
		$cmd = "SELECT SVorname FROM Schueler WHERE SLogin = '$login'";
		$result = mysql_query($cmd);
		if ($row = mysql_fetch_array($result)) {
			$salt = md5($row["SVorname"]);
			$password = sha1($_POST["password"].$salt);
			$cmd = "SELECT id, SName, SVorname FROM Schueler WHERE SLogin = '$login' AND SPasswort = '$password'";
			$result = mysql_query($cmd);
			if(($row = mysql_fetch_array($result)) && readConfig("OFFEN") == "true") {
				$_SESSION["eltern"] = true;
				$_SESSION["id"] = $row["id"];
				$_SESSION["vorname"] = $row["SVorname"];
				$_SESSION["name"] = $row["SName"];
				$_SESSION["login"] = $login;
				Header('Location: http://'.$hostname.($path == '/' ? '' : $path)."/eltern_start.php");
				exit;
			}
		}

		$cmd = "SELECT LVorname FROM Lehrer WHERE LLogin = '$login'";
		$result = mysql_query($cmd);
		if ($row = mysql_fetch_array($result)) {
			$salt = md5($row["LVorname"]);
			$password = sha1($_POST["password"].$salt);
			$cmd = "SELECT id, LName, LVorname FROM Lehrer WHERE LLogin = '$login' AND LPasswort = '$password'";
			$result = mysql_query($cmd);
			if( ($row = mysql_fetch_array($result)) && readConfig("OFFEN") == "true") {
				$_SESSION["lehrer"] = true;
				$_SESSION["id"] = $row["id"];
				$_SESSION["name"] = $row["LName"];
				$_SESSION["vorname"] = $row["LVorname"];
				$_SESSION["login"] = $login;
				Header('Location: http://'.$hostname.($path == '/' ? '' : $path)."/lehrer_ausgabe.php"); 
				exit;
			}
		}

		$cmd = "SELECT AVorname FROM Administratoren WHERE ALogin = '$login'";
		$result = mysql_query($cmd);
		if ($row = mysql_fetch_array($result)) {
			$salt = md5($row["AVorname"]);
			$password = sha1($_POST["password"].$salt);
			$cmd = "SELECT id, AName, AVorname FROM Administratoren WHERE ALogin = '$login' AND APasswort = '$password'";
			$result = mysql_query($cmd);
			if ($row = mysql_fetch_array($result)) {
				$_SESSION["admin"] = true;
				$_SESSION["id"] = $row["id"];
				$_SESSION["name"] = $row["AName"];
				$_SESSION["vorname"] = $row["AVorname"];
				$_SESSION["login"] = $login;
				Header('Location: http://'.$hostname.($path == '/' ? '' : $path)."/admin_config.php"); 
				exit;
			}
		}

		$message = "<h1>Anmeldung fehlgeschlagen.</h1>\n";
		session_destroy();
	}
?>
		<?php echo $message; ?>
		<h2>Login</h2>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<p>
			<label for="login">Benutzername:</label> <input type="text" name="login" id="login" /><br />
			<label for="password">Passwort:</label> <input type="password" name="password" id="password" /><br />
			<input type="submit" value="Anmelden" />
			</p>
		</form>
		<p><a href="./passwort-vergessen.php">Zugangsdaten vergessen</a></p>
<?php foot(); ?>
