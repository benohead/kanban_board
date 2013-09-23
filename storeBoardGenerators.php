<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once "functions.php";
$board_id = $_POST['boardid'];
$generators = serialize((isset($_POST['generators'])?$_POST['generators']:array()));
set_board_generators($board_id, $generators);
?>