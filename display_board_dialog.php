<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");
require_once("functions.php");

$board_id = trim($_REQUEST["boardid"]);

$board=get_board_details($board_id);
?>
 <div id='display_board_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<h3>
		<?php echo translate('Board export ID prefix'); ?>
	</h3>
	<input type="text" id="export_id_prefix" name="export_id_prefix" size="32" value="<?php echo get_board_export_id_prefix($board_id); ?>" />
	<h3>
		<?php echo translate('Board appearance'); ?>
	</h3>
	<textarea id="board_css" name="board_css" style="height: 300px; width: 600px;">
<?php echo get_board_css($board_id); ?>
	</textarea>
	<h3>
		<?php echo translate('Board Scripting'); ?>
	</h3>
	<textarea id="board_js" name="board_js" style="height: 300px; width: 600px;">
<?php echo get_board_javascript($board_id); ?>
	</textarea>
	<h3>
		<?php echo translate('Card attributes'); ?>
	</h3>
	<textarea id="card_attr" name="card_attr" style="height: 300px; width: 600px;">
<?php echo get_card_attributes($board_id); ?>
	</textarea>
	<h3>
		<?php echo translate('Card appearance'); ?>
	</h3>
	<textarea id="card_css" name="card_css" style="height: 300px; width: 600px;">
<?php echo get_card_css($board_id); ?>
	</textarea>
	<h3>
		<?php echo translate('Card Scripting'); ?>
	</h3>
	<textarea id="card_js" name="card_js" style="height: 300px; width: 600px;">
<?php echo get_card_javascript($board_id); ?>
	</textarea>
	<div id="actions">
		<a id="update_button" href="#" onclick="update_board();" class='action with-text' title='<?php echo translate("Update board"); ?>'><span class='image save'></span><?php echo translate('Update board'); ?></a>
	</div>
	<script type="text/javascript">
		function update_board() {
			var export_id_prefix = $('#export_id_prefix').val();
			var board_css = $('#board_css').val();
			var board_js = $('#board_js').val();
			var card_attr = $('#card_attr').val();
			var card_css = $('#card_css').val();
			var card_js = $('#card_js').val();
			$.ajax({
				type: "POST", 
				url: "display_board_action.php", 
				data: {
					export_id_prefix: export_id_prefix,
					board_css: board_css,
					board_js: board_js,
					card_attr: card_attr,
					card_css: card_css,
					card_js: card_js,
					verbose: 1,
					boardid: <?php echo $board_id; ?>
				},
				success: function(data) {
					data = JSON.parse(data);
					if (!data.error) {
						//parent.$("#popup_dialog").bPopup().close();
						location.reload(true);						
					}
					else {
					   $('#error_message').html(data.messages);
					}
				}
			});
		}
	</script>
</div>
