<?php
	$sql['host'] = 'localhost';
	$sql['uid'] = 'pseminar';
	$sql['pwd'] = 'pseminar';
	$sql['db'] = 'pseminar';
	$connection = mysql_connect($sql['host'],$sql['uid'],$sql['pwd']);
		if($connection) {
			if(mysql_select_db($sql['db'])) {}
			else echo "Es konnte keine Verbindung zur Datenbank hergestellt werden!";
		}
		else echo "Es konnte keine Verbindung zum MySQL-Server hergestellt werden!";
?>
