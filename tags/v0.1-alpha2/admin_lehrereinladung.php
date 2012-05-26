<?php

require_once('./includes/main.inc.php');
require_once('./includes/admin_auth.inc.php');

head();


if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(!isset($_POST['betreff'])) $_POST['betreff'] = "Elternsprechtag am LMGU";

	$cmd = "SELECT LEmail, LLogin, LPasswort, LName, LVorname FROM Lehrer GROUP BY LEmail";
	$result = mysql_query($cmd);
	$i = 0;
	while($row = mysql_fetch_array($result)) {
		if (filter_var($row["LEmail"], FILTER_VALIDATE_EMAIL)) {
			$lehrername = $row["LName"];
			$mytext = str_replace(	array("{username}","{password}","{lehrername}"),
									array($row["LLogin"], $row["LPasswort"], $lehrername),
									$_POST["mailtext"] );
			//echo "<p><strong>Sende Mail an $row[LEmail]...</strong></p>";
		    $header  = "MIME-Version: 1.0\n";
		    $header .= "Content-type: text/plain; charset=utf-8\n";
			$header .= "From: Elternsprechtag ".readConfig("SCHULNAME")." <".readConfig("E-MAIL").">";
			if (@mail($row["LEmail"], $_POST['betreff'], $mytext, $header))	{
				$i++;
			}
		}
	}
	echo "<p><strong>$i E-Mails wurden versendet.</strong></p>";

	$general_text = mysql_real_escape_string($_POST["mailtext"]);
	$betreff = mysql_real_escape_string($_POST['betreff']);
	$cmd = "INSERT INTO LEinladungen (Zeit, Anzahl, Text, Betreff) VALUES (NOW(), '$i', '$general_text', '$betreff');";
	mysql_query($cmd);
}

?>
<form action="" method="post">
<h3>Bereits gesendete Einladungen</h3>
<table>
<tr>
<th>Datum</th>
<th>Anzahl gesendeter E-Mails</th>
</tr>
<?php
$cmd = "SELECT * FROM (SELECT DATE_FORMAT(Zeit, '%e.%c.%Y') as Tag, Zeit, Anzahl FROM LEinladungen ORDER BY Zeit DESC LIMIT 0, 5) as _t ORDER BY Zeit ASC;";
$result = mysql_query($cmd);
$i = 0;
while($row = mysql_fetch_array($result)) {
	echo "<tr><td>$row[Tag]</td><td>$row[Anzahl]</td></tr>";
	$i++;
}
if ($i == 0) echo '<tr><td colspan="2"><i>Keine Einladung abgesendet.</i></td></tr>';

?>
</table>
<h3>Platzhalter</h3>
<p>Sie können im Einladungstext folgende Platzhalter verwenden. Diese werden beim Absenden durch die Daten des jeweiligen Benutzers ersetzt.</p>
<table>
<tr><th>Platzhalter</th><th>Bedeutung</th></tr>
<tr><td><code>{lehrername}</code></td><td>Nachname des Lehrers</td></tr>
<tr><td><code>{username}</code></td><td>Benutzername zum Anmelden</td></tr>
<tr><td><code>{password}</code></td><td>Passwort zum Anmelden</td></tr>
</table>
<h3>Anmeldedaten an Lehrer versenden</h3>
<p><label>Betreff:</label><input type="text" name="betreff" value="Elternsprechtag am LMGU" /></p>
<textarea name="mailtext" style="width: 600px; height: 380px;">
Sehr geehrter Herr {lehrername},

Der nächste Elternsprechtag ist am <?php echo readConfig("TAG")." von ".readConfig("STARTZEIT")." Uhr bis ".readConfig("ENDZEIT")." Uhr geplant.\n";?>

Bitte wählen Sie sobald wie möglich Ihre Fächer aus. Innerhalb von <?php echo readConfig("FRIST"); ?> Stunden vor dem Elternsprechtag haben Sie die Möglichkeit Ihren Plan mit den Terminen mit den Elternteilen auszudrucken.

Bitte melden Sie sich auf folgender Internetseite an:
  <?php echo 'http://'.$hostname.($path == '/' ? '' : $path)."\n"; ?>

Ihre Zugangsdaten lauten:
* Benutzername: {username}
* Passwort: {password}

Mit freundlichen Grüßen
<?php echo $_SESSION["vorname"].' '.$_SESSION["name"]."\n"; ?>
Elternsprechtagsorganisator 
</textarea><br /><br/>
<input type="button" value="Einladungen absenden" onclick="if(confirm('Sind Sie sich wirklich sicher, dass Sie heute Einladungen mit dem gewählten Text an alle Lehrer verschicken möchten?')) this.form.submit()" />
</form>
<?php

foot();

?>
