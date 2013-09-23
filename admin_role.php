<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
$roleId = $_GET['id'];

//Check if selected role exists
if(!roleIdExists($roleId)){
	header("Location: admin_roles.php"); die();
}

$roleDetails = fetchRoleDetails($roleId); //Fetch information specific to role

//Forms posted
if(!empty($_POST)){

	//Delete selected role
	if(!empty($_POST['delete'])){
		$deletions = $_POST['delete'];
		if ($deletion_count = deleteRole($deletions)){
			$successes[] = translate('Successfully deleted %1$d role(s)', $deletion_count);
		}
		else {
			$errors[] = translate('Fatal SQL error');
		}
	}
	else
	{
		//Update role name
		if($roleDetails['name'] != $_POST['name']) {
			$role = trim($_POST['name']);

			//Validate new name
			if (roleNameExists($role)){
				$errors[] = translate('Role name "%1$s" is already in use', $role);
			}
			elseif (min_max_range(1, 50, $role)){
				$errors[] = translate('Role names must be between %1$d and %2$d characters in length', 1, 50);
			}
			else {
				if (updateRoleName($roleId, $role)){
					$successes[] = translate('Role name changed to "%1$s"', $role);
				}
				else {
					$errors[] = translate('Fatal SQL error');
				}
			}
		}

		//Remove role
		if(!empty($_POST['removeRole'])){
			$remove = $_POST['removeRole'];
			if ($deletion_count = removeRole($roleId, $remove)) {
				$successes[] = translate('Successfully removed %1$d user(s)', $deletion_count);
			}
			else {
				$errors[] = translate('Fatal SQL error');
			}
		}

		//Add role
		if(!empty($_POST['addRole'])){
			$add = $_POST['addRole'];
			if ($addition_count = addRole($roleId, $add)) {
				$successes[] = translate('Successfully added %1$d user(s)', $addition_count);
			}
			else {
				$errors[] = translate('Fatal SQL error');
			}
		}

		$roleDetails = fetchRoleDetails($roleId);
	}
}

$roleUsers = fetchRoleUsers($roleId); //Retrieve list of users with membership
$userData = fetchAllUsers(); //Fetch all users

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
				<?php echo translate('Admin Roles'); ?>
			</h2>
				<?php
				include("left-nav.php");
				?>
			<div id='main'>
				<?php
				echo resultBlock($errors,$successes);
				?>
				<form name='adminRole'
					action='<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $roleId; ?>' method='post'>
					<table class='admin'>
						<tr>
							<td>
								<h3>
									<?php echo translate('Role Information'); ?>
								</h3>
								<div id='regbox'>
									<p>
										<label><?php echo translate('ID:'); ?> </label>
										<?php echo $roleDetails['id']; ?>
									</p>
									<p>
										<label><?php echo translate('Name:'); ?> </label> 
										<input type='text' name='name' value='<?php echo $roleDetails['name']; ?>' placeholder='<?php echo translate('Role Name'); ?>'/>
									</p>
									<p>
										<label><?php echo translate('Delete:'); ?> </label> <input type='checkbox'
											name='delete[<?php echo $roleDetails['id']; ?>]'
											id='delete[<?php echo $roleDetails['id']; ?>]'
											value='<?php echo $roleDetails['id']; ?>'>
									</p>
								</div>
							</td>
							<td>
								<h3>
									<?php echo translate('Role Membership'); ?>
								</h3>
								<div id='regbox'>
									<p>
										<?php echo translate('Remove Members:'); ?>
										<?php
										//List users with role
										foreach ($userData as $v1) {
	if(isset($roleUsers[$v1['id']])){
?>
										<br> <input type='checkbox' name='removeRole[<?php echo $v1['id']; ?>]'
											id='removeRole[<?php echo $v1['id']; ?>]' value='<?php echo $v1['id']; ?>'>
										<?php echo $v1['display_name']; ?>
										<?php
	}
}

?>
									</p>
									<p>
										<?php echo translate('Add Members:'); ?>
										<?php
										//List users without role
										foreach ($userData as $v1) {
	if(!isset($roleUsers[$v1['id']])){
?>
										<br> <input type='checkbox' name='addRole[<?php echo $v1['id']; ?>]'
											id='addRole[<?php echo $v1['id']; ?>]' value='<?php echo $v1['id']; ?>'>
										<?php echo $v1['display_name']; ?>
										<?php
	}
}
?>
									</p>
								</div>
							</td>
						</tr>
					</table>
					<p>
						<label>&nbsp;</label> <input type='submit' value='<?php echo translate('Update'); ?>'
							class='submit' />
					</p>
				</form>
			</div>
			<div id='bottom'></div>
		</div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
