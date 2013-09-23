<?php

if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
?>
			<div id='left-nav'>
<?php
//Links for logged in user
if(isUserLoggedIn()) {
	?>
<ul>
	<li><a href='board.php'><?php echo translate('Board'); ?></a></li>
	<li><a href='account.php'><?php echo translate('Account Home'); ?></a></li>
	<li><a href='user_settings.php'><?php echo translate('User Settings'); ?></a></li>
	<li><a href='logout.php'><?php echo translate('Logout'); ?></a></li>
</ul>
<?php	
//Links for role 2 (default admin)
if ($logged_in_user->checkRole(array(2))){
?>
<ul>
	<li><a href='admin_configuration.php'><?php echo translate('Admin Configuration'); ?></a></li>
	<li><a href='admin_users.php'><?php echo translate('Admin Users'); ?></a></li>
	<li><a href='admin_roles.php'><?php echo translate('Admin Roles'); ?></a></li>
	<li><a href='admin_projects.php'><?php echo translate('Admin Projects'); ?></a></li>
	<li><a href='admin_templates.php'><?php echo translate('Admin Templates'); ?></a></li>
</ul>
<?php
	}
}
//Links for users not logged in
else {
?>
<ul>
	<li><a href='index.php'><?php echo translate('Home'); ?></a></li>
	<li><a href='login.php'><?php echo translate('Login'); ?></a></li>
	<li><a href='register.php'><?php echo translate('Register'); ?></a></li>
	<li><a href='forgot-password.php'><?php echo translate('Forgot Password'); ?></a></li>
	<?php
	if ($email_activation) {
?>
	<li><a href='resend-activation.php'><?php echo translate('Resend Activation Email'); ?></a></li>
	<?php
	}
	?>
</ul>
<?php
}

?>
</div>
<?php //include 'models/menu.php'; ?>
