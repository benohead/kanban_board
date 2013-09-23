<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");

$projectid = trim($_GET["projectid"]);
?>
<div id='deactivate_project_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<?php echo translate('Press "Deactivate project" if you really want to deactivate the project'); ?>
	</p>
	<p>
		<a id="deactivate_button" href="#" onclick="deactivate_project();" class='action with-text' title='<?php echo translate("Deactivate project"); ?>'><span class='image active-0'></span><?php echo translate('Deactivate project'); ?></a>
	</p>
	<script type="text/javascript">
		function deactivate_project() {
			$.ajax({
				type: "POST", 
				url: "deactivate_project_action.php", 
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
