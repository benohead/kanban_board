<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	
	$errors = array();
	if (isset($_REQUEST["verbose"])) {
		$verbose = trim($_REQUEST["verbose"]);
	}
	else {
		$verbose = 0;
	}

	$role = trim($_POST['rolename']);

	//Validate request
	if (roleNameExists($role)){
		$errors[] = translate('Role name %1$s is already in use', $role);
	}
	elseif (min_max_range(1, 50, $role)){
		$errors[] = translate('Role names must be between %1$d and %2$d characters in length', 1, 50);
	}
	else{
		if (createRole($role)) {
			$successes[] = translate('Successfully created the role "%1$s"', $role);
		}
		else {
			$errors[] = translate('Fatal SQL error');
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