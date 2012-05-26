<?php

    require('./includes/main.inc.php');
	require('./includes/mysql.inc.php');
	require('./includes/eltern_auth.inc.php');
	require('./includes/eltern_termine.inc.php');
	$ID = $_SESSION["id"];



	head();
?>
<h2>Terminübersicht</h2>
<?php elternTermine($ID); ?>
    <p id="print"><a href="eltern_ausdruck.php?schueler=<?php echo $ID;?>">Terminausdruck</a></p>
<?php foot(); ?>
