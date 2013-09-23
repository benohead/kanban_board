<div class="dock">
  <ul>
<?php

if (!securePage($_SERVER['PHP_SELF'])){
	die();
}

//Links for logged in user
if(isUserLoggedIn()) {
	?>
	<li><a href='board.php'><span><?php echo translate('Board'); ?></span><span id='board-image'></span></a></li>
	<li><a href='account.php'><span><?php echo translate('Account Home'); ?></span><img src="models/site-templates/images/home.png" /></a></li>
	<li><a href='user_settings.php'><span><?php echo translate('User Settings'); ?></span><img src="models/site-templates/images/user_settings.png" /></a></li>
	<li><a href='logout.php'><span><?php echo translate('Logout'); ?></span><img src="models/site-templates/images/logout.png" /></a></li>
<?php	
//Links for role 2 (default admin)
if ($logged_in_user->checkRole(array(2))){
?>
	<li><a href='admin_configuration.php'><span><?php echo translate('Admin Configuration'); ?></span><img src="models/site-templates/images/settings.png" /></a></li>
	<li><a href='admin_users.php'><span><?php echo translate('Admin Users'); ?></span><img src="models/site-templates/images/users.png" /></a></li>
	<li><a href='admin_roles.php'><span><?php echo translate('Admin Roles'); ?></span><img src="models/site-templates/images/roles.png" /></a></li>
	<li><a href='admin_projects.php'><span><?php echo translate('Admin Projects'); ?></span><img src="models/site-templates/images/projects.png" /></a></li>
<?php
	}
}
//Links for users not logged in
else {
?>
	<li><a href='index.php'><span><?php echo translate('Home'); ?></span><img src="models/site-templates/images/home.png" /></a></li>
	<li><a href='login.php'><span><?php echo translate('Login'); ?></span><img src="models/site-templates/images/login.png" /></a></li>
	<li><a href='register.php'><span><?php echo translate('Register'); ?></span><img src="models/site-templates/images/register.png" /></a></li>
	<li><a href='forgot-password.php'><span><?php echo translate('Forgot Password'); ?></span><img src="models/site-templates/images/forgot_password.png" /></a></li>
	<?php
	if ($email_activation) {
?>
	<li><a href='resend-activation.php'><span><?php echo translate('Resend Activation Email'); ?></span><img src="models/site-templates/images/email.png" /></a></li>
<?php
	}
}
?>
  </ul>
</div>
