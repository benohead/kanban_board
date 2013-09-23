<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once "functions.php";
$board_id = $_POST['boardid'];
$cards = /*objectToArray(json_decode(decompress(*/objectToArray(json_decode($_POST['cards']))/*)))*/;
addCardsToHistory($board_id);
add_cards_to_board($board_id, $cards);
emptyBoardForwardHistory($board_id);
//gatherBoardStatistics($board_id);
?>