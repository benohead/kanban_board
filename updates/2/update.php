<?php
require_once ("../models/db-settings.php");
require_once ("../models/funcs.php");
require_once ("../functions.php");

function update_2() {
	global $db_table_prefix;
	$db_issue = false;
	
	$versions_sql = "
				CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "versions` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`version_type` varchar(50) NOT NULL,
				`version_number` int(11) NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
				";
	
	$versions_entry = "
				INSERT INTO `" . $db_table_prefix . "versions` (`version_type`, `version_number`) VALUES
				('application',2),
				('db schema', 2);
				";
	
	$board_columns_sql = "ALTER TABLE " . $db_table_prefix . "board_columns ADD description TEXT;";
	
	$board_template_columns_sql = "ALTER TABLE " . $db_table_prefix . "board_template_columns ADD description TEXT;";

	$template_path = "../install/templates";
	$board_templates_path = $template_path."/boards";
	$card_templates_path = $template_path."/cards";
	
	$db_issue = $db_issue | install_table($versions_sql, "versions");
	$db_issue = $db_issue | install_basic_settings($versions_entry, "versions");
	$db_issue = $db_issue | update_table($board_columns_sql, "board_columns");
	$db_issue = $db_issue | update_table($board_template_columns_sql, "board_template_columns");
	$db_issue = $db_issue | import_board_templates($board_templates_path);
	$db_issue = $db_issue | import_card_templates($card_templates_path);

	return $db_issue;
}
?>