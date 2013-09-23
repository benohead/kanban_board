<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}

$userData = fetchAllUsers(); //Fetch information for all users

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
				<?php echo translate('Admin Users'); ?>
			</h2>
				<?php
				include("left-nav.php");
				?>
			<div id='main'>
				<?php
				echo resultBlock($errors,$successes);
				?>
					<table class='admin'>
						<tr>
							<th></th>
							<th><?php echo translate('Username'); ?></th>
							<th><?php echo translate('Display Name'); ?></th>
							<th><?php echo translate('Title'); ?></th>
							<th><?php echo translate('Sign Up'); ?></th>
							<th><?php echo translate('Last Sign In'); ?></th>
						</tr>
						<?php
						//Cycle through users
						foreach ($userData as $v1) {
?>
						<tr>
							<td>
								<a class='action' title='<?php echo translate("Delete user"); ?>' 
									onclick='delete_user("<?php echo $v1['id']; ?>");' 
									id='delete[<?php echo $v1['id']; ?>]' href='#'>
									<span class='image trash'></span></a>
							</td>
							<td>
								<a href='#' onclick='admin_user("<?php echo $v1['id']; ?>");'><?php echo $v1['user_name']; ?></a>
							</td>
							<td><?php echo $v1['display_name']; ?></td>
							<td><?php echo $v1['title']; ?></td>
							<td><?php echo date("j M, Y", $v1['sign_up_stamp']); ?></td>
							<td><?php	
							//Interprety last login
							if ($v1['last_sign_in_stamp'] == '0'){
								echo translate('Never');
							}
							else {
								echo date("j M, Y", $v1['last_sign_in_stamp']);
							}
							?>
							</td>
						</tr>
						<?php
}
?>
					</table>
				<div id="actions">
					<a class="action with-text" href="#" onclick='register();' title="<?php echo translate('Add new user');?>">
						<img src="models/site-templates/images/add.png">
						<?php echo translate('Add new user');?>
					</a>
				</div>
				</div>
			<div id='bottom'></div>
		</div>
	</div>
	<div id="popup_dialog"></div>
	<script type="text/javascript" src="scripts/jquery.bpopup.min.js"></script>
	<script type="text/javascript">	
		function delete_user(userid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'delete_user_dialog.php?userid='+userid 
			});
		}
		
		function admin_user(userid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'admin_user_dialog.php?userid='+userid 
			});
		}
		
		function register() {
			$('#popup_dialog').bPopup({
				loadUrl: 'register_dialog.php'
			});
		}
	</script>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
