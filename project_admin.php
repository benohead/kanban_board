<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/db-settings.php");
require_once("functions.php");

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<?php echo CssCrush::tag(dirname($_SERVER['REQUEST_URI']).'/styles/main.css'); ?>
</head>
<body>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1><?php echo $website_name; ?></h1>
<h2><?php echo translate('Project Administration'); ?></h2>	
<?php
include("left-nav.php");
?>
<div id='main'>
<?php
	$projects=get_all_projects();
	if (isset($projects)) {
?>
		<table class='admin'>
			<tr>
				<th><?php echo translate('Short name'); ?></th>
				<th><?php echo translate('Display name'); ?></th>
				<th><?php echo translate('Active'); ?></th>
				<th class="actions"><?php echo translate('Actions'); ?></th>
			</tr>
<?php
		foreach ($projects as $project) {
?>
			<tr>
			<td><?php echo $project["project_name"]; ?></td>
			<td><?php echo $project["display_name"]; ?></td>
<?php			
			if ($project["active"] == 0) {
?>
				<td><a href='activate_project.php?id=<?php echo $project["id"]; ?>'><span class='image active-0'></span></a></td>
<?php
			}
			else {
?>
				<td><a href='deactivate_project.php?id=<?php echo $project["id"]; ?>'><span class='image active-1'></span></a></td>
<?php
			}
?>
			<td>
			    <a href='delete_project.php?id=<?php echo $project["id"]; ?>'><img src='models/site-templates/images/trash.jpg'></a>
			    <a href='display_project.php?id=<?php echo $project["id"]; ?>'><span class='image display'></span></a>
			</td>
			</tr>
<?php
		}
?>
		</table>
		<br>
<?php		
	}
	else {
		echo "No project yet available.";
	}
?>
	<a href='add_new_project.php'><?php echo translate('Add a new project'); ?></a>
	</div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>