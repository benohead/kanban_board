<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
?>
<div id='register_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<label><?php echo translate('User Name:'); ?> </label>
		<input type='text' id='username' name='username' placeholder='<?php echo translate('Username'); ?>'/>
	</p>
	<p>
		<label><?php echo translate('Display Name:'); ?> </label>
		<input type='text' id='displayname' name='displayname' placeholder='<?php echo translate('Display Name'); ?>'/>
	</p>
	<p>
		<label><?php echo translate('Password:'); ?> </label>
		<input type='password' id='password' name='password' />
	</p>
	<p>
		<label><?php echo translate('Confirm:'); ?> </label>
		<input type='password' id='passwordc' name='passwordc' />
	</p>
	<p>
		<label><?php echo translate('Email:'); ?> </label>
		<input type='text' name='email' id='email' placeholder='<?php echo translate('Email Address'); ?>'/>
	</p>
	<p>
		<label><?php echo translate('Security Code:'); ?> </label>
		<img src='models/captcha.php'>
	</p>
	<p>
		<label><?php echo translate('Enter Security Code:'); ?> </label>
		<input name='captcha' id='captcha' type='text' placeholder='<?php echo translate('Security Code'); ?>'>
	</p>
	<p>
		<a id="register_button" href="#" onclick="register_user();" class='action with-text' title='<?php echo translate("Register user"); ?>'><span class='image add'></span><?php echo translate('Register user'); ?></a>
	</p>
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
		$('#username').removeClass("available").removeClass("notavailable");
	}
	else {
		jQuery.ajax({
			type: 'POST',
			url: 'check_username.php',
			data: 'username='+ username,
			cache: false,
			success: function(response){
				if(response == 1){
					$('#username').removeClass("available").addClass("notavailable");
					$('input[type=submit]').attr('disabled', 'disabled');
				}
				else {
					$('#username').removeClass("notavailable").addClass("available");
					$('input[type=submit]').removeAttr('disabled');
				}
			}
		});
	}
}

function check_displayname(){
	var displayname = $('#displayname').val();
	if(displayname == '' || displayname.length < 5){
		$('#displayname').removeClass("available").removeClass("notavailable");
	}
	else {
		jQuery.ajax({
			type: 'POST',
			url: 'check_displayname.php',
			data: 'displayname='+ displayname,
			cache: false,
			success: function(response){
				if(response == 1){
					$('#displayname').removeClass("available").addClass("notavailable");
					$('input[type=submit]').attr('disabled', 'disabled');
				}
				else {
					$('#displayname').removeClass("notavailable").addClass("available");
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
		$('#passwordc').removeClass("available").addClass("notavailable");
		$('input[type=submit]').attr('disabled', 'disabled');
	}
	else {
		$('#passwordc').removeClass("notavailable").addClass("available");
		$('input[type=submit]').removeAttr('disabled');
	}
}
function register_user() {
	var username = $('#username').val();
	var displayname = $('#displayname').val();
	var email = $('#email').val();
	var captcha = $('#captcha').val();
	var password = $('#password').val();
	var passwordc = $('#passwordc').val();
		$.ajax({
		type: "POST", 
		url: "register_action.php", 
		data: {
			username: username,
			displayname: displayname,
			email: email,
			password: password,
			passwordc: passwordc,
			captcha: captcha,
			verbose: 1
		},
		success: function(data) {
			data = JSON.parse(data);
			if (!data.error) {
				//parent.$("#popup_dialog").bPopup().close();
				location.reload(true);						
			}
			else {
			   $('#error_message').html(data.messages);
			}
		}
	});
}
</script>
