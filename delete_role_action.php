<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");

	$roleid = trim($_POST["roleid"]);
	if (isset($_REQUEST["verbose"])) {
		$verbose = trim($_REQUEST["verbose"]);
	}
	else {
		$verbose = 0;
	}
	
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."roles WHERE id=?");
	$stmt->bind_param("i", $roleid);
	$stmt->execute();
	$stmt->close();

	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_roles WHERE role_id=?");
	$stmt->bind_param("i", $roleid);
	$stmt->execute();
	$stmt->close();
	
	$successes[] = translate('Successfully deleted the role');

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