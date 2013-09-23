<?php
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once "functions.php";
$board_id = $_POST['boardid'];
$previousversion = getLastHistoryForwardVersionId($board_id);
if (isset($previousversion)) {
	addCardsToHistory($board_id);
	reactivateHistoryForwardVersion($board_id, $previousversion);
}
?>