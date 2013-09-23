<?php
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
} 
require_once "functions.php";
$board_id = $_POST['boardid'];
$previousversion = getLastHistoryVersionId($board_id);
if (isset($previousversion)) {
	addCardsToHistoryForward($board_id);
	reactivateHistoryVersion($board_id, $previousversion);
}
?>
