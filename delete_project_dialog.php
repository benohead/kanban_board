<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");

	$projectid = trim($_GET["projectid"]);
?>
<div id='delete_project_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<?php echo translate('Press "Delete project" if you really want to delete the project'); ?>
	</p>
	<p>
		<a id="delete_button" href="#" onclick="delete_project();" class='action with-text' title='<?php echo translate("Delete project"); ?>'><span class='image trash'></span><?php echo translate('Delete project'); ?></a>
	</p>
	<script type="text/javascript">
		function delete_project() {
			$.ajax({
				type: "POST", 
				url: "delete_project_action.php", 
				data: {
					verbose: 1,
					projectid: <?php echo $projectid; ?>
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
