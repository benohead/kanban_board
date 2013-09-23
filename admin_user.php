<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
$userId = $_GET['id'];

//Check if selected user exists
if(!userIdExists($userId)){
	header("Location: admin_users.php"); die();
}

$userdetails = fetchUserDetails(NULL, NULL, $userId); //Fetch user details

//Forms posted
if(!empty($_POST))
{
	//Delete selected account
	if(!empty($_POST['delete'])){
		$deletions = $_POST['delete'];
		if ($deletion_count = deleteUsers($deletions)) {
			$successes[] = translate('You have successfully deleted %1$d users', $deletion_count);
		}
		else {
			$errors[] = translate('Fatal SQL error');
		}
	}
	else
	{
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

		$userdetails = fetchUserDetails(NULL, NULL, $userId);
	}
}

$userRole = fetchUserRoles($userId);
$roleData = fetchAllRoles();

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
				<?php echo translate('Admin User'); ?>
			</h2>
				<?php
				include("left-nav.php");

				?>
			<div id='main'>
				<?php
				echo resultBlock($errors,$successes);
				?>
				<form name='adminUser' action='<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $userId; ?>'
					method='post'>
					<table class='admin'>
						<tr>
							<td>
								<h3>
									<?php echo translate('User Information'); ?>
								</h3>
								<div id='regbox'>
									<p>
										<label><?php echo translate('ID:'); ?> </label>
										<?php echo $userdetails['id']; ?>
									</p>
									<p>
										<label><?php echo translate('Username:'); ?> </label>
										<?php echo $userdetails['user_name']; ?>
									</p>
									<p>
										<label><?php echo translate('Display Name:'); ?> </label>
										<input type='text' name='display' value='<?php echo $userdetails['display_name']; ?>' placeholder='<?php echo translate('Display Name'); ?>'/>
									</p>
									<p>
										<label><?php echo translate('Email:'); ?> </label> <input type='text' name='email' value='<?php echo $userdetails['email']; ?>' placeholder='<?php echo translate('Email Address'); ?>'/>
									</p>
									<p>
										<label><?php echo translate('Active:'); ?> </label>
										<?php
										//Display activation link, if account inactive
										if ($userdetails['active'] == '1'){
	echo translate('Yes');
}
else{
	echo translate('No');
	?>
									</p>
									<p>
										<label><?php echo translate('Activate:'); ?> </label>
										<input type='checkbox' name='activate' id='activate' value='activate'>
										<?php
}

?>
									</p>
									<p>
										<label><?php echo translate('Title:'); ?> </label>
										<input type='text' name='title' value='<?php echo $userdetails['title']; ?>' placeholder='<?php echo translate('Title'); ?>'/>
									</p>
									<p>
										<label><?php echo translate('Sign Up:'); ?> </label>
										<?php echo date("j M, Y", $userdetails['sign_up_stamp']); ?>
									</p>
									<p>
										<label><?php echo translate('Last Sign In:'); ?> </label>
										<?php
										//Last sign in, interpretation
										if ($userdetails['last_sign_in_stamp'] == '0'){
	echo translate('Never');
}
else {
	echo date("j M, Y", $userdetails['last_sign_in_stamp']);
}

?>
									</p>
									<p>
										<label><?php echo translate('Delete:'); ?> </label> <input type='checkbox'
											name='delete[<?php echo $userdetails['id']; ?>]'
											id='delete[<?php echo $userdetails['id']; ?>]' value='<?php echo $userdetails['id']; ?>'>
									</p>
									<p>
										<label>&nbsp;</label> <input type='submit' value='<?php echo translate('Update'); ?>'
											class='submit' />
									</p>
								</div>
							</td>
							<td>
								<h3>
									<?php echo translate('Role Membership'); ?>
								</h3>
								<div id='regbox'>
									<p>
										<?php echo translate('Remove Role:'); ?>
										<?php
										//List of roles user is apart of
										foreach ($roleData as $v1) {
	if(isset($userRole[$v1['id']])){
?>
										<br> <input type='checkbox' name='removeRole[<?php echo $v1['id']; ?>]'
											id='removeRole[<?php echo $v1['id']; ?>]' value='<?php echo $v1['id']; ?>'>
										<?php echo $v1['name']; ?>
										<?php	
	}
}

//List of roles user is not apart of
?>
									</p>
									<p>
										<?php echo translate('Add Role:'); ?>
										<?php
										foreach ($roleData as $v1) {
	if(!isset($userRole[$v1['id']])){
?>
										<br> <input type='checkbox' name='addRole[<?php echo $v1['id']; ?>]'
											id='addRole[<?php echo $v1['id']; ?>]' value='<?php echo $v1['id']; ?>'>
										<?php echo $v1['name']; ?>
										<?php
	}
}

?>
									</p>
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div id='bottom'></div>
		</div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
