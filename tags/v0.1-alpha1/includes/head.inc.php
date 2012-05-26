<?php

error_reporting(-1);

session_start();

ob_start();

$GLOBALS["errors"] = "";

function error_handler($errno, $errstr, $errfile, $errline ) {
  $GLOBALS["errors"] .= '<div class="php-error">';
  $GLOBALS["errors"] .= "<strong>Fehler:</strong> [$errno] $errstr<br />\n";
  $GLOBALS["errors"] .= "<strong>Zeile:</strong> $errline, <strong>Datei:</strong> $errfile<br />";
  $GLOBALS["errors"] .= "PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
  $GLOBALS["errors"] .= '</div>';
}
set_error_handler("error_handler");

$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);

if (!isset($page)) {
	$page = "";
}

if ( ($page!="login" && $page!="impressum" && $page!="passwort-vergessen" && $page!="passwort-anzeigen")
	&& !isset($_SESSION["eltern"]) && !isset($_SESSION["lehrer"]) && !isset($_SESSION["admin"]) )
{
	header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/login.php');
}

$title = "Willkommen";
$heading = "Elternsprechtag am LMGU";
$site_description = "Melden Sie sich für ein Gespräch mit den Lehrern ihrer Wahl zum Elternsprechtag an";

$user = '';

if (isset($_SESSION["schueler"])) {
	foreach ($_SESSION["schueler"] as $kind) {
		$user .= '<div id="user">'.$kind["vorname"]." ".$kind["name"]." (".$kind["klasse"].')</div>';
	}
} elseif (isset($_SESSION["vorname"]) && isset($_SESSION["name"])) {
  $user = '<div id="user">'.$_SESSION["vorname"]." ".$_SESSION["name"].'</div>';
}

$menu = "";

function menu_entry($href, $value) {
	global $page, $menu;
	if($href==$page) {
		return '<li class="current"><a href="'.$href.'.php">'.$value.'</a></li>';
	} else {
		return '<li><a href="'.$href.'.php">'.$value.'</a></li>';
	}
}

$menu = '<ul id="nav">';
if(isset($_SESSION["eltern"])) {
  //$menu .= menu_entry("./", "Startseite");
  $menu .= menu_entry("eltern_lehrer", "Lehrerauswahl");
  $menu .= menu_entry("eltern_termin", "Terminauswahl");
  $menu .= menu_entry("eltern_ausgabe", "Terminübersicht");
  $menu .= menu_entry("eltern_benutzer", "Benutzer verbinden");
  $menu .= menu_entry("impressum", "Impressum");
  $menu .= menu_entry("logout", "Logout");
} elseif (isset($_SESSION["lehrer"])) {
  //$menu .= menu_entry("./", "Startseite");
  $menu .= menu_entry("lehrer_ausgabe", "Terminübersicht");
  $menu .= menu_entry("impressum", "Impressum");
  $menu .= menu_entry("logout", "Logout");
} elseif (isset($_SESSION["admin"])) {
  //$menu .= menu_entry("./", "Startseite");
  $menu .= menu_entry("admin_einladung", "Einladungen");
  $menu .= menu_entry("admin_config", "Konfiguration");
  $menu .= menu_entry("admin_datenbanken", "Datenbanken");
  $menu .= menu_entry("impressum", "Impressum");
  $menu .= menu_entry("logout", "Logout");
} else {
  $menu .= menu_entry("login", "Login");
  $menu .= menu_entry("impressum", "Impressum");
}
$menu .= '</ul>';

?>

<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8" />

    <title><?php echo $title; ?> - <?php echo $heading; ?></title>
    <meta name="description" content="<?php echo $site_description; ?>" />

    <link rel="stylesheet" type="text/css" media="screen, handheld" href="design/style.css" />
	<link rel="stylesheet" type="text/css" media="print" href="design/print.css" />
	<!--[if lt IE 9]>
	<script src="design/IE9.js"></script>
	<![endif]-->
  </head>
  <body>
    <div id="header">
      <h1><a href="."><?php echo $heading; ?></a></h1>
    </div>
    <div id="menu">
		<?php echo $user; ?>
		<?php echo $menu; ?>
	</div>
    <div id="content">

<?php

/*

Alternative:

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
	<head>
		<title>Elternsprechtag-Anmeldung</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta name="author" content="P-Seminar Informatik 2011/12" />
		<link rel="stylesheet" type="text/css" href="style.css" media="screen" />
	</head>
	<body>

*/

?>
