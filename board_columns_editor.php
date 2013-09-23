<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");
require_once("functions.php");
require_once("models/header.php");

?>
<body>
	<div id='top'>
		<div id='logo'></div>
	</div>
	<div id='content'>
		<h1>
			<?php echo $website_name; ?>
		</h1>
		<h2>
			<?php echo translate('Board Columns Editor'); ?>
		</h2>
		<?php
		$board_id = trim($_REQUEST["id"]);
		?>
		<h2>
			<?php echo get_board_display_name($board_id); ?>
		</h2>
			<?php
			include("left-nav.php");
			?>
		<div id='main' class='column-editor'>
			<?php
			echo resultBlock($errors,$successes);
			?>
			<h3>
				<?php echo translate('Column order'); ?>
			</h3>
			<ul class="sortable">
<?php
				$columns = get_board_columns($board_id);
				foreach ($columns as $columnname => $columndata) {
?>
				<li id="<?php echo $columnname; ?>" class="ui-state-default">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span><span class="columnname"><?php echo $columndata['display_name']; ?></span>
					<div class="wip">
						WIP:<input type="text" class="wip-value" size="2"
							value="<?php if (isset($columndata['wip_limit'])) { echo $columndata['wip_limit']; } ?>"></input>
					</div>
					<div class="remove" onclick="javascript:return removeColumn('<?php echo $columnname; ?>');"></div>
				</li>
				<?php
				}
				?>
			</ul>
			<input id="columnNameToAdd" type="text" size="20"></input>
			<button type="button" onclick="addColumn(); $('#columnNameToAdd').val(''); return false;">
				<?php echo translate('Add column'); ?>
			</button>
			<br />
			<button type="button" onclick="saveColumns(); return false;">
				<?php echo translate('Save columns'); ?>
			</button>
		</div>
	</div>
	<script>
			function addColumn() {				
				var val = $("#columnNameToAdd").val();
				if (val.length == 0) {
					return;
				}
				var id = val.replace(/[^a-zA-Z0-9]+/g, "");
				$("ul#sortable").append('<li id="'+id+'" class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'+val+'<div class="wip"><?php echo translate('WIP:'); ?><input type="text" class="wip-value" size="2"></input></div><div class="remove" onclick="javascript:return removeColumn("'+id+'");"></div></li>');
			}
			function removeColumn(id) {
				var moveToTrash=confirm("<?php echo translate('Do you really want to delete this column?'); ?>");
				if (moveToTrash) {
					$("#sortable #"+id).remove();
				}
				return false;
			}
			function saveColumns() {				
				var $columns = {};
				$("ul#sortable li").each(function(){
					var $id = $(this).attr('id');
					var $name = $(this).children('.columnname').html();
					var $wip = $(this).find('.wip .wip-value').val();
					$columns[$id] = {};
					$columns[$id]['display_name'] = $name;
					$columns[$id]['wip_limit'] = 0;
					if ($wip.length > 0) {
						$columns[$id]['wip_limit'] = parseInt($wip, 10);
					}
				});
				$.ajax({ type: "POST", url: "storeBoardColumns.php", data: {columns: $columns, boardid: <?php echo $board_id; ?>}});
			}
		</script>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
