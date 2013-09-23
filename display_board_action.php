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
	$export_id_prefix = trim($_REQUEST["export_id_prefix"]);
	$board_css = trim($_REQUEST["board_css"]);
	$board_js = trim($_REQUEST["board_js"]);
	$card_attr = trim($_REQUEST["card_attr"]);
	$card_css = trim($_REQUEST["card_css"]);
	$card_js = trim($_REQUEST["card_js"]);

	if (set_board_metadata($board_id, $board_css, $board_js, $card_attr, $card_css, $card_js, $export_id_prefix)) {
		$successes[] = translate('Board updated');
	}
	else {
		$errors[] = translate('Error updating the board');
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