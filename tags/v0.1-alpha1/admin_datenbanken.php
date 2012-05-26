<?php

$page = "admin_datenbanken";

require("./includes/head.inc.php");
require("./includes/admin_auth.inc.php");
?>
<h2>Datenbank-Upload</h2>
<form action="" method="post">
<label>Schülerdatenbank</label><br/>
<input name="schuelerdb" type="file" size="50"/>
</form>
<br/>
<button>Hochladen</button><br/><br/>
<br/>
<form action="" method="post">
<label>Lehrerdatenbank</label><br/>
<input name="lehrerdb" type="file" size="50"/><br/>
<br/>
<button>Hochladen</button><br/>
<br/>
</form>
<?php
require("./includes/foot.inc.php");

?>
