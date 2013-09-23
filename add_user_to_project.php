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
			<?php echo translate('Add user to project'); ?>
		</h2>
		<?php
		//Forms posted
		if(!empty($_POST)) {
			$projectid = trim($_POST["projectid"]);
			$userids = $_POST["userids"];
			echo print_r($userids);
		}
		else {
			$projectid = trim($_GET["projectid"]);
		}
		include("left-nav.php");
?>
		<div id='main'>
<?php
			echo resultBlock($errors,$successes);

			if(empty($_POST)) {
?>
			<div id='regbox'>
				<form name='add_userToProject' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
<?php
			$users = get_non_project_users($projectid);
?>
					<p>
						<select name='userids[]' size="<?php echo min(count($users), 10); ?>" multiple>
<?php			
							foreach ($users as $userid => $userdata) {
?>
								<option value="<?php echo $userid; ?>"><?php echo $userdata['user_display_name']; ?></option>		
<?php
							}
?>
						</select>
					</p>
					<p>
						<select name='access_type'>
							<option value='R'><?php echo translate("Read-only"); ?></option>
							<option value='N'><?php echo translate("Normal user"); ?></option>
							<option value='A'><?php echo translate("Administrator"); ?></option>
						</select>
					</p>
					<input type='hidden' name='projectid' value='<?php echo $projectid; ?>' />
					<p>
						<input type='submit' value='<?php echo translate('Add user(s)'); ?>' />
					</p>
				</form>
			</div>
<?php
			}
?>
			<a href='admin_projects.php'><?php echo translate('To the project administration'); ?></a>
		</div>
		<div id='bottom'></div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
