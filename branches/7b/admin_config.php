<?php

require_once("./includes/main.inc.php");
require_once("./includes/admin_auth.inc.php");
require_once("./includes/termine.inc.php");
require_once("./includes/frist.inc.php");

function selected($i,$selected) {
	if ( $i == $selected ) {
		return 'selected="selected"';
	} else {
		return "";
	}
}

$eingetragene_termine = eingetragene_termine_berechnen();

$config_errors = '';

if ( $_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST["ok"]) || isset($_POST['starten'])) ) {
	if (readConfig("OFFEN")=="true" || $eingetragene_termine>0) {
		$_POST["gespraechsdauer"] = (string) $gespraechsdauer;
		$_POST["tag"] = readConfig('TAG');
		$_POST["start_hour"] = (string) $startzeit['stunde'];
		$_POST["start_minute"] = (string) $startzeit['minuten'];
		$_POST["end_minute"] = (string) $endzeit['minuten'];
		$_POST["end_hour"]  = (string) $endzeit['stunde'];
	}

	if (!isset($_POST["gespraechsdauer"])) $_POST["gespraechsdauer"] = (string) $gespraechsdauer;
	if (!isset($_POST["tag"])) $_POST["tag"] = readConfig('TAG');
	if (!isset($_POST["start_hour"])) $_POST["start_hour"] = (string) $startzeit['stunde'];
	if (!isset($_POST["start_minute"])) $_POST["start_minute"] = (string) $startzeit['minuten'];
	if (!isset($_POST["end_minute"])) $_POST["end_minute"] = (string) $endzeit['minuten'];
	if (!isset($_POST["end_hour"])) $_POST["end_hour"]  = (string) $endzeit['stunde'];

	$_POST["start_hour"] = str_pad($_POST["start_hour"], 2, '0', STR_PAD_LEFT);
	$_POST["start_minute"] = str_pad($_POST["start_minute"], 2, '0', STR_PAD_LEFT);
	$_POST["end_hour"] = str_pad($_POST["end_hour"], 2, '0', STR_PAD_LEFT);
	$_POST["end_minute"] = str_pad($_POST["end_minute"], 2, '0', STR_PAD_LEFT);

	if($_POST["gespraechsdauer"]>0 && ctype_digit($_POST["gespraechsdauer"])) {}
	else {
		$_POST["gespraechsdauer"] = (string) $gespraechsdauer;
		$config_errors .= "<p><strong>Fehler:</strong> Bitte geben Sie eine gültige Gesprächsdauer ein.</p>";
	}

	$gespraechsdauer_new = $_POST["gespraechsdauer"];
	$endzeit_new['minuten'] = $_POST["end_minute"];
	$endzeit_new['stunde'] = $_POST["end_hour"];
	$startzeit_new['stunde'] = $_POST["start_hour"];
	$startzeit_new['minuten'] = $_POST["start_minute"];
	$anzahl_termine_new = ($endzeit_new['minuten']+($endzeit_new['stunde']-$startzeit_new['stunde'])*60-$startzeit_new['minuten'])/$gespraechsdauer_new;

	if ($anzahl_termine_new > 0) {
		writeConfig("STARTZEIT", $_POST["start_hour"].":".$_POST["start_minute"]);
		writeConfig("ENDZEIT", $_POST["end_hour"].":".$_POST["end_minute"]);
	} else {
		$config_errors .= "<p><strong>Fehler:</strong> Bitte geben Sie eine gültige Start- und Endzeit ein, sodass mindenstens ein Termin möglich wird. Falls Sie keinen Termin ermöglichen möchten, stellen Sie stattdessen bitte die Sperrung ein.</p>";
		// Werte bei Fehler zurücksetzen, damit Pausen nicht aufgrund einer vermeintlichen Änderung gelöscht werden
		$_POST["start_hour"] = str_pad($startzeit['stunde'], 2, '0', STR_PAD_LEFT);
		$_POST["start_minute"] = str_pad($startzeit['minuten'], 2, '0', STR_PAD_LEFT);
		$_POST["end_hour"] = str_pad($endzeit['stunde'], 2, '0', STR_PAD_LEFT);
		$_POST["end_minute"] = str_pad($endzeit['minuten'], 2, '0', STR_PAD_LEFT);
	}

	// Hinweis: Die Gesprächsdauer wird weiter oben überprüft.
	writeConfig("GESPRAECHSDAUER", (int) $_POST["gespraechsdauer"]);

	if ((int) $_POST["frist"] >= 0 && (int) $_POST["frist"] <= 200 && ctype_digit($_POST["frist"])) {
		writeConfig("FRIST", (int) $_POST["frist"]);
	} else {
		$config_errors .= "<p><strong>Fehler:</strong> Bitte geben Sie eine gültige Eintragungsfrist ein, welche mindestens 0 und höchstens 200 Stunden betragen darf.</p>";
	}

	$old_startzeit = str_pad($startzeit['stunde'], 2, '0', STR_PAD_LEFT);
	$old_startzeit .= ":".str_pad($startzeit['minuten'], 2, '0', STR_PAD_LEFT);

	$old_endzeit = str_pad($endzeit['stunde'], 2, '0', STR_PAD_LEFT);
	$old_endzeit .= ":".str_pad($endzeit['minuten'], 2, '0', STR_PAD_LEFT);

	if ($_POST["gespraechsdauer"] != $gespraechsdauer
		|| $_POST["start_hour"].":".$_POST["start_minute"] != $old_startzeit
		|| $_POST["end_hour"].":".$_POST["end_minute"] != $old_endzeit) {
		$row = mysql_fetch_row(mysql_query('SELECT COUNT(*) FROM Pausen'));
		if ($row[0]>0) {
			mysql_query("TRUNCATE TABLE Pausen;");
			$config_errors .= '<p><strong>Hinweis:</strong> Da sie die Gesprächsdauer, die Startzeit oder die Endzeit geändert haben, wurden alle eingestellten Pausen gelöscht. Bitte stellen Sie die gewünschten <a href="admin_pausen.php">Pausen</a>, falls erforderlich, erneut ein. Falls sie keine Pausen einstellen möchten, können sie sofort erneut versuchen, das Portal zu starten.</p>';
		}
	}

	if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		writeConfig("E-MAIL", $_POST["email"]);
	} else {
		$config_errors .= '<p><strong>Bitte geben Sie eine gültige E-Mail-Adresse an.</strong></p>';
	}

	writeConfig("TITEL", $_POST["titel"]);
	$heading = readConfig("TITEL");

	// Normale Felder ohne besondere Ansprüche
	writeConfig("TAG", $_POST["tag"]);
	writeConfig("SCHULNAME", $_POST["schulname"]);
	writeConfig("IMPRESSUM", $_POST["impressum"]);

	// Neue Termindaten verwenden
	$gespraechsdauer = $_POST["gespraechsdauer"];
	$endzeit['minuten'] = $_POST["end_minute"];
	$endzeit['stunde'] = $_POST["end_hour"];
	$startzeit['stunde'] = $_POST["start_hour"];
	$startzeit['minuten'] = $_POST["start_minute"];
	$anzahl_termine = ($endzeit['minuten']+($endzeit['stunde']-$startzeit['stunde'])*60-$startzeit['minuten'])/$gespraechsdauer;

}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loesche_termine'])) {
	if ($_POST['loesche_termine']=="true") {
		$cmd = "TRUNCATE TABLE VSchuelerLehrer";
		$result = mysql_query($cmd);
		$cmd = "TRUNCATE TABLE VTermine";
		$result2 = mysql_query($cmd);
		if(!$result||!$result2) $config_errors .= "<p><strong>Fehler:</strong> Die Datenbank hat das Leeren der Termin-Tabelle nicht zugelassen. Bitte überprüfen Sie die MySQL-Berechtigungen:</p><p><em>".mysql_error()."</em></p>";
		$eingetragene_termine = eingetragene_termine_berechnen();
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['starten'])) {
	if ($config_errors == '') writeConfig("OFFEN", "true");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['beenden'])) {
	if ($_POST['beenden']=="true") writeConfig("OFFEN", "false");
}

// http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css
// http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js
$scripts .= '
  <script type="text/javascript" src="./js/jquery.js"> </script>
  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="./js/jquery-ui.min.js"> </script>';

$scripts .=  "<script>
jQuery(function($){
        $.datepicker.regional['de'] = {clearText: 'löschen', clearStatus: 'aktuelles Datum löschen',
                closeText: 'schließen', closeStatus: 'ohne Änderungen schließen',
                prevText: '&#x3c;zurück', prevStatus: 'letzten Monat zeigen',
                nextText: 'Vor&#x3e;', nextStatus: 'nächsten Monat zeigen',
                currentText: 'heute', currentStatus: '',
                monthNames: ['Januar','Februar','März','April','Mai','Juni',
                'Juli','August','September','Oktober','November','Dezember'],
                monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun',
                'Jul','Aug','Sep','Okt','Nov','Dez'],
                monthStatus: 'anderen Monat anzeigen', yearStatus: 'anderes Jahr anzeigen',
                weekHeader: 'Wo', weekStatus: 'Woche des Monats',
                dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
                dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
                dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
                dayStatus: 'Setze DD als ersten Wochentag', dateStatus: 'Wähle D, M d',
                dateFormat: 'dd.mm.yy', firstDay: 1, 
                initStatus: 'Wähle ein Datum', isRTL: false};
        $.datepicker.setDefaults($.datepicker.regional['de']);
});

  $(document).ready(function() {
    $('#tag').datepicker();
  });
  </script>

  <style>
#ui-datepicker-div {
font-size: 62.5%;
}
  </style>
";

$scripts .= '<script src="js/tinymce/jscripts/tiny_mce/tiny_mce.js" type="text/javascript"></script>
<script type="text/javascript">
tinyMCE.init({
        mode : "textareas",
        theme : "simple"   //(n.b. no trailing comma, this will be critical as you experiment later)
});
</script>';

head();

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<h3>Elternsprechtag</h3>
<?php

echo $config_errors;

if (readConfig("OFFEN")=="true" || $eingetragene_termine>0) {
	$disabled = ' disabled="disabled"';
	if ($eingetragene_termine>0 && readConfig("OFFEN")!="true")
		echo "<p><strong>Hinweis:</strong> Um einen neuen Elternsprechtag zu starten, löschen Sie bitte zuerst alle alten Termine des letzten Elternsprechtags.</p>";
}
else $disabled = '';
?>
<p><label>Status</label>
<?php
	if (readConfig("OFFEN")!="true")
		echo '<span style="color:red;">Portal gesperrt</span>';
	elseif (eintragungsfrist())
		echo '<span style="color:grey;">Termine stehen fest</span>';
	else
		echo '<span style="color:green;">Anmeldung möglich</span>';
?>
</p>

<p>
<label>Zeitrahmen</label>

	<?php echo '<input type="text" name="tag" id="tag" value="'.htmlspecialchars(readConfig('TAG'), ENT_QUOTES, "UTF-8").'"'.$disabled.' />'; ?>


    <select name="start_hour"<?php echo $disabled; ?>>
<?php
for ($i = 0; $i<24; $i++) {
	if ($i<10) $number = "0".$i; else $number = $i;
	echo '<option value="'.$number.'" '.selected($i,$startzeit['stunde']).'>'.$number.'</option>';
}
?>
	</select> :
	<select name="start_minute"<?php echo $disabled; ?>>
<?php
for ($i = 0; $i<60; $i++) {
	if ($i<10) $number = "0".$i; else $number = $i;
	echo '<option value="'.$number.'" '.selected($i,$startzeit['minuten']).'>'.$number.'</option>';
}
?>
	</select>
–

    <select name="end_hour"<?php echo $disabled; ?>>
<?php
for ($i = 0; $i<24; $i++) {
	if ($i<10) $number = "0".$i; else $number = $i;
	echo '<option value="'.$number.'" '.selected($i,$endzeit['stunde']).'>'.$number.'</option>';
}
?>
	</select> :
	<select name="end_minute"<?php echo $disabled; ?>>
<?php
for ($i = 0; $i<60; $i++) {
	if ($i<10) $number = "0".$i; else $number = $i;
	echo '<option value="'.$number.'" '.selected($i,$endzeit['minuten']).'>'.$number.'</option>';
}
?>
	</select> Uhr
</p>
<p>
	<label for="gespraechsdauer">Gesprächsdauer</label>
	<input type="number" size="1" maxlength="2" name="gespraechsdauer"
		   value="<?php echo $gespraechsdauer; ?>"<?php echo $disabled; ?> />
	Minuten
</p>

<?php if (! (readConfig("OFFEN")!="true" && $eingetragene_termine==0)) { ?>
<p><label>Termine</label>
<?php
echo $eingetragene_termine;
?>

<input name="loesche_termine" id="loesche_termine" value="false" type="hidden" />
<?php
if (readConfig("OFFEN")!="true") {
?>
<input type="button" value="Termine löschen"
	   onclick="if (confirm('Sind Sie sicher, dass Sie alle Termine unwiderruflich löschen möchten?'))
				{ document.getElementById('loesche_termine').value='true'; this.form.submit(); }" />
<?php } ?>
</p>
<?php } ?>

<?php
if (readConfig("OFFEN")=="true") {
	echo '<input name="beenden" id="beenden" value="false" type="hidden" />';
	echo '<input type="button" value="Portal sperren"
				 onclick="if (confirm(\'Sind Sie sicher, dass Sie das Portal sperren möchten? Bitte sperren Sie das Portal erst, nachdem der Elternsprechtag stattgefunden hat. Eltern und Lehrer würden ansonsten ihre Pläne nicht mehr ausdrucken können.\'))
						  { document.getElementById(\'beenden\').value=\'true\'; this.form.submit(); }" />';
} else {
	if ($eingetragene_termine==0)
		echo '<input type="submit" value="Eintragungsphase starten" name="starten" /> ';
    else
		echo '<input type="submit" value="Wiedereröffnen" name="starten" /> ';
}
?>

<h3>Weitere Einstellungen</h3>

<p>
	<label for="frist">Eintragungsfrist</label>
	<input type="number" name="frist" size="1" maxlength="3" value="<?php echo readConfig("FRIST"); ?>" />
	Stunden vorher
</p>
<p>
	<label for="email">Kontakt-E-Mail</label>
	<?php echo '<input type="email" name="email" value="'.htmlspecialchars(readConfig("E-MAIL"), ENT_QUOTES, "UTF-8").'" />'; ?>
</p>
<p>
	<label for="schulname">Schulname</label>
	<?php echo '<input type="text" name="schulname" value="'.htmlspecialchars(readConfig("SCHULNAME"), ENT_QUOTES, "UTF-8").'" />'; ?>
</p>
<p>
	<label for="titel">Website-Titel</label>
	<?php echo '<input type="text" name="titel" value="'.htmlspecialchars(readConfig("TITEL"), ENT_QUOTES, "UTF-8").'" />'; ?>
</p>
<p>
	<label for="impressum">Impressum</label>
	<?php echo '<textarea name="impressum">'.htmlspecialchars(readConfig("IMPRESSUM"), ENT_QUOTES, "UTF-8").'</textarea>'; ?>
</p>
<input type="submit" name="abbrechen" value="Abbrechen" />
<input type="submit" name="ok" value="Übernehmen" />
</form>

<?php
foot();

?>
