<?php
require("./includes/main.inc.php");
head();
echo "<h2>Impressum</h2>\n";
echo readConfig("IMPRESSUM");
foot();
?>
