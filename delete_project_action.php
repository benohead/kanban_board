<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");

	$projectid = trim($_POST["projectid"]);
	if (isset($_REQUEST["verbose"])) {
		$verbose = trim($_REQUEST["verbose"]);
	}
	else {
		$verbose = 0;
	}
	
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."projects WHERE id=?");

	$stmt->bind_param("i", $projectid);
	$stmt->execute();
	$stmt->close();

	$successes[] = translate('Successfully deleted the project');

	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."boards WHERE project_id=?");

	$stmt->bind_param("i", $projectid);
	$stmt->execute();
	$stmt->close();

	$successes[] = translate('Successfully deleted the corresponding boards');

	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_projects WHERE project_id=?");

	$stmt->bind_param("i", $projectid);
	$stmt->execute();
	$stmt->close();

	$successes[] = translate('Successfully deleted the corresponding user-project relationships');

	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_boards WHERE board_id NOT IN (SELECT id FROM ".$db_table_prefix."boards)");

	$stmt->execute();
	$stmt->close();

	$successes[] = translate('Successfully deleted the corresponding user-board relationships');

	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."board_columns WHERE board_id NOT IN (SELECT id FROM ".$db_table_prefix."boards)");

	$stmt->execute();
	$stmt->close();

	$successes[] = translate('Successfully deleted the corresponding board columns');

	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."board_cards WHERE board_id NOT IN (SELECT id FROM ".$db_table_prefix."boards)");
	
	$stmt->execute();
	$stmt->close();
	
	$successes[] = translate('Successfully deleted the corresponding board cards');
	
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."board_card_attributes WHERE board_id NOT IN (SELECT id FROM ".$db_table_prefix."boards)");
	
	$stmt->execute();
	$stmt->close();
	
	$successes[] = translate('Successfully deleted the corresponding board card attributes');
	
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users " .
			"SET last_board_open=NULL " .
			"WHERE last_board_open NOT IN (SELECT id FROM ".$db_table_prefix."boards)");

	$stmt->execute();
	$stmt->close();

	$successes[] = translate('Successfully reset corresponding last accessed boards for users');

	if(count($errors) == 0) {
		$results = array(
				"error" => false,
				"messages" => $successes);
	}
	else {
		$results = array(
				"error" => true,
				"messages" => $errors);
	}
	if($verbose != 0) {
		echo json_encode($results);
	}
?>