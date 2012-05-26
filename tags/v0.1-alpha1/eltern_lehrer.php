<?php

	$page = "eltern_lehrer";
	
	require("./includes/head.inc.php");
	
	require("./includes/eltern_auth.inc.php");
	require("./includes/mysql.inc.php");

	/*if($_SERVER["REQUEST_METHOD"] == "POST") {
		mysql_query("DELETE FROM VSchuelerLehrer WHERE schuelerID=".$_SESSION['id']);
		if (isset($_POST['lehrer'])) {
			foreach ($_POST['lehrer'] as $lehrer) {
				$lehrer = mysql_real_escape_string($lehrer);
				$result=mysql_query("SELECT schuelerID, lehrerID FROM VSchuelerLehrer WHERE schuelerID=".$_SESSION['id']." AND lehrerID=".$lehrer);
				if(!$row = mysql_fetch_array($result)) {
					$result=mysql_query("INSERT INTO VSchuelerLehrer(schuelerID, lehrerID) VALUES ('".$_SESSION['id']."', '".$lehrer."');");
				}
			}
			header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/eltern_termin');
		}
		echo "bla";
	}*/
	
	function faecher($id) {
		$result=mysql_query("SELECT Kuerzel FROM Faecher, VLehrerFach WHERE Faecher.id=fachID AND lehrerID=".$id);
		while ($row=mysql_fetch_array($result)) 
		{
			if (isset($faecher)) $faecher.=" / ".$row['Kuerzel'];
			else $faecher=$row['Kuerzel'];
		}
		if (isset($faecher)) return $faecher;
	}

	function checked($id) {
		$result = mysql_query("SELECT schuelerID, lehrerID FROM VSchuelerLehrer WHERE schuelerID=".$_SESSION['id']." AND lehrerID=".$id);
		if ( !mysql_fetch_array($result) ) {
			return "";
		} else {
			return 'checked="checked"';
		}
	}
	echo '<h2>Lehrerauswahl</h2>';
	echo '<h3>Meine Lehrer</h3>';
	echo '<table id="meine">
			<tr>
				<th></th><th>Name</th> <th>Fächer</th> <th>E-Mail</th>
			</tr>';
	$result=mysql_query("SELECT id, LName, LVorname, LEmail FROM Lehrer, VSchuelerLehrer WHERE schuelerID=".$_SESSION['id']." AND lehrerID=id ORDER BY LName");
		while ($row=mysql_fetch_array($result)) {
			 echo '<tr> <td><input type="checkbox" class="lehrer" id="'.$row['id'].'" checked="checked" /></td>';
			 echo '<td>'.$row['LName'].', '.$row['LVorname'].'</td>';
			 echo '<td>'.faecher($row['id']).'</td><td>'.$row['LEmail'].'</td></tr>';
		}
	echo '</table>';

	echo '<h3>Andere Lehrer</h3>';
	echo '<table id="andere">
			<tr>
				<th></th><th>Name</th> <th>Fächer</th> <th>E-Mail</th>
			</tr>';
	$result=mysql_query("SELECT id, LName, LVorname, LEmail FROM Lehrer WHERE id NOT IN (SELECT lehrerID FROM VSchuelerLehrer WHERE schuelerID=$_SESSION[id]) ORDER BY LName");
		while ($row=mysql_fetch_array($result)) {
			 echo '<tr> <td><input type="checkbox" class="lehrer" id="'.$row['id'].'" '.checked($row['id']).' /></td>';
			 echo '<td>'.$row['LName'].', '.$row['LVorname'].'</td>';
			 echo '<td>'.faecher($row['id']).'</td><td>'.$row['LEmail'].'</td></tr>';
		}
	echo '</table>';
	echo '<script src="js/jquery.js" type="text/javascript"> </script>';
	echo '<script src="eltern_lehrer.js" type="text/javascript"> </script>';	
require("./includes/foot.inc.php"); ?>
