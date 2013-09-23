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
			<h1>
				<?php echo $website_name; ?>
			</h1>
			<h2>
				<?php echo translate('Admin Templates'); ?>
			</h2>
			<?php


			include("left-nav.php");
			?>
			<div id='main'>
<?php 
	$board_templates = fetchBoardTemplates();
	$card_templates = fetchCardTemplates();
	$rule_types = fetchRuleTypes();
?>
				<div class='admin-container'>
					<div class='header left dark'>
						<a class='action with-text' title='<?php echo translate("Add new board template"); ?>'
							href='#' onclick='add_new_board_template();'><span class='image add'></span> <?php echo translate('New Board Template'); ?>
						</a>
						<a class='action with-text' title='<?php echo translate("Reload board templates from installation folder"); ?>'
							href='#' onclick='reload_board_templates();'><img src='models/site-templates/images/reload_from_folder.png'> <?php echo translate('Reload Board Templates'); ?>
						</a>
					</div>
					<div id="board-templates">
<?php 
	foreach ($board_templates as $templateid => $templatedata) {
?>
						<div id='board-template-<?php echo $templateid; ?>' class='board-template'>
							<div class='header'>
								<div class='collapse collapse-board-template'></div>
								<span class='board-template-display-name'><?php echo $templatedata['display_name']; ?> </span>
								<?php if ($templatedata["active"] == 0) { ?>
								<a class='action' title='<?php echo translate("Board template not active. Activate it."); ?>'
									href='#' onclick='activate_board_template("<?php echo $templatedata["id"]; ?>");'><span class='image active-0'></span></a>
								<?php } else { ?>
								<a class='action' title='<?php echo translate("Board template active. Deactivate it."); ?>'
									href='#' onclick='deactivate_board_template("<?php echo $templatedata["id"]; ?>");'><span class='image active-1'></span></a>
								<?php } ?>
								<a class='action' title='<?php echo translate("Delete board template"); ?>'
									href='#' onclick='delete_board_template("<?php echo $templatedata["id"]; ?>");'><span class='image trash'></span></a>
								<a class='action'
									title='<?php echo translate("Display board template"); ?>'
									href='#' onclick='display_board_template("<?php echo $templatedata["id"]; ?>");'><span class='image display'></span></a>
								<a class='action' title='<?php echo translate('Clone board template'); ?>'
									href='#' onclick='clone_board_template("<?php echo $templatedata["id"]; ?>");'><img src='models/site-templates/images/clone.png'></a> 
								<a class='action' title='<?php echo translate('Export to disk'); ?>'
									href='#' onclick='export_board_template("<?php echo $templatedata["id"]; ?>");'><img src='models/site-templates/images/export.png'></a>
							</div>
						</div>
<?php 
	}
?>
					</div>
					<div class='header left dark'>
						<a class='action with-text' title='<?php echo translate("Add new card template"); ?>'
							href='#' onclick='add_new_card_template();'><span class='image add'></span> <?php echo translate('New Card Template'); ?>
						</a>
						<a class='action with-text' title='<?php echo translate("Reload card templates from installation folder"); ?>'
							href='#' onclick='reload_card_templates();'><img src='models/site-templates/images/reload_from_folder.png'> <?php echo translate('Reload Card Templates'); ?>
						</a>
					</div>
					<div id="card-templates">
<?php 
		
	foreach ($card_templates as $templateid => $templatedata) {
?>
						<div id='card-template-<?php echo $templateid; ?>' class='card-template'>
							<div class='header'>
								<div class='collapse collapse-card-template'></div>
								<span class='card-template-display-name'><?php echo $templatedata['display_name']; ?> </span>
								<?php if ($templatedata["active"] == 0) { ?>
								<a class='action' title='<?php echo translate("Card template not active. Activate it."); ?>'
									href='#' onclick='activate_card_template("<?php echo $templatedata["id"]; ?>");'><span class='image active-0'></span></a>
								<?php } else { ?>
								<a class='action' title='<?php echo translate("Card template active. Deactivate it."); ?>'
									href='#' onclick='deactivate_card_template("<?php echo $templatedata["id"]; ?>");'><span class='image active-1'></span></a>
								<?php } ?>
								<a class='action' title='<?php echo translate("Delete card template"); ?>'
									href='#' onclick='delete_card_template("<?php echo $templatedata["id"]; ?>");'><span class='image trash'></span></a>
								<a class='action'
									title='<?php echo translate("Display card template"); ?>'
									href='#' onclick='display_card_template("<?php echo $templatedata["id"]; ?>");'><span class='image display'></span></a>
								<a class='action' title='<?php echo translate('Clone card template'); ?>'
									href='#' onclick='clone_card_template("<?php echo $templatedata["id"]; ?>");'><img src='models/site-templates/images/clone.png'></a> 
								<a class='action' title='<?php echo translate('Export to disk'); ?>'
									href='#' onclick='export_card_template("<?php echo $templatedata["id"]; ?>");'><img src='models/site-templates/images/export.png'></a>
							</div>
						</div>
<?php 
	}
?>
					</div>
					<div class='header left dark'>
						<a class='action with-text' title='<?php echo translate("Add new rule type"); ?>'
							href='#' onclick='add_new_rule_type();'><span class='image add'></span> <?php echo translate('New Rule Type'); ?>
						</a>
						<a class='action with-text' title='<?php echo translate("Reload rule types from installation folder"); ?>'
							href='#' onclick='reload_rule_types();'><img src='models/site-templates/images/reload_from_folder.png'> <?php echo translate('Reload Rule Types'); ?>
						</a>
					</div>
					<div id="rule-types">
<?php 
	foreach ($rule_types as $typeid => $typedata) {
?>
						<div id='rule-type-<?php echo $typeid; ?>' class='rule-type'>
							<div class='header'>
								<div class='collapse collapse-rule-type'></div>
								<span class='rule-type-display-name'><?php echo $typedata['display_name']; ?> </span>
								<?php if ($typedata["active"] == 0) { ?>
								<a class='action' title='<?php echo translate("Rule type not active. Activate it."); ?>'
									href='#' onclick='activate_rule_type("<?php echo $typedata["id"]; ?>");'><span class='image active-0'></span></a>
								<?php } else { ?>
								<a class='action' title='<?php echo translate("Rule type active. Deactivate it."); ?>'
									href='#' onclick='deactivate_rule_type("<?php echo $typedata["id"]; ?>");'><span class='image active-1'></span></a>
								<?php } ?>
								<a class='action' title='<?php echo translate("Delete rule type"); ?>'
									href='#' onclick='delete_rule_type("<?php echo $typedata["id"]; ?>");'><span class='image trash'></span></a>
								<a class='action'
									title='<?php echo translate("Display rule type"); ?>'
									href='#' onclick='display_rule_type("<?php echo $typedata["id"]; ?>");'><span class='image display'></span></a>
								<a class='action' title='<?php echo translate('Clone rule type'); ?>'
									href='#' onclick='clone_rule_type("<?php echo $typedata["id"]; ?>");'><img src='models/site-templates/images/clone.png'></a> 
								<a class='action' title='<?php echo translate('Export to disk'); ?>'
									href='#' onclick='export_rule_type("<?php echo $typedata["id"]; ?>");'><img src='models/site-templates/images/export.png'></a>
							</div>
						</div>
<?php 
	}
?>
					</div>
				</div>
				<div id='bottom'></div>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="scripts/jquery.ui.touch-punch.min.js"></script>
	<script type="text/javascript" src="scripts/jquery.bpopup.min.js"></script>
	<script type="text/javascript" src="scripts/jqueryui-editable.min.js"></script>
	<script type="text/javascript">
		$('document').ready(function() {
		});
	</script>
	<div id="popup_dialog"></div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	<script type="text/javascript">
		function add_new_board_template() {
			$('#popup_dialog').bPopup({
				loadUrl: 'add_new_board_template_dialog.php' 
			});		
		}
		
		function reload_board_templates() {
			$('#popup_dialog').bPopup({
				loadUrl: 'reload_board_templates_dialog.php' 
			});		
		}
		
		function reload_card_templates() {
			$('#popup_dialog').bPopup({
				loadUrl: 'reload_card_templates_dialog.php' 
			});		
		}
		
		function reload_rule_types() {
			$('#popup_dialog').bPopup({
				loadUrl: 'reload_rule_types_dialog.php' 
			});		
		}		
	</script>
	</body>
</html>
