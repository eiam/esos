<?php

    require('./includes/main.inc.php');
	require_once('./includes/mysql.inc.php');
	require_once('./includes/eltern_auth.inc.php');
	
	head();
?>
<h2>&Uuml;bersicht</h2>
<p>Willkommen auf der offiziellen Anmeldeseite f&uuml;r den Elternsprechtag am <?php echo readConfig("SCHULNAME"); ?>!<br />
Auf dieser Webseite k&ouml;nnen Sie sich f&uuml;r Termine mit allen Lehrern der Schule eintragen, mit denen Sie sprechen m&ouml;chten. Die Eintragungsfrist endet am <b><?php echo eintragungsfristString(); ?></b>.<br />
Hier ist eine &Uuml;bersicht &uuml;ber die n&ouml;tigen Schritte, die Sie in den n&auml;chsten Minuten durchf&uuml;hren sollten:</p>

<ol>
<li>Unter „Lehrer &amp; Termine“ w&auml;hlen Sie zuallererst die Lehrer aus, die Sie sprechen m&ouml;chten.
<li>In der „Manuellen Terminauswahl“ k&ouml;nnen Sie Ihre Terminzeiten von Hand &auml;ndern, wenn Sie mit der automatischen Zuweisung nicht zufrieden sind. Doppeltermine sind hier auch m&ouml;glich, wenn Sie mit mehr als einer Person kommen (z.B. beide Elternteile).
<li>Einen Ausdruck Ihrer Terminliste k&ouml;nnen Sie &uuml;ber dem Men&uuml;punkt „Termin&uuml;bersicht“ erstellen (PDF-Datei).
<li>Sind Sie mit Ihrer Auswahl zufrieden, melden Sie sich einfach wieder &uuml;ber „Logout“ ab. Sie k&ouml;nnen Ihre Auswahl nat&uuml;rlich jederzeit wieder &auml;ndern.
</ol>
<input type="button" id="weiter" class="rightfloat" onclick="window.location.href ='eltern_lehrer.php'" value="Weiter" />

<?php foot(); ?>
