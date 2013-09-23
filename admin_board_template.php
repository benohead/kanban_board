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
			<?php echo translate('Board Template Details'); ?>
		</h2>
		<?php
		$templateid = trim($_REQUEST["id"]);

		//Forms posted
		if(!empty($_POST))
		{
			$template_data_css = trim($_REQUEST["board_css"]);
			$template_data_js = trim($_REQUEST["board_js"]);

			updateBoardTemplate($templateid, $template_data_css, $template_data_js);
			$successes[] = translate('Board template updated');
		}
		
		$template_data = getBoardTemplate($templateid);
			if (isset($template_data)) {
		?>
		<h2>
			<?php echo $template_data['display_name']; ?>
		</h2>
			<?php
			include("left-nav.php");
			?>
		<div id='main'>
			<?php
			echo resultBlock($errors,$successes);

?>
			<div>
				<?php echo translate('Short name:'); ?>
				<?php echo $template_data["template_name"]; ?>
			</div>
			<div>
				<?php echo translate('Display name:'); ?>
				<?php echo $template_data["display_name"]; ?>
			</div>
			<div>
				<?php echo translate('Active:'); ?>
				<?php
				if ($template_data["active"] == 0) {
?>
				<a title='<?php echo translate('Activate template'); ?>'
					href='activate_board.php?id=<?php echo $templateid; ?>'><span class='image active-0'></span></a>
				<?php
		}
		else {
?>
				<a title='<?php echo translate('Deactivate template'); ?>'
					href='deactivate_board.php?id=<?php echo $templateid; ?>'><span class='image active-1'></span></a>
				<?php		
		}
		?>
			</div>
			<div>
				<a title='<?php echo translate('Delete template'); ?>'
					href='delete_board_template.php?id=<?php echo $templateid; ?>'><img src='models/site-templates/images/trash.jpg'></a> <a
					title='<?php echo translate('Clone template'); ?>'
					href='clone_board_template.php?id=<?php echo $templateid; ?>'><img src='models/site-templates/images/clone.png'></a>
			</div>
			<form name='updateboardtemplate' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
				<h3>
					<?php echo translate('Board columns'); ?>
				</h3>
			<div class='column-editor'>
				<ul class="sortable">
<?php
					$columns = getBoardTemplateColumns($templateid);
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
				<a class='action with-text'
					title='<?php echo translate("Add column to board"); ?>'
					onclick="addColumn(); $('#columnNameToAdd').val(''); return false;"
					href='#'><span class='image add'></span> <?php echo translate('Add column'); ?>
				</a>
			</div>
				<h3>
					<?php echo translate('Board appearance'); ?>
				</h3>
				<textarea id="board_css" name="board_css"
					style="height: 300px; width: 600px;">
<?php
echo $template_data['board_css'];
?>
</textarea>
				<h3>
					<?php echo translate('Board preview'); ?>
				</h3>
<div id="board-template-preview-wrap">
	<iframe id="board-template-preview" src="board_template_preview.php?id=<?php echo $templateid;?>"></iframe>
</div>
				<h3>
					<?php echo translate('Board Scripting'); ?>
				</h3>
				<textarea id="board_js" name="board_js"
					style="height: 300px; width: 600px;">
<?php
echo $template_data['board_js'];
?>
</textarea>
<input type='hidden' name='id' value='<?php echo $templateid; ?>' /> <input type='submit'
					value='Update'
					onclick='saveColumns();' />
			</form>
		</div>
			<?php
	}
	else {
		echo translate('Board template not found !');
	}
	?>
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
				$.ajax({ type: "POST", async: false, url: "storeBoardTemplateColumns.php", data: {columns: $columns, templateid: <?php echo $templateid; ?>}});
			}
	</script>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
