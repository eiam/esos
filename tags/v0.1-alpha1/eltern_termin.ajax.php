<?php
    require 'includes/mysql.inc.php';
    require 'includes/termine.inc.php';

    function insert($lehrer, $schueler) {
		$cmd = "SELECT Zeit 
			FROM VTermine
			WHERE schuelerID=".$schueler." ORDER BY Zeit";
		$result = mysql_query($cmd);
		$belegt = array();
		while($row = mysql_fetch_array($result)) {
			$belegt[] = substr($row["Zeit"],0,5);
		}
	
		global $startzeit, $anzahl_termine, $akt_minute, $akt_stunde, $gespraechsdauer;
	
		$akt_minute = $startzeit['minuten'];
		$akt_stunde = $startzeit['stunde'];
	
		while(true) {
			$akt_minute=(strlen((string)$akt_minute)==1?'0':'').$akt_minute;
			$zeit=$akt_stunde.":".$akt_minute;
			if(in_array($zeit, $belegt)) {
				$akt_minute+=$gespraechsdauer;
				if ($akt_minute >= 60) {
					$akt_stunde+=1;
					$akt_minute-=60;
				}
			}
			else break;	
		}

		$cmd = "INSERT INTO VTermine (schuelerID, lehrerID, Zeit) VALUES('".$schueler."', '".$lehrer."', '".$zeit."')";
		mysql_query($cmd);
	}


    

    if (isset($_POST['Lid'], $_POST['sid'], $_POST['zeitabfrage'])) {
    	insert($_POST['Lid'], $_POST['sid']);
    } else if (isset($_POST['terminspeichern'], $_POST['tid'], $_POST['zeit'])) {
        $befehl = "UPDATE VTermine SET Zeit='".$_POST['zeit']."' WHERE Tid=".$_POST['tid'];
        mysql_query($befehl);
    } else if (isset($_POST['loeschen'], $_POST['tid'])) {
        $befehl = "DELETE FROM `VTermine` WHERE Tid=".$_POST['tid'];
        mysql_query($befehl);
		echo $befehl;
	}
	else if (isset($_POST['reload'])) {
    	table($_POST['sid']);
	}
	else if (isset($_POST['reloadLehrer'])) {
    	lehrer($_POST['sid']);
	}




?>
