<?php
	$hostname = $_SERVER['HTTP_HOST'];
	$path = dirname($_SERVER['PHP_SELF']);


	if(!isset($_SESSION["lehrer"]) || !$_SESSION["lehrer"]) {
		header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/login');
		die;
	}
?>
