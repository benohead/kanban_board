<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}

$roleData = fetchAllRoles(); //Retrieve list of all roles

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
					<table class='admin'>
						<tr>
							<th></th>
							<th><?php echo translate('Role Name'); ?></th>
						</tr>
						<?php
						//List each role
						foreach ($roleData as $v1) {
?>
						<tr>
							<td>
								<a class='action' title='<?php echo translate("Delete role"); ?>' 
									onclick='delete_role("<?php echo $v1['id']; ?>");' 
									id='delete[<?php echo $v1['id']; ?>]' href='#'>
									<span class='image trash'></span></a>
							</td>
							<td><a href='admin_role.php?id=<?php echo $v1['id']; ?>'><?php echo $v1['name']; ?></a>
							</td>
						</tr>
						<?php
}
?>
					</table>
				<div id="actions">
					<a class="action with-text" href="#" onclick='add_role();' title="<?php echo translate('Add new role');?>">
						<img src="models/site-templates/images/add.png">
						<?php echo translate('Add new role');?>
					</a>
				</div>
			</div>
			<div id='bottom'></div>
		</div>
	</div>
	<div id="popup_dialog"></div>
	<script type="text/javascript" src="scripts/jquery.bpopup.min.js"></script>
	<script type="text/javascript">	
		function delete_role(roleid) {
			$('#popup_dialog').bPopup({
				loadUrl: 'delete_role_dialog.php?roleid='+roleid 
			});
		}
		
		function add_role() {
			$('#popup_dialog').bPopup({
				loadUrl: 'add_role_dialog.php'
			});
		}
	</script>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
