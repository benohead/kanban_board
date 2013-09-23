<?php 
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once "functions.php";
	$templateid = trim($_REQUEST["id"]);
	$template_data = getBoardTemplate($templateid);
?>
<html>
	<head>
		<?php echo CssCrush::tag(dirname($_SERVER['REQUEST_URI']).'/styles/main.css'); ?>
		<style>
<?php
	echo $template_data['board_css'];
?>
		</style>
	</head>
	<body id="board-body">
		<div id="header">
			<div id="welcome" class="header-part">
				Current user:
				<div id="username">Henri</div>
			</div>
			<a href="#" class="header-part">Logout</a> 
			<a href="#" class="header-part">Project Administration</a>
			<a href="#" class="header-part" id="undo">Undo</a>
			<a href="#" class="header-part" id="redo">Redo</a>
			<a href="#" class="header-part" id="fullscreen">Fullscreen</a>
			<a href="#" class="header-part" id="list-view-link">List view</a>
			<select name="projectid">
				<option selected="selected">Project No. 1</option>
			</select>
			<select name="boardid">
				<option selected="selected">Board No. 1</option>
			</select>
		</div>
		<div id="board">

<?php 
	$columns = getBoardTemplateColumns($templateid);
	
	foreach ($columns as $columnname => $columndata) {
		$wip_limit_text = "";
		$wip_limit = -1;
		if (isset($columndata['wip_limit']) && ($columndata['wip_limit'] > 0)) {
			$wip_limit_text = " - WIP: ".$columndata['wip_limit'];
			$wip_limit = $columndata['wip_limit'];
		}
?>
			<div id="<?php echo $columnname; ?>" class="boardcolumn" data-wip-limit="<?php echo $wip_limit; ?>">
				<div class="title"><span><?php echo $columndata['display_name'].$wip_limit_text; ?></span><span class='addcard'></span></div>
			</div>
<?php
	}
?>
			<div id="delete-area"></div>
			<div style="clear:both"></div>
		</div>
	</body>
</html>
