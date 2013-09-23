<?php

require_once("models/config.php");
require_once("functions.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}

//Prevent the user visiting the login page if he/she is already logged in
if(isUserLoggedIn()) {
	if (isset($_REQUEST['return'])) {
		header("Location: ".urldecode($_REQUEST['return']));
	}
	else {
		header("Location: account.php");
	}
	die();
}

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$username = sanitize(trim($_POST["username"]));
	$password = trim($_POST["password"]);

	//Perform some validation
	//Feel free to edit / change as required
	if($username == "")
	{
		$errors[] = translate('Please enter your username');
	}
	if($password == "")
	{
		$errors[] = translate('Please enter your password');
	}

	if(count($errors) == 0)
	{
		//A security note here, never tell the user which credential was incorrect
		if(!usernameExists($username))
		{
			$errors[] = translate('Username or password is invalid');
		}
		else
		{
			$userdetails = fetchUserDetails($username);
			//See if the user's account is activated
			if($userdetails["active"]==0)
			{
				$errors[] = translate('Your account is in-active. Check your emails / spam folder for account activation instructions');
			}
			else
			{
				//Hash the password and use the salt from the database to compare the password.
				$entered_pass = generate_hash($password,$userdetails["password"]);

				if($entered_pass != $userdetails["password"])
				{
					//Again, we know the password is at fault here, but lets not give away the combination incase of someone bruteforcing
					$errors[] = translate('Username or password is invalid');
				}
				else
				{
					//Passwords match! we're good to go'
						
					//Construct a new logged in user object
					//Transfer some db data to the session object
					$logged_in_user = new logged_in_user();
					$logged_in_user->email = $userdetails["email"];
					$logged_in_user->user_id = $userdetails["id"];
					$logged_in_user->hash_pw = $userdetails["password"];
					$logged_in_user->title = $userdetails["title"];
					$logged_in_user->displayname = $userdetails["display_name"];
					$logged_in_user->username = $userdetails["user_name"];
						
					//Update last sign in
					$logged_in_user->update_last_sign_in();
					$_SESSION["kanbanUser"] = $logged_in_user;

					$newVersion = checkUpdates();
					$nextUrl = (isset($_REQUEST['return'])) ? urldecode($_REQUEST['return']) : "board.php"; 
					if (!$newVersion) {
						header("Location: ".$nextUrl);
						die();
					}
					else {
						header('Location: updates/index.php?return='.urlencode($nextUrl));
						die();
					}
				}
			}
		}
	}
}

require_once("models/header.php");

?>
<body>
	<div id='wrapper'>
		<div id='top'>
			<div id='logo'></div>
		</div>
		<div id='content'>
			<h1>
				<?php echo $website_name; ?>
			</h1>
			<h2>
				<?php echo translate('Login'); ?>
			</h2>
				<?php
				include("left-nav.php");
				?>
			<div id='main'>
				<?php
				echo resultBlock($errors,$successes);
				?>
				<div id='regbox'>
					<form name='login' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
						<p>
							<label><?php echo translate('Username:'); ?> </label>
							<input type='text' name='username' placeholder='<?php echo translate('Username'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('Password:'); ?> </label>
							<input type='password' name='password' />
						</p>
						<p>
							<label>&nbsp;</label>
						<?php if (isset($_REQUEST['return'])) {?>
							<input type='hidden' value='<?php echo urldecode($_REQUEST['return']); ?>' name='return' />
						<?php } ?>
							<input type='submit' value='<?php echo translate('Login'); ?>' class='submit' />
						</p>
					</form>
				</div>
			</div>
			<div id='bottom'></div>
		</div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
