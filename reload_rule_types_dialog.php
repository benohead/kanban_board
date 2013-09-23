<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");

?>
<div id='reload_rule_types_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<?php echo translate('Do you really want to reload all rule types from the installation folder ?'); ?><br>
		<br>
		<?php echo translate('Note that it will cause all changes made to rule types to be lost.'); ?>
	</p>
	<br>
	<p>
		<a id="reload_rule_types" href="#" onclick="reload_rule_types();" class='action with-text' title='<?php echo translate("Reload rule types"); ?>'><img src='models/site-templates/images/reload_from_disk.png'><?php echo translate('Reload'); ?></a>
		<a id="cancel_button" href="#" onclick="close_dialog();" class='action with-text' title='<?php echo translate("Cancel"); ?>'><img src='models/site-templates/images/delete.png'><?php echo translate('Cancel'); ?></a>
		</p>
	<script type="text/javascript">
		function reload_rule_types() {
			$.ajax({
				type: "POST", 
				url: "reload_rule_types_action.php", 
				data: {
					verbose: 1
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
		function close_dialog() {
			parent.$("#popup_dialog").bPopup().close();
		}
	</script>
</div>