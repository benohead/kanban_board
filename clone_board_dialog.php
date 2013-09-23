<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/funcs.php");
require_once("functions.php");

$cloneboardid=$_REQUEST['cloneboardid'];
?>
<div id='clone_board_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<label><?php echo translate('Board Name:'); ?> </label>
		<input type='text' name='boardname' id='clone_boardname' placeholder='<?php echo translate('Board Name'); ?>'/>
	</p>
	<p>
		<label><?php echo translate('Display Name:'); ?> </label>
		<input type='text' name='displayname' id='clone_displayname' placeholder='<?php echo translate('Display Name'); ?>'/>
	</p>
	<p>
		<label><?php echo translate('Activate:'); ?> </label>
		<input type='checkbox' name='active' id='clone_active' value=1 />
	</p>
	<p>
		<label><?php echo translate('Copy cards:'); ?> </label>
		<input type='checkbox' name='copycards' id='clone_copycards' value=1 />
	</p>
	<p>
		<a id="clone_button" href="#" onclick="clone();" class='action with-text' title='<?php echo translate("Clone board"); ?>'><img src='models/site-templates/images/clone.png'><?php echo translate('Clone board'); ?></a>
	</p>
	<script type="text/javascript">
		function clone() {
			var clone_boardname = $('#clone_boardname').val();
			var clone_displayname = $('#clone_displayname').val();
			var clone_active = $('#clone_active').val();
			var clone_copycards = $('#clone_copycards').val();
			$.ajax({
				type: "POST", 
				url: "clone_board_action.php", 
				data: {
					boardname: clone_boardname, 
					displayname: clone_displayname, 
					active: clone_active, 
					copycards: clone_copycards,
					verbose: 1,
					cloneboardid: <?php echo $cloneboardid; ?>
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
