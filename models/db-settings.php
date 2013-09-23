<?php
require_once (dirname(__FILE__).'/../cssCrush/CssCrush.php');
require_once ('funcs.php');

$dirname = $_SERVER['REQUEST_URI'];
if (!preg_match('/\/$/', $dirname)) {
	$dirname = dirname($dirname);
}
while ( preg_match('/\.php$/', $dirname) ) {
	$dirname = dirname($dirname);
}
$dirname = rtrim($dirname,"/");
$dirname = rtrim($dirname,"\\");

//Database Information
require_once ('db-config.php');

GLOBAL $errors;
GLOBAL $successes;

$errors = array ();
$successes = array ();

GLOBAL $standard_card_attributes;

$standard_card_attributes = array (
	"cardNumber" => array( "name" => "Number", "sourceType" => "NUMERIC" ),
	"cardTitle" => array( "name" => "Task", "sourceType" => "TEXT" ),
	"cardOwner" => array( "name" => "Owner", "sourceType" => "USERS" ),
	"cardDueDate" => array( "name" => "Due Date", "sourceType" => "DATE" ),
	"cardCreationDate" => array( "name" => "Creation Date", "sourceType" => "DATE" ),
	"cardSize" => array( "name" => "Card Size", "sourceType" => "NUMERIC" )
);

/* Create a new mysqli object with database connection parameters */
$mysqli = @ new mysqli($db_host, $db_user, $db_pass, $db_name);
GLOBAL $mysqli;

if (mysqli_connect_errno()) {
?>
<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<title><?php echo translate('amazingweb Kanban'); ?></title>
		<?php echo CssCrush::tag($dirname.'/styles/main.css'); ?>
	</head>
	<body>
		<div id='top'>
			<div id='logo'></div>
		</div>
		<div id='content'>
			<h1><?php echo translate('Installer'); ?></h1>	
			<br />
			<?php echo translate('Please update the file %1$s before starting the installer.', $db_config_path); ?><br />
			<br />
			<?php echo translate('Currently configured:'); ?><br />
			<?php echo translate('Host : %1$s', $db_host); ?><br />
			<?php echo translate('Database: %1$s', $db_name); ?><br />
			<?php echo translate('User : %1$s', $db_user); ?><br />
			<br />
			<?php echo translate('If the data are right, please make sure that the configured database and user exist and are accessible.'); ?><br />
			<br />
			<?php echo translate('Once the required data are entered in the file, please click on this link:'); ?>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>"><?php echo translate('Installer'); ?></a><br />
		</div>
	</body>
</html>
<?php

	die();
}

//Direct to install directory, if it exists
if (is_dir("install/") && !file_exists(".installed")) {
	header("Location: install/");
	die();
}
?>