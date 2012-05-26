<?php
	session_start();
	require('./includes/mysql.inc.php');
	function faecher($id) {
		$result=mysql_query("SELECT Kuerzel FROM Faecher, VLehrerFach WHERE Faecher.id=fachID AND lehrerID=".$id);
		while ($row=mysql_fetch_array($result)) 
		{
			if (isset($faecher)) $faecher.=" / ".$row['Kuerzel'];
			else $faecher=$row['Kuerzel'];
		}
		if (isset($faecher)) return $faecher;
	}

	switch($_POST['step']) {
		case "check":
			$checked=$_POST['checked'];
			if ($checked=="true") {
				$result=mysql_query("SELECT schuelerID, lehrerID FROM VSchuelerLehrer WHERE schuelerID=".$_SESSION['id']." AND lehrerID=".mysql_real_escape_string($_POST['id']));
					if(!$row = mysql_fetch_array($result)) {
						$result=mysql_query("INSERT INTO VSchuelerLehrer(schuelerID, lehrerID) VALUES ('".$_SESSION['id']."', '".mysql_real_escape_string($_POST['id'])."');");
					}
			}
			else {
				$result=mysql_query("DELETE FROM VSchuelerLehrer WHERE schuelerID=".$_SESSION['id']." AND lehrerID=".mysql_real_escape_string($_POST['id']));
			}
			break;
		case "meine":
			echo '<tr>
					<th></th><th>Name</th> <th>Fächer</th> <th>E-Mail</th>
					</tr>';
			$result=mysql_query("SELECT id, LName, LVorname, LEmail FROM Lehrer, VSchuelerLehrer WHERE schuelerID=".$_SESSION['id']." AND lehrerID=id ORDER BY LName");
			while ($row=mysql_fetch_array($result)) {
				echo '<tr> <td><input type="checkbox" class="lehrer" id="'.$row['id'].'" checked="checked" /></td>';
				echo '<td>'.$row['LName'].', '.$row['LVorname'].'</td>';
				echo '<td>'.faecher($row['id']).'</td><td>'.$row['LEmail'].'</td></tr>';
			}
			break;
		case "andere":
			echo '<tr>
				<th></th><th>Name</th> <th>Fächer</th> <th>E-Mail</th>
				</tr>';
			$result=mysql_query("SELECT id, LName, LVorname, LEmail FROM Lehrer WHERE id NOT IN (SELECT lehrerID FROM VSchuelerLehrer WHERE schuelerID=$_SESSION[id]) ORDER BY LName");
			while ($row=mysql_fetch_array($result)) {
				echo '<tr> <td><input type="checkbox" class="lehrer" id="'.$row['id'].'" /></td>';
				echo '<td>'.$row['LName'].', '.$row['LVorname'].'</td>';
				echo '<td>'.faecher($row['id']).'</td><td>'.$row['LEmail'].'</td></tr>';
			}
			break;
	}
?>
