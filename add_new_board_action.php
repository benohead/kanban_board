<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){die();}
	require_once("models/db-settings.php");
	require_once("functions.php");

	$errors = array();
	if (isset($_REQUEST["verbose"])) {
		$verbose = trim($_REQUEST["verbose"]);
	}
	else {
		$verbose = 0;
	}
	$projectid = trim($_POST["projectid"]);
	$boardname = trim($_POST["boardname"]);
	$displayname = trim($_POST["displayname"]);
	$templateid = trim($_POST["templateid"]);
	$card_template_id = trim($_POST["card_template_id"]);
	if (isset($_POST["active"])) {
		$active = trim($_POST["active"]);	
	}
	else {
		$active = 0;
	}
	
	if(min_max_range(1,50,$boardname)) {
		$errors[] = translate('The board name must have between %1$d and %2$d characters.', 1, 50);
	}
	if(!ctype_alnum($boardname)) {
		$errors[] = translate('The board name must only contain alphanumeric characters.');
	}
	if(min_max_range(1,50,$displayname)) {
		$errors[] = translate('The display name must have between %1$d and %2$d characters.', 1, 50);
	}

	//End data validation
	if(count($errors) == 0) {	
		//Construct a user object
		$board = new board($projectid,$boardname,$displayname,$active,$templateid,$card_template_id,0,0);
		
		//Checking this flag tells us whether there were any errors such as possible data duplication occured
		if(!$board->status) {
			if($board->boardname_taken) $errors[] = translate('Board name already taken');
			if($board->displayname_taken) $errors[] = translate('Display name already taken');
		}
		else {
			if(!$board->addboard()) {
				if($board->sql_failure)  $errors[] = translate('Error inserting the board in the database');
			}
			else {
				$successes[] = translate('Board "%1$s" successfully created.', $board->displayname);
			}
		}
	}
	if(count($errors) == 0) {
		$results = array(
				"error" => false,
				"messages" => $successes);
	}
	else {
		$results = array(
				"error" => true,
				"messages" => $errors);
	}
	if($verbose != 0) {
		echo json_encode($results);
	}
?>