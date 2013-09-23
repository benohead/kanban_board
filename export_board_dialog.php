<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("functions.php");
	
	if(!empty($_GET) && isset($_GET["boardid"])) {
		$board_id = trim($_GET["boardid"]);
	}
?>
<div id='export_board_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<div class="border">
		<p>
			<label><?php echo translate('Board Template Name:'); ?> </label>
			<input type='text' name='boardtemplatename' id='boardtemplatename' placeholder='<?php echo translate('Board Template Name'); ?>'/>
		</p>
		<p>
			<label><?php echo translate('Board Template Display Name:'); ?> </label>
			<input type='text' name='boarddisplayname' id='boarddisplayname' placeholder='<?php echo translate('Board Template Display Name'); ?>'/>
		</p>
		<p>
			<label><?php echo translate('Activate:'); ?> </label>
			<input type='checkbox' name='boardactive' id='boardactive' value=1 />
		</p>
	</div>
	<div class="border">
		<p>
			<label><?php echo translate('Card Template Name:'); ?> </label>
			<input type='text' name='cardtemplatename' id='cardtemplatename' placeholder='<?php echo translate('Card Template Name'); ?>'/>
		</p>
		<p>
			<label><?php echo translate('Card Template Display Name:'); ?> </label>
			<input type='text' name='carddisplayname' id='carddisplayname' placeholder='<?php echo translate('Card Template Display Name'); ?>'/>
		</p>
		<p>
			<label><?php echo translate('Activate:'); ?> </label>
			<input type='checkbox' name='cardactive' id='cardactive' value=1 />
		</p>
	</div>
	<p>
		<a id="export_button" href="#" onclick="export_template();" class='action with-text' title='<?php echo translate("Export board"); ?>'><img src='models/site-templates/images/export.png'><?php echo translate('Export board'); ?></a>
	</p>
	<script type="text/javascript">
		function export_template() {
			var boardtemplatename = $('#boardtemplatename').val();
			var boarddisplayname = $('#boarddisplayname').val();
			var boardactive = $('#boardactive').val();
			var cardtemplatename = $('#cardtemplatename').val();
			var carddisplayname = $('#carddisplayname').val();
			var cardactive = $('#cardactive').val();
					$.ajax({
				type: "POST", 
				url: "export_board_action.php", 
				data: {
					boardtemplatename: boardtemplatename, 
					boarddisplayname: boarddisplayname, 
					boardactive: boardactive, 
					cardtemplatename: cardtemplatename, 
					carddisplayname: carddisplayname, 
					cardactive: cardactive, 
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
