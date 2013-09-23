<?php
require_once ("../models/db-settings.php");
require_once ("../models/funcs.php");
require_once ("../functions.php");

function update_6() {
	global $db_table_prefix;
	$db_issue = false;
	
	$kanban_versions_entry = "
				INSERT INTO `" . $db_table_prefix . "versions` (`version_type`, `version_number`) VALUES
				('application',6),
				('db schema', 6);
				";
	
	$db_issue = $db_issue | install_basic_settings($kanban_versions_entry, "versions");
	$generator_types_path = "../install/generator_types";
	$db_issue = $db_issue | import_generator_types($generator_types_path);
	
	return $db_issue;
}
?>