<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");
require_once("functions.php");
require_once("models/header.php");

?>
	<body>
		<div id='top'>
			<div id='logo'></div>
		</div>
		<div id='content'>
			<h1>
				<?php echo $website_name; ?>
			</h1>
			<h2>
				<?php echo translate('Project Details'); ?>
			</h2>
			<?php
			$projectid = trim($_GET["id"]);
			?>
			<h2>
				<?php echo getProjectDisplayName($projectid); ?>
			</h2>
			<?php
			include("left-nav.php");
			?>
			<div id='main'>
				<?php	
				$users = get_project_users($projectid);
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
				<?php } ?>
				</table>
				<br>
				<?php
				}
				else {
					echo translate('No user yet assigned to this project.');
				} 
				?>
				<a href='add_user_to_project.php?projectid=<?php echo $projectid; ?>'><?php echo translate('Add an existing user to this project.'); ?>
				</a>
			</div>
		</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
		</body>
</html>
