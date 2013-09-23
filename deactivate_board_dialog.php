<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");

$board_id = trim($_GET["boardid"]);
?>
<div id='deactivate_board_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<?php echo translate('Press "Deactivate board" if you really want to deactivate the board'); ?>
	</p>
	<p>
		<a id="deactivate_button" href="#" onclick="deactivate_board();" class='action with-text' title='<?php echo translate("Deactivate board"); ?>'><span class='image active-0'></span><?php echo translate('Deactivate board'); ?></a>
	</p>
	<script type="text/javascript">
		function deactivate_board() {
			$.ajax({
				type: "POST", 
				url: "deactivate_board_action.php", 
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
