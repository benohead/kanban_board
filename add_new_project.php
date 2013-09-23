<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");
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
			<?php echo translate('Register project'); ?>
		</h2>
		<?php
		//Forms posted
		if(!empty($_POST))
		{
			$projectname = trim($_POST["projectname"]);
			$displayname = trim($_POST["displayname"]);
			if (isset($_POST["active"])) {
				$active = trim($_POST["active"]);
			}
			else {
				$active = 0;
			}

			if(min_max_range(1,50,$projectname)) {
				$errors[] = translate('The project name must have between %1$d and %2$d characters.', 1, 50);
			}
			if(!ctype_alnum($projectname)) {
				$errors[] = translate('The project name must only contain alphanumeric characters.');
			}
			if(min_max_range(1,50,$displayname)) {
				$errors[] = translate('The display name must have between %1$d and %2$d characters.', 1, 50);
			}

			//End data validation
			if(count($errors) == 0)
			{
				//Construct a user object
				$project = new Project($projectname,$displayname,$active);

				//Checking this flag tells us whether there were any errors such as possible data duplication occured
				if(!$project->status) {
					if($project->projectname_taken) $errors[] = translate('Project name already taken');
					if($project->displayname_taken) $errors[] = translate('Display name already taken');
					if (count($errors) == 0) {
						$errors[] = translate('An unknown error occured while preparing to add the project');
					}
				}
				else if(!$project->addProject()) {
					if($project->sql_failure)  $errors[] = translate('Error inserting the project in the database');
					if (count($errors) == 0) {
						$errors[] = translate('An unknown error occured while adding the project');
					}
				}
				else {
					$successes[] = translate('Project "%1$s" successfully created.', $displayname);
				}
			}
		}

		?>
			<?php
			include("left-nav.php");
			?>
		<div id='main'>
			<?php
				echo resultBlock($errors,$successes);
			?>
			<div id='regbox'>
				<form name='newProject' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>

					<p>
						<label><?php echo translate('Project Name:'); ?> </label>
						<input type='text' name='projectname' placeholder='<?php echo translate('Project Name'); ?>'/>
					</p>
					<p>
						<label><?php echo translate('Display Name:'); ?> </label>
						<input type='text' name='displayname' placeholder='<?php echo translate('Display Name'); ?>'/>
					</p>
					<p>
						<label><?php echo translate('Activate:'); ?> </label> <input type='checkbox' name='active'
							value=1 />
					</p>
					<br>
					<p>
						<input type='submit' value='<?php echo translate('Register'); ?>' />
					</p>

				</form>
				<a href='admin_projects.php'><?php echo translate('To the project administration'); ?></a>
			</div>

		</div>
		<div id='bottom'></div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
