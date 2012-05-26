<?php

session_start();

$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);

if(!(isset($_SESSION["admin"]) || isset($_SESSION['lehrer']))) {
    header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/login.php');
	die;
}

require_once('includes/mysql.inc.php');
require("./includes/lehrer.inc.php");

if (isset($_SESSION['lehrer'])) {
	$ID = $_SESSION['id'];
}

if (isset($_SESSION["admin"])) {
	$ID = (int) mysql_real_escape_string($_GET['lehrer']);
}

$sql='SELECT LVorname, LName, Raum FROM Lehrer WHERE id='.$ID;
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$name=$row['LVorname'].' '.$row["LName"];


require_once("mpdf/mpdf.php");
$mpdf=new mPDF('de','A4',9,'ubuntu');
$mpdf->SetTitle('ESOS Lehreraushang '.$name);
$stylesheet = file_get_contents('design/print.css');
$mpdf->WriteHTML($stylesheet,1);

ob_start();

echo '<h1>'.$name.'</h1>
	<h2>Raum: '.$row['Raum'].'</h2>';

termin_tabelle();

$html = ob_get_contents();
ob_end_clean();

$mpdf->WriteHTML($html,2);

$mpdf->Output('ESOS Lehreraushang '.$name.'.pdf','D');
exit;

?>
