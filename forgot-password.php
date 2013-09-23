<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}

//User has confirmed they want their password changed
if(!empty($_GET["confirm"]))
{
	$token = trim($_GET["confirm"]);

	if($token == "" || !validateActivationToken($token,TRUE))
	{
		$errors[] = translate('Your activation token is not valid');
	}
	else
	{
		$rand_pass = getUniqueCode(15); //Get unique code
		$secure_pass = generate_hash($rand_pass); //Generate random hash
		$userdetails = fetchUserDetails(NULL,$token); //Fetchs user details
		$mail = new userMail();

		//Setup our custom hooks
		$hooks = array(
				"searchStrs" => array("#GENERATED-PASS#","#USERNAME#"),
				"subjectStrs" => array($rand_pass,$userdetails["display_name"])
		);

		if(!$mail->newTemplateMsg("your-lost-password.txt",$hooks))
		{
			$errors[] = translate('Error building email template');
		}
		else
		{
			if(!$mail->sendMail($userdetails["email"],"Your new password"))
			{
				$errors[] = translate('Fatal error attempting mail, contact your server administrator');
			}
			else
			{
				if(!updatePasswordFromToken($secure_pass,$token))
				{
					$errors[] = translate('Fatal SQL error');
				}
				else
				{
					if(!flagLostPasswordRequest($userdetails["user_name"],0))
					{
						$errors[] = translate('Fatal SQL error');
					}
					else {
						$successes[]  = translate('We have emailed you a new password');
					}
				}
			}
		}
	}
}

//User has denied this request
if(!empty($_GET["deny"]))
{
	$token = trim($_GET["deny"]);

	if($token == "" || !validateActivationToken($token,TRUE))
	{
		$errors[] = translate('Your activation token is not valid');
	}
	else
	{

		$userdetails = fetchUserDetails(NULL,$token);

		if(!flagLostPasswordRequest($userdetails["user_name"],0))
		{
			$errors[] = translate('Fatal SQL error');
		}
		else {
			$successes[] = translate('Lost password request cancelled');
		}
	}
}

//Forms posted
if(!empty($_POST))
{
	$email = $_POST["email"];
	$username = sanitize($_POST["username"]);

	//Perform some validation
	//Feel free to edit / change as required

	if(trim($email) == "")
	{
		$errors[] = translate('Please enter your email address');
	}
	//Check to ensure email is in the correct format / in the db
	else if(!isValidEmail($email) || !email_exists($email))
	{
		$errors[] = translate('Invalid email address');
	}

	if(trim($username) == "")
	{
		$errors[] = translate('Please enter your username');
	}
	else if(!usernameExists($username))
	{
		$errors[] = translate('Invalid username');
	}

	if(count($errors) == 0)
	{

		//Check that the username / email are associated to the same account
		if(!emailUsernameLinked($email,$username))
		{
			$errors[] =  translate('Username or email address is invalid');
		}
		else
		{
			//Check if the user has any outstanding lost password requests
			$userdetails = fetchUserDetails($username);
			if($userdetails["lost_password_request"] == 1)
			{
				$errors[] = translate('There is already a outstanding lost password request on this account');
			}
			else
			{
				//Email the user asking to confirm this change password request
				//We can use the template builder here

				//We use the activation token again for the url key it gets regenerated everytime it's used.

				$mail = new userMail();
				$confirm_url = translate('Confirm')."\n".$website_url."forgot-password.php?confirm=".$userdetails["activation_token"];
				$deny_url = translate('Deny')."\n".$website_url."forgot-password.php?deny=".$userdetails["activation_token"];

				//Setup our custom hooks
				$hooks = array(
						"searchStrs" => array("#CONFIRM-URL#","#DENY-URL#","#USERNAME#"),
						"subjectStrs" => array($confirm_url,$deny_url,$userdetails["user_name"])
				);

				if(!$mail->newTemplateMsg("lost-password-request.txt",$hooks))
				{
					$errors[] = translate('Error building email template');
				}
				else
				{
					if(!$mail->sendMail($userdetails["email"],"Lost password request"))
					{
						$errors[] = translate('Fatal error attempting mail, contact your server administrator');
					}
					else
					{
						//Update the DB to show this account has an outstanding request
						if(!flagLostPasswordRequest($userdetails["user_name"],1))
						{
							$errors[] = translate('Fatal SQL error');
						}
						else {
								
							$successes[] = translate('We have emailed you instructions on how to regain access to your account');
						}
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
				<?php echo translate('Forgot Password'); ?>
			</h2>
				<?php
				include("left-nav.php");
				?>
			<div id='main'>
				<?php
				echo resultBlock($errors,$successes);
				?>
				<div id='regbox'>
					<form name='newLostPass' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
						<p>
							<label><?php echo translate('Username:'); ?> </label>
							<input type='text' name='username' placeholder='<?php echo translate('Username'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('Email:'); ?> </label>
							<input type='text' name='email' placeholder='<?php echo translate('Email Address'); ?>'/>
						</p>
						<p>
							<label>&nbsp;</label> <input type='submit' value='<?php echo translate('Submit'); ?>'
								class='submit' />
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
