<?php
require_once ("../models/db-settings.php");
require_once ("../models/funcs.php");
require_once ("../functions.php");

function update_5() {
	global $db_table_prefix;
	$db_issue = false;
	
	$kanban_versions_entry = "
				INSERT INTO `" . $db_table_prefix . "versions` (`version_type`, `version_number`) VALUES
				('application',5),
				('db schema', 5);
				";
	
	$kanban_card_archive_sql = "
		CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "card_archive` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`board_id` int(11) NOT NULL,
		`cards` longtext NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		";
	
	$db_issue = $db_issue | install_basic_settings($kanban_versions_entry, "versions");
	$db_issue = $db_issue | install_table($kanban_card_archive_sql, "card_archive");

	return $db_issue;
}
?>