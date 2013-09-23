<?php
if (isset($projects) && count($projects) > 0) {
	?>
			<form class="header-part" name='changeProject' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
				<select name='projectid' id='projectid' onchange="this.form.submit()">
<?php
			foreach ($projects as $project){
				if ($current_project == $project['id']){
?>
					<option value='<?php echo $project['id']; ?>' selected='selected'><?php echo $project['display_name']; ?></option>
<?php
				}
				else {
?>
					<option value='<?php echo $project['id']; ?>'><?php echo $project['display_name']; ?></option>
<?php
				}
			}
?>
				</select>
				<input type='hidden' name='readonly' value=<?php echo $readonly; ?> />
			</form>
<?php 
		}
	
		if (isset($boards) && count($boards) > 0) {
?>
			<form class="header-part" name='changeBoard' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
				<select name='boardid' id='boardid' onchange="this.form.submit()">
<?php
			foreach ($boards as $board){
				if ($current_board == $board['id']) {
?>
					<option value='<?php echo $board['id']; ?>' selected='selected'><?php echo $board['display_name']; ?></option>
<?php
				}
				else {
?>
					<option value='<?php echo $board['id']; ?>'><?php echo $board['display_name']; ?></option>
<?php			
				}
			}
?>
				</select>
				<input type='hidden' name='readonly' value=<?php echo $readonly; ?> />
			</form>
<?php 
		}
?>
