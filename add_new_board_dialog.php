<?php
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");
require_once("functions.php");

$projectid = trim($_GET["projectid"]);
?>
<div id='add_new_board_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<label><?php echo translate('Board Name:'); ?> </label> 
		<input type='text' name='boardname' id='boardname' placeholder='<?php echo translate('Board Name'); ?>' />
	</p>
	<p>
		<label><?php echo translate('Display Name:'); ?> </label> 
		<input type='text' name='displayname' id='displayname' placeholder='<?php echo translate('Display Name'); ?>' />
	</p>
	<p>
		<label><?php echo translate('Activate:'); ?> </label> 
		<input type='checkbox' name='active' id='active' value=1 />
	</p>
	<p>
		<label><?php echo translate('Board Template:'); ?> </label> 
		<select name='templateid' id='templateid'>
			<?php
			$templates = fetchActiveTemplates();
			foreach ($templates as $template) {
?>
			<option value='<?php echo $template['id']; ?>'>
				<?php echo $template['display_name']; ?>
			</option>
			<?php
}
?>
		</select>
	</p>
	<p>
		<label><?php echo translate('Card Template:'); ?> </label> 
		<select name='card_template_id' id='card_template_id'>
			<?php
			$card_templates = fetchActiveCardTemplates();
			foreach ($card_templates as $card_template) {
?>
			<option value='<?php echo $card_template['id']; ?>'>
				<?php echo $card_template['display_name']; ?>
			</option>
			<?php
}
?>
		</select>
	</p>
	<p>
		<a id="add_button" href="#" onclick="add_board();" class='action with-text' title='<?php echo translate("Add board"); ?>'><span class='image add'></span><?php echo translate('Add board'); ?></a>
	</p>
	<script type="text/javascript">
		function add_board() {
			var boardname = $('#boardname').val();
			var displayname = $('#displayname').val();
			var active = $('#active').val();
			var templateid = $('#templateid').val();
			var card_template_id = $('#card_template_id').val();
						$.ajax({
				type: "POST", 
				url: "add_new_board_action.php", 
				data: {
					boardname: boardname,
					displayname: displayname,
					active: active,
					templateid: templateid,
					card_template_id: card_template_id,
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
