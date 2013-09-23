<?php require_once("functions.php"); ?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<title><?php echo $website_name; ?></title>
<?php
$dirname = $_SERVER['REQUEST_URI'];
if (!preg_match('/\/$/', $dirname)) {
	$dirname = dirname($dirname);
}
while ( preg_match('/\.php$/', $dirname) ) {
	$dirname = dirname($dirname);
}
$dirname = rtrim($dirname,"/");
$dirname = rtrim($dirname,"\\");
?>
<?php echo CssCrush::tag($dirname.'/styles/main.css'); ?>		
		<link rel='stylesheet' type='text/css' href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css" />
		<?php echo CssCrush::tag($dirname.'/styles/jqueryui-editable.css'); ?>
		<?php echo CssCrush::tag($dirname.'/styles/jquery.multiselect.css'); ?>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('.sortable').each(function() {
					$(this).sortable();
				});
			});
		</script>
	</head>
