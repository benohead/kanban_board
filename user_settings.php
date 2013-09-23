<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}

//Prevent the user visiting the logged in page if he is not logged in
if(!isUserLoggedIn()) {
	header("Location: login.php"); die();
}

if(!empty($_POST))
{
	$errors = array();
	$successes = array();
	$password = $_POST["password"];
	$password_new = $_POST["passwordc"];
	$password_confirm = $_POST["passwordcheck"];

	$errors = array();
	$email = $_POST["email"];

	//Perform some validation
	//Feel free to edit / change as required

	//Confirm the hashes match before updating a users password
	$entered_pass = generate_hash($password,$logged_in_user->hash_pw);

	if (trim($password) == ""){
		$errors[] = translate('Please enter your password');
	}
	else if($entered_pass != $logged_in_user->hash_pw)
	{
		//No match
		$errors[] = translate('Your password and confirmation password must match');
	}
	if($email != $logged_in_user->email)
	{
		if(trim($email) == "")
		{
			$errors[] = translate('Please enter your email address');
		}
		else if(!isValidEmail($email))
		{
			$errors[] = translate('Invalid email address');
		}
		else if(email_exists($email))
		{
			$errors[] = translate('Email %1$s is already in use', $email);
		}

		//End data validation
		if(count($errors) == 0)
		{
			$logged_in_user->updateEmail($email);
			$successes[] = translate('Account email updated');
		}
	}

	if ($password_new != "" OR $password_confirm != "")
	{
		if(trim($password_new) == "")
		{
			$errors[] = translate('Please enter your new password');
		}
		else if(trim($password_confirm) == "")
		{
			$errors[] = translate('Please confirm your new password');
		}
		else if(min_max_range(8,50,$password_new))
		{
			$errors[] = translate('New password must be between %1$d and %2$d characters in length',8,50);
		}
		else if($password_new != $password_confirm)
		{
			$errors[] = translate('Your password and confirmation password must match');
		}

		//End data validation
		if(count($errors) == 0)
		{
			//Also prevent updating if someone attempts to update with the same password
			$entered_pass_new = generate_hash($password_new,$logged_in_user->hash_pw);
				
			if($entered_pass_new == $logged_in_user->hash_pw)
			{
				//Don't update, this fool is trying to update with the same password
				$errors[] = translate('You cannot update with the same password');
			}
			else
			{
				//This function will create the new hash and update the hash_pw property.
				$logged_in_user->updatePassword($password_new);
				$successes[] = translate('Account password updated');
			}
		}
	}
	if(count($errors) == 0 AND count($successes) == 0){
		$errors[] = translate('Nothing to update');
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
				<?php echo translate('User Settings'); ?>
			</h2>
				<?php
				include("left-nav.php");
				?>
			<div id='main'>
				<?php
				echo resultBlock($errors,$successes);
				?>
				<div id='regbox'>
					<form name='updateAccount' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
						<p>
							<label><?php echo translate('Password:'); ?> </label>
							<input type='password' name='password' />
						</p>
						<p>
							<label><?php echo translate('Email:'); ?> </label>
							<input type='text' name='email' value='<?php echo $logged_in_user->email; ?>' placeholder='<?php echo translate('Email Address'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('New Password:'); ?> </label>
							<input type='password' name='passwordc' />
						</p>
						<p>
							<label><?php echo translate('Confirm Password:'); ?> </label>
							<input type='password' name='passwordcheck' />
						</p>
						<p>
							<label>&nbsp;</label>
							<input type='submit' value='<?php echo translate('Update'); ?>' class='submit' />
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
