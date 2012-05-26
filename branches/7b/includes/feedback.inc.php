<?php

function feedback_formular() {
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['meinung'])) {
if(!isset($_POST["BenutzeroberflaecheImAllgemeinen"])) $_POST["BenutzeroberflaecheImAllgemeinen"] = "";
if(!isset($_POST["SoftwareVerwendenWieder"])) $_POST["SoftwareVerwendenWieder"] = "";
if(!isset($_POST["SoftwareIntuitiv"])) $_POST["SoftwareIntuitiv"] = "";
if(!isset($_POST["BenutzeroberflaecheUebersichtlich"])) $_POST["BenutzeroberflaecheUebersichtlich"] = "";
if(!isset($_POST["OberflaecheAnsprechend"])) $_POST["OberflaecheAnsprechend"] = "";
if(!isset($_POST["SchritteBeschreibungen"])) $_POST["SchritteBeschreibungen"] = "";
if(!isset($_POST["DokumentationZurecht"])) $_POST["DokumentationZurecht"] = "";
if(!isset($_POST["HilfeBenoetigt"])) $_POST["HilfeBenoetigt"] = "";
if(!isset($_POST["TerminauswahlVorgehensweise"])) $_POST["TerminauswahlVorgehensweise"] = "";

if($_POST["BenutzeroberflaecheImAllgemeinen"]!=''||$_POST["f01"]!=""||$_POST["f02"]!=""||$_POST["f03"]!=""||$_POST["SoftwareVerwendenWieder"]!="")
	$meinung = "
**** Allgemeine Fragen zur Software ****
";

if($_POST["BenutzeroberflaecheImAllgemeinen"]!='') $meinung .= "\n
Wie zufrieden sind Sie mit unserer Benutzeroberfläche im Allgemeinen?

(Sehr unzufrieden, sehr zufrieden)
$_POST[BenutzeroberflaecheImAllgemeinen]";

if($_POST["f01"]!="") $meinung .= "\n
Haben Sie Kritik oder Vorschläge zur Veränderung an dieser Software?

$_POST[f01]";

if($_POST["f02"]!="") $meinung .= "\n
Haben sie irgendwelche Bedenken bezüglich des Schutzes Ihrer Daten?

$_POST[f02]";

if($_POST["f03"]!="") $meinung .= "\n
Welchen Internet-Browser haben Sie benutzt?

$_POST[f03]";

if($_POST["SoftwareVerwendenWieder"]!="") $meinung .= "\n
Würden Sie die Software wieder verwenden?

$_POST[SoftwareVerwendenWieder]";

if($_POST["SoftwareIntuitiv"]!=""||$_POST["f04"]!=""||$_POST["f05"]!=""||$_POST["TerminauswahlVorgehensweise"]!=""||$_POST["f06"]!=""||$_POST["BenutzeroberflaecheUebersichtlich"]!=""||$_POST["f07"]!="")
$meinung .= "\n
**** Fragen zur Bedienung ****
";

if($_POST["SoftwareIntuitiv"]!="") $meinung .= "\n
War die Software intuitiv zu bedienen?

$_POST[SoftwareIntuitiv]";

if($_POST["f04"]!="") $meinung .= "\n
Wie finden sie die Vorgehensweise zur Anmeldung beim Portal?

$_POST[f04]";

if($_POST["f05"]!="") $meinung .= "\n
Sind ihnen Fehlfunktionen oder Ähnliches aufgefallen?

$_POST[f05]";

if($_POST["TerminauswahlVorgehensweise"]!="") $meinung .= "\n
Wie finden Sie die Vorgehensweise für die Terminauswahl?

(Gut und einfach, Mühsam und umständlich)
$_POST[TerminauswahlVorgehensweise]";

if($_POST["f06"]!="") $meinung .= "\n
Würden Sie etwas an dieser Vorgehensweise ändern?

$_POST[f06]";

if($_POST["BenutzeroberflaecheUebersichtlich"]!="") $meinung .= "\n
War die Benutzeroberfläche für sie immer übersichtlich aufgebaut und kamen Sie gut damit zurecht?

(Ja, sehr übersichtlich, Nein, ich kam damit überaupt nicht zurecht.)
$_POST[BenutzeroberflaecheUebersichtlich]";

if($_POST["f07"]!="") $meinung .= "\n
War die Bedienung an manchen Stellen unverständlich oder umständlich, und wenn ja, an welchen?

$_POST[f07]";

if($_POST["OberflaecheAnsprechend"]!=""||$_POST["f08"]!=""||$_POST["f09"]!="")
$meinung .= "\n
**** Frage zur Gestaltung ****
";

if($_POST["OberflaecheAnsprechend"]!="") $meinung .= "\n
Finden Sie die Gestaltung der Oberfläche ansprechend?

(überhaupt nicht ansprechend, Sehr ansprechend)
$_POST[OberflaecheAnsprechend]";

if($_POST["f08"]!="") $meinung .= "\n
Gab es Grafiken, die Sie als störend empfunden haben?

$_POST[f08]";

if($_POST["f09"]!="") $meinung .= "\n
Gab es sonstige unschöne Formatierungen, und wenn ja, welche?

$_POST[f09]";

if($_POST["SchritteBeschreibungen"]!=""||$_POST["DokumentationZurecht"]!=""||$_POST["HilfeBenoetigt"]!=""||$_POST["f10"]!="")
$meinung .= "\n
**** Fragen zur Dokumentation ****
";

if($_POST["SchritteBeschreibungen"]!="") $meinung .= "\n
Wie hilfreich fanden sie die Beschreibungen zu den einzelnen Schritten?

(Sehr hilfreich, Überhaupt nicht hilfreich.)
$_POST[SchritteBeschreibungen]";

if($_POST["DokumentationZurecht"]!="") $meinung .= "\n
Kamen sie mit der Dokumentation gut zurecht?

$_POST[DokumentationZurecht]";

if($_POST["HilfeBenoetigt"]!="") $meinung .= "\n
Hätten sie etwas mehr Hilfe bei der Terminauswahl oder der Anmeldung benötigt?

$_POST[HilfeBenoetigt]";

if($_POST["f10"]!="") $meinung .= "\n
An welchen Stellen gibt es ihrer Meinung nach Verbesserungbedarf?

$_POST[f10]";

$meinung .= "\nHTTP-User-Agent: ".$_SERVER['HTTP_USER_AGENT'];

		$cmd = "INSERT INTO Feedback (schuelerID, Text, Datum) VALUES ('$_SESSION[id]', '$meinung', NOW())";
		$result = mysql_query($cmd);
		echo '<p class="success">Vielen Dank für Ihre Meinung!</p>';
		echo '<pre>'.$meinung.'</pre>';
	} else {
		echo '<h2>Bewertungsbogen für den Testlauf des Elternsprechabends am 21. November</h2>';
		echo '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
		echo '<p>Vielen Dank für ihre Teilnahme am Beta-Test des Elternsprechtagsanmeldungsportal ESOS,
		welches von Schülern des Projekt-Seminars Informatik entwickelt wurde. Ihr Feedback ist uns für
		eine erfolgreiche Evaluation sehr wichtig.</p>';
		echo '<input type="hidden" name="meinung" value="true" />';
	function radio_group_vote($name, $min, $max) {
		echo '<p><b>'.$min.'</b><input type="radio" name="'.$name.'" value="1" id="'.$name.'1"><label for="'.$name.'1" style="width:auto;">1</label>';
		echo '<input type="radio" name="'.$name.'" value="2" id="'.$name.'2"><label for="'.$name.'2" style="width:auto;">2</label>';
		echo '<input type="radio" name="'.$name.'" value="3" id="'.$name.'3"><label for="'.$name.'3" style="width:auto;">3</label>';
		echo '<input type="radio" name="'.$name.'" value="4" id="'.$name.'4"><label for="'.$name.'4" style="width:auto;">4</label>';
		echo '<input type="radio" name="'.$name.'" value="5" id="'.$name.'5"><label for="'.$name.'5" style="width:auto;">5</label> <b>'.$max.'</b></p> ';
	}
	function radio_group_janein($name) {
		echo '<input type="radio" name="'.$name.'" value="Ja" id="'.$name.'1"><label for="'.$name.'1" style="width:auto;">Ja</label>';
		echo '<input type="radio" name="'.$name.'" value="Nein" id="'.$name.'0"><label for="'.$name.'0" style="width:auto;">Nein</label>';
	}
	echo '<style>label {}</style>';

		echo '<h3>Allgemeine Fragen zur Software</h3>
<p>Wie zufrieden sind Sie mit unserer Benutzeroberfläche im Allgemeinen? ';
radio_group_vote("BenutzeroberflaecheImAllgemeinen", "Sehr unzufrieden", "sehr zufrieden");
echo '

<p><label for="f01">Haben Sie Kritik oder Vorschläge zur Veränderung an dieser Software?</label>
<textarea class="feedback_textarea" name="f01"></textarea></p>

<p><label for="f02">Haben sie irgendwelche Bedenken bezüglich des Schutzes Ihrer Daten?</label>
<textarea class="feedback_textarea" name="f02"></textarea>

<p><label for="f03">Welchen Internet-Browser haben Sie benutzt?</label>
<textarea class="feedback_textarea" name="f03"></textarea>

<p>Würden Sie die Software wieder verwenden?</p>';
radio_group_janein("SoftwareVerwendenWieder");
echo'
<h3>Fragen zur Bedienung</h3>

<p>War die Software intuitiv zu bedienen?';
radio_group_janein("SoftwareIntuitiv");
echo '
<p><label for="f04">Wie finden sie die Vorgehensweise zur Anmeldung beim Portal?</label>
<textarea class="feedback_textarea" name="f04"></textarea>

<p><label for="f05">Sind ihnen Fehlfunktionen oder Ähnliches aufgefallen?</label>
<textarea class="feedback_textarea" name="f05"></textarea>

<p>Wie finden Sie die Vorgehensweise für die Terminauswahl?';
radio_group_vote("TerminauswahlVorgehensweise", "Gut und einfach", "Mühsam und umständlich");
echo '
<p><label for="f06">Würden Sie etwas an dieser Vorgehensweise ändern?</label>
<textarea class="feedback_textarea" name="f06"></textarea>

<p>War die Benutzeroberfläche für sie immer übersichtlich aufgebaut und kamen Sie gut damit zurecht?';
radio_group_vote("BenutzeroberflaecheUebersichtlich", "Ja, sehr übersichtlich", "Nein, ich kam damit überaupt nicht zurecht.");

echo'
<p><label for="f07">War die Bedienung an manchen Stellen unverständlich oder umständlich, und wenn ja, an welchen?</label>
<textarea class="feedback_textarea" name="f07"></textarea>

<h3>Frage zur Gestaltung</h3>

<p>Finden Sie die Gestaltung der Oberfläche ansprechend?';
radio_group_vote("OberflaecheAnsprechend", "überhaupt nicht ansprechend", "Sehr ansprechend ");
echo '
<p><label for="f08">Gab es Grafiken, die Sie als störend empfunden haben?</label>
<textarea class="feedback_textarea" name="f08"></textarea>

<p><label for="f09">Gab es sonstige unschöne Formatierungen, und wenn ja, welche?</label>
<textarea class="feedback_textarea" name="f09"></textarea>


<h3>Fragen zur Dokumentation</h3>

<p>Wie hilfreich fanden sie die Beschreibungen zu den einzelnen Schritten?';
radio_group_vote("SchritteBeschreibungen", "Sehr hilfreich", " Überhaupt nicht hilfreich.");
echo '
<p>Kamen sie mit der Dokumentation gut zurecht?';
radio_group_janein("DokumentationZurecht");
echo '

<p>Hätten sie etwas mehr Hilfe bei der Terminauswahl oder der Anmeldung benötigt?</p>';
radio_group_janein("HilfeBenoetigt");
echo '
<p><label for="f10">An welchen Stellen gibt es ihrer Meinung nach Verbesserungbedarf?</label>
<textarea class="feedback_textarea" name="f10"></textarea>';

		echo '<p><input type="submit" value="Abschicken" /></p>';
		echo '</form>';
	}
}

function feedback_anzeigen() {
	echo '<h2>Feedback</h2>';
	echo '<table>';
	echo '<tr><th>Schüler</th><th>Datum</th><th>Meinung</th></tr>';
	$cmd = "SELECT * FROM Feedback, Schueler WHERE schuelerID = Schueler.id ORDER BY DATUM DESC";
	$result = mysql_query($cmd);
	$i=0;
	while($row=mysql_fetch_array($result)) {
		echo '<tr><td>'.$row['SVorname'].' '.$row['SName'].'</td><td>'.$row['Datum'].'</td><td><pre>'.$row['Text'].'</pre></td></tr>';
		$i++;
	}
	if($i==0) { echo '<tr><td colspan="3"><i>Bisher wurde noch kein Feedback abgegeben.</i></td></tr>'; }
	echo '</table>';
}
?>
