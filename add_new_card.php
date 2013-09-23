<div id='add-new-card'>
	<?php 
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once "functions.php";
	$board_id = $_REQUEST['boardid'];
	$cardid = $_REQUEST['cardid'];
	$attributes = getCompleteCardAttributes($board_id);

	foreach ($attributes as $attributeid => $attributedata) {
?>
		<div class="attribute-name">
			<?php echo $attributedata['name']; ?>
		</div>
		<div class="attribute-value">
			<?php echo getInputFieldCardAttribute($board_id, $attributeid, $attributedata['sourceType'], isset($attributedata['source'])?$attributedata['source']:'', ''); ?>
		</div>
<?php
	}
?>
	<input type='hidden' name='saved' id='saved' value='no'>
	<a id="add_button" href="#" onclick='$("#saved").val("yes");' class='action with-text' title='<?php echo translate("Add card"); ?>'><span class='image add'></span><?php echo translate('Add card'); ?></a>
</div>
