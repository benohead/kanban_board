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
			<?php echo translate('Export board as template'); ?>
		</h2>
		<?php
		//Forms posted
		if(!empty($_GET) && isset($_GET["id"])) {
			$board_id = trim($_GET["id"]);
		}
		else if(!empty($_POST))
		{
			$errors = array();
			$board_id = trim($_POST["boardid"]);
			$boardtemplatename=trim($_POST['boardtemplatename']);
			$boarddisplayname=trim($_POST['boarddisplayname']);
			if (isset($_POST["boardactive"])) {
				$boardactive = trim($_POST["boardactive"]);
			}
			else {
				$boardactive = 0;
			}
			$cardtemplatename=trim($_POST['cardtemplatename']);
			$carddisplayname=trim($_POST['carddisplayname']);
			if (isset($_POST["cardactive"])) {
				$cardactive = trim($_POST["cardactive"]);
			}
			else {
				$cardactive = 0;
			}

			if(min_max_range(0,50,$boardtemplatename)) {
				$errors[] = translate('The board template name must have between %1$d and %2$d characters.', 0, 50);
			}
			if(!ctype_alnum($boardtemplatename)) {
				$errors[] = translate('The board template name must only contain alphanumeric characters.');
			}
			if(min_max_range(0,50,$boarddisplayname)) {
				$errors[] = translate('The board template display name must have between %1$d and %2$d characters.', 0, 50);
			}

			if(min_max_range(0,50,$cardtemplatename)) {
				$errors[] = translate('The card template name must have between %1$d and %2$d characters.', 0, 50);
			}
			if(!ctype_alnum($cardtemplatename)) {
				$errors[] = translate('The card template name must only contain alphanumeric characters.');
			}
			if(min_max_range(0,50,$carddisplayname)) {
				$errors[] = translate('The card template display name must have between %1$d and %2$d characters.', 0, 50);
			}

			if(strlen($boardtemplatename)==0 && strlen($cardtemplatename)==0) {
				$errors[] = translate('Both the board template name and the board template name cannot be empty.');
			}

			//End data validation

			if(count($errors) == 0) {
				if (strlen($boardtemplatename)>0) {
					$result = createBoardTemplateFromBoard($board_id, $boardtemplatename, $boarddisplayname, $boardactive);					
					if($result == 1) {
						$errors[] = translate('Board template name already exists.');
					}
									if($result == 2) {
						$errors[] = translate('Board template display name already exists.');
					}
					if($result == 3) {
						$errors[] = translate('SQL syntax error while creating the board template.');
					}
					if($result == 4) {
						$errors[] = translate('Failed to create board template.');
					}
				}
			}
			if(count($errors) == 0) {
				if (strlen($cardtemplatename)>0) {
					$result = createCardTemplateFromBoard($board_id, $cardtemplatename, $carddisplayname, $cardactive);
					if($result == 1) {
						$errors[] = translate('Card template name already exists.');
					}
					if($result == 3) {
						$errors[] = translate('SQL syntax error while creating the card template.');
					}
					if($result == 4) {
						$errors[] = translate('Failed to create card template.');
					}
									}
			}
			if(count($errors) == 0) {
				$successes[] = translate('Template(s) successfully created.');
			}
		}
		include("left-nav.php");
		?>
		<div id='main'>
			<?php
			echo resultBlock($errors,$successes);
			?>
			<div id='regbox'>
				<form name='exportboard' id='exportboard' action='<?php echo $_SERVER['PHP_SELF']; ?>'
					method='post'>
					<div class="border">
						<p>
							<label><?php echo translate('Board Template Name:'); ?> </label>
							<input type='text' name='boardtemplatename' placeholder='<?php echo translate('Board Template Name'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('Board Template Display Name:'); ?> </label>
							<input type='text' name='boarddisplayname' placeholder='<?php echo translate('Board Template Display Name'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('Activate:'); ?> </label>
							<input type='checkbox' name='boardactive' value=1 />
						</p>
					</div>
					<div class="border">
						<p>
							<label><?php echo translate('Card Template Name:'); ?> </label>
							<input type='text' name='cardtemplatename' placeholder='<?php echo translate('Card Template Name'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('Card Template Display Name:'); ?> </label>
							<input type='text' name='carddisplayname' placeholder='<?php echo translate('Card Template Display Name'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('Activate:'); ?> </label>
							<input type='checkbox' name='cardactive' value=1 />
						</p>
					</div>
					<p>
						<br> <input type='hidden' name='boardid' value='<?php echo $board_id; ?>' /> <input
							type='submit' value='<?php echo translate('Export'); ?>' />
					</p>

				</form>
				<a href='display_board.php?id=<?php echo $board_id; ?>'><?php echo translate('Back to the board details'); ?>
				</a>
			</div>

		</div>
		<div id='bottom'></div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
