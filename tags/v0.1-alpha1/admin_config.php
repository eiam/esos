<?php

$page = "admin_config";

require("./includes/head.inc.php");
require("./includes/admin_auth.inc.php");
require("./includes/termine.inc.php");

function selected($i,$selected) {
	if ( $i == $selected ) {
		return 'selected="selected"';
	} else {
		return "";
	}
}

?>
<p><strong>Achtung:</strong> Ihre Änderungen werden erst übernommen, wenn Sie auf „<i>Übernehmen</i>“ klicken.</p>
<form action="" method="post">
<table>
	<tr>
		<th>Elternsprechtag</th>
		<th>Anfangszeit</th>
		<th>Endzeit</th>
</tr>
<tr>
    <td>1. Januar 1970</td>
	<td>
    <select name="start_hour">
<?php
for ($i = 0; $i<24; $i++) {
	echo '<option value="'.$i.'" '.selected($i,$startzeit['stunde']).'>'.$i.'</option>';
}
?>
	</select> :
	<select name="start_minute">
<?php
for ($i = 0; $i<60; $i++) {
	echo '<option value="'.$i.'" '.selected($i,$startzeit['minuten']).'>'.$i.'</option>';
}
?>
	</select> Uhr
</td>
<td>
    <select name="end_hour">
<?php
for ($i = 0; $i<24; $i++) {
	echo '<option value="'.$i.'" '.selected($i,$endzeit['stunde']).'>'.$i.'</option>';
}
?>
	</select> :
	<select name="end_minute">
<?php
for ($i = 0; $i<60; $i++) {
	echo '<option value="'.$i.'" '.selected($i,$endzeit['minuten']).'>'.$i.'</option>';
}
?>
	</select> Uhr
</td>
<td>
<input type="submit" value="-" class="remove" onclick="confirm('Sind Sie sicher, dass Sie diesen Elternsprechtag absagen möchten? Alle Termine zu diesem Elternsprechtag werden unwiderruflich gelöscht werden.');" />
</td>
</tr>
<tr>
<td><input type="date" size="6" maxlength="10" /></td>
<td colspan="3"><input type="submit" value="Elternsprechtag hinzufügen" class="add_entry" /></td>
</tr>
</table>
<p>
	<label>Gesprächsdauer</label>
	<input type="number" size="1" maxlength="2" value="<?php echo $gespraechsdauer; ?>" />
	Minuten
</p>
<p>
	<label>Eintragungsfrist</label>
	<input type="number" size="1" maxlength="2" value="12" />
	Stunden vorher
</p>
<input type="submit" value="Abbrechen" />
<input type="submit" value="Übernehmen" />
</form>
<?php
require("./includes/foot.inc.php");

?>
