<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");
require_once("functions.php");

$board_id = trim($_REQUEST["boardid"]);
?>
 <div id='customize_columns_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<div id='board-<?php echo $board_id; ?>' class='board'>
		<div class='column-editor'>
			<ul class="sortable">
<?php
	$columns = get_board_columns($board_id);

	foreach ($columns as $columnname => $columndata) {
?>
				<li id="<?php echo $columnname; ?>" class="ui-state-default">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<div class="remove" onclick="javascript:return removeColumn('<?php echo $board_id; ?>', '<?php echo $columnname; ?>');"></div>
					<div class="columnname"><?php echo $columndata['display_name']; ?></div>
					<div class="wip">
						<label><?php echo translate('WIP:'); ?></label>
						<input type="text" class="wip-value" size="2" value="<?php if (isset($columndata['wip_limit'])) { echo $columndata['wip_limit']; } ?>" placeholder='<?php echo translate('None'); ?>'></input>
					</div>
					<div class="description">
						<textarea class="description-text" style="height: 50px; width: 300px;"><?php echo $columndata['description']; ?></textarea>
					</div>
				</li>
<?php
	}
?>
			</ul>
			<input id="columnNameToAdd" type="text" size="20" placeholder='<?php echo translate('Column Name'); ?>'></input>
			<a class='action with-text' title='<?php echo translate("Add column to board"); ?>' onclick="addColumn('<?php echo $board_id; ?>'); $('#board-<?php echo $board_id; ?> #columnNameToAdd').val(''); return false;" href='#'>
				<span class='image add'></span><?php echo translate('Add column'); ?></a>
		</div>
	</div>
	<div id="actions">
		<a class='action with-text' title='<?php echo translate("Save columns"); ?>' onclick="saveColumns('<?php echo $board_id; ?>'); return false;" href='#'>
			<span class='image save'></span><?php echo translate('Save columns'); ?></a>
		<a id="cancel_button" href="#" onclick='parent.$("#card_popup").bPopup().close();' class='action with-text' title='<?php echo translate("Cancel"); ?>'><img src='models/site-templates/images/delete.png'><?php echo translate('Cancel'); ?></a>
	</div>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#customize_columns_dialog .sortable').each(function() {
				$(this).sortable();
			});
		});
	</script>
	<script type="text/javascript" src="scripts/jquery.ui.touch-punch.min.js"></script>
	<script type="text/javascript" src="scripts/jquery.bpopup.min.js"></script>
	<script type="text/javascript" src="scripts/jqueryui-editable.min.js"></script>
	<script type="text/javascript">
		function addColumn(boardid) {				
			var val = $("#board-"+boardid+" #columnNameToAdd").val();
			if (val.length == 0) {
				return;
			}
			var id = val.replace(/[^a-zA-Z0-9]+/g, "").toLowerCase();
			$("#board-"+boardid+" ul.sortable")
				.append('<li id="'+id+'" class="ui-state-default">'+
							'<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'+
							'<div class="remove" onclick="javascript:return removeColumn(\''+boardid+'\',\''+id+'\');"></div>'+
							'<div class="columnname">'+val+'</div>'+
							'<div class="wip">'+
								'<label><?php echo translate('WIP:'); ?></label>'+
								'<input type="text" placeholder="None" class="wip-value" size="2"></input>'+
							'</div>'+
							'<div class="description">'+
								'<textarea class="description-text" style="height: 50px; width: 300px;"></textarea>'+
							'</div>'+
						'</li>');
			$('#'+id+' .columnname').editable({
			    type: 'text',
			});			
		}
		function removeColumn(boardid, id) {
			var moveToTrash=confirm("<?php echo translate('Do you really want to delete this column?'); ?>");
			if (moveToTrash) {
				$("#board-"+boardid+" .sortable #"+id).remove();
			}
			return false;
		}
		function saveColumns(boardid) {				
			var $columns = {};
			$("#board-"+boardid+" ul.sortable li").each(function(){
				var $id = $(this).attr('id');
				var $name = $(this).children('.columnname').html();
				var $wip = $(this).find('.wip .wip-value').val();
				var $description = $(this).find('.description .description-text').val();
				$columns[$id] = {};
				console.log($name);
				$columns[$id]['display_name'] = $name;
				$columns[$id]['description'] = $description;
				$columns[$id]['wip_limit'] = 0;
				if ($wip.length > 0) {
					$columns[$id]['wip_limit'] = parseInt($wip, 10);
				}
			});
			$.ajax({ type: "POST", url: "storeBoardColumns.php", data: {columns: $columns, boardid: boardid}, complete: function() { location.reload(true); }});
		}
	</script>
</div>
