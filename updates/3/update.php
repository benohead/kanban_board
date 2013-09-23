<?php
require_once ("../models/db-settings.php");
require_once ("../models/funcs.php");
require_once ("../functions.php");

function update_3() {
	global $db_table_prefix;
	$db_issue = false;
	
	$kanban_versions_entry = "
				INSERT INTO `" . $db_table_prefix . "versions` (`version_type`, `version_number`) VALUES
				('application',3),
				('db schema', 3);
				";
	
	$kanban_generator_types_sql = "
		CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "generator_types` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`generator_name` varchar(50) NOT NULL,
		`display_name` varchar(50) NOT NULL,
		`active` tinyint(1) NOT NULL,
		`action` varchar(32) NOT NULL,
		`generator_js` longtext NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		";
	
	$boards_sql = "ALTER TABLE " . $db_table_prefix . "boards ADD board_gens TEXT;";
	
	$db_issue = $db_issue | install_basic_settings($kanban_versions_entry, "versions");
	$db_issue = $db_issue | install_table($kanban_generator_types_sql, "generator_types");
	$generator_types_path = "../install/generator_types";
	$db_issue = $db_issue | import_generator_types($generator_types_path);
	$db_issue = $db_issue | update_table($boards_sql, "boards");

	return $db_issue;
}
?>