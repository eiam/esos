<?php
	if (session_id()=="") trigger_error("Bitte session_start() verwenden!");
	$hostname = $_SERVER['HTTP_HOST'];
	$path = dirname($_SERVER['PHP_SELF']);

	if(!isset($_SESSION["admin"]) || !$_SESSION["admin"]) {
        header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/login.php');
		die;
	}
?>
