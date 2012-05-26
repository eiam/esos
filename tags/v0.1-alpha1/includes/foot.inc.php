<?php

// HTML Foot

?>
    </div>
  </body>
</html>
<?php

$html = ob_get_clean();

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
  throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

// Funktion um XML oder HTML einzuruecken, gibt String aus
function indentXML($string) {
 $dom = new DOMDocument();
 $dom->preserveWhiteSpace = false;
 $dom->formatOutput = true;
 try {
   $dom->loadXML($string);
   $dom = $dom->saveXML();
   return $dom;
 } catch (Exception $e) {
    $GLOBALS["errors"] .= '<div class="php-error">'.$e->getMessage().'</div>';
	return $string;
 }
}

$tidy = indentXML($html);

echo $tidy;

if ($GLOBALS["errors"]!="") {
  echo '<div id="php-errors">'.$GLOBALS["errors"].'</div>';
}
?>
