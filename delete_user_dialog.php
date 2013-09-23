<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");

	$userid = trim($_GET["userid"]);
?>
<div id='delete_user_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<?php echo translate('Press "Delete user" if you really want to delete the user'); ?>
	</p>
	<p>
		<a id="delete_button" href="#" onclick="delete_user();" class='action with-text' title='<?php echo translate("Delete user"); ?>'><span class='image trash'></span><?php echo translate('Delete user'); ?></a>
	</p>
	<script type="text/javascript">
		function delete_user() {
			$.ajax({
				type: "POST", 
				url: "delete_user_action.php", 
				data: {
					verbose: 1,
					userid: <?php echo $userid; ?>
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
