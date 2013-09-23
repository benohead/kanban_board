<?php
require_once ("../models/db-settings.php");
require_once ("../models/funcs.php");
require_once ("../functions.php");

function update_9() {
	global $db_table_prefix;
	$db_issue = false;
	
	$kanban_versions_entry = "
				INSERT INTO `" . $db_table_prefix . "versions` (`version_type`, `version_number`) VALUES
				('application',9),
				('db schema', 9);
				";
	
	$kanban_board_cards_sql = "
		CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "board_cards` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`board_id` int(11) NOT NULL,
		`card_name` varchar(50) NOT NULL,
		`attributes` text NOT NULL,
		`order_nr` int(11) NOT NULL,					
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		";
	
	$db_issue = $db_issue | install_basic_settings ( $kanban_versions_entry, "versions" );
	$db_issue = $db_issue | install_table ( $kanban_board_cards_sql, "board_cards" );
	$db_issue = $db_issue | migrate_board_cards ();
	
	return $db_issue;
}
?>