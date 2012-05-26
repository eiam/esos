<?php

function saveOnPost($fields, $table, $idname) {
	if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['neu']) || isset($_POST['save']) ) ) {
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
					$sqlfields = '';
					$first = true;
					foreach($fields as $field) {
						if ($first) { $sqlfields .= '`'.$field['sql_name'].'`'; $first = false; }
						else {
							$sqlfields .= ', `'.$field['sql_name'].'`';
						}
					}
					$sqlvalues = '';
					$first = true;
					foreach($fields as $field) {
						if ($first) { $sqlvalues .= '"'.$_POST[$field['name']].'"'; $first = false; }
						else {
							$sqlvalues .= ', "'.$_POST[$field['name']].'"';
						}
					}
					$cmd = "INSERT INTO `$table` ($sqlfields) VALUES ($sqlvalues)";
				} elseif (isset($_POST['save'])) {
					$sqlKeyValues = '';
					$first = true;
					foreach($fields as $field) {
						if ($first) {
							$sqlKeyValues .= $field['sql_name'].' = "'.$_POST[$field['name']].'"';
							$first = false;
						} else {
							$sqlKeyValues .= ', '.$field['sql_name'].' = "'.$_POST[$field['name']].'"';
						}
					}
					$cmd = "UPDATE `$table` SET $sqlKeyValues WHERE id = '$_POST[$idname]'";
				}
				mysql_query($cmd);
			} else {
				echo "<p>Bitte füllen Sie alle benötigten Felder aus.</p>";
				return false;
			}
		} else {
			echo "<p>Es wurden nicht genug Angaben gemacht.</p>";
			return false;
		}
	}
	return true;
}

function deleteOnPost($idname, $table) {
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
		$table = mysql_real_escape_string($table);
		$id = mysql_real_escape_string($_POST[$idname]);
		$cmd = "DELETE FROM $table WHERE id = '$id'";
		$result = mysql_query($cmd);
	}
}

function formular($fields, $idname) {
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	foreach ($fields as $field) {
		echo '<p>';
		echo '	<label for="'.$field['name'].'">'.$field['label'].'</label>';
		if(!isset($field['type'])) $field['type'] = "text";
		if(!isset($field['attr'])) $field['attr'] = "";
		$value = htmlspecialchars($field['value'], ENT_QUOTES, "UTF-8");
		echo '	<input type="'.$field['type'].'" '.$field['attr'].' name="'.$field['name'].'" value="'.$value.'" />';
		echo '</p>';
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
		echo '<input type="hidden" name="'.htmlspecialchars($idname, ENT_QUOTES, "UTF-8").'" value="'.htmlspecialchars($_POST[$idname], ENT_QUOTES, "UTF-8").'" />';
		echo '<p><input type="submit" name="save" value="Speichern" /></p>';
	} else {
		echo '<p><input type="submit" name="neu" value="Hinzufügen" /></p>';
	}
	echo '</form>';
}
?>
