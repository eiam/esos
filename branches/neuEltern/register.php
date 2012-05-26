<?php

require_once('./includes/main.inc.php');
require_once('./securimage/securimage.php');
require_once("./includes/benutzer.inc.php");

logout();

head();

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<h2>Registrieren</h2>
<p>Bitte geben Sie Ihre E-Mail-Adresse an, um einen Link zu erhalten, mit dem sie die Einverständniserklärung bestätigen können.</p>
<p><label for="email">Ihre E-Mail-Adresse:</label> <input type="email" name="email" /></p>
<h3>Einverständnis</h3>
<p>Ich bin damit einverstanden, dass der Administrator dieses Portals
sowie die Lehrer, bei denen ich mich anmelde, Zugriff auf folgende Daten erhalten: Die Namen meiner Kinder, ihre Klassen, meine E-Mail-Adresse
sowie die von mir gewählten Termine. Ich bin damit einverstanden, dass mir per E-Mail elternsprechtagsbezogene Nachrichten zugeschickt werden.
Mir ist bekannt, dass ich dieses Einverständnis jederzeit mittels dieses Portals, aber auch schriftlich widerrufen kann.</p>
<h3>Captcha</h3>
<p><img id="captcha" src="./securimage/securimage_show.php" alt="[CAPTCHA]" onclick="document.getElementById('captcha').src = './securimage/securimage_show.php?' + Math.random(); return false" /></p>
<p><label for="captcha_code">Sicherheitscode</label><input type="text" name="captcha_code" size="10" maxlength="6" required="required" /></p>
<p><input type="submit" class="right" value="Freischaltung anfordern" /></p>
</form>
<?php

foot();

?>
