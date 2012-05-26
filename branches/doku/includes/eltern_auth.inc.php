<?php
	$hostname = $_SERVER['HTTP_HOST'];
	$path = dirname($_SERVER['PHP_SELF']);

	if(!isset($_SESSION["eltern"]) || !$_SESSION["eltern"] || readConfig("OFFEN") != "true") {
        header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/login.php');
		die;
	}

	if (!isset($_SESSION["id"])) {
		trigger_error('Fehler beim Login: Es wurde keine $_SESSION["id"] gesetzt.');
	}
?>
