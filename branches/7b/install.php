<?php

require_once("includes/sqlInit.php");

function generatePassword ($length = 8) {
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

	$install=TRUE;

	require_once("includes/main.inc.php");

	head();

	// Prüfen, ob bereits eingerichtet
	if(file_exists('includes/mysql.conf')) {
		$configfile = file('includes/mysql.conf');
		foreach ($configfile as $configline) {
			$configline = str_replace("\n", "", $configline);
			$line = explode(" ", $configline);
			if($line[0] == "ACTIVE")
				$active = $line[1];
		}
		if($active=="true") {
			header('HTTP/1.0 403 Forbidden');
			echo "<p>ESOS wurde bereits eingerichtet.</p>";
			foot();
			exit();
		}
	}

	$gen_pw=generatePassword(20);
	
	if(!is_writable('includes/mysql.conf')) {
		echo '<p><strong>Bitte richten Sie Schreibrechte für die Datei „includes/mysql.conf“ ein, um ESOS einrichten zu können!</strong></p>';
		foot(); exit();
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['host']) && isset($_POST['rootuser']) &&
	   isset($_POST['rootpwd']) && isset($_POST['dbname']) && isset($_POST['dbuser']) && isset($_POST['dbpwd'])
	   && isset($_POST['email']) && isset($_POST['vorname']) && isset($_POST['name']) && isset($_POST['pwd'])
	   && is_writable('includes/mysql.conf')) {
		@$connection=mysql_connect($_POST['host'], $_POST['rootuser'], $_POST['rootpwd']);
		if(!$connection) { echo "<p><strong>Fehler:</strong> Bitte überprüfen Sie Ihre MySQL-Server-Daten!</p>"; }
		if ($connection) {
			dbInit($_POST['dbname'], $_POST['dbuser'], $_POST['dbpwd']);
			$confFile = fopen('includes/mysql.conf', 'w');
			$config="HOST ".$_POST['host']."
UID ".$_POST['dbuser']."
PWD ".$_POST['dbpwd']."
DB ".$_POST['dbname']."
ACTIVE true";
			fwrite($confFile, $config);
			fclose($confFile);
			require_once('includes/mysql.inc.php');
			tableInit($_POST['dbname']);
			$query = "INSERT INTO Administratoren VALUES (
					NULL , '".$_POST['admin']."', '".$_POST['pwd']."', '".$_POST['vorname']."', '".$_POST['name']."', '".$_POST['email']."'
					)";
			mysql_query($query);
		}
	}	
?>
	<p>Bitte füllen Sie das folgende Formular sorgfältig aus, um ESOS einzurichten.</p>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<h3>MySQL-Server</h3>
		<p><label for="host">Serveradresse</label><input type="text" name="host" value="localhost" required="required" /></p>
		<p><label for="rootuser">Administrator</label><input type="text" name="rootuser" value="root" required="required" /></p>
		<p><label for="rootpwd">Passwort des Administrators</label><input type="password" name="rootpwd" required="required" /></p>

		<h3>Datenbank</h3>
		<p>ESOS wird auf dem MySQL-Server eine eigene Datenbank mit einem eigenen Benutzer anlegen. Diese Einstellungen brauchen Sie normalerweise nicht zu verändern.</p>
		<p><label for="dbuser">Benutzername</label><input type="text" name="dbuser" value="esos" required="required" /></p>
		<p><label for="dbpwd">Passwort</label><input type="text" name="dbpwd" value="<?php echo $gen_pw;?>" required="required" /></p>
		<p><label for="dbname">Datenbankname</label><input type="text" name="dbname" value="esos" required="required" /></p>

		<h3>ESOS-Administrator</h3>
		<p>Um ESOS zu verwalten wird ein Administrator-Konto benötigt.</p>
		<p><label for="admin">Benutzername</label><input type="text" name="admin" value="admin" required="required" /></p>
		<p><label for="email">E-Mail</label><input type="email" name="email" value="" required="required" /></p>
		<p><label for="vorname">Vorname</label><input type="text" name="vorname" required="required" /></p>
		<p><label for="name">Nachname</label><input type="text" name="name" required="required" /></p>
		<p><label for="pwd">Passwort</label><input type="password" name="pwd" required="required" /></p>
		<p><input type="submit" value="ESOS einrichten" /></p>
	</form>
<?
	foot();
?>
