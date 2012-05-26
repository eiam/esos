<?php
	$page = "eltern_benutzer";

    require('./includes/head.inc.php');
	require('./includes/mysql.inc.php');
	require('./includes/eltern_auth.inc.php');
	$ID = $_SESSION["id"];
?>
<h2>Meine Kinder</h2>
<table>
<tr>
	<th>Vorname</th><th>Name</th><th>Klasse</th><th>Eltern-E-Mail-Adresse</th><th>Benutzername</th>
</tr>
<?php
 $query = mysql_query("SELECT SVorname, SName, SKlasse, SEmail, SLogin FROM VGeschwister, Schueler WHERE (schuelerID1=$ID AND id=schuelerID2) OR (schuelerID2=$ID AND id=schuelerID1) OR id=$ID GROUP BY id ORDER BY SVorname");
 while ($row = mysql_fetch_array($query)) {
	echo "<tr><td>$row[SVorname]</td><td>$row[SName]</td><td>$row[SKlasse]</td><td>$row[SEmail]</td>
		  <td>$row[SLogin]</td></tr>\n";
 }
?>
</table>
<?php
	function zusammenfuegen($id1, $id2) {
		$cmd = "SELECT * FROM VGeschwister WHERE schuelerID1='".$id1."' AND schuelerID2='".$id2."'";	
		if ($result = mysql_query($cmd)) {
			if(!$row = mysql_fetch_array($result)) {		
				$cmd = "INSERT INTO VGeschwister(schuelerID1, schuelerID2) VALUES ('$id1', '$id2'), ('$id2', '$id1');";
				if ($result = mysql_query($cmd)) {
					echo "<p>Die Benutzer ".$_SESSION['login']." und ".$_POST["login"]." sind verbunden worden.";
					exit;
				}
			}
			else echo "<p>Diese Benutzer sind bereits verbunden.</p>";
		}
	}
	
	echo "<h2>Benutzer verbinden</h2>";
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$login = mysql_real_escape_string($_POST["login"]);
		$hash = md5($_POST["passwort"]);
		$cmd = "SELECT id FROM Schueler WHERE SLogin = '$login' AND SPasswort = '$hash'";
		$result = mysql_query($cmd);
		if($row = mysql_fetch_array($result)) {
			if ($row['id']!= $ID) zusammenfuegen($ID, $row['id']);
			else echo '<p>Sie sind mit diesem Benutzer angemeldet!</p>';
		}
		else echo "<p>Dieser Benutzer existiert nicht oder das Passwort wurde falsch angegeben.</p>";
	}
?>
<p>
<form method="post" action="">
	<label for="login">Benutzername: </label><input type="text" name="login" /><br />
	<label for="password">Passwort: </label><input type="password" name="passwort" /><br />
	<input type="submit" value="Mit Benutzer verbinden" />
</form>
</p>

<?php require('./includes/foot.inc.php'); ?>
