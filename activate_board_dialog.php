<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");

$board_id = trim($_GET["boardid"]);
?>
<div id='activate_board_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<?php echo translate('Press "Activate board" if you really want to activate the board'); ?>
	</p>
	<p>
		<a id="activate_button" href="#" onclick="activate_board();" class='action with-text' title='<?php echo translate("Activate board"); ?>'><span class='image active-1'></span><?php echo translate('Activate board'); ?></a>
	</p>
	<script type="text/javascript">
		function activate_board() {
			$.ajax({
				type: "POST", 
				url: "activate_board_action.php", 
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
