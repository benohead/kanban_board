<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
if (!isUserLoggedIn()){
	header('Location: login.php');
}
else {
	header('Location: board.php');
}
?>
