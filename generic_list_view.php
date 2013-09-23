<?php 
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){die();}
	require_once("functions.php");
	require_once("models/header.php");
	if (!isUserLoggedIn()){
		header('Location: login.php');
	}
	else {
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title><?php echo translate('Kanban Board'); ?></title>
		<link media="print, projection, screen" type="text/css" href="styles/tablesorter.css" rel="stylesheet">
<?php		
	include "includes/board_request_data.php";
?>		
		<script type="text/JavaScript">
		<!--
			function AutoRefresh(interval) {
				setTimeout("location.reload(true);",interval);
			}
		//   -->
		</script>
	</head>
<?php
		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "";
		if ($readonly==0) {
?>
	<body id="board-body" style="overflow: auto !important;">
<?php
		}
		else {
?>
	<body id="board-body" onload="JavaScript: AutoRefresh(5000);">
<?php
		}
?>
		<div id="header-list">
			<div class="header-part" id="welcome"><?php echo translate('Current user: %1$s', '<div id="username">'.$logged_in_user->displayname.'</div>'); ?></div>
			<a class="header-part" href="logout.php"><?php echo translate('Logout'); ?></a>
			<a class="header-part" href="admin_projects.php"><?php echo translate('Project Administration'); ?></a>
<?php
		if ($readonly == 0  && $type!="archive") {
?>
			<a id="undo" class="header-part" href="#"><?php echo translate('Undo'); ?></a>
			<a id="redo" class="header-part" href="#"><?php echo translate('Redo'); ?></a>
<?php
		}
?>
			<a id="kanban-view-link" class="header-part" href="board.php?boardid=<?php echo $current_board; ?>&readonly=<?php echo $readonly; ?>"><?php echo translate('Kanban view'); ?></a>
 <?php 
 		if ($type!="archive") {
?>
 			<a id="archive-view-link" class="header-part" href="generic_list_view.php?type=archive&boardid=<?php echo $current_board; ?>&readonly=<?php echo $readonly; ?>"><?php echo translate('Archive view'); ?></a>
 			<a id="admin-statistics-link" class="header-part" href="admin_statistics.php?boardid=<?php echo $current_board; ?>&readonly=<?php echo $readonly; ?>"><?php echo translate('Manage statistics'); ?></a>
<?php
		}
 		if ($type!="board") {
?>
			<a id="list-view-link" class="header-part" href="generic_list_view.php?type=board&boardid=<?php echo $current_board; ?>&readonly=<?php echo $readonly; ?>"><?php echo translate('List view'); ?></a>
<?php
		}
		include 'includes/project_board_change_combos.php';
?>
		</div>
		<div id="list-view">
<?php 
		include 'includes/not_set_current_board.php';

		$cardattributes = getCompleteCardAttributes($current_board);
		if ($type != "archive") {
			$cards = get_cards_on_board($current_board);
			$boardcolumns = get_board_columns($current_board);
			$rules = unserialize_or_empty_array(get_board_rules($current_board));
			$ruleTypes = getActiveRuleTypes();
			$rulesPerAction = getRulesPerAction($rules, $ruleTypes);
		}
		else {
			$cards = unserialize_or_empty_array(getArchivedCards($current_board));
		}
		if (!isset($cards)) {
			$cards = array();
		}
		include 'includes/reduce_board_table.php';
?>
			<table id="list-view-table" class="tablesorter">
				<thead>
					<tr>
<?php
		if ($readonly==0) {
?>
						<th class="actions no-filter"><?php echo translate('Actions'); ?></th>
<?php
		}
?>
						<th class="sortcolumn no-filter"><?php echo translate('Column'); ?></th>
<?php
		foreach ($cardattributes as $attribute_id => $attribute_data) {
?>
						<th <?php if (in_array($attribute_id, $remove_attributes)) echo 'style="display: none;"'; ?> class="sortcolumn"><?php echo $attribute_data['name']; ?></th>
<?php
		}
?>
					</tr>
					<tr class='filters'>
						<td></td>						
						<td><input type="text" class="table-filter" onchange="filterOnField('filter_column', '<?php echo translate('Column'); ?>');" id="filter_column" placeholder="<?php echo translate("No filter");?>" size="10"></td>						
<?php
		foreach ($cardattributes as $attribute_id => $attribute_data) {
?>
						<td <?php if (in_array($attribute_id, $remove_attributes)) echo 'style="display: none;"'; ?>><input type="text" class="table-filter" onchange="filterOnField('filter_<?php echo $attribute_id; ?>', '<?php echo $attribute_data['name']; ?>');" id="filter_<?php echo $attribute_id; ?>" placeholder="<?php echo translate("No filter");?>" size="10"></td>
<?php
		}
?>
					</tr>
				</thead>
				<tbody>
<?php
		foreach ($cards as $card_id => $card_data) {
?>
					<tr id="<?php echo $card_id; ?>" class="card-row">
<?php
			if ($readonly==0) {
?>
						<td class='nosort'>
<?php
				if ($type != "archive") {
?>
							<a title='Delete card' href='#' onclick="deleteCard(<?php echo $card_id; ?>)"><img src='models/site-templates/images/trash.jpg'></a>
							<a title='Clone card' href='#' onclick="cloneCard(<?php echo $card_id; ?>)"><img src='models/site-templates/images/clone.png'></a>
<?php 
				}
?>
						</td>
<?php
			}
?>
<?php
			if ($type != "archive") {
?>
						<td data-column-name="<?php echo translate('Column'); ?>" data-filter-field="filter_column">
							<select class="board" <?php if ($readonly != 0) echo "disabled"; ?>>
<?php					
				foreach ($boardcolumns as $column_name => $column_data) {
					$wip_limit = -1;
					if (isset($column_data['wip_limit']) && ($column_data['wip_limit'] > 0)) {
						$wip_limit = $column_data['wip_limit'];
					}
?>
								<option data-wip-limit="<?php echo $wip_limit; ?>" value="<?php echo $column_name; ?>" <?php if ($column_name == $card_data['board']) echo "selected='selected'"; ?>><?php echo $column_data['display_name']; ?></option>
<?php
				}
?>
							</select>
						</td>
<?php
			}
			else {
?>
						<td data-column-name="<?php echo translate('Column'); ?>" data-filter-field="filter_column"><?php echo $card_data['board']; ?></td>
<?php
			}
			foreach ($cardattributes as $attribute_id => $attribute_data) {
?>
						<td <?php if (in_array($attribute_id, $remove_attributes)) echo 'style="display: none;"'; ?> data-column-name="<?php echo $attribute_data['name']; ?>" class="<?php echo $attribute_id; ?>"  data-filter-field="filter_<?php echo $attribute_id; ?>"><?php echo isset($card_data[$attribute_id]) ? $card_data[$attribute_id] : ""; ?></td>
<?php
			}
?>
					</tr>
<?php
		}
?>
				</tbody>
			</table>
		</div>
		<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script type='text/javascript' src='scripts/jquery.tablesorter.js'></script> 
		<script type='text/javascript' src='scripts/jquery.bpopup.min.js'></script>
		<script type='text/javascript' src='scripts/jquery.tablefilter.js'></script>
		<script type='text/javascript' src='scripts/jquery.contextmenu.r2.packed.js'></script>
		<script type="text/javascript" src="scripts/date.js"></script>
		<script type="text/javascript" src="scripts/jquery.numeric.js"></script> 
		<script type="text/javascript" src="scripts/LZW.min.js"></script>
		<script type='text/javascript'>
<?php
		if ($readonly == 0 && $type != "archive") {
			foreach ($ruleTypes as $ruleType) {
				echo $ruleType['rule_js'];
			}
?>
			$('document').ready(init);
<?php
		}
		else {
?>
	        $('document').ready(function() {
	        	$table = $("#list-view-table").tablesorter({
			        cancelSelection: true,
			        sortList: [[1,0]],
			        selectorHeaders: 'thead th.sortcolumn',
			        textExtraction: function(node) {
			            // Check if option selected is set
			            if ($(node).find('option:selected').text() != "") {
			                return $(node).find('option:selected').text();
			            }
			            // Otherwise return text
			            else return $(node).text();
			        }
			    }); 


				$('#list-view-table tbody tr td:not(".nosort")').contextMenu('myMenu1', {
				  bindings: {
				    'filter': function(t) {
					    filtertext="";
			            if ($(t).find('option:selected').text() != "") {
			            	filtertext = $(t).find('option:selected').text();
			            }
			            else {
				            filtertext= $(t).text();
			            }
			            columnName = $(t).data("column-name");
			            filterField = $(t).data("filter-field");
					    filterOnCell(filtertext, columnName, filterField);
				    },
				  }
				});
						
		        $('#list-view-table thead tr:first th').each(function() {
	        	   $(this).css({'width': $(this).width()+"px"});
	        	});
			}); 			
<?php				
		}
		if ($type != "archive") {
?>			
			function init() {
				$table = $("#list-view-table").tablesorter({
			        cancelSelection: true,
			        sortList: [[1,0]],
			        selectorHeaders: 'thead th.sortcolumn',
			        textExtraction: function(node) {
			            // Check if option selected is set
			            if ($(node).find('option:selected').text() != "") {
			                return $(node).find('option:selected').text();
			            }
			            // Otherwise return text
			            else return $(node).text();
			        }
			    }); 

				$('#list-view-table tbody tr td:not(".nosort")').contextMenu('myMenu1', {
					  bindings: {
					    'filter': function(t) {
						    filtertext="";
				            if ($(t).find('option:selected').text() != "") {
				            	filtertext = $(t).find('option:selected').text();
				            }
				            else {
					            filtertext= $(t).text();
				            }
				            columnName = $(t).data("column-name");
				            filterField = $(t).data("filter-field");
						    filterOnCell(filtertext, columnName, filterField);
					    },
					  }
					});
										
		        $('#list-view-table thead tr:first th').each(function() {
	        	   $(this).css({'width': $(this).width()+"px"});
	        	});
	        	
				$(".card-row").dblclick(function() {
					cardid=$(this).attr('id');
					var $cardForRules = $(this);
					var $column_id = $(this).find('.board').val();
					var $action_data={};
					$action_data['column'] = $column_id;
					var $attribute_matching = 1;
<?php
		if (isset($rulesPerAction['editcard'])) {
			printRules($rulesPerAction['editcard'], '');
		}
?>
					$('#card_popup').bPopup({
						loadUrl: 'edit_card.php?boardid=<?php echo $current_board; ?>&cardid='+cardid,
						onClose: function() {
<?php
		foreach ($cardattributes as $attribute_id_edit => $attribute_data_edit) {
?>
			var val = "false";
			var field = $('#edit-card .attribute-value #<?php echo $attribute_id_edit; ?>');
			if (field.attr('type') == "checkbox") {
				$('#edit-card .attribute-value #<?php echo $attribute_id_edit; ?>:checked').each(function() { val = "true"; });  
			}
			else {
				val = field.val();
			}
							$('#'+cardid+' .<?php echo $attribute_id_edit; ?>').html(val.replace(/\r?\n/g, "<br />"));
<?php
		}
?>							
							sendCard(cardid);
						}
					});
		 		});

    			$('.board').each(function() {
			 		$(this).data('pre', $(this).val());
			 	});
			 	$('.board').change(function(){
					var $parent_column_id = $(this).data('pre');
					var $column_id =  $(this).val();
					var $wip_limit = $(this).find("option:selected").data('wip-limit');
					if ($wip_limit > 0) {
						var $current_no_of_cards = -1;
						$("#list-view-table .board option:selected").each(function() {
							if ($(this).attr('value') == $column_id) {
								$current_no_of_cards++;
							}
						});
						if ($current_no_of_cards >= $wip_limit) {
							alert('<?php echo translate('WIP Limit reached !'); ?>');
							$(this).val($parent_column_id);
							return "ERROR";
						}
					}
					var $action_data={};
					$action_data['from'] = $parent_column_id;
					$action_data['to'] = $column_id;
					var $attribute_matching = 1;
					var $cardForRules = $(this).parent().parent();
<?php
		if (isset($rulesPerAction['moved'])) {
			printRules($rulesPerAction['moved'], '$(this).val($parent_column_id);');
		}
?>
					sendCard($cardForRules.attr('id'));
					$(this).data('pre', $(this).val());
			 	});
			}

			function sendCard(cardid) {
				var $cards = {};
				$("#"+cardid+".card-row").each(function() {
					var $id = $(this).attr('id');
					$cards[$id] = {};
					var $board = $(this).find('.board').val();
					$cards[$id]['board']=$board;
<?php
		foreach ($cardattributes as $attribute_id => $attribute_data) {
?>
					$cards[$id]["<?php echo $attribute_id; ?>"]= $(this).children(".<?php echo $attribute_id; ?>").first().html();
<?php
		}
?>
				});
				$.ajax({ 
					type: "POST", 
					url: "add_card_to_board.php", 
					data: {
						cards: JSON.stringify($cards), 
						boardid: <?php echo $current_board; ?>
					},
					success: function() {
						hideOneValueColumns('list-view-table', 'filters', 'no-hide');
					}
				});
			}
			
			function deleteCard($card_id) {
				var $cardForRules = $('#'+$card_id);
				var $column_id = $cardForRules.parent().attr('id');
				var $action_data={};
				$action_data['column'] = $column_id;
				var $attribute_matching = 1;
<?php
		if (isset($rulesPerAction['deletecard'])) {
			printRules($rulesPerAction['deletecard'], '');
		}
?>
				archiveDeletedCard($cardForRules);
				$('#'+$card_id).remove();
				$.ajax({ type: "POST", url: "remove_card_from_board.php", data: {cardid: $card_id, boardid: <?php echo $current_board; ?>} });
				hideOneValueColumns('list-view-table', 'filters', 'no-hide');
			}
			
			function archiveDeletedCard($cardForRules) {
				$cards = {};
				var $id = $cardForRules.attr('id');
				$cards[$id] = {};
				var $board = $(this).find('.board').val();
				$cards[$id]['board']=$board;
<?php
		foreach ($cardattributes as $attribute_id => $attribute_data) {
?>
				$cards[$id]["<?php echo $attribute_id; ?>"]= $(this).children(".<?php echo $attribute_id; ?>").first().html();
<?php
		}
?>
				$.ajax({ type: "POST", async: false, url: "archive_cards.php", data: {cards: $cards, boardid: <?php echo $current_board; ?>}});				
			}

			function cloneCard(cloneid) {
				alert("<?php echo translate('Not yet implemented !'); ?>");
			}
<?php 
	}
?>
			FilterText = "";
			function filterOnField(filterid, columnname) {
				selectedText = $('#'+filterid).val();
				FilterText = ((FilterText == selectedText) ? "" : selectedText );
				if (FilterText == "") {
					$('.table-filter').val('');
				}
				$("#list-view-table").filterTable(FilterText, columnname);
			}

			function filterOnCell(selectedText, columnname, filterfield) {
				$('#'+filterfield).val(selectedText);
				filterOnField(filterfield, columnname);
			}

			function hideOneValueColumns(table_id, ignore_row_class, ignore_column_class) {
				var row_count =  $('#'+table_id+' tr:not(.'+ignore_row_class+')').length;
				if (row_count > 2) { 
					$('#'+table_id+' th').each(function(i) {
						if (!$(this).hasClass(ignore_column_class)) {
							var all_tds = $(this).parents('table').find('tr td:nth-child(' + (i + 1) + ')');
							var filtered_tds = $(this).parents('table').find('tr:not(.'+ignore_row_class+') td:nth-child(' + (i + 1) + ')');
							var values = new Array();
							filtered_tds.each(function() {
								var value = this.innerHTML;
								if (values.indexOf(value) == -1) {
									values.push(value);
								}
							});
							if (values.length < 2) {
						        $(this).hide();
						        all_tds.hide();					
							}
							else {
						        $(this).show();
						        all_tds.show();					
						    }
						}
					});
				}
				else {
					$('#'+table_id+' th').show();
					$('#'+table_id+' tr td').show();
				}
			}
		</script> 
		<div id="card_popup"></div>
	    <div class="contextMenu" id="myMenu1">
	      <ul>
	        <li id="filter"><img src="models/site-templates/images/filter_small.png" /><?php echo translate('Filter'); ?></li>
	      </ul>
	    </div>
		<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
<?php
	}
?>