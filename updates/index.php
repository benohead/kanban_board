<?php
require_once ("../models/db-settings.php");
require_once ("../models/funcs.php");
require_once ("../functions.php");
$directory = "";
$updateList = getUpdateList($directory);
if (isset($_REQUEST['return'])) {
	$return = urldecode($_REQUEST['return']);
}
else {
	$return = '../board.php';
}

if (count($updateList) == 0) {
	header('Location: '.$return);
	die();
}
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<title><?php echo translate('amazingweb Kanban'); ?></title>
<?php echo CssCrush::tag(dirname($_SERVER['REQUEST_URI']).'/../styles/main.css'); ?>
</head>
<body>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1><?php echo translate('Updater'); ?></h1>	
<?php

if (isset ($_GET["install"])) {
	foreach ($updateList as $update) {
?>
	<div><?php echo translate('Installing update %1$d', $update); ?></div>
<?php
		require_once($update.'/update.php');
		$db_issues = call_user_func('update_'.$update);
		if ($db_issues) {
?>
			<div><?php echo translate('Failed to install update %1$d', $update); ?></div><br>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>"><?php echo translate('Retry'); ?></a>
<?php		die();
		}
	}
?>
			<div><?php echo translate('All updates installed.'); ?></div><br>
			<a href="<?php echo $return; ?>"><?php echo translate('Continue'); ?></a>
<?php		die();

} 
else {
?>
	<div><?php echo count($updateList); ?> update(s) to be installed.</div><br> 
<?php
?>
	<a href='?install=true'><?php echo translate('Update now !'); ?></a>
<?php
}
?>
</div>
</body>
</html>
