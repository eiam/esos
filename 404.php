<?php

header("HTTP/1.0 404 Not Found");

$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER["REQUEST_URI"]);

header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/');

?>
