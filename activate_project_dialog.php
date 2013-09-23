<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");

$projectid = trim($_GET["projectid"]);
?>
<div id='activate_project_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<?php echo translate('Press "Activate project" if you really want to activate the project'); ?>
	</p>
	<p>
		<a id="activate_button" href="#" onclick="activate_project();" class='action with-text' title='<?php echo translate("Activate project"); ?>'><span class='image active-1'></span><?php echo translate('Activate project'); ?></a>
	</p>
	<script type="text/javascript">
		function activate_project() {
			$.ajax({
				type: "POST", 
				url: "activate_project_action.php", 
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
