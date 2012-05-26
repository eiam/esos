<?php

function sqlfields($fields) {
	$sqlfields = '';
	$first = true;
	foreach($fields as $field) {
		if(!isset($field['sql_name'])) continue;
		if ($first) { $sqlfields .= '`'.$field['sql_name'].'`'; $first = false; }
		else {
			$sqlfields .= ', `'.$field['sql_name'].'`';
		}
	}
	return $sqlfields;
}

function sqlvalues($fields) {
	$sqlvalues = '';
	$first = true;
	foreach($fields as $field) {
		if ($first) { $sqlvalues .= '"'.$_POST[$field['name']].'"'; $first = false; }
		else {
			$sqlvalues .= ', "'.$_POST[$field['name']].'"';
		}
	}
	return $sqlvalues;
}

function sqlKeyValues($fields) {
	$sqlKeyValues = '';
	$first = true;
	foreach($fields as $field) {
		if(!isset($field['sql_name'])) continue;
		if ($first) {
			$sqlKeyValues .= $field['sql_name'].' = "'.$_POST[$field['name']].'"';
			$first = false;
		} else {
			$sqlKeyValues .= ', '.$field['sql_name'].' = "'.$_POST[$field['name']].'"';
		}
	}
	return $sqlKeyValues;
}

function saveOnPost($fields, $table, $idname) {
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['validate']) ) {
		$valid = true;
		foreach($fields as $field) {
			if(!isset($_POST[$field['name']])) { $valid = false; break; }
		}
		if($valid) {
			foreach($fields as $field) {
				if($_POST[$field['name']]==""&&isset($field['required'])) { $valid = false; break; }
			}
		   	if ($valid) {
				foreach($fields as $field) {
					$_POST[$field['name']] = mysql_real_escape_string($_POST[$field['name']]);
				}
				$table = mysql_real_escape_string($table);
				if (isset($_POST['neu'])) {
					$sqlfields = sqlfields($fields);
					$sqlvalues = sqlvalues($fields);
					$cmd = "INSERT INTO `$table` ($sqlfields) VALUES ($sqlvalues)";
				} elseif (isset($_POST['save'])) {
					$sqlKeyValues = sqlKeyValues($fields);
					$cmd = "UPDATE `$table` SET $sqlKeyValues WHERE id = '$_POST[$idname]'";
				}
				mysql_query($cmd);
			} else {
				echo '<p class="error">Bitte füllen Sie alle benötigten Felder aus.</p>';
				return false;
			}
		} else {
			echo '<p class="error">Es wurden nicht genug Angaben gemacht.</p>';
			return false;
		}
		return true;
	}
	return false;
}

function deleteOnPost($idname, $table) {
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
		$table = mysql_real_escape_string($table);
		$id = mysql_real_escape_string($_POST[$idname]);
		$cmd = "DELETE FROM $table WHERE id = '$id'";
		$result = mysql_query($cmd);
	}
}

function formular($fields, $idname, $table, $success=false, $edit_modus=false) {
	/* Felderwerte initalisieren */
	foreach ($fields as $key => $field) {
		if(!isset($field['value'])) { $fields[$key]['value'] =''; }
	}

	/* Fehlerhafte Daten nocheinmal zeigen */
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['validate']) && !$success) {
		foreach ($fields as $key => $field) {
			if(isset($_POST[$field['name']])) $fields[$key]['value'] = $_POST[$field['name']];
		}
	} elseif($_POST[$idname]!='') {
		/* Felderwerte aus Datenbank lesen */
		$id = mysql_real_escape_string($_POST[$idname]);
		$cmd = "SELECT * FROM `$table` WHERE id = '$id'";
		$result = mysql_query($cmd);
		$values = mysql_fetch_assoc($result);
		foreach ($fields as $key => $field) {
			if(isset($field['sql_name'])&&($fields[$key]['value']==''||$fields[$key]['type']=='password')) { $fields[$key]['value'] = $values[$field['sql_name']]; }
		}
	}

	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	foreach ($fields as $field) {
		echo '<p>';
		echo '	<label for="'.$field['name'].'">'.$field['label'].'</label>';
		if(!isset($field['type'])) $field['type'] = "text";
		// Die If-Abfrage bewirkt, dass, wenn ein Administrator editiert wird, das Passwort nicht auf dem Bildschirm angezeigt wird, beim Neuanlegen aber schon
		if ($field['type']=='password') $field['type'] = ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])? 'password':'text');
		if(!isset($field['attr'])) $field['attr'] = "";
		$value = htmlspecialchars($field['value'], ENT_QUOTES, "UTF-8");
		echo '	<input type="'.$field['type'].'" '.$field['attr'].' name="'.$field['name'].'" value="'.$value.'" />';
		echo '</p>';
	}
	if (($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) || $edit_modus==true) {
		echo '<input type="hidden" name="'.htmlspecialchars($idname, ENT_QUOTES, "UTF-8").'" value="'.htmlspecialchars($_POST[$idname], ENT_QUOTES, "UTF-8").'" />';
		echo '<input type="hidden" name="edit" value="true" />';
		echo '<p><input type="submit" name="save" value="Speichern" /></p>';
	} else {
		echo '<p><input type="submit" name="neu" value="Hinzufügen" /></p>';
	}
	echo '<input type="hidden" name="validate" value="true" />';
	echo '</form>';
}

function data_table($fields, $table, $idname) {
	echo '<table>';
	echo '<thead>';
	echo '<tr>';
	foreach ($fields as $field) {
		echo '<th>'.$field['label'].'</th>';
	}
	echo '</tr>';
	echo '</thead>';

	$cmd = "SELECT `id`, ".sqlfields($fields)." FROM $table";
	$result = mysql_query($cmd);
	while($row = mysql_fetch_assoc($result)) {
		echo '<tr>';
		foreach ($row as $key => $value) {
			if($key=='id') continue;
			echo '<td>'.htmlspecialchars($value, ENT_QUOTES, "UTF-8").'</td>';
		}
		echo '<td><form method="post" action="'.$_SERVER['PHP_SELF'].'">
<input type="hidden" name="'.htmlspecialchars($idname, ENT_QUOTES, "UTF-8").'" value="'.$row["id"].'" />
<input type="hidden" name="edit" value="true" />
<input type="image" src="design/edit.png" width="16" height="16" alt="[Bearbeiten]" /></form></td>';
		echo '<td><form method="post" action="'.$_SERVER['PHP_SELF'].'">
<input type="hidden" name="'.htmlspecialchars($idname, ENT_QUOTES, "UTF-8").'" value="'.$row["id"].'" />
<input type="hidden" name="delete" value="true" />';
		if (mysql_num_rows($result)>1) {
			echo '<input type="image" src="design/trash.png" width="16" height="16" alt="[Löschen]" />';
		}
		echo '</form></td></tr>';
	}
	echo '</table>';
}

function bigaddbutton($label) {
	if(!isset($_POST['neu'])) {
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">
		<p>
		<input type="hidden" name="neu" value="true" />
		<input type="submit" value="'.$label.'" class="bigaddbutton" />
		</p></form>';
	}
}

?>
