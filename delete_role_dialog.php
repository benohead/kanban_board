<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");

	$roleid = trim($_GET["roleid"]);
?>
<div id='delete_role_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<?php echo translate('Press "Delete role" if you really want to delete the role'); ?>
	</p>
	<p>
		<a id="delete_button" href="#" onclick="delete_role();" class='action with-text' title='<?php echo translate("Delete role"); ?>'><span class='image trash'></span><?php echo translate('Delete role'); ?></a>
	</p>
	<script type="text/javascript">
		function delete_role() {
			$.ajax({
				type: "POST", 
				url: "delete_role_action.php", 
				data: {
					verbose: 1,
					roleid: <?php echo $roleid; ?>
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
