<?php
require_once ("../models/db-settings.php");
require_once ("../models/funcs.php");
require_once ("../functions.php");

function update_8() {
	global $db_table_prefix;
	$db_issue = false;
	
	$kanban_versions_entry = "
				INSERT INTO `" . $db_table_prefix . "versions` (`version_type`, `version_number`) VALUES
				('application',8),
				('db schema', 8);
				";

	$kanban_statistics_sql = "
		CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "statistics` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`statistics_name` varchar(50) NOT NULL,
		`display_name` varchar(50) NOT NULL,
		`active` tinyint(1) NOT NULL,
		`board_id` int(11) NOT NULL,
		`frequency` char(1) NOT NULL,
		`type` char(1) NOT NULL,
		`attribute_id` varchar(50) NOT NULL,
		`groups` text NOT NULL,
		`conditions` text NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		";

	$kanban_statistics_results_sql = "
		CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "statistics_results` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`statistics_id` int(11) NOT NULL,
		`stat_time` varchar(32) NOT NULL,
		`card_group` text NOT NULL,
		`card_count` int(11) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		";
	
	$db_issue = $db_issue | install_basic_settings($kanban_versions_entry, "versions");
	$db_issue = $db_issue | install_table($kanban_statistics_sql, "statistics");
	$db_issue = $db_issue | install_table($kanban_statistics_results_sql, "statistics_results");
	
	return $db_issue;
}
?>