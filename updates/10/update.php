<?php
require_once ("../models/db-settings.php");
require_once ("../models/funcs.php");
require_once ("../functions.php");

function update_10() {
	global $db_table_prefix;
	$db_issue = false;
	
	$kanban_versions_entry = "
				INSERT INTO `" . $db_table_prefix . "versions` (`version_type`, `version_number`) VALUES
				('application', 10),
				('db schema', 10);
				";

	$boards_sql = "ALTER TABLE " . $db_table_prefix . "boards ADD export_id_prefix VARCHAR(32);";
	
	$db_issue = $db_issue | install_basic_settings($kanban_versions_entry, "versions");
	$db_issue = $db_issue | update_table($boards_sql, "boards");
	
	return $db_issue;
}
?>