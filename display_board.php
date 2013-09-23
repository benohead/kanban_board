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
			<?php echo translate('Board Details'); ?>
		</h2>
		<?php
		$board_id = trim($_REQUEST["id"]);

		//Forms posted
		if(!empty($_POST))
		{
			$board_css = trim($_REQUEST["board_css"]);
			$board_js = trim($_REQUEST["board_js"]);
			$rules = trim($_REQUEST["rules"]);
			$card_attr = trim($_REQUEST["card_attr"]);
			$card_css = trim($_REQUEST["card_css"]);
			$card_js = trim($_REQUEST["card_js"]);

			set_board_metadata($board_id, $board_css, $board_js, $card_attr, $card_css, $card_js, $rules);
			$successes[] = translate('Board updated');
		}
		?>
		<h2>
			<?php echo get_board_display_name($board_id); ?>
		</h2>
			<?php
			include("left-nav.php");
			?>
		<div id='main'>
			<?php
			echo resultBlock($errors,$successes);

			$board=get_board_details($board_id);
			if (isset($board)) {
?>
			<div>
				<?php echo translate('Short name:'); ?>
				<?php echo $board["board_name"]; ?>
			</div>
			<div>
				<?php echo translate('Display name:'); ?>
				<?php echo $board["display_name"]; ?>
			</div>
			<div>
				<?php echo translate('Active:'); ?>
				<?php
				if ($board["active"] == 0) {
?>
				<a title='<?php echo translate('Activate board'); ?>'
					href='activate_board.php?id=<?php echo $board["id"]; ?>'><span class='image active-0'></span></a>
				<?php
		}
		else {
?>
				<a title='<?php echo translate('Deactivate board'); ?>'
					href='deactivate_board.php?id=<?php echo $board["id"]; ?>'><span class='image active-1'></span></a>
				<?php		
		}
		?>
			</div>
			<div>
				<a title='<?php echo translate('Delete board'); ?>'
					href='delete_board.php?id=<?php echo $board["id"]; ?>'><img src='models/site-templates/images/trash.jpg'></a> <a
					title='<?php echo translate('Clone board'); ?>'
					href='clone_board.php?id=<?php echo $board["id"]; ?>'><img src='models/site-templates/images/clone.png'></a>
			</div>
			<br> <br /> <a href="board_columns_editor.php?id=<?php echo $board_id; ?>"><?php echo translate('Edit board columns'); ?>
			</a> <br /> <a href="board_rules_editor.php?id=<?php echo $board_id; ?>"><?php echo translate('Edit board rules'); ?>
			</a> <br />
			<form name='updateboard' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
				<h3>
					<?php echo translate('Board appearance'); ?>
				</h3>
				<textarea id="board_css" name="board_css"
					style="height: 300px; width: 600px;">
<?php
echo get_board_css($board_id);
?>
</textarea>
				<h3>
					<?php echo translate('Board Scripting'); ?>
				</h3>
				<textarea id="board_js" name="board_js"
					style="height: 300px; width: 600px;">
<?php
echo get_board_javascript($board_id);
?>
</textarea>
				<h3>
					<?php echo translate('Rules'); ?>
				</h3>
				<textarea id="rules" name="rules"
					style="height: 300px; width: 600px;">
<?php
echo get_board_rules($board_id);
?>
</textarea>
				<h3>
					<?php echo translate('Card attributes'); ?>
				</h3>
				<textarea id="card_attr" name="card_attr"
					style="height: 300px; width: 600px;">
<?php
echo get_card_attributes($board_id);
?>
</textarea>
				<h3>
					<?php echo translate('Card appearance'); ?>
				</h3>
				<textarea id="card_css" name="card_css"
					style="height: 300px; width: 600px;">
<?php
echo get_card_css($board_id);
?>
</textarea>
				<h3>
					<?php echo translate('Card Scripting'); ?>
				</h3>
				<textarea id="card_js" name="card_js"
					style="height: 300px; width: 600px;">
<?php
echo get_card_javascript($board_id);
?>
</textarea>
				<input type='hidden' name='id' value='<?php echo $board_id; ?>' /> <input type='submit'
					value='Update' />
			</form>
			<?php
	}
	else {
		echo translate('Board not found !');
	}
	?>
		</div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
