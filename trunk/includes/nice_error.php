<?php
/*
	Nice Error
	Daniel Bock
*/

ob_start(function($buffer) {

if ($GLOBALS["nice_errors"]!="") {
  return $buffer."\n".'<div id="php-errors">'.$GLOBALS["nice_errors"].'</div>';
} else {
  return $buffer;
}

});

$GLOBALS["nice_errors"] = "";

function contains($a, $b) {
	return strpos($a,$b)!==false;
}

function nice_error_handler($errno, $errstr, $errfile, $errline ) {
  $GLOBALS["nice_errors"] .= '<div class="php-error">';
  if (contains($errstr, "mysql") && contains($errstr, "expects parameter 1 to be resource, boolean given")) {
	$errstr = mysql_error();
	$errno = mysql_errno();
  }
  $GLOBALS["nice_errors"] .= "<strong>Fehler:</strong> [$errno] $errstr<br />\n";
  $GLOBALS["nice_errors"] .= "<strong>Zeile:</strong> $errline, <strong>Datei:</strong> $errfile<br />";
  $GLOBALS["nice_errors"] .= "PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
  $GLOBALS["nice_errors"] .= '</div>';
}
set_error_handler("nice_error_handler");

?>
