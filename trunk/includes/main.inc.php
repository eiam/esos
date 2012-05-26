<?php

if (!isset($install)) {
	require_once("./includes/nice_error.php");
	require_once("./includes/config.inc.php");
	require_once("./includes/benutzer.inc.php");
}

session_start();

$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);
$base = 'http://'.$hostname.($path == '/' ? '' : $path);

$page = basename($_SERVER['PHP_SELF'],".php");
if($page=="") $page = "./";

if(isset($GLOBALS['install'])) {
	$heading = "Installation von ESOS";
} else {
	$heading = readConfig("TITEL");
}
$site_description = "Melden Sie sich f&uuml;r ein Gespr&auml;ch mit den Lehrern ihrer Wahl zum Elternsprechtag an";

$scripts = "";

$menu = "";

function menu_entry($href, $value) {
	global $page, $menu;
	if($href==$page) {
		return '<li class="current"><a href="'.$href.'.php">'.$value.'</a></li>';
	} else {
		return '<li><a href="'.$href.'.php">'.$value.'</a></li>';
	}
}

function makeMenu() {
	global $menu;
	$menu = '<ul id="nav">';
	if(isset($_SESSION["eltern"])) {
		$menu .= menu_entry("eltern_start", "Startseite");
		$menu .= menu_entry("eltern_kinder", "Meine Kinder");
		$menu .= menu_entry("eltern_lehrer", "Lehrer & Termine");
		$menu .= menu_entry("eltern_termin", "Manuelle Terminauswahl");
		$menu .= menu_entry("eltern_ausgabe", "Terminübersicht");
		$menu .= menu_entry("eltern_konto", "Zugangsdaten");
		$menu .= menu_entry("impressum", "Impressum");
		$menu .= menu_entry("logout", "Logout");
	} elseif (isset($_SESSION["lehrer"])) {
		//$menu .= menu_entry("./", "Startseite");
		$menu .= menu_entry("lehrer_ausgabe", "Terminübersicht");
		$menu .= menu_entry("lehrer_faecher", "Fächerauswahl");
		$menu .= menu_entry("lehrer_konto", "Zugangsdaten");
		$menu .= menu_entry("impressum", "Impressum");
		$menu .= menu_entry("logout", "Logout");
	} elseif (isset($_SESSION["admin"])) {
		//$menu .= menu_entry("./", "Startseite");
		$menu .= menu_entry("admin_config", "Konfiguration");
		$menu .= menu_entry("admin_einladung", "Eltern einladen");
		$menu .= menu_entry("admin_lehrereinladung", "Lehrer einladen");
		$menu .= menu_entry("admin_lehrer", "Lehrer");
		$menu .= menu_entry("admin_schueler", "Schüler");
		$menu .= menu_entry("admin_faecher", "Fächer");
		$menu .= menu_entry("admin_pausen", "Pausen");
		$menu .= menu_entry("admin_raeume", "Räume");
		$menu .= menu_entry("admin_teilzeit", "Teilzeitlehrer");
		$menu .= menu_entry("admin_datenbanken", "Datenbanken");
		$menu .= menu_entry("admin_statistik", "Statistik");
		$menu .= menu_entry("admin_konto", "Administratoren");
		$menu .= menu_entry("admin_feedback", "Feedback");
		$menu .= menu_entry("impressum", "Impressum");
		$menu .= menu_entry("logout", "Logout");
	} else {
		$menu .= menu_entry("login", "Login");
		$menu .= menu_entry("impressum", "Impressum");
	}
	$menu .= '</ul>';
	if(isset($GLOBALS['install'])) { $menu = ''; }
}

$gesperrt = '<h1>Derzeit ist kein Elternsprechtag geplant.</h1><p>Für Eltern und Lehrer ist daher derzeit keine Anmeldung im Portal möglich.</p>';

function head() {
	global $heading, $site_description, $scripts, $base, $heading, $menu, $gesperrt;

	header("Content-type: text/html;charset=utf-8");
	header("X-UA-Compatible: IE=Edge,chrome=IE8");

	if ($menu=="") makeMenu();

	$user = "";
	if (isset($_SESSION["id"])&&!isset($GLOBALS['install'])) {
		if (isset($_SESSION["vorname"]) && isset($_SESSION["name"])) {
			$class = (isset($_SESSION["lehrer"]))?'lehrer':'';
			$class = (isset($_SESSION["eltern"]))?'schueler':$class;
			$show = true;
			if (isset($_SESSION["eltern"])) {
				if (count(geschwister($_SESSION["id"]))>1) $show = false;
			}
			if ($show) {
				$user = '<div id="user" class="'.$class.'">'.$_SESSION["vorname"]." ".$_SESSION["name"].'</div>';
			}
		}
	}

?>
<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8" />

    <title><?php echo $heading; ?></title>
    <meta name="description" content="<?php echo $site_description; ?>" />

	<!--[if lt IE 8]>
        <script>var IE7_PNG_SUFFIX = ".png";</script>
        <script src="<?php echo $base.'/design/IE9.js'; ?>"> </script>
    <![endif]-->
	
    <link rel="stylesheet" type="text/css" media="screen, handheld" href="design/style.css" />
    <link rel="stylesheet" type="text/css" media="screen, handheld" href="design/nav.css" />
    <link rel="stylesheet" type="text/css" media="print" href="design/print.css" />
    <link rel="shortcut icon" href="design/favicon.ico" type="image/x-icon" />
    <?php echo $scripts; ?>
  </head>
  <body>
    <!--[if lt IE 8]> <div style=' clear: both; height: 59px; padding:0 0 0 15px; position: relative;'> <a href="http://windows.microsoft.com/de-DE/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode"><img src="./design/IE_warning.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." /></a></div> <![endif]-->
    <div id="header">
      <h1><a href="."><?php echo $heading; ?></a></h1>
    </div>
    <div id="menu">
		<?php echo $user; ?>
		<?php echo $menu; ?>
	</div>
    <div id="content">
		<noscript><strong>Bitte aktivieren Sie in Ihrem Browser JavaScript!</strong></noscript>
	<?php
		if (!isset($GLOBALS['install'])) {
			if(readConfig("OFFEN") != "true" && !isset($_SESSION["admin"])) {
				echo "$gesperrt\n";
			}
		}
	?>
<?php
} // end of function head()
?>
<?php

function foot() {
?>
    </div>
  </body>
</html>
<?php
} // end of function foot()
?>
