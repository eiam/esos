<?php

require("./includes/main.inc.php");

if ( !isset($_SESSION["eltern"]) && !isset($_SESSION["lehrer"]) && !isset($_SESSION["admin"]) )
{
	header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/login.php');
}
if ( isset($_SESSION["eltern"]) )
{
	header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/eltern_lehrer.php');
}
if ( isset($_SESSION["lehrer"]) )
{
	header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/lehrer_ausgabe.php');
}
if ( isset($_SESSION["admin"]) )
{
	header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/admin_config.php');
}
?>
