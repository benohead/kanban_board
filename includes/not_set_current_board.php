<?php
if (!isset($current_board)) {
	?>
			<p><a class="header-part" href="admin_projects.php"><?php echo translate('No board available. Please go to the Project Administration.'); ?></a></p>
<?php
			die();
		}
?>