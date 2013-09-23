<?php require_once("models/config.php"); ?>
<div id='edit-card'>
	<?php 
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("functions.php");
	$board_id = $_REQUEST['boardid'];
	$cardid = $_REQUEST['cardid'];
	$attributes = getCompleteCardAttributes($board_id);
	$cards = get_cards_on_board($board_id);

	if (isset($cards[$cardid])) {
		$card = $cards[$cardid];
		foreach ($attributes as $attributeid => $attributedata) {			
			if ($attributeid <> 'board') {
				$attributeval = isset($card[$attributeid]) ? $card[$attributeid] : ""; 
				$attributeval = preg_replace("/<br>/", "\n", $attributeval);
				?>
	<div class="attribute-name">
		<?php echo $attributedata['name']; ?>
	</div>
	<div class="attribute-value">
		<?php echo getInputFieldCardAttribute($board_id, $attributeid, $attributedata['sourceType'], isset($attributedata['source'])?$attributedata['source']:'', $attributeval); ?>
	</div>
	<?php
			}
		}
	}
	else {
		echo "No card data found !";
	}
?>
	<input type='hidden' name='saved' id='saved' value='no'>
	<div id='actions'>
		<a id="save_button" href="#" onclick="save_card();" class='action with-text' title='<?php echo translate("Save card"); ?>'><span class='image save'></span><?php echo translate('Save card'); ?></a>
		<a id="cancel_button" href="#" onclick="close_dialog();" class='action with-text' title='<?php echo translate("Cancel"); ?>'><img src='models/site-templates/images/delete.png'><?php echo translate('Cancel'); ?></a>
	</div>
	<script type="text/javascript">
		$(".datepicker").each( function(index){
	        var datepicker_default_val = parseDate($(this).val());
	        $(this).datepicker();
	        $(this).datepicker("option", "dateFormat", 'yy-mm-dd');
	        if (datepicker_default_val) {
	        	$(this).datepicker("setDate", formatDate(datepicker_default_val, 'yyyy-MM-dd'));
	        }
	    });
		$(".numeric").numeric();
		
		function save_card() {
			$('#saved').val('yes');
			close_dialog();
		}

		function close_dialog() {
			parent.$("#card_popup").bPopup().close();
		}
	</script>
</div>
