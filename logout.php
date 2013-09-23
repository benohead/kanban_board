<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}

//Log the user out
if(isUserLoggedIn())
{
	$logged_in_user->userLogOut();
}

header("Location: login.php");
die();

?>

