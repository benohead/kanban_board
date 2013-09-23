<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once "functions.php";
$board_id = $_POST['boardid'];
$cardid = $_POST['cardid'];
addCardsToHistory($board_id);
remove_card_from_board($board_id, $cardid);
emptyBoardForwardHistory($board_id);
//gatherBoardStatistics($board_id);
?>