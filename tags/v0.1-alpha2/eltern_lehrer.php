<?php
	
require_once("./includes/main.inc.php");
require_once("./includes/eltern_auth.inc.php");
require_once("./includes/mysql.inc.php");
require_once("./includes/lehrer.inc.php");
require_once("./includes/benutzer.inc.php");


$scripts .= '<script src="js/jquery.js" type="text/javascript"> </script>';
$scripts .= '<script src="eltern_lehrer.js" type="text/javascript"> </script>';

head();

echo '<h2>Lehrerauswahl</h2>';

KindAuswahl();

if (count(geschwister($_SESSION["id"]))>1) {
	$extra = ' für jedes Ihrer Kinder';
} else $extra = '';

echo '<p>W&auml;hlen Sie bitte'.$extra.' die Lehrer aus, mit denen Sie einen Termin vereinbaren m&ouml;chten. Wenn Sie damit fertig sind, klicken Sie bitte unten auf &#132;Weiter&#148;.</p>';

echo '<p><strong>Tipp:</strong> W&auml;hlen Sie zuerst die Lehrer aus, bei denen sie unbedingt einen Termin haben wollen und dann die Lehrer, die Ihnen weniger wichtig sind, um einen optimalen Terminplan f&uuml;r den Elternsprechtag zu erhalten.</p>';

echo '<div id="info"><!-- workaround: JQuery needs some content here --></div>';

echo '<input type="button" id="weiter" class="rightfloat" onclick="window.location.href = \'eltern_termin.php\'" value="Weiter" />';

echo '<h3>Meine Lehrer</h3>';
echo '<table id="meine">';
meine_lehrer();
echo '</table>';

echo '<h3 id="andereh">Andere Lehrer</h3>';
echo '<table id="andere">';
andere_lehrer();
echo '</table>';

foot(); ?>
