<?php

session_start();

require_once('includes/admin_auth.inc.php');
require_once('includes/mysql.inc.php');
require("./includes/lehrer.inc.php");

require_once("mpdf/mpdf.php");
$mpdf=new mPDF('de','A4',9,'ubuntu');
$mpdf->SetTitle('ESOS Raumaushang');
$stylesheet = file_get_contents('design/print.css');
$mpdf->WriteHTML($stylesheet,1);

ob_start();

$krank=array();

	echo '<table id="lehrer">
			<tr>
				<th>Name</th> <th>Fächer</th> <th>Raum</th>
			</tr>';
$result=mysql_query("SELECT id, LName, LVorname, Raum, Krank FROM Lehrer ORDER BY LName");
		while ($row=mysql_fetch_array($result)) {
		    if(!$row['Krank']) {
		    	echo '<tr>';
				echo '<td>'.$row['LName'].', '.$row['LVorname'].'</td>';
				echo '<td>'.faecher($row['id']).'</td><td>'.$row['Raum'].'</td>';
				echo '</tr>';
			}
			else {
				$krank[]=' '.$row['LVorname'].' '.$row['LName'];
			}
		}
	echo '</table><br />';
	if (!empty($krank)) echo '<b>Abwesende Lehrer</b>:'.implode(',',$krank);

	echo '<p><b>Fachkürzel</b>: ';
	$cmd = 'SELECT * FROM `Faecher` WHERE id IN (SELECT fachID FROM VLehrerFach WHERE lehrerID NOT IN (SELECT id FROM Lehrer WHERE Krank=1)) ORDER BY Kuerzel';
	$result = mysql_query($cmd);
	$first = true;
	while($row = mysql_fetch_array($result)) {
		if($first) { $first = false; }
		else { echo ', '; }
		echo '<span style="font-weight:bold;color:grey">'.$row['Kuerzel'].'</span> '.$row['Fach'];
	}
	echo '</p>';

$html = ob_get_contents();
ob_end_clean();

$mpdf->WriteHTML($html,2);

$mpdf->Output('ESOS Raumaushang.pdf','D');
exit;

?>
