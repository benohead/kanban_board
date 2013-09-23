<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}

//Forms posted
if(!empty($_POST) && $email_activation)
{
	$email = $_POST["email"];
	$username = $_POST["username"];

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
		$errors[] =  translate('Please enter your username');
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
			$errors[] = translate('Username or email address is invalid');
		}
		else
		{
			$userdetails = fetchUserDetails($username);
				
			//See if the user's account is activation
			if($userdetails["active"]==1)
			{
				$errors[] = translate('Your account is already activated');
			}
			else
			{
				if ($resend_activation_threshold == 0) {
					$hours_diff = 0;
				}
				else {
					$last_request = $userdetails["last_activation_request"];
					$hours_diff = round((time()-$last_request) / (3600*$resend_activation_threshold),0);
				}

				if($resend_activation_threshold!=0 && $hours_diff <= $resend_activation_threshold)
				{
					$errors[] = translate('An activation email has already been sent to this email address in the last %1$d hour(s)',$resend_activation_threshold);
				}
				else
				{
					//For security create a new activation url;
					$new_activation_token = generate_activation_token();
						
					if(!updateLastActivationRequest($new_activation_token,$username,$email))
					{
						$errors[] = translate('Fatal SQL error');
					}
					else
					{
						$mail = new userMail();

						$activation_url = $website_url."activate-account.php?token=".$new_activation_token;

						//Setup our custom hooks
						$hooks = array(
								"searchStrs" => array("#ACTIVATION-URL","#USERNAME#"),
								"subjectStrs" => array($activation_url,$userdetails["display_name"])
						);

						if(!$mail->newTemplateMsg("resend-activation.txt",$hooks))
						{
							$errors[] = translate('Error building email template');
						}
						else
						{
							if(!$mail->sendMail($userdetails["email"],"Activate your ".$website_name." Account"))
							{
								$errors[] = translate('Fatal error attempting mail, contact your server administrator');
							}
							else
							{
								//Success, user details have been updated in the db now mail this information out.
								$successes[] = translate('We have emailed you a new activation link, please check your email');
							}
						}
					}
				}
			}
		}
	}
}

//Prevent the user visiting the logged in page if he/she is already logged in
if(isUserLoggedIn()) {
	header("Location: account.php"); die();
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
				<?php echo translate('Resend Activation'); ?>
			</h2>
				<?php
				include("left-nav.php");
				?>
			<div id='main'>
				<?php
				echo resultBlock($errors,$successes);
				?>
				<div id='regbox'>
					<?php
					//Show disabled if email activation not required
					if(!$email_activation)
					{
						echo translate('This feature is currently disabled');
					}
					else
					{
						?>
					<form name='resendActivation' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
						<p>
							<label><?php echo translate('Username:'); ?> </label>
							<input type='text' name='username' placeholder='<?php echo translate('Username'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('Email:'); ?> </label>
							<input type='text' name='email' placeholder='<?php echo translate('Email Address'); ?>'/>
						</p>
						<p>
							<label>&nbsp;</label>
							<input type='submit' value='<?php echo translate('Submit'); ?>' class='submit' />
						</p>
					</form>
					<?php
}
?>
				</div>
			</div>
			<div id='bottom'></div>
		</div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
