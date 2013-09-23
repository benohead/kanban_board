<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	$userId = $_REQUEST['userid'];
	if (isset($_REQUEST["verbose"])) {
		$verbose = trim($_REQUEST["verbose"]);
	}
	else {
		$verbose = 0;
	}

	$userdetails = fetchUserDetails(NULL, NULL, $userId); //Fetch user details

	//Delete selected account
	//Update display name
	if ($userdetails['display_name'] != $_POST['display']){
		$displayname = trim($_POST['display']);

		//Validate display name
		if(displayname_exists($displayname))
		{
			$errors[] = translate('Display name %1$s is already in use',$displayname);
		}
		elseif(min_max_range(5,25,$displayname))
		{
			$errors[] = translate('Your display name must be between %m1% and %m2% characters in length',5,25);
		}
		elseif(!ctype_alnum($displayname)){
			$errors[] = translate('Display name can only include alpha-numeric characters');
		}
		else {
			if (updateDisplayName($userId, $displayname)){
				$successes[] = translate('Display name changed to %1$s', $displayname);
			}
			else {
				$errors[] = translate('Fatal SQL error');
			}
		}

	}
	else {
		$displayname = $userdetails['display_name'];
	}

	//Activate account
	if(isset($_POST['activate']) && $_POST['activate'] == "activate"){
		if (setUserActive($userdetails['activation_token'])){
			$successes[] = translate('%1$s\'s account has been manually activated', $displayname);
		}
		else {
			$errors[] = translate('Fatal SQL error');
		}
	}

	//Update email
	if ($userdetails['email'] != $_POST['email']){
		$email = trim($_POST["email"]);

		//Validate email
		if(!isValidEmail($email))
		{
			$errors[] = translate('Invalid email address');
		}
		elseif(email_exists($email))
		{
			$errors[] = translate('Email %1$s is already in use',$email);
		}
		else {
			if (updateEmail($userId, $email)){
				$successes[] = translate('Account email updated');
			}
			else {
				$errors[] = translate('Fatal SQL error');
			}
		}
	}

	//Update title
	if ($userdetails['title'] != $_POST['title']){
		$title = trim($_POST['title']);

		//Validate title
		if(min_max_range(1,50,$title))
		{
			$errors[] = translate('Titles must be between %1$d and %2$d characters in length',1,50);
		}
		else {
			if (updateTitle($userId, $title)){
				$successes[] = translate('%1$s\'s title changed to %2$s', $displayname, $title);
			}
			else {
				$errors[] = translate('Fatal SQL error');
			}
		}
	}

	//Remove role
	if(!empty($_POST['removeRole'])){
		$remove = $_POST['removeRole'];
		if ($deletion_count = removeRole($remove, $userId)){
			$successes[] = translate('Removed access from %1$d roles', $deletion_count);
		}
		else {
			$errors[] = translate('Fatal SQL error');
		}
	}

	if(!empty($_POST['addRole'])){
		$add = $_POST['addRole'];
		if ($addition_count = addRole($add, $userId)){
			$successes[] = translate('Added access to %1$d roles', $addition_count);
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