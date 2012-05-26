<?php

session_start();

require_once('includes/mysql.inc.php');
require_once('./includes/eltern_termine.inc.php');
require_once('./includes/benutzer.inc.php');

require('./includes/eltern_auth.inc.php');

$ID = $_SESSION['id'];

$geschwister_anzahl = count(geschwister($ID));

$sql='SELECT SVorname, SName, SKlasse FROM Schueler WHERE id='.$ID;
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$name=$row['SVorname'].' '.$row["SName"];


require_once("mpdf/mpdf.php");
$mpdf=new mPDF('de','A4',9,'ubuntu');
$mpdf->SetTitle('ESOS Termine '.$name.' ('.$row['SKlasse'].')');
$stylesheet = file_get_contents('design/print.css');
$mpdf->WriteHTML($stylesheet,1);

ob_start();

if ($geschwister_anzahl>1) {
	echo '<h1>Meine Termine</h1>';
} else {
	echo '<h1>'.$name.' ('.$row['SKlasse'].')</h1>';
}

elternTermine($ID);

$html = ob_get_contents();
ob_end_clean();

$mpdf->WriteHTML($html,2);

$mpdf->Output('ESOS Termine '.$name.'.pdf','D');
exit;

?>
