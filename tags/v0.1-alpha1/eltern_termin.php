<?php

$page = "eltern_termin";

require("./includes/head.inc.php");
require("./includes/eltern_auth.inc.php");
require("./includes/mysql.inc.php");
require("./includes/termine.inc.php");

$sid = $_SESSION['id'];

?>

<h2>Terminauswahl</h2>

<form action="" method="post">
	<select id="lehrer">
<?php
	lehrer($sid);
?>
    </select>
    <input type="button" id="terminhinzufuegen" value="Termin mit Lehrer hinzufügen" />

<?php

    echo '<input id="sid" type="hidden" value="'.$sid.'"></input>';
	
	echo "<table id=\"termine\">";
	table($sid);
	echo "</table>";

?>
	<input type="button" id="weiter" class="right" value="Bestätigen" />
</form>
<script src="js/jquery.js" type="text/javascript"> </script>
<script src="eltern_termin.js" type="text/javascript"> </script>
<?php

include("./includes/foot.inc.php");

?>
