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
		if(!empty($_POST)) {
	$projectid = trim($_POST["id"]);

	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."projects SET active=1 WHERE id=?");

	$stmt->bind_param("i", $projectid);
	$stmt->execute();
	$stmt->close();

	$successes[] = translate('Successfully activated the project');
}
else {
	$projectid = trim($_GET["id"]);
}
?>
			<?php
			include("left-nav.php");
			?>
		<div id='main'>
			<?php
			echo resultBlock($errors,$successes);

			if(empty($_POST)) {
?>
			<div id='regbox'>
				<form name='newProject' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>

					<p>
						<?php echo translate('Press "Activate" if you really want to activate the project'); ?>
					</p>
					<br> <input type='hidden' name='id' value='<?php echo $projectid; ?>' />
					<p>
						<input type='submit' value='<?php echo translate('Activate'); ?>' />
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
