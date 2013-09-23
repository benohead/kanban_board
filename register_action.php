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
	$email = trim($_POST["email"]);
	$username = trim($_POST["username"]);
	$displayname = trim($_POST["displayname"]);
	$password = trim($_POST["password"]);
	$confirm_pass = trim($_POST["passwordc"]);
	$captcha = md5($_POST["captcha"]);


	if ($captcha != $_SESSION['captcha'])
	{
		$errors[] = translate('Failed security question');
	}
	if(min_max_range(5,25,$username))
	{
		$errors[] = translate('Your username must be between %1$d and %2$d characters in length', 5, 25);
	}
	if(!ctype_alnum($username)){
		$errors[] = translate('Username can only include alpha-numeric characters');
	}
	if(min_max_range(5,25,$displayname))
	{
		$errors[] = translate('Your display name must be between %1$d and %2$d characters in length', 5, 25);
	}
	if(!ctype_alnum($displayname)){
		$errors[] = translate('Display name can only include alpha-numeric characters');
	}
	if(min_max_range(8,50,$password) && min_max_range(8,50,$confirm_pass))
	{
		$errors[] = translate('Your password must be between %1$d and %2$d characters in length', 8, 50);
	}
	else if($password != $confirm_pass)
	{
		$errors[] = translate('Your password and confirmation password must match');
	}
	if(!isValidEmail($email))
	{
		$errors[] = translate('Invalid email address');
	}
	//End data validation
	if(count($errors) == 0)
	{
		//Construct a user object
		$user = new User($username,$displayname,$password,$email,isUserLoggedIn());

		//Checking this flag tells us whether there were any errors such as possible data duplication occured
		if(!$user->status)
		{
			if($user->username_taken) $errors[] = translate('Username %1$s is already in use',$username);
			if($user->displayname_taken) $errors[] = translate('Display name %1$s is already in use',$displayname);
			if($user->email_taken) 	  $errors[] = translate('Email %1$s is already in use',$email);
		}
		else
		{
			//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)
			if(!$user->userAddUser())
			{
				if($user->mail_failure) $errors[] = translate('Fatal error attempting mail, contact your server administrator');
				if($user->sql_failure)  $errors[] = translate('Fatal SQL error');
			}
		}
	}
	if(count($errors) == 0) {
		$successes[] = $user->success;
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