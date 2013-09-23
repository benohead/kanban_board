<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");
	require_once("functions.php");

	$board_id = trim($_REQUEST["boardid"]);
	if (isset($_REQUEST["verbose"])) {
		$verbose = trim($_REQUEST["verbose"]);
	}
	else {
		$verbose = 0;
	}
	$board_cards = $_REQUEST["board_cards"];

	if (!moveCardsFromArchiveToBoard($board_cards, $board_id)) {
		$successes[] = translate('Card retrieved');
	}
	else {
		error_log("error code returned: ".$rc);
		$errors[] = translate('Error retrieving the cards');
	}
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