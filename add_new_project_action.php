<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");
	
	if (isset($_REQUEST["verbose"])) {
		$verbose = trim($_REQUEST["verbose"]);
	}
	else {
		$verbose = 0;
	}
	$projectname = trim($_POST["projectname"]);
	$displayname = trim($_POST["displayname"]);
	if (isset($_POST["active"])) {
		$active = trim($_POST["active"]);
	}
	else {
		$active = 0;
	}
	
	if(min_max_range(1,50,$projectname)) {
		$errors[] = translate('The project name must have between %1$d and %2$d characters.', 1, 50);
	}
	if(!ctype_alnum($projectname)) {
		$errors[] = translate('The project name must only contain alphanumeric characters.');
	}
	if(min_max_range(1,50,$displayname)) {
		$errors[] = translate('The display name must have between %1$d and %2$d characters.', 1, 50);
	}
	
	//End data validation
	if(count($errors) == 0)
	{
		//Construct a user object
		$project = new Project($projectname,$displayname,$active);
	
		//Checking this flag tells us whether there were any errors such as possible data duplication occured
		if(!$project->status) {
			if($project->projectname_taken) $errors[] = translate('Project name already taken');
			if($project->displayname_taken) $errors[] = translate('Display name already taken');
			if (count($errors) == 0) {
				$errors[] = translate('An unknown error occured while preparing to add the project');
			}
		}
		else if(!$project->addProject()) {
			if($project->sql_failure)  $errors[] = translate('Error inserting the project in the database');
			if (count($errors) == 0) {
				$errors[] = translate('An unknown error occured while adding the project');
			}
		}
		else {
			$successes[] = translate('Project "%1$s" successfully created.', $displayname);
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