<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");

	$projectid = trim($_POST["projectid"]);
		
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."projects SET active=1 WHERE id=?");

	$stmt->bind_param("i", $projectid);
	$stmt->execute();
	$stmt->close();

	$successes[] = translate('Successfully activated the project');

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