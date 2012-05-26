<?php

require_once("./includes/main.inc.php");
require_once("./includes/admin_auth.inc.php");
require_once("./includes/termine.inc.php");

head();

$eingetragene_termine = eingetragene_termine_berechnen();

?>
<h2>Datenbanken</h2>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload'])) {
	if (readConfig("OFFEN")=="true" || $eingetragene_termine>0) {
		echo '<p><strong>Fehler:</strong> Das Hochladen ist nicht erlaubt, wenn das Portal offen ist oder noch Termine eintragen sind.</p>';
	} else {
		echo "<p><strong>Fehler:</strong> Hochladen fehlgeschlagen. Funktion noch nicht implementiert.</p>";
	}
}
?>
<?php
$c = readConfig("ImportDatum");
if ($c!="") {
echo '
<p>
<label>Letzter Import</label>
<span class="output">'.$c.'</span>
</p>
';
}
?>
<p>Die neuen Datenbanken werden die alten nach dem Hochladen ersetzen. Passwörter von Benutzern, die sowohl in der alten als auch in der neuen Datenbank existieren, bleiben dabei erhalten.</p>
<?php
$disabled = '';
if (readConfig("OFFEN")=="true" || $eingetragene_termine>0) {
 	echo '<p><strong>Bitte sperren Sie zuerst das Portal auf der <a href="admin_config.php">Konfigurationsseite</a> und löschen dort auch alle alten Termine, um danach eine neue Datenbank einzuspielen oder eine alte Datenbank löschen zu können.</strong></p>';
	$disabled = ' disabled="disabled"';
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<h3>Schülerdatenbank hochladen</h3>
<p><input name="schuelerdb" type="file"<?php echo $disabled; ?> /></p>
<input type="hidden" name="upload" value="true" />
<p><input type="submit" value="Hochladen"<?php echo $disabled; ?> /> (<strong>Achtung!</strong> Noch nicht implementiert!)</p>
</form>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<h3>Lehrerdatenbank hochladen</h3>
<p><input name="lehrerdb" type="file"<?php echo $disabled; ?> /></p>
<input type="hidden" name="upload" value="true" />
<p><input type="submit" value="Hochladen"<?php echo $disabled; ?> /> (<strong>Achtung!</strong> Noch nicht implementiert!)</p>
</form>

<h3>Datenbanken leeren</h3>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['schueler_loeschen'])) {
	$cmd = 'TRUNCATE TABLE VSchuelerLehrer';
	$result = mysql_query($cmd);
	$cmd = 'TRUNCATE TABLE Schueler';
	mysql_query($cmd);

}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lehrer_loeschen'])) {
	$cmd = 'TRUNCATE TABLE VSchuelerLehrer';
	$result = mysql_query($cmd);
	$cmd = 'TRUNCATE TABLE Lehrer';
	mysql_query($cmd);
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p><label>Schüleranzahl</label> <?php $result = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM Schueler")); echo $result[0]; if($result[0]==0) { $sdisabled = ' disabled="disabled"'; } else { $sdisabled = $disabled; } ?> <input type="hidden" name="schueler_loeschen" value="true" /> <input type="button" value="Schülerdatenbank löschen"<?php echo $sdisabled; ?> onclick="if(confirm('Sind Sie wirklich sicher, dass Sie alle Schülereinträge aus der Datenbank entfernen möchten?')) this.form.submit();" /></p>
</form>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p><label>Lehreranzahl</label> <?php $result = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM Lehrer")); echo $result[0]; if($result[0]==0) { $ldisabled = ' disabled="disabled"'; } else { $ldisabled = $disabled; } ?> <input type="hidden" name="lehrer_loeschen" value="true" /> <input type="button" value="Lehrerdatenbank löschen"<?php echo $ldisabled; ?> onclick="if(confirm('Sind Sie wirklich sicher, dass Sie alle Lehrereinträge aus der Datenbank entfernen möchten?')) this.form.submit();" /></p>
</form>
<?php

foot();

?>
