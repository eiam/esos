<?php

	$sql['host'] = '';
	$sql['uid'] = '';
	$sql['pwd'] = '';
	$sql['db'] = '';
	$sql['active'] = 'false';

	if(file_exists('includes/mysql.conf')) {
		$configfile = file('includes/mysql.conf');
		foreach ($configfile as $configline) {
			$configline = str_replace("\n", "", $configline);
			$line = explode(" ", $configline);
			if($line[0] == "HOST")
				$sql['host'] = $line[1];
			elseif($line[0] == "UID")
				$sql['uid'] = $line[1];
			elseif($line[0] == "PWD")
				$sql['pwd'] = $line[1];
			elseif($line[0] == "DB")
				$sql['db'] = $line[1];
			elseif($line[0] == "ACTIVE")
				$sql['active'] = $line[1];
		}
		unset($configfile);
		unset($configline);
		unset($line);
	}

	if($sql['active']=="true") {
		$connection = mysql_connect($sql['host'],$sql['uid'],$sql['pwd']);
		if ($connection) {
			if (!mysql_select_db($sql['db'])) trigger_error("Es konnte keine Verbindung zur Datenbank hergestellt werden: ".mysql_error());
		}
		else trigger_error('Es konnte keine Verbindung zum MySQL-Server hergestellt werden!');
		mysql_set_charset('utf8');
	} elseif (!isset($install)) {
		$hostname = $_SERVER['HTTP_HOST'];
		$path = dirname($_SERVER['PHP_SELF']);
		$base = 'http://'.$hostname.($path == '/' ? '' : $path);
		header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/install.php');
	}

	unset($sql);
?>
