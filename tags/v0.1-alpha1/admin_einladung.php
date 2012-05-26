<?php

$page = "admin_einladung";

require("./includes/head.inc.php");
require("./includes/admin_auth.inc.php");
?>
<form action="" method="post">
<table>
<tr>
<th>Bereits gesendete Einladungen</th>
</tr>
<tr><td><i>Keine Einladung abgesendet.</i></td></tr>
</table>
<h3>Einladungstext für Eltern</h3>
<p>Sie können im Einladungstext folgende Variablen verwenden:
<table>
<tr><th>Variable</th><th>Bedeutung</th></tr>
<tr><td><code>{username}</code></td><td>Benutzername zum Anmelden</td></tr>
<tr><td><code>{password}</code></td><td>Passwort zum Anmelden</td></tr>
</table>
</p>
<textarea>
Liebe Eltern, liebe Erziehungsberechtigten,

wir laden Sie herzlichst zum Elternsprechtag am Lise-Meitner-Gymnasium Unterhaching für ein Gespräch mit den Lehrern Ihres Sohnes, Ihrer Tochter beziehungsweise Ihrer Kinder ein. Der Elternsprechtag findet am 1. Januar 1970 ab 17:00 Uhr statt.

Bitte melden Sie sich auf folgender Internetseite an:
  http://elternsprechtag.lmgu.de

Ihre Zugangsdaten lauten:
* s7L4M87
* x5-lmww

Mit freundlichen Grüßen,
Vorname Nachname
Elternsprechtagorganisator 
</textarea><br /><br/>
<input type="submit" value="Einladungen absenden"/>
</form>
<?php
require("./includes/foot.inc.php");

?>
