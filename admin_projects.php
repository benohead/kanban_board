<?php
require_once ("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])) {
	die();
}

require_once ("models/db-settings.php");
require_once ("functions.php");
require_once ("models/header.php");

//Forms posted
if (!empty ($_POST)) {
}
?>
<body>
	<div id='wrapper'>
		<div id='top'>
			<div id='logo'></div>
		</div>
		<div id='content'>
			<h1><?php echo $website_name; ?></h1>
			<h2><?php echo translate('Admin Projects'); ?></h2>
<?php
			include("left-nav.php");
?>
			<div id='main'>
<?php
				echo resultBlock($errors, $successes);
?>
				<div class='admin-container'>
					<div class='header left dark'>
						<a class='action with-text' title='<?php echo translate("Add new project"); ?>'
							href='#' onclick='add_new_project();'><span class='image add'></span><?php echo translate('New Project'); ?>
						</a>
					</div>
					<div id="projects">
<?php
						$ruleTypes = getActiveRuleTypes();
						$generatorTypes = getActiveGeneratorTypes();
						if (!isset($generatorTypes)) {
							$generatorTypes = array();
						}					
?>
						<script>
							function removeCardAttribute(boardid, id) {
								var moveToTrash=confirm("<?php echo translate('Do you really want to delete this card attribute?'); ?>");
								if (moveToTrash) {
									$("#board-"+boardid+" #attributes #"+id).remove();
								}
								return false;
							}
							function saveAttributes(boardid) {				
								var $card_attributes = {};
								$("#board-"+boardid+" #attributes .card-attribute").each(function(){
									var $id = $(this).attr('id');
									var $name = $(this).find('.name').val();
									var $sourceType = $(this).find('.sourceType').val();
									var $source = $(this).find('.source').val();
									var $tooltip = $(this).find('.tooltip').val();
									var $hideoncolumns = $.map( $(this).find('.hideoncolumns option:selected'), function(e) { return $(e).val(); } ); 
										//$(this).find('.hideoncolumns').val();
									$card_attributes[$id] = {};
									$card_attributes[$id]['name'] = $name;
									$card_attributes[$id]['sourceType'] = $sourceType;
									$card_attributes[$id]['source'] = $source;
									$card_attributes[$id]['tooltip'] = $tooltip;
									$card_attributes[$id]['hideoncolumns'] = $hideoncolumns;
								});
								$.ajax({ type: "POST", url: "storeBoardCardAttributes.php", data: {attributes: $card_attributes, boardid: boardid}, complete: function() { location.reload(true); }});
							}
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
							function removeRule(boardid, id) {
								var moveToTrash=confirm("<?php echo translate('Do you really want to delete this rule?'); ?>");
								if (moveToTrash) {
									$("#board-"+boardid+" #rules #"+id).remove();
								}
								return false;
							}
							function removeGenerator(boardid, id) {
								var moveToTrash=confirm("<?php echo translate('Do you really want to delete this generator?'); ?>");
								if (moveToTrash) {
									$("#board-"+boardid+" #generators #"+id).remove();
								}
								return false;
							}
							function saveGenerators(boardid) {
								var $generators = {};
								$("#board-"+boardid+" #generators .generator").each(function(){
									var $id = $(this).attr('id');
									$generators[$id] = {};
									$generators[$id]['generatorType'] = $(this).children('.generatorType').val();					
									$generators[$id]['column'] = $(this).children('.column').val();					
									$generators[$id]['attribute'] = $(this).children('.attribute').val();
									$generators[$id]['generatorTrigger'] = $(this).children('.generatorTrigger').val();
									if ($.trim($generators[$id]['value']) == "") {
										$generators[$id]['value'] = "-";
									}
								});
								$.ajax({ type: "POST", url: "storeBoardGenerators.php", data: {generators: $generators, boardid: boardid}, complete: function() { location.reload(true); }});
								return $generators;
							}
							function saveRules(boardid) {
								var $rules = {};
								$("#board-"+boardid+" #rules .rule").each(function(){
									var $id = $(this).attr('id');
									$rules[$id] = {};
									$rules[$id]['ruleType'] = $(this).children('.ruleType').val();					
									$rules[$id]['column'] = $(this).children('.column').val();					
									$rules[$id]['attribute'] = $(this).children('.attribute').val();
									$rules[$id]['value'] = $(this).children('.value').val();
									if ($.trim($rules[$id]['value']) == "") {
										$rules[$id]['value'] = "-";
									}
								});
								$.ajax({ type: "POST", url: "storeBoardRules.php", data: {rules: $rules, boardid: boardid}, complete: function() { location.reload(true); }});
								return $rules;
							}
						</script>
<?php
						$projects = get_all_projects();
						if (!isset($projects)) {
							$projects = array();
						}
						foreach ($projects as $projectid => $projectdata) {
?>
						<div id='project-<?php echo $projectid; ?>' class='project'>
							<div class='header'>
								<div class='collapse collapse-project'></div>
								<span class='project-display-name'><?php echo $projectdata['display_name']; ?> </span>
								<?php if ($projectdata["active"] == 0) { ?>
								<a class='action' title='<?php echo translate("Project not active. Activate it."); ?>'
									href='#' onclick='activate_project("<?php echo $projectdata["id"]; ?>");'><span class='image active-0'></span></a>
								<?php } else { ?>
								<a class='action' title='<?php echo translate("Project active. Deactivate it."); ?>'
									href='#' onclick='deactivate_project("<?php echo $projectdata["id"]; ?>");'><span class='image active-1'></span></a>
								<?php } ?>
								<a class='action' title='<?php echo translate("Delete project"); ?>'
									href='#' onclick='delete_project("<?php echo $projectdata["id"]; ?>");'><span class='image trash'></span></a>
								<a class='action'
									title='<?php echo translate("Display project"); ?>'
									href='#' onclick='display_project("<?php echo $projectdata["id"]; ?>");'><span class='image display'></span></a>
								<a class='action with-text'
									title='<?php echo translate("Add new board to project"); ?>'
									href='#' onclick='add_new_board("<?php echo $projectid; ?>");'>
									<span class='image add'></span><?php echo translate('New board'); ?></a>
							</div>
							<div class='boards'>
<?php
							$boards = get_all_boards_in_project($projectid);
							if (!isset($boards)) {
							$boards = array();
						}
						foreach ($boards as $board_id => $board) {
							$attributes = getCompleteCardAttributes($board_id);
							$columns = get_board_columns($board_id);
?>
							<script type="text/javascript">
								function addCardAttribute_<?php echo $board_id; ?>() {				
									var val = $("#board-<?php echo $board_id; ?> #attributeNameToAdd").val();
									if (val.length == 0) {
										return;
									}
									var id = val.replace(/[^a-zA-Z0-9]+/g, "").toLowerCase();
									$("#board-<?php echo $board_id; ?> #attributes")
										.append('<div class=\'card-attribute\' id=\''+id+'\'>'+
													'<div onclick="javascript:return removeCardAttribute(\'<?php echo $board_id; ?>\',\''+id+'\');" class=\'remove\'></div>'+
													'<input type=\'text\' value=\''+val+'\' placeholder=\'Attribute name\' size=\'20\' class=\'name\'>'+
														'<select class=\'sourceType\'>'+
															'<option selected=\'selected\' value=\'TEXT\'>Multi-line text</option>'+
															'<option value=\'SQL\'>SQL command</option>'+
															'<option value=\'RANGE\'>List of values</option>'+
															'<option value=\'STRING\'>Single line text</option>'+
															'<option value=\'NUMERIC\'>Numeric</option>'+
															'<option value=\'DATE\'>Date</option>'+
															'<option value=\'USERS\'>List of users</option>'+
															'<option value=\'CHECKBOX\'>Checkbox</option>'+
													'</select>'+
													'<input type=\'text\' value=\'\' class=\'source\' size=\'30\'>'+
													'<?php 
														$attributesArray = array_merge(array('DEFAULT' => translate('Default tooltip')), extractArrayFromArray($attributes, 'name'));
														$attributesCombobox3 = createDropDownFromArray($attributesArray, '', 'tooltip');
														echo jsAddSlashes($attributesCombobox3); 
													?>'+
													'<?php 
														echo translate(' hide on: ');
														$attributesArray = extractArrayFromArray($columns, 'display_name');
														$columnsCombobox2 = createDropDownFromArray($attributesArray, '', 'hideoncolumns', TRUE);
														echo jsAddSlashes($columnsCombobox2); 
													?>'+
										'</div>');
								}
							</script>
							<div id='board-<?php echo $board_id; ?>' class='board'>
									<div class='board-header'>
										<div class='collapse collapse-board'></div>
										<span><?php echo $board["display_name"]; ?> </span>
										<?php if ($board["active"] == 0) { ?>
										<a class='action' title='<?php echo translate('Board not active. Activate board.'); ?>' href='#' onclick='activate_board("<?php echo $board["id"]; ?>");'>
											<span class='image active-0'></span>
										</a>
										<?php } else { ?>
										<a class='action' title='<?php echo translate('Board active. Deactivate board.'); ?>' href='#' onclick='deactivate_board("<?php echo $board["id"]; ?>");'>
											<span class='image active-1'></span>
										</a>
										<?php } ?>
										<a class='action' title='<?php echo translate('Delete board'); ?>' href='#' onclick='delete_board("<?php echo $board["id"]; ?>");'>
											<span class='image trash'></span>
										</a>
										<a class='action' title='<?php echo translate('Display board info'); ?>' href='#' onclick='display_board("<?php echo $board["id"]; ?>");'>
											<span class='image display'></span>
										</a>
										<a class='action' title='<?php echo translate('Clone board'); ?>' href='#' onclick='clone_board("<?php echo $board["id"]; ?>");'>
											<img src='models/site-templates/images/clone.png'>
										</a>
										<a class='action' title='<?php echo translate('Export as template'); ?>' href='#' onclick='export_board("<?php echo $board["id"]; ?>");'>
											<img src='models/site-templates/images/export.png'>
										</a>
										<!-- a class='action' title='<?php echo translate('Export as template to disk'); ?>' href='export_board_to_disk.php?id=<?php echo $board["id"]; ?>'>
											<img src='models/site-templates/images/export_to_disk.png'>
										</a -->
										<a class='action' title='<?php echo translate('Export to Excel'); ?>' href='excel_export.php?boardid=<?php echo $board["id"]; ?>' target='_blank'>
											<img src='models/site-templates/images/excel.png'>
										</a>
									</div>
									<div class='column-editor'>
										<ul class="sortable">
											<?php
											$columnsCombobox = '<select class="column">';
											$columnsCombobox = $columnsCombobox . '<option value="ALL">' . translate('All') . '</option>';
											foreach ($columns as $columnname => $columndata) {
												$columnsCombobox = $columnsCombobox . '<option value="' . $columnname . '">' . $columndata['display_name'] . '</option>';
											}
											$columnsCombobox = $columnsCombobox . '</select>';
									
											$attributesCombobox = '<select class="attribute">';
											$attributesCombobox = $attributesCombobox . '<option value="ALL">' . translate('Always') . '</option>';
											foreach ($attributes as $attributeid => $attributedata) {
												$attributename = $attributedata['name'];
												$attributesCombobox = $attributesCombobox . '<option value="' . $attributeid . '">' . $attributename . '</option>';
											}
											$attributesCombobox = $attributesCombobox . '<option value="loggedinuser">' . translate('logged in user') . '</option>';
											$attributesCombobox = $attributesCombobox . '<option value="loggedinusersrole">' . translate('role of logged in user') . '</option>';
											$attributesCombobox = $attributesCombobox . '</select>';
									
											$attributesCombobox2 = '<select class="attribute">';
											foreach ($attributes as $attributeid => $attributedata) {
												$attributename = $attributedata['name'];
												$attributesCombobox2 = $attributesCombobox2 . '<option value="' . $attributeid . '">' . $attributename . '</option>';
											}
											$attributesCombobox2 = $attributesCombobox2 . '</select>';
											
											$ruleTypesCombobox = '<select class="ruleType">';
											foreach ($ruleTypes as $ruleType) {
												$ruleTypesCombobox = $ruleTypesCombobox . '<option value="' . $ruleType['rule_name'] . '">' . $ruleType['display_name'] . '</option>';
											}
											$ruleTypesCombobox = $ruleTypesCombobox . '</select>';
									
											$generatorTypesCombobox = '<select class="generatorType">';
											foreach ($generatorTypes as $generatorType) {
												$generatorTypesCombobox = $generatorTypesCombobox . '<option value="' . $generatorType['generator_name'] . '">' . $generatorType['display_name'] . '</option>';
											}
											$generatorTypesCombobox = $generatorTypesCombobox . '</select>';
									
											$generatorTriggers = array(array("generator_name" => "move_from", "display_name" => translate('a card is moved from')), array("generator_name" => "move_to", "display_name" => translate('a card is moved to')));
											
											$generatorTriggersCombobox = '<select class="generatorTrigger">';
											foreach ($generatorTriggers as $generatorTrigger) {
												$generatorTriggersCombobox = $generatorTriggersCombobox . '<option value="' . $generatorTrigger['generator_name'] . '">' . $generatorTrigger['display_name'] . '</option>';
											}
											$generatorTriggersCombobox = $generatorTriggersCombobox . '</select>';
?>
											<script type="text/javascript">
													function addRule<?php echo $board_id; ?>(boardid) {
														var date = new Date();
														var components = [date.getYear(),date.getMonth(),date.getDate(),date.getHours(),date.getMinutes(),date.getSeconds(),date.getMilliseconds()];
														var id = components.join("");
														
														var $newrule = $('<div class="rule" id="'+id+'"><?php echo translate('%1$s column %2$s when %3$s is %4$s', $ruleTypesCombobox, $columnsCombobox, $attributesCombobox, '<input type="text" size="20" class="value" name="value"></input>'); ?><div class="remove" onclick="javascript:return removeRule(\''+boardid+'\', \''+id+'\');"></div></div>');
														$("#board-"+boardid+" #rules").append($newrule);
														return $newrule;
													}
													function addGenerator<?php echo $board_id; ?>(boardid) {
														var date = new Date();
														var components = [date.getYear(),date.getMonth(),date.getDate(),date.getHours(),date.getMinutes(),date.getSeconds(),date.getMilliseconds()];
														var id = components.join("");
														
														var $newgenerator = $('<div class="generator" id="'+id+'"><?php echo translate('Write %1$s in attribute %4$s when %2$s column %3$s', $generatorTypesCombobox, $generatorTriggersCombobox, $columnsCombobox, $attributesCombobox2); ?><div class="remove" onclick="javascript:return removeGenerator(\''+boardid+'\', \''+id+'\');"></div></div>');
														$("#board-"+boardid+" #generators").append($newgenerator);
														return $newgenerator;
													}
											</script>
<?php											
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
											<?php } ?>
										</ul>
										<input id="columnNameToAdd" type="text" size="20" placeholder='<?php echo translate('Column Name'); ?>'></input>
										<a class='action with-text' title='<?php echo translate("Add column to board"); ?>' onclick="addColumn('<?php echo $board_id; ?>'); $('#board-<?php echo $board_id; ?> #columnNameToAdd').val(''); return false;" href='#'>
											<span class='image add'></span><?php echo translate('Add column'); ?></a>
										<a class='action with-text' title='<?php echo translate("Save columns"); ?>' onclick="saveColumns('<?php echo $board_id; ?>'); return false;" href='#'>
											<span class='image save'></span><?php echo translate('Save columns'); ?></a>
									</div>
									<div class="attributes-editor">
										<div id="attributes">
<?php
											global $standard_card_attributes;
											foreach ($attributes as $attributeid => $attributedata) { ?>											
												<div id='<?php echo $attributeid; ?>' class='card-attribute'>
<?php
												if (!isset($standard_card_attributes[$attributeid])) {
?>													
													<div class="remove" onclick="javascript:return removeCardAttribute('<?php echo $board_id; ?>','<?php echo $attributeid; ?>');"></div>
<?php
												}
												else {
?>	
													<div class="no-remove"></div>												
<?php												
												}
?>
													<input type='text' class='name' size=20 placeholder="<?php echo translate('Attribute name'); ?>" value='<?php echo $attributedata['name']; ?>'></input>
													<select class='sourceType' <?php if (isset($standard_card_attributes[$attributeid])) echo 'disabled="disabled"'; ?>>
														<option value='TEXT' <?php echo $attributedata['sourceType']=='TEXT' ? "selected='selected'" : ""; ?>><?php echo translate('Multi-line text'); ?></option>
														<option value='SQL' <?php echo $attributedata['sourceType']=='SQL' ? "selected='selected'" : ""; ?>><?php echo translate('SQL command'); ?></option>
														<option value='RANGE' <?php echo $attributedata['sourceType']=='RANGE' ? "selected='selected'" : ""; ?>><?php echo translate('List of values'); ?></option>
														<option value='STRING' <?php echo $attributedata['sourceType']=='STRING' ? "selected='selected'" : ""; ?>><?php echo translate('Single line text'); ?></option>
														<option value='NUMERIC' <?php echo $attributedata['sourceType']=='NUMERIC' ? "selected='selected'" : ""; ?>><?php echo translate('Numeric'); ?></option>
														<option value='DATE' <?php echo $attributedata['sourceType']=='DATE' ? "selected='selected'" : ""; ?>><?php echo translate('Date'); ?></option>
														<option value='USERS' <?php echo $attributedata['sourceType']=='USERS' ? "selected='selected'" : ""; ?>><?php echo translate('List of users'); ?></option>
														<option value='CHECKBOX' <?php echo $attributedata['sourceType']=='CHECKBOX' ? "selected='selected'" : ""; ?>><?php echo translate('Checkbox'); ?></option>
													</select>
													<input type='text' size=30 class='source' value='<?php echo isset($attributedata['source']) ? $attributedata['source'] : ""; ?>' <?php if (isset($standard_card_attributes[$attributeid])) echo 'disabled="disabled"'; ?>></input>
<?php 
												$attributesArray = array_merge(array('DEFAULT' => translate('Default tooltip')), extractArrayFromArray($attributes, 'name'));
												$attributesCombobox3 = createDropDownFromArray($attributesArray, isset($attributedata['tooltip']) ? $attributedata['tooltip'] : '', 'tooltip');
												echo $attributesCombobox3; 
												echo translate(' hide on: ');
												$attributesArray = extractArrayFromArray($columns, 'display_name');
												$columnsCombobox2 = createDropDownFromArray($attributesArray, isset($attributedata['hideoncolumns']) ? $attributedata['hideoncolumns'] : '', 'hideoncolumns', TRUE);
												echo $columnsCombobox2; 
?>
												</div>
<?php
											}
?>
										</div>
										<input id="attributeNameToAdd" type="text" size="20" placeholder='<?php echo translate('Attribute Name'); ?>'></input>
										<a class='action with-text' title='<?php echo translate("Add card attribute to board"); ?>' onclick="addCardAttribute_<?php echo $board_id; ?>(); $('#board-<?php echo $board_id; ?> #attributeNameToAdd').val(''); return false;" href='#'>
											<span class='image add'></span><?php echo translate('Add attribute'); ?></a>
										<a class='action with-text' title='<?php echo translate("Save attributes"); ?>' onclick="saveAttributes('<?php echo $board_id; ?>'); return false;" href='#'>
											<span class='image save'></span><?php echo translate('Save attributes'); ?></a>
									</div>
									<div class="rules-editor">
										<div id="rules">
<?php
											$rules = unserialize_or_empty_array(get_board_rules($board_id));

											if (!isset ($rules) || (count($rules) == 0)) {
												$rules = array ();
												echo translate("No rules defined for this board yet.<br><br>");
											}
											else {
												foreach ($rules as $ruleid => $ruledata) { 
?>
												<script>
													var $newrule = addRule<?php echo $board_id; ?>('<?php echo $board_id; ?>');
													$newrule.children('.ruleType').val('<?php echo $ruledata['ruleType']; ?>');
													$newrule.children('.column').val('<?php echo $ruledata['column']; ?>');
													$newrule.children('.attribute').val('<?php echo $ruledata['attribute']; ?>');
													$newrule.children('.value').val('<?php echo $ruledata['value']; ?>');
												</script>
<?php
												}
											}
?>
										</div>
										<br>
										<a class='action with-text' title='<?php echo translate("Add rule"); ?>'
											onclick="addRule<?php echo $board_id; ?>('<?php echo $board_id; ?>'); return false;"
											href='#'><span class='image add'></span><?php echo translate('Add rule'); ?>
										</a>
										<a class='action with-text' title='<?php echo translate("Save rules"); ?>' onclick="saveRules('<?php echo $board_id; ?>'); return false;" href='#'>
											<span class='image save'></span><?php echo translate('Save rules'); ?></a>
									</div>
									<div class="generators-editor">
										<div id="generators">
<?php
											$generators = unserialize_or_empty_array(get_board_generators($board_id));

											if (!isset ($generators) || (count($generators) == 0)) {
												$generators = array ();
												echo translate("No generators defined for this board yet.<br><br>");
											}
											else {
												foreach ($generators as $generatorid => $generatordata) { 
?>
												<script>
													var $newgenerator = addGenerator<?php echo $board_id; ?>('<?php echo $board_id; ?>');
													$newgenerator.children('.generatorType').val('<?php echo $generatordata['generatorType']; ?>');
													$newgenerator.children('.generatorTrigger').val('<?php echo $generatordata['generatorTrigger']; ?>');
													$newgenerator.children('.column').val('<?php echo $generatordata['column']; ?>');
													$newgenerator.children('.attribute').val('<?php echo $generatordata['attribute']; ?>');
													$newgenerator.children('.value').val('<?php echo $generatordata['value']; ?>');
												</script>
<?php
												}
											}
?>
										</div>
										<br>
										<a class='action with-text' title='<?php echo translate("Add generator"); ?>'
											onclick="addGenerator<?php echo $board_id; ?>('<?php echo $board_id; ?>'); return false;"
											href='#'><span class='image add'></span><?php echo translate('Add generator'); ?>
										</a>
										<a class='action with-text' title='<?php echo translate("Save generators"); ?>' onclick="saveGenerators('<?php echo $board_id; ?>'); return false;" href='#'>
											<span class='image save'></span><?php echo translate('Save generators'); ?></a>
									</div>
								</div>
								<?php } ?>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<div id='bottom'></div>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="scripts/jquery.ui.touch-punch.min.js"></script>
	<script type="text/javascript" src="scripts/jquery.bpopup.min.js"></script>
	<script type="text/javascript" src="scripts/jqueryui-editable.min.js"></script>
	<script type="text/javascript" src="scripts/jquery.multiselect.min.js"></script>
	<script type="text/javascript">
		$('document').ready(function() {
			$("select.hideoncolumns").multiselect();
			$('.collapse.collapse-project').click(function() {
				$(this).parent().parent().children('.boards').slideToggle('slow');
			});
			$('.collapse.collapse-board').click(function() {
				$(this).parent().parent().children('.column-editor,.rules-editor,.attributes-editor,.generators-editor').slideToggle('fast');
			});
			$.fn.editable.defaults.mode = 'inline';
			$('.columnname').editable({
			    type: 'text',
			});			
		});

		function clone_board(boardid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'clone_board_dialog.php?cloneboardid='+boardid 
			});
		}

		function export_board(boardid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'export_board_dialog.php?boardid='+boardid 
			});
		}

		function delete_board(boardid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'delete_board_dialog.php?boardid='+boardid 
			});
		}

		function activate_board(boardid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'activate_board_dialog.php?boardid='+boardid 
			});
		}

		function deactivate_board(boardid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'deactivate_board_dialog.php?boardid='+boardid 
			});
		}

		function activate_project(projectid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'activate_project_dialog.php?projectid='+projectid 
			});
		}

		function deactivate_project(projectid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'deactivate_project_dialog.php?projectid='+projectid 
			});
		}
		
		function display_board(boardid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'display_board_dialog.php?boardid='+boardid 
			});
		}
		
		function add_new_board(projectid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'add_new_board_dialog.php?projectid='+projectid 
			});
		}
		
		function delete_project(projectid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'delete_project_dialog.php?projectid='+projectid 
			});
		}
		
		function display_project(projectid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'display_project_dialog.php?projectid='+projectid 
			});
		}
		
		function add_new_project() {
			$('#popup_dialog').bPopup({
				loadUrl: 'add_new_project_dialog.php' 
			});
		}
		
		function export_board_to_excel(boardid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'display_board_dialog.php?boardid='+boardid 
			});
		}
			</script>
		<div id="popup_dialog"></div>
		<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
