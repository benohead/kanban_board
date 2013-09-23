<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");
	require_once("functions.php");
	
	$board_id = trim($_REQUEST["boardid"]);
?>
<div id='delete_board_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<?php echo translate('Press "Delete board" if you really want to delete the board'); ?>
	</p>
	<p>
		<a id="delete_button" href="#" onclick="delete_board();" class='action with-text' title='<?php echo translate("Delete board"); ?>'><span class='image trash'></span><?php echo translate('Delete board'); ?></a>
	</p>
	<script type="text/javascript">
	function delete_board() {
		$.ajax({
			type: "POST", 
			url: "delete_board_action.php", 
			data: {
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
