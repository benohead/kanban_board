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
			<?php echo translate('Deactivate board'); ?>
		</h2>
		<?php
		//Forms posted
		if(!empty($_POST)) {
	$board_id = trim($_POST["id"]);

	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."boards SET active=0 WHERE id=?");

	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->close();

	$successes[] = translate('Successfully deactivated the board');
}
else {
	$board_id = trim($_GET["id"]);
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
						<?php echo translate('Press "Deactivate" if you really want to deactivate the board'); ?>
					</p>
					<br> <input type='hidden' name='id' value='".$board_id."' />
					<p>
						<input type='submit' value='<?php echo translate('Deactivate'); ?>' />
					</p>

				</form>
			</div>
			<?php
}
?>
			<a href='display_board.php?id=<?php echo $board_id; ?>'><?php echo translate('Display board information'); ?>
			</a>
		</div>
		<div id='bottom'></div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
