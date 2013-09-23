<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once "functions.php";
$board_id = $_POST['boardid'];
$rules = serialize((isset($_POST['rules'])?$_POST['rules']:array()));
set_board_rules($board_id, $rules);
?>