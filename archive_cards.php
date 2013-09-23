<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once "functions.php";
$board_id = $_POST['boardid'];
$cards = $_POST['cards'];
addCardsToArchive($board_id, $cards);
?>