<?php

require("./includes/main.inc.php");
	
require("./includes/admin_auth.inc.php");
require("./includes/lehrer.inc.php");

	if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["krank_melden"])) {
		$ID = mysql_real_escape_string($_POST["lehrerID"]);
		krank_melden($ID);
	}

	if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["gesund_melden"])) {
		$ID = mysql_real_escape_string($_POST["lehrerID"]);
		gesund_melden($ID);
	}

head();
?>
<h2>Lehrer</h2>
<p id="print"><a href="admin_lehrerdruck.php">Raumeinteilung</a></p>
<?php
	echo '<table id="lehrer">
			<tr>
				<th>Name</th> <th>Fächer</th> <th>E-Mail</th> <th>Ausdruck</th>
			</tr>';
$result=mysql_query("SELECT id, LName, LVorname, LEmail, Krank FROM Lehrer ORDER BY LName");
		while ($row=mysql_fetch_array($result)) {
		    echo '<tr>';
			echo '<td>'.$row['LName'].', '.$row['LVorname'].'</td>';
			echo '<td>'.faecher($row['id']).'</td><td>'.$row['LEmail'].'</td>';
			echo '<td><span class="print"><a href="lehrer_ausdruck.php?lehrer='.$row['id'].'">Termine</a></span></td>';
			if ($row["Krank"]!="1") {
				echo '<td><form method="post" action="">
<input type="hidden" name="lehrerID" value="'.$row["id"].'" />
<input type="hidden" name="krank_melden" value="true" />
<input type="button" onclick="if(confirm(\'Sind Sie wirklich sicher, dass Sie diesen Lehrer krankmelden möchten? Wenn Sie diesen Lehrer krankmelden, werden alle Termine bei diesem Lehrer gelöscht und die betroffenen Eltern darüber per E-Mail benachrichtigt werden.\')) this.form.submit();" value="Krank melden" /></form></td>';
			} else {
				echo '<td><form method="post" action="">
<i>Krank</i>
<input type="hidden" name="lehrerID" value="'.$row["id"].'" />
<input type="submit" name="gesund_melden" value="Wieder gesund!" /></form></td>';
			}
			echo '</tr>';
		}
	echo '</table>';
?>

<?php
foot();
?>
