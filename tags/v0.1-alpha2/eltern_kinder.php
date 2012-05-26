<?php

    require('./includes/main.inc.php');
	require_once('./includes/mysql.inc.php');
	require_once('./includes/eltern_auth.inc.php');
	require_once("./includes/benutzer.inc.php");

	$ID = $_SESSION["id"];

	$add_child_message = '';
	$bereits_verbunden = "<p>Diese Benutzer sind bereits verbunden.</p>";

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(!empty($_POST['connect'])) {
			$login = mysql_real_escape_string($_POST["login"]);
			$hash = mysql_real_escape_string($_POST["passwort"]);
			$cmd = "SELECT id FROM Schueler WHERE SLogin = '$login' AND SPasswort = '$hash'";
			$result = mysql_query($cmd);
			if($row = mysql_fetch_array($result)) {
				if ($row['id']!= $ID) {
					zusammenfuegen($ID, $row['id']);
				}
				else $add_child_message = $bereits_verbunden;
			}
			else $add_child_message = "<p>Dieser Benutzer existiert nicht oder das Passwort wurde falsch angegeben.</p>";
		} elseif (!empty($_POST['loeschen']) && count(geschwister($_SESSION["id"]))>1) {
			// mysql_real_escape_string() ist nötig, um das Einschleusen von SQL-Befehlen zu verhindern. 
			$kindID = mysql_real_escape_string($_POST['kindID']);

			// Session-ID wechseln, damit man sich selber loeschen kann ohne zu einer anderen Kinderliste zu wechseln
			if ($kindID == $_SESSION["id"]) {
				$cmd  = "SELECT id, SLogin, SName, SVorname FROM Schueler WHERE elternID = '".ElternIDdesKindes($_SESSION["id"])."' AND id != '$_SESSION[id]' ORDER BY SVorname";
				$result = mysql_query($cmd);
				if ($row = mysql_fetch_array($result)) {
					$ID = $_SESSION["id"] = $row["id"];
					$_SESSION["login"] = $row["SLogin"];
					$_SESSION["vorname"] = $row["SVorname"];
					$_SESSION["name"] = $row["SName"];
				}
			}

			// Hier wird aus Sicherheitsgründen überprüft,
			// ob $_POST["kindID"] tatsächlich ein Geschwister von $_SESSION["id"] ist.
			$cmd  = "SELECT id, elternID FROM Schueler WHERE id = '$kindID'";
			$cmd .= " AND elternID = (SELECT elternID FROM Schueler WHERE id = '$_SESSION[id]')";
			$result = mysql_query($cmd);
			if ($row = mysql_fetch_array($result)) {
				// Eigentlicher Löschvorgang
		        $cmd = "UPDATE Schueler SET elternID = UUID() WHERE id = '".$kindID."'";
		        mysql_query($cmd);
			}
		}
	}

	head();
?>
<h2>Meine Kinder</h2>
<table>
<tr>
	<th>Vorname</th><th>Name</th><th>Klasse</th><th>Benutzername</th>
</tr>
<?php
 $schuelerListe = geschwister($_SESSION["id"]);
 foreach ($schuelerListe as $row) {
	echo "<tr><td>$row[SVorname]</td><td>$row[SName]</td><td>$row[SKlasse]</td>
		  <td>$row[SLogin]</td>";
	if (count($schuelerListe)>1) {
		echo '<td><form method="post" action="">
<input type="hidden" name="kindID" value="'.$row["id"].'" />
<input type="hidden" name="loeschen" value="true" />
<input type="image" src="design/trash.png" width="16" height="16" title="Benutzerverknüpfung aufheben" /></form></td>';
	} else {
		echo "<td></td>";
	}
	echo "</tr>\n";
 }
?>
</table>
<?php
	function zusammenfuegen($id1, $id2) {
		global $add_child_message, $bereits_verbunden;
		$cmd  = "SELECT * FROM Schueler WHERE id='$id1'";
		$cmd .= " AND elternID = (SELECT elternID FROM Schueler WHERE id='$id2')";
		if ($result = mysql_query($cmd)) {
			if(!$row = mysql_fetch_array($result)) {

				$elternID = ElternIDdesKindes($id1);
				$elternID2 = ElternIDdesKindes($id2);

				// Setze bei allen Geschwistern von Schueler 2 inkl. Schueler 2 selber die elternID von Schueler 1
				$cmd  = "UPDATE Schueler SET elternID = '$elternID' WHERE elternID = '$elternID2'";

				if ($result = mysql_query($cmd)) {
					$add_child_message = "<p>Die Benutzer ".$_SESSION['login']." und ".$_POST["login"]." sind verbunden worden.</p>";
				}
			}
			else $add_child_message = $bereits_verbunden;
		}
	}
	
	echo "<h2>Kind hinzufügen</h2>";
	echo $add_child_message;
?>
<p>
<form method="post" action="">
	<p><label for="login">Benutzername: </label><input type="text" name="login" /></p>
	<p><label for="password">Passwort: </label><input type="password" name="passwort" autocomplete="off" /></p>
	<p><input type="submit" name="connect" value="Mit Benutzer verbinden" /></p>
</form>
</p>

<?php foot(); ?>
