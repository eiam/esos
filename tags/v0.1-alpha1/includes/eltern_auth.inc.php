<?php
	$hostname = $_SERVER['HTTP_HOST'];
	$path = dirname($_SERVER['PHP_SELF']);

	if(!isset($_SESSION["eltern"]) || !$_SESSION["eltern"]) {
        header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/login');
		die;
	}
?>
