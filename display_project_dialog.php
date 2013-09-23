<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");
	require_once("functions.php");

	$projectid = trim($_GET["projectid"]);

	$users = get_project_users($projectid);
?>
<div id='display_project_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
<?php
	if (isset($users)) {
?>
	<table class='admin'>
		<tr>
			<th><?php echo translate('User name'); ?></th>
			<th><?php echo translate('Access type'); ?></th>
			<th class="actions"><?php echo translate('Actions'); ?></th>
		</tr>
<?php
		foreach ($users as $user) {
?>
		<tr>
			<td><?php echo $user["user_display_name"]; ?></td>
			<td><?php echo $user["access_type"]; ?></td>
			<td><a title='<?php echo translate('Remove from project'); ?>'
				href='delete_board.php?id=<?php echo $user["user_id"]; ?>'><img src='models/site-templates/images/trash.jpg'></a>
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
		echo translate('No user yet assigned to this project.');
?>
	<br>
<?php 
	} 
?>
	<br>
	<a href='add_user_to_project.php?projectid=<?php echo $projectid; ?>'><?php echo translate('Add an existing user to this project.'); ?>
	</a>
</div>
