<?php

$config = fopen('includes/config.conf', 'r');
$inhalt = fread($config, filesize('includes/config.conf'));
$startzeitR = substr($inhalt, strpos($inhalt, "STARTZEIT")+1+9,5);// im format hh:mm
$endzeitR =substr($inhalt, strpos($inhalt, "ENDZEIT")+1+7,5);// im format hh:mm

$gespraechsdauer = (int)substr($inhalt, strpos($inhalt, "GESPRAECHSDAUER")+1+15,2);// im format mm

$startzeit['stunde'] = (int)substr($startzeitR, 0, 2);
$startzeit['minuten'] = (int)substr($startzeitR, 3, 2);

$endzeit['stunde'] = (int)substr($endzeitR, 0, 2);
$endzeit['minuten'] = (int)substr($endzeitR, 3, 2);

$anzahl_termine = ($endzeit['minuten']+($endzeit['stunde']-$startzeit['stunde'])*60-$startzeit['minuten'])/$gespraechsdauer;

function zeit($zeitTermin, $lehrer, $id, $sid) {
	$cmd = "SELECT DISTINCT Zeit 
			FROM VTermine
			WHERE schuelerID=".$sid." OR lehrerID=".$lehrer." ORDER BY Zeit";
	$result = mysql_query($cmd);
	$belegt = array();
	while($row = mysql_fetch_array($result)) {
		$belegt[] = substr($row["Zeit"],0,5);
	}
	
	global $startzeit, $anzahl_termine, $akt_minute, $akt_stunde, $gespraechsdauer;
	
	$o = '<select class="zeit" id="zeit'.$id.'">';
	
	$akt_minute = $startzeit['minuten'];
	$akt_stunde = $startzeit['stunde'];
	
	for ($i = 0; $i<$anzahl_termine; $i++) {
		$selected='';
		$akt_minute=(strlen((string)$akt_minute)==1?'0':'').$akt_minute;
		$zeit=$akt_stunde.":".$akt_minute;
		if ($zeit==substr($zeitTermin,0,5)) $selected=' selected="selected"';
		
		if (!in_array($zeit, $belegt) || $zeit==substr($zeitTermin,0,5))
			$o.='<option'.$selected.'>'.$zeit.'</option>';
		
		$akt_minute+=$gespraechsdauer;
		if ($akt_minute >= 60) {
			$akt_stunde+=1;
			$akt_minute-=60;
		}
	}
	
	$o .= "</select>";
	return $o;
}

function table($sid) {
		
	echo "<tr><th>Zeit</th><th>Lehrer</th><th>Raum</th><th></th></tr>\n";
	$cmd = "SELECT LName, LVorname, id, Zeit, Raum, Tid
			FROM Lehrer, VTermine
			WHERE schuelerID=".$sid." AND lehrerID = Lehrer.id ORDER BY Zeit";
	$result = mysql_query($cmd);
	while($row = mysql_fetch_array($result)) {
		$lehrer = $row["id"];
		$termin_id = 0;
		echo "<tr><td>".zeit($row["Zeit"], $lehrer, $row['Tid'], $sid)."</td>
			<td>$row[LName], $row[LVorname]</td>
			<td>$row[Raum]</td>
			<td><input type=\"button\" class=\"delete\" id=\"termin".$row['Tid']."\" value=\"X\" /></td></tr>\n";
	}
	
}


function lehrer($sid) {

	$cmd = "SELECT id FROM Lehrer, VSchuelerLehrer WHERE id=lehrerID AND schuelerID=$sid";
	$result = mysql_query($cmd);
	$lehrerIDs=array();
	while ($row = mysql_fetch_array($result)) {
		$lehrerIDs[] = $row['id'];
	}
	
	foreach($lehrerIDs as $lehrer)
	{
		$result2 = mysql_query("SELECT * FROM VTermine WHERE schuelerID=".$sid." AND lehrerID=".$lehrer); 
		if (!mysql_fetch_array($result2)) {	
			$result3 = mysql_query("SELECT LName, LVorname FROM Lehrer WHERE id=".$lehrer); 
			$name = mysql_fetch_array($result3);	
			echo '<option value="'.$lehrer.'">'.$name['LName'].', '.$name['LVorname'].'</option>';
		}
	}
	echo '<option value="other">Weitere Lehrer</option>';
}

?>
