<?php 
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){die();}
	require_once("functions.php");
	if (!isUserLoggedIn()){
		header('Location: login.php');
	}
	else {
		include "includes/board_request_data.php";
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<title><?php echo $website_name; ?></title>
<?php
$dirname = $_SERVER['REQUEST_URI'];
if (!preg_match('/\/$/', $dirname)) {
	$dirname = dirname($dirname);
}
while ( preg_match('/\.php$/', $dirname) ) {
	$dirname = dirname($dirname);
}
$dirname = rtrim($dirname,"/");
$dirname = rtrim($dirname,"\\");
?>
		<?php echo CssCrush::tag($dirname.'/styles/main.css'); ?>		
		<link rel='stylesheet' type='text/css' href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css" />
		<?php echo CssCrush::tag($dirname.'/styles/jqueryui-editable.css'); ?>
		<?php echo CssCrush::tag($dirname.'/styles/jquery.multiselect.css'); ?>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('.sortable').each(function() {
					$(this).sortable();
				});
				$("select.groups").multiselect();
			});
		</script>
	</head>
	<body id="board-body" style="overflow: auto !important;">
		<script type="text/javascript" src="scripts/jquery.tablesorter.js"></script> 
		<script type="text/javascript" src="scripts/jquery.bpopup.min.js"></script>
		<script type="text/javascript" src="scripts/jquery.tablefilter.js"></script>
		<script type="text/javascript" src="scripts/jquery.contextmenu.r2.packed.js"></script>
		<script type="text/javascript" src="scripts/date.js"></script>
		<script type="text/javascript" src="scripts/jquery.numeric.js"></script> 
		<script type="text/javascript" src="scripts/jquery.multiselect.min.js"></script>
		<div id="header-list">
			<div class="header-part" id="welcome"><?php echo translate('Current user: %1$s', '<div id="username">'.$logged_in_user->displayname.'</div>'); ?></div>
			<a class="header-part" href="logout.php"><?php echo translate('Logout'); ?></a>
			<a class="header-part" href="admin_projects.php"><?php echo translate('Project Administration'); ?></a>
			<a id="kanban-view-link" class="header-part" href="board.php?boardid=<?php echo $current_board; ?>&readonly=<?php echo $readonly; ?>"><?php echo translate('Kanban view'); ?></a>
 			<a id="archive-view-link" class="header-part" href="generic_list_view.php?type=archive&boardid=<?php echo $current_board; ?>&readonly=<?php echo $readonly; ?>"><?php echo translate('Archive view'); ?></a>
			<a id="list-view-link" class="header-part" href="generic_list_view.php?type=board&boardid=<?php echo $current_board; ?>&readonly=<?php echo $readonly; ?>"><?php echo translate('List view'); ?></a>
<?php 
		include 'includes/project_board_change_combos.php';
?>
		</div>
		<div id="manage-statistics">
			<div id="statistics-list-<?php echo $current_board; ?>">
<?php
		include 'includes/not_set_current_board.php';

		$attributes = getCompleteCardAttributes($current_board);
		$boardcolumns = get_board_columns($current_board);
		$statistics = getBoardStatistics($current_board);
		$frequencyArray = array('D' => translate('Daily'), 'W' => translate('Weekly'), 'M' => translate('Monthly'));
		$typeArray = array('C' => translate('Count'), 'S' => translate('Sum'), 'A' => translate('Average'), 'M' => translate('Max'), 'm' => translate('Min'));
		$attributesArray0 = extractArrayFromArray($attributes, 'name');
		$attributesArray = array_merge(array('' => translate('n.a.')), $attributesArray0);
		$attributesArray0 = array_merge(array('board' => translate('Board column')), $attributesArray0);
?>
		<script type="text/javascript">
			function addStatistics(boardid) {
				var val = $("#statisticsNameToAdd").val();
				if (val.length == 0) {
					return;
				}
				var id = val.replace(/[^a-zA-Z0-9]+/g, "").toLowerCase();
				$('#statistics-list-'+boardid).append(
					'<div class="statistics" id="'+id+'">'+
						'<div class="remove" onclick="javascript:return removeStatistics("'+boardid+'", "'+id+'");"></div>'+
						'<input class="statisticsname" value="'+val+'"></input>'+
						'<?php echo createDropDownFromArray($frequencyArray, '', 'frequency'); ?>'+
						'<?php echo createDropDownFromArray($typeArray, '', 'type'); ?>'+
						'<?php echo createDropDownFromArray($attributesArray, '', 'attribute'); ?>'+
						' grouped by '+
						'<?php echo createDropDownFromArray($attributesArray0, '', 'groups', TRUE); ?>'+
					'</div>');
				$('#'+id+" select.groups").multiselect();
			}
			function saveStatistics(boardid) {				
				var $statistics = {};
				$("#statistics-list-"+boardid+" .statistics").each(function(){
					var $id = $(this).attr('id');
					var $name = $(this).find('.statisticsname').val();
					var $frequency = $(this).find('.frequency').val();
					var $type = $(this).find('.type').val();
					var $attribute = $(this).find('.attribute').val();
					var $groups = $.map( $(this).find('.groups option:selected'), function(e) { return $(e).val(); } ); 
					$statistics[$id] = {};
					$statistics[$id]['display_name'] = $name;
					$statistics[$id]['frequency'] = $frequency;
					$statistics[$id]['type'] = $type;
					$statistics[$id]['attribute_id'] = $attribute;
					$statistics[$id]['groups'] = $groups;
				});
				$.ajax({ type: "POST", url: "storeBoardStatistics.php", data: {statistics: $statistics, boardid: boardid}, complete: function() { location.reload(true); }});
			}
		</script>
<?php
		foreach ($statistics as $id => $data) {
?>
				<div class="statistics" id="<?php echo $id;?>">
					<div class="remove" onclick="javascript:return removeStatistics('<?php echo $current_board; ?>', '<?php echo $id; ?>');"></div>
					<input class="statisticsname" value="<?php echo $data['display_name']; ?>"></input>
<?php
			echo createDropDownFromArray($frequencyArray, $data['frequency'], 'frequency');
			echo createDropDownFromArray($typeArray, $data['type'], 'type');
			echo createDropDownFromArray($attributesArray, $data['attribute_id'], 'attribute');
			echo createDropDownFromArray($attributesArray0, $data['groups'], 'groups', TRUE);
?>
				</div>			
<?php 
		}
?>
			</div>
			<input id="statisticsNameToAdd" type="text" size="20" placeholder='<?php echo translate('Statistics Name'); ?>'></input>
			<a class='action with-text' title='<?php echo translate("Add statistics for board"); ?>' onclick="addStatistics('<?php echo $current_board; ?>'); $('#statisticsNameToAdd').val(''); return false;" href='#'>
				<span class='image add-small'></span><?php echo translate('Add'); ?></a>
			<a class='action with-text' title='<?php echo translate("Save statistics"); ?>' onclick="saveStatistics('<?php echo $current_board; ?>'); return false;" href='#'>
				<span class='image save-small'></span><?php echo translate('Save'); ?></a>
		</div>
		<div id="card_popup"></div>
	    <div class="contextMenu" id="myMenu1">
	      <ul>
	      </ul>
	    </div>
		<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>		
	</body>
</html>
<?php
	}
?>