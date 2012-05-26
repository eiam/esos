<?php
	$hostname = $_SERVER['HTTP_HOST'];
	$path = dirname($_SERVER['PHP_SELF']);

	if(!isset($_SESSION["admin"]) || !$_SESSION["admin"]) {
		echo $_SESSION["admin"];
        //header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/login');
		die;
	}
?>
