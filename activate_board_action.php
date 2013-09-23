<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");

	$board_id = trim($_POST["boardid"]);
		
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."boards SET active=1 WHERE id=?");

	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->close();

	$successes[] = translate('Successfully activated the board');

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