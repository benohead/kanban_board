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
			<?php echo translate('Clone board'); ?>
		</h2>
		<?php
		//Forms posted
		if(!empty($_GET) && isset($_GET["id"])) {
			$cloneboardid = trim($_GET["id"]);
			$projectid = getBoardProjectId($cloneboardid);
		}
		else if(!empty($_POST))
		{
			include('clone_board_action.php');
		}
		else {
			$projectid = trim($_GET["projectid"]);
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
				<form name='newboard' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
<?php include('clone_board_dialog.php'); ?>
					<p>
						<br> <input type='hidden' name='cloneboardid' value='<?php echo $cloneboardid; ?>' /> <input
							type='hidden' name='projectid' value='<?php echo $projectid; ?>' /> <input type='submit'
							value='<?php echo translate('Register'); ?>' />
					</p>

				</form>
				<a href='display_project.php?id=<?php echo $projectid; ?>'><?php echo translate('Back to the project details'); ?>
				</a>
			</div>

		</div>
		<div id='bottom'></div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
