<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");
require_once("functions.php");

$errors = array();
if (isset($_REQUEST["verbose"])) {
	$verbose = trim($_REQUEST["verbose"]);
}
else {
	$verbose = 0;
}
$board_id = trim($_POST["boardid"]);
$boardtemplatename=trim($_POST['boardtemplatename']);
$boarddisplayname=trim($_POST['boarddisplayname']);
if (isset($_POST["boardactive"])) {
	$boardactive = trim($_POST["boardactive"]);
}
else {
	$boardactive = 0;
}
$cardtemplatename=trim($_POST['cardtemplatename']);
$carddisplayname=trim($_POST['carddisplayname']);
if (isset($_POST["cardactive"])) {
	$cardactive = trim($_POST["cardactive"]);
}
else {
	$cardactive = 0;
}

if(min_max_range(0,50,$boardtemplatename)) {
	$errors[] = translate('The board template name must have between %1$d and %2$d characters.', 0, 50);
}
if(!ctype_alnum($boardtemplatename)) {
	$errors[] = translate('The board template name must only contain alphanumeric characters.');
}
if(min_max_range(0,50,$boarddisplayname)) {
	$errors[] = translate('The board template display name must have between %1$d and %2$d characters.', 0, 50);
}

if(min_max_range(0,50,$cardtemplatename)) {
	$errors[] = translate('The card template name must have between %1$d and %2$d characters.', 0, 50);
}
if(!ctype_alnum($cardtemplatename)) {
	$errors[] = translate('The card template name must only contain alphanumeric characters.');
}
if(min_max_range(0,50,$carddisplayname)) {
	$errors[] = translate('The card template display name must have between %1$d and %2$d characters.', 0, 50);
}

if(strlen($boardtemplatename)==0 && strlen($cardtemplatename)==0) {
	$errors[] = translate('Both the board template name and the board template name cannot be empty.');
}

//End data validation

if(count($errors) == 0) {
	if (strlen($boardtemplatename)>0) {
		$result = createBoardTemplateFromBoard($board_id, $boardtemplatename, $boarddisplayname, $boardactive);					
		if($result == 1) {
			$errors[] = translate('Board template name already exists.');
		}
						if($result == 2) {
			$errors[] = translate('Board template display name already exists.');
		}
		if($result == 3) {
			$errors[] = translate('SQL syntax error while creating the board template.');
		}
		if($result == 4) {
			$errors[] = translate('Failed to create board template.');
		}
	}
}
if(count($errors) == 0) {
	if (strlen($cardtemplatename)>0) {
		$result = createCardTemplateFromBoard($board_id, $cardtemplatename, $carddisplayname, $cardactive);
		if($result == 1) {
			$errors[] = translate('Card template name already exists.');
		}
		if($result == 3) {
			$errors[] = translate('SQL syntax error while creating the card template.');
		}
		if($result == 4) {
			$errors[] = translate('Failed to create card template.');
		}
						}
}
if(count($errors) == 0) {
	$successes[] = translate('Template(s) successfully created.');
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
