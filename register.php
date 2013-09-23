<?php
require_once ("models/config.php");
if (! securePage ( $_SERVER ['PHP_SELF'] )) {
	die ();
}

// Prevent the user visiting the logged in page if he/she is already logged in and is not an administrator
if (isUserLoggedIn ()) {
	if (! $logged_in_user->checkRole ( array (
			2 
	) )) {
		header ( "Location: account.php" );
		die ();
	}
}

// Forms posted
if (! empty ( $_POST )) {
	$errors = array ();
	$email = trim ( $_POST ["email"] );
	$username = trim ( $_POST ["username"] );
	$displayname = trim ( $_POST ["displayname"] );
	$password = trim ( $_POST ["password"] );
	$confirm_pass = trim ( $_POST ["passwordc"] );
	$captcha = md5 ( $_POST ["captcha"] );
	
	if ($captcha != $_SESSION ['captcha']) {
		$errors [] = translate ( 'Failed security question' );
	}
	if (min_max_range ( 5, 25, $username )) {
		$errors [] = translate ( 'Your username must be between %1$d and %2$d characters in length', 5, 25 );
	}
	if (! ctype_alnum ( $username )) {
		$errors [] = translate ( 'Username can only include alpha-numeric characters' );
	}
	if (min_max_range ( 5, 25, $displayname )) {
		$errors [] = translate ( 'Your display name must be between %1$d and %2$d characters in length', 5, 25 );
	}
	if (! ctype_alnum ( $displayname )) {
		$errors [] = translate ( 'Display name can only include alpha-numeric characters' );
	}
	if (min_max_range ( 8, 50, $password ) && min_max_range ( 8, 50, $confirm_pass )) {
		$errors [] = translate ( 'Your password must be between %1$d and %2$d characters in length', 8, 50 );
	} else if ($password != $confirm_pass) {
		$errors [] = translate ( 'Your password and confirmation password must match' );
	}
	if (! isValidEmail ( $email )) {
		$errors [] = translate ( 'Invalid email address' );
	}
	// End data validation
	if (count ( $errors ) == 0) {
		// Construct a user object
		$user = new User ( $username, $displayname, $password, $email, isUserLoggedIn () );
		
		// Checking this flag tells us whether there were any errors such as possible data duplication occured
		if (! $user->status) {
			if ($user->username_taken)
				$errors [] = translate ( 'Username %1$s is already in use', $username );
			if ($user->displayname_taken)
				$errors [] = translate ( 'Display name %1$s is already in use', $displayname );
			if ($user->email_taken)
				$errors [] = translate ( 'Email %1$s is already in use', $email );
		} else {
			// Attempt to add the user to the database, carry out finishing tasks like emailing the user (if required)
			if (! $user->userAddUser ()) {
				if ($user->mail_failure)
					$errors [] = translate ( 'Fatal error attempting mail, contact your server administrator' );
				if ($user->sql_failure)
					$errors [] = translate ( 'Fatal SQL error' );
			}
		}
	}
	if (count ( $errors ) == 0) {
		$successes [] = $user->success;
	}
}

require_once ("models/header.php");
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
				<?php echo translate('Register'); ?>
			</h2>


			<div id='main' class='full-width'>
				<?php
				echo resultBlock ( $errors, $successes );
				?>
				<div id="page_signup">
					<div class="center">
						<form name='newUser' action='<?php echo $_SERVER['PHP_SELF']; ?>'
							method='post'>
							<fieldset>
								<label for="email"><strong>Email</strong></label>
								<div class="input_container">
									<div>
										<input type="text" id="email" name="email" value="">
									</div>
								</div>
							</fieldset>
							<fieldset>
								<label for="displayname"><strong>Full name</strong></label>
								<div class="input_container">
									<div>
										<input type="text" id="displayname" name="displayname" value="">
									</div>
								</div>
							</fieldset>
							<fieldset>
								<label for="password"><strong>Password</strong></label>
								<div class="input_container">
									<div>
										<input type="password" id="password" name="password" value="">
									</div>
								</div>
							</fieldset>
							<fieldset>
								<label for="password"><strong>Repeat password</strong></label>
								<div class="input_container">
									<div>
										<input type="password" id="passwordc" name="passwordc" value="">
									</div>
								</div>
							</fieldset>
							<fieldset>
								<label for="username"><strong>Username</strong></label>
								<div class="input_container">
									<div>
										<input type="text" id="username" name="username" value="">
									</div>
								</div>
							</fieldset>
							<fieldset>
								<label for="captcha"><strong>Security code (<img src='models/captcha.php'>)</strong></label>								
								<div class="input_container">
									<div>
										<input type="text" id="captcha" name="captcha" value="">
									</div>
								</div>								
							</fieldset>
							<input type="submit" class="disabled" value="Register me" disabled="disabled">
						</form>
					</div>
				</div>
			</div>

			<div id='bottom'></div>
		</div>
	</div>

	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script>
$(document).ready(function(){
	$('#username').keyup(check_username);
	$('#displayname').keyup(check_displayname);
	$('#passwordc').keyup(check_passwordc);
	$('#password').keyup(check_passwordc);
});

function check_username(){
	var username = $('#username').val();
	if(username == '' || username.length < 5){
		$('#username').parent().removeClass("available").removeClass("notavailable");
		$('input[type=submit]').addClass('disabled');
		$('input[type=submit]').addAttr('disabled');
	}
	else {
		jQuery.ajax({
			type: 'POST',
			url: 'check_username.php',
			data: 'username='+ username,
			cache: false,
			success: function(response){
				if(response == 1){
					$('#username').parent().removeClass("available").addClass("notavailable");
					$('input[type=submit]').addClass('disabled');
					$('input[type=submit]').addAttr('disabled');
}
				else {
					$('#username').parent().removeClass("notavailable").addClass("available");
					$('input[type=submit]').removeClass('disabled')
					$('input[type=submit]').removeAttr('disabled');
				}
			}
		});
	}
}

function check_displayname(){
	var displayname = $('#displayname').val();
	if(displayname == '' || displayname.length < 5){
		$('#displayname').parent().removeClass("available").removeClass("notavailable");
		$('input[type=submit]').addClass('disabled');
		$('input[type=submit]').addAttr('disabled');
	}
	else {
		jQuery.ajax({
			type: 'POST',
			url: 'check_displayname.php',
			data: 'displayname='+ displayname,
			cache: false,
			success: function(response){
				if(response == 1){
					$('#displayname').parent().removeClass("available").addClass("notavailable");
					$('input[type=submit]').addClass('disabled');
					$('input[type=submit]').addAttr('disabled');
				}
				else {
					$('#displayname').parent().removeClass("notavailable").addClass("available");
					$('input[type=submit]').removeClass('disabled');
					$('input[type=submit]').removeAttr('disabled');
				}
			}
		});
	}
}

function check_passwordc(){
	var password = $('#password').val();
	var passwordc = $('#passwordc').val();
	if (password != passwordc) {
		$('#passwordc').parent().removeClass("available").addClass("notavailable");
		$('input[type=submit]').addClass('disabled');
		$('input[type=submit]').addAttr('disabled');
	}
	else {
		$('#passwordc').parent().removeClass("notavailable").addClass("available");
		$('input[type=submit]').removeClass('disabled');
		$('input[type=submit]').removeAttr('disabled');
	}
}
</script>
	<div id="copyright">
		&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a>
	</div>
</body>
</html>
