<?php

    require('./includes/main.inc.php');
	require_once('./includes/mysql.inc.php');
	require_once('./includes/eltern_auth.inc.php');
	
	head();
?>
<h2>&Uuml;bersicht</h2>
<p>Willkommen auf der offiziellen Anmeldeseite f&uuml;r den Elternsprechtag am <?php echo readConfig("SCHULNAME"); ?>!<br />
Auf dieser Webseite k&ouml;nnen Sie sich f&uuml;r Termine mit allen Lehrern der Schule eintragen, mit denen Sie sprechen m&ouml;chten.
Hier ist eine &Uuml;bersicht &uuml;ber die n&ouml;tigen Schritte, die Sie in den n&auml;chsten Minuten durchf&uuml;hren sollten:</p>

<ol>
<li>Besuchen mehrere Ihrer Kinder unsere Schule, klicken Sie auf „Meine Kinder“, um die Ihren Kindern zugeh&ouml;rigen Benutzerkonten zu verbinden. Dies wird die Auswahl f&uuml;r Sie erheblich erleichtern.
<li>Unter „Lehrer & Termine“ w&auml;hlen Sie zuallererst die Lehrer aus, die Sie sprechen m&ouml;chten. Dies machen Sie f&uuml;r jedes Ihrer Kinder einzeln (Der Lehrer kann sich damit auf das Gespr&auml;ch &uuml;ber das Kind vorbereiten).
<li>In der „Manuellen Terminauswahl“ k&ouml;nnen Sie Ihre Terminzeiten von Hand &auml;ndern, wenn Sie mit der automatischen Zuweisung nicht zufrieden sind. Doppeltermine sind hier auch m&ouml;glich, wenn Sie mit mehr als einer Person kommen (z.B. beide Elternteile).
<li>Einen Ausdruck Ihrer Terminliste k&ouml;nnen Sie &uuml;ber dem Men&uuml;punkt „Termin&uuml;bersicht“ erstellen (PDF-Datei).
<li>Sind Sie mit Ihrer Auswahl zufrieden, melden Sie sich einfach wieder &uuml;ber „Logout“ ab. Sie k&ouml;nnen Ihre Auswahl nat&uuml;rlich jederzeit wieder &auml;ndern.
</ol>
<input type="button" id="weiter" class="rightfloat" onclick="window.location.href ='eltern_kinder.php'" value="Weiter" />

<?php foot(); ?>
