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
			<?php echo translate('Delete board template'); ?>
		</h2>
		<?php
		//Forms posted
		$template_id = trim($_REQUEST["id"]);
		if(!empty($_POST)) {

	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."board_templates WHERE id=?");

	$stmt->bind_param("i", $template_id);
	$stmt->execute();
	$stmt->close();

	$successes[] = translate('Successfully deleted the board template');
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
				<form name='newboard' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>

					<p>
						<?php echo translate('Press "Delete" if you really want to delete the board template'); ?>
					</p>
					<br> <input type='hidden' name='id' value='<?php echo $template_id; ?>' />
					<p>
						<input type='submit' value='<?php echo translate('Delete'); ?>' />
					</p>

				</form>
			</div>
			<?php
}
?>
		</div>
		<div id='bottom'></div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
