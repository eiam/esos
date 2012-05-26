<?php
	session_start();
	session_destroy();

	$page = "login";

	require("includes/head.inc.php");	

	require("includes/mysql.inc.php");
	$message = "";

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$login = mysql_real_escape_string($_POST["login"]);
		$hash = md5($_POST["password"]);

		$cmd = "SELECT id, SName, SVorname FROM Schueler WHERE SLogin = '$login' AND SPasswort = '$hash'";
		$result = mysql_query($cmd);
		if($row = mysql_fetch_array($result)) {
			$_SESSION["eltern"] = true;
			$_SESSION["login"] = $login;

			$ID = $row["id"];

			$_SESSION["schueler"] = array();
			$query = mysql_query("SELECT id, SVorname, SName, SKlasse, SEmail, SLogin FROM VGeschwister, Schueler WHERE (schuelerID1=$ID AND id=schuelerID2) OR (schuelerID2=$ID AND id=schuelerID1) OR id=$ID GROUP BY id ORDER BY SVorname");
			while ($row = mysql_fetch_array($query)) {
				$_SESSION["schueler"][] = array(
					id => $row["id"],
					vorname => $row["SVorname"],
					name => $row["SName"],
					klasse => $row["SKlasse"]);
 			}

			$_SESSION["id"] = $_SESSION["schueler"][0]["id"];

			Header('Location: http://'.$hostname.($path == '/' ? '' : $path)."/eltern_lehrer.php");
			exit;
		}

		$cmd = "SELECT id, LName, LVorname FROM Lehrer WHERE LLogin = '$login' AND LPasswort = '$hash'";
		$result = mysql_query($cmd);
		if($row = mysql_fetch_array($result)) {
			$_SESSION["lehrer"] = true;
			$_SESSION["id"] = $row["id"];
			$_SESSION["name"] = $row["LName"];
			$_SESSION["vorname"] = $row["LVorname"];
			$_SESSION["login"] = $login;
			Header('Location: http://'.$hostname.($path == '/' ? '' : $path)."/lehrer_ausgabe.php"); 
			exit;
		} elseif ($login == "admin" && $hash == "21232f297a57a5a743894a0e4a801fc3") {
			// Admin Login mit Kennung "admin", Passwort "admin"
			$_SESSION["admin"] = true;
			$_SESSION["id"] = -1;
			$_SESSION["name"] = "Admin";
			$_SESSION["vorname"] = "Jack";
			$_SESSION["login"] = $login;
			Header('Location: http://'.$hostname.($path == '/' ? '' : $path)."/"); 
			exit;
		} else {
			$message = "<h1>Anmeldung fehlgeschlagen.</h1>\n";
			session_destroy();
		}
	}
?>
		<?php echo $message; ?>
		<h2>Login</h2>
		<p>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<label for="login">Benutzername:</label> <input type="text" name="login" /><br />
			<label for="password">Passwort:</label> <input type="password" name="password" /><br />
			<input type="submit" value="Anmelden" />
		</form>
		</p>
		<p><a href="./passwort-vergessen.php">Zugangsdaten vergessen</a></p>
<?php require("includes/foot.inc.php"); ?>
