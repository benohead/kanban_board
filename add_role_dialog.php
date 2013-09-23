<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
?>
<div id='register_dialog' class='awkb_dialog'>
	<div id="error_message"></div>
	<p>
		<label><?php echo translate('Role Name:'); ?> </label>
		<input type='text' id='rolename' name='rolename' placeholder='<?php echo translate('Role name'); ?>'/>
	</p>
	<p>
		<a id="add_button" href="#" onclick="add_role();" class='action with-text' title='<?php echo translate("Add role"); ?>'><span class='image add'></span><?php echo translate('Add role'); ?></a>
	</p>
</div>

	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script>
$(document).ready(function(){
	$('#rolename').keyup(check_rolename);
});

function check_rolename(){
	var rolename = $('#rolename').val();
	if(rolename == '' || rolename.length < 5){
		$('#rolename').removeClass("available").addClass("notavailable");
	}
	else {
		$('#rolename').removeClass("notavailable").addClass("available");
	}
}
function add_role() {
	var rolename = $('#rolename').val();
	$.ajax({
		type: "POST", 
		url: "add_role_action.php", 
		data: {
			rolename: rolename,
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
