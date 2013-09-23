<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
$userId = $_GET['userid'];

$userdetails = fetchUserDetails(NULL, NULL, $userId); //Fetch user details
$userRole = fetchUserRoles($userId);
$roleData = fetchAllRoles();
?>
<div id='admin_user_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<table class='admin'>
		<tr>
			<td>
				<h3>
					<?php echo translate('User Information'); ?>
				</h3>
				<div id='regbox'>
					<p>
						<label><?php echo translate('Display Name:'); ?> </label>
						<input type='text' name='display' id='display' value='<?php echo $userdetails['display_name']; ?>' placeholder='<?php echo translate('Display Name'); ?>'/>
					</p>
					<p>
						<label><?php echo translate('Email:'); ?> </label>
						<input type='text' name='email' id='email' value='<?php echo $userdetails['email']; ?>' placeholder='<?php echo translate('Email Address'); ?>'/>
					</p>
					<?php
					if ($userdetails['active'] != '1'){
					?>
					<p>
						<label><?php echo translate('Activate:'); ?> </label>
						<input type='checkbox' name='activate' id='activate' value='activate'>
					</p>
					<?php
					}
					?>
					<p>
						<label><?php echo translate('Title:'); ?> </label>
						<input type='text' name='title' id='title' value='<?php echo $userdetails['title']; ?>' placeholder='<?php echo translate('Title'); ?>'/>
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
						<br> <input type='checkbox' name='removeRole[<?php echo $v1['id']; ?>]' class='removeRole'
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
						<br> <input type='checkbox' name='addRole[<?php echo $v1['id']; ?>]' class='addRole'
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
	<p>
		<a id="update_button" href="#" onclick="update();" class='action with-text' title='<?php echo translate("Update user"); ?>'><span class='image save'></span><?php echo translate('Update user'); ?></a>
	</p>
	<script type="text/javascript">
		function update() {
			var display = $('#display').val();
			var email = $('#email').val();
			var activate = $('#activate').val();
			var title = $('#title').val();
			var removeRoles = $('input:checkbox:checked.removeRole').map(function () { return this.value; }).get();
			var addRoles = $('input:checkbox:checked.addRole').map(function () { return this.value; }).get();
			$.ajax({
				type: "POST", 
				url: "admin_user_action.php", 
				data: {
					display: display, 
					email: email, 
					activate: activate, 
					title: title,
					removeRole: removeRoles,
					addRole: addRoles,
					verbose: 1,
					userid: <?php echo $userId; ?>
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
</div>
	