<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once "functions.php";
$board_id = $_POST['boardid'];
$card_attributes = $_POST['attributes'];
set_board_card_attributes($board_id, serialize($card_attributes));
?>