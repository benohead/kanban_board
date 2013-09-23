<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once "functions.php";
$templateid = $_POST['templateid'];
$columns = $_POST['columns'];
updateBoardTemplateColumns($templateid, $columns);
?>