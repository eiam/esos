<?php

require("./includes/main.inc.php");
require("./includes/admin_auth.inc.php");
require_once("./includes/termine.inc.php");

head();

 $cmd  = "SELECT COUNT(*) FROM VTermine";
 $cmd .= " WHERE schuelerID in (SELECT id as schuelerID FROM Schueler)";
 $cmd .= " AND lehrerID in (SELECT id as lehrerID FROM Lehrer);";
 $row = mysql_fetch_row(mysql_query($cmd));
 $belegte_termine = $row[0];

 $cmd = "SELECT COUNT(*) FROM Lehrer;";
 $row = mysql_fetch_row(mysql_query($cmd));
 $anzahl_lehrer = $row[0];

 $cmd = "SELECT COUNT(*) FROM Schueler WHERE id IN (SELECT DISTINCT schuelerID FROM VTermine);";
 $row = mysql_fetch_row(mysql_query($cmd));
 $anzahl_schueler = $row[0];

 $cmd = "SELECT COUNT(*) FROM Pausen;";
 $row = mysql_fetch_row(mysql_query($cmd));
 $anzahl_termine -= $row[0];

 $alle_termine = $anzahl_termine*$anzahl_lehrer;

 $freie_termine = $alle_termine-$belegte_termine;

?>
<h2>Statistik</h2>
<p>
<label>Belegung</label>
<span class="output">
<?php
	$belegt = round(($belegte_termine/$alle_termine)*100);
	$frei = round(($freie_termine/$alle_termine)*100);
	echo '<progress value="'.$belegte_termine.'" max="'.$alle_termine.'">';
	echo ' </progress>';
	echo ' '.$belegt.'% belegt, ';
	echo $frei.'% frei';
?>
</span>
</p>
<p>
<label>Termine pro Schüler</label>
<span class="output">
<?php
 if($anzahl_schueler==0) $termine_pro_schueler = 0;
 else $termine_pro_schueler = round($belegte_termine/$anzahl_schueler);
 echo $termine_pro_schueler;
 echo ' von '.$anzahl_termine;
?>
</span>
</p>
<p>
<label>Termine pro Lehrer</label>
<span class="output">
<?php
 if ($anzahl_lehrer==0) $termine_pro_lehrer = 0;
 else $termine_pro_lehrer = round($belegte_termine/$anzahl_lehrer);
 echo $termine_pro_lehrer;
 echo ' von '.$anzahl_termine;
?>
</span>
</p>
<p>
<label>Vollausgelastete Lehrer</label>
<span class="output">
<?php
 $cmd = "SELECT COUNT(*) FROM (SELECT COUNT(*) as Termine, lehrerID FROM VTermine WHERE lehrerID IN (SELECT id FROM Lehrer) GROUP BY lehrerID) AS _t WHERE Termine >= '".$anzahl_termine."'";
 $row = mysql_fetch_row(mysql_query($cmd));
 echo $row[0];
?>
</span>
</p>
<p>
<label>Erfolglose Buchungen pro Schüler</label>
<span class="output">
<?php
 $cmd = "SELECT COUNT(*) FROM VSchuelerLehrer";
 $row = mysql_fetch_row(mysql_query($cmd));
 $reservierungen = $row[0];

 $reservierungen_ohne_erfolg = $reservierungen-$belegte_termine;
 if($anzahl_schueler==0) echo 0;
 else echo round($reservierungen_ohne_erfolg/$anzahl_schueler);
?>
</span>
</p>
<p>
<label>Erfolgreiche Buchung</label>
<span class="output">
<?php
 if($reservierungen==0) echo "0%";
 else echo round(($belegte_termine/$reservierungen)*100)."%";
?>
</span>
</p>
<p>
<label>Defekte Termine</label>
<span class="output">
<?php
 $cmd  = "SELECT COUNT(*) FROM VTermine";
 $cmd .= " WHERE VTermine.lehrerID NOT IN (SELECT id FROM Lehrer)";
 $cmd .= " OR    VTermine.schuelerID NOT IN (SELECT id FROM Schueler);";
 $row = mysql_fetch_row(mysql_query($cmd));
 echo $row[0];
?>
</span>
</p>
<?php
foot();

?>
