<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");

?>
<div id='add_new_project_dialog' class='awkb_dialog'>
	<div id="error_message"></div>

	<p>
		<label><?php echo translate('Project Name:'); ?> </label>
		<input type='text' name='projectname' id='projectname' placeholder='<?php echo translate('Project Name'); ?>'/>
	</p>
	<p>
		<label><?php echo translate('Display Name:'); ?> </label>
		<input type='text' name='displayname' id='displayname' placeholder='<?php echo translate('Display Name'); ?>'/>
	</p>
	<p>
		<label><?php echo translate('Activate:'); ?> </label>
		<input type='checkbox' name='active' id='active' value=1 />
	</p>
	<br>
	<p>
		<a id="add_new_button" href="#" onclick="add_new_project();" class='action with-text' title='<?php echo translate("Add project"); ?>'><span class='image add'></span><?php echo translate('Add project'); ?></a>
	</p>
	<script type="text/javascript">
		function add_new_project() {
			var projectname = $('#projectname').val();
			var displayname = $('#displayname').val();
			var active = $('#active').val();
			$.ajax({
				type: "POST", 
				url: "add_new_project_action.php", 
				data: {
					verbose: 1,
					projectname: projectname,
					displayname: displayname,
					active: active
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