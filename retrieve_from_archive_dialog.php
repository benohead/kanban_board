<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");
require_once("functions.php");

$board_id = trim($_REQUEST["boardid"]);

$cards=unserialize_or_empty_array(getArchivedCards($board_id));
?>
 <div id='retrieve_cards_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<table id="archived-cards">
		<tr>
			<td>
				<table class="table-header">
					<tr>
						<th></th>
						<th class='card-number'><?php echo translate('Number'); ?></th>
						<th class='card-title'><?php echo translate('Title'); ?></th>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<div class="overflow-auto">
					<table class="table-body">
<?php
	foreach ( $cards as $cardid => $carddata ) {
?>
						<tr>
							<td><input type="checkbox" name="dearchive[]" value="<?php echo $cardid; ?>"></input></td>
							<td class='card-number'><?php echo $carddata['cardNumber']; ?></td>
							<td class='card-title'><?php echo $carddata['cardTitle']; ?></td>
						</tr>
<?php		
	}
?>
					</table>
				</div>
			</td>
		</tbody>
	</table>
	<div id="actions">
		<a id="retrieve_button" href="#" onclick="retrieve_cards();" class='action with-text' title='<?php echo translate("Retrieve cards"); ?>'><span class='image save'></span><?php echo translate('Retrieve cards'); ?></a>
		<a id="cancel_button" href="#" onclick="close_dialog();" class='action with-text' title='<?php echo translate("Cancel"); ?>'><img src='models/site-templates/images/delete.png'><?php echo translate('Cancel'); ?></a>
	</div>
	<script type="text/javascript">
		function retrieve_cards() {
			var board_cards = new Array();
			$("input:checked").each(function() {
			   board_cards.push($(this).val());
			});
			$.ajax({
				type: "POST", 
				url: "retrieve_from_archive_action.php", 
				data: {
					board_cards: board_cards,
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
		function close_dialog() {
			parent.$("#card_popup").bPopup().close();
		}
	</script>
</div>
