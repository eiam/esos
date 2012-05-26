<?php

require_once("./includes/main.inc.php");
require_once("./includes/eltern_auth.inc.php");
require_once("./includes/mysql.inc.php");
require_once("./includes/termine.inc.php");

$sid = $_SESSION['id'];

$scripts .= '<script src="js/jquery.js" type="text/javascript"> </script>
<script src="eltern_termin.js" type="text/javascript"> </script>';

head();

// Lehrerauswahl von ×-Terminlink der Lehreruebersicht nicht mehr relevant
unset($_SESSION['mein_lehrer']);

?>

<h2>Manuelle Terminauswahl</h2>

<?php if(!eintragungsfrist()) { ?>
<p>Hier haben Sie die Möglichkeit Ihre Termine manuell einzustellen.</p>
<p>Bitte beachten Sie hierbei:</p>
 <ul><li>   Wenn Sie alleine kommen, können sie keine Doppeltermine wahrnehmen</li>
     <li>   Ein Gespräch dauert <strong><?php echo readConfig('GESPRAECHSDAUER'); ?> Minuten</strong></li></ul>
 
<?php } ?>

<input type="button" class="rightfloat" onclick="window.location.href = 'eltern_ausgabe.php'" value="Weiter" />

<?php

	if(!eintragungsfrist()) {
	
	

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<?php
	echo '<p id="lehrerauswahl">';
	lehrer($sid);
	echo '</p>';
?>
	<div id="info"><!-- workaround: JQuery needs some content here --></div>

<?php
	
	echo "<table id=\"termine\">";
	table($sid);
	echo "</table>";

?>
</form>
<?php

} else {
	echo "<p>Die Termine können ab ".readConfig("FRIST")." Stunden vor dem Elternsprechtag nicht mehr geändert werden.</p>";
}

foot();

?>
