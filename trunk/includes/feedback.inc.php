<?php

function feedback_formular() {
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['meinung'])) {
		$cmd = "INSERT INTO Feedback (schuelerID, Text) VALUES ('$_SESSION[id]', '$_POST[meinung]')";
		$result= mysql_query($cmd);
		echo '<p class="success">Vielen Dank für Ihre Meinung!</p>';
	} else {
		echo '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
		echo '<p>Vielen Dank für ihre Teilnahme am Beta-Test des Elternsprechtagsanmeldungsportal ESOS,
		welches von Schülern des Projekt-Seminars Informatik entwickelt wurde. Ihr Feedback ist uns für
		eine erfolgreiche Evaluation sehr wichtig.</p>';
		echo '<p><label for="meinung">Ihre Meinung</label></p>';
		echo '<p><textarea name="meinung"></textarea></p>';
		echo '<p><input type="submit" value="Abschicken" /></p>';
		echo '</form>';
	}
}

function feedback_anzeigen() {
	echo '<table>';
	$cmd = "SELECT * FROM Feedback";
	$result = mysql_query($cmd);
	while($row=mysql_fetch_array($result)) {
		echo '<tr><td>'.$row['Text'].'</td></tr>';
	}
	echo '</table>';
}
?>
