<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once "functions.php";
$board_id = $_POST['boardid'];
$cards = serialize(/*objectToArray(json_decode(decompress(*/objectToArray(json_decode($_POST['cards'])))/*)))*/;
addCardsToHistory($board_id);
store_cards_on_board($board_id, $cards);
emptyBoardForwardHistory($board_id);
//gatherBoardStatistics($board_id);
?>