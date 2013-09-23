<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");

	$userid = trim($_POST["userid"]);
	if (isset($_REQUEST["verbose"])) {
		$verbose = trim($_REQUEST["verbose"]);
	}
	else {
		$verbose = 0;
	}
	
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."users WHERE id=?");
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$stmt->close();

	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_roles WHERE user_id=?");
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$stmt->close();

	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_boards WHERE user_id=?");
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$stmt->close();

	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_projects WHERE user_id=?");
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$stmt->close();
	
	$successes[] = translate('Successfully deleted the user');

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