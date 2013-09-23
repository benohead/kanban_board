<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");
require_once("functions.php");
require_once("models/header.php");

?>
<body>
	<div id='top'>
		<div id='logo'></div>
	</div>
	<div id='content'>
		<h1>
			<?php echo $website_name; ?>
		</h1>
		<h2>
			<?php echo translate('Board Rules Editor'); ?>
		</h2>
		<?php
		$board_id = trim($_REQUEST["id"]);

		//Forms posted
		if(!empty($_POST))
		{
		}
		?>
		<h2>
			<?php echo get_board_display_name($board_id); ?>
		</h2>
			<?php
			include("left-nav.php");
			?>
		<div id='main'>
			<?php				
			echo resultBlock($errors,$successes);

			$columns = get_board_columns($board_id);
			$attributes = getCompleteCardAttributes($board_id);
			$rules = unserialize_or_empty_array(get_board_rules($board_id));
			$ruleTypes = getActiveRuleTypes();

			if (!isset($rules)) {
					$rules=array();
				}

				$columnsCombobox = '<select class="column">';
				$columnsCombobox = $columnsCombobox.'<option value="ALL">'.translate('All').'</option>';
				foreach ($columns as $columnname => $columndata) {
					$columnsCombobox = $columnsCombobox.'<option value="'.$columnname.'">'.$columndata['display_name'].'</option>';
				}
				$columnsCombobox = $columnsCombobox.'</select>';

				$attributesCombobox = '<select class="attribute">';
				$attributesCombobox = $attributesCombobox.'<option value="ALL">'.translate('Always').'</option>';
				foreach ($attributes as $attributeid => $attributedata) {
					$attributename = $attributedata['name'];
					$attributesCombobox = $attributesCombobox.'<option value="'.$attributeid.'">'.$attributename.'</option>';
				}
				$attributesCombobox = $attributesCombobox.'<option value="loggedinuser">'.translate('logged in user').'</option>';
				$attributesCombobox = $attributesCombobox.'<option value="loggedinusersrole">'.translate('role of logged in user').'</option>';
				$attributesCombobox = $attributesCombobox.'</select>';

				$ruleTypesCombobox = '<select class="ruleType">';
				foreach ($ruleTypes as $ruleType) {
					$ruleTypesCombobox = $ruleTypesCombobox.'<option value="'.$ruleType['rule_name'].'">'.$ruleType['display_name'].'</option>';
				}
				$ruleTypesCombobox = $ruleTypesCombobox.'</select>';

				?>
			<script>
			function addRule() {
				var date = new Date();
				var components = [date.getYear(),date.getMonth(),date.getDate(),date.getHours(),date.getMinutes(),date.getSeconds(),date.getMilliseconds()];
				var id = components.join("");
				
				var $newrule = $('<div class="rule" id="'+id+'"><?php echo $ruleTypesCombobox; ?> column <?php echo $columnsCombobox; ?> when <?php echo $attributesCombobox; ?> is <input type="text" size="20" class="value" name="value"></input></div>');
				$('#rules').append($newrule);
				return $newrule;
			}
			function removeRule(id) {
				var moveToTrash=confirm("<?php echo translate('Do you really want to delete this rule?'); ?>");
				if (moveToTrash) {
					$("#rules #"+id).remove();
				}
				return false;
			}
			function saveRules() {
				var $rules = {};
				$("#rules .rule").each(function(){
					var $id = $(this).attr('id');
					$rules[$id] = {};
					$rules[$id]['ruleType'] = $(this).children('.ruleType').val();					
					$rules[$id]['column'] = $(this).children('.column').val();					
					$rules[$id]['attribute'] = $(this).children('.attribute').val();
					$rules[$id]['value'] = $(this).children('.value').val();
				});
				$.ajax({ type: "POST", url: "storeBoardRules.php", data: {rules: $rules, boardid: <?php echo $board_id; ?>}});
				return $rules;
			}
		</script>
			<h3>Rules</h3>
			<div id="rules">
				<?php
				foreach($rules as $ruleid => $ruledata) {
?>
				<script>
						var $newrule = addRule();
						$newrule.children('.ruleType').val('<?php echo $ruledata['ruleType']; ?>');
						$newrule.children('.column').val('<?php echo $ruledata['column']; ?>');
						$newrule.children('.attribute').val('<?php echo $ruledata['attribute']; ?>');
						$newrule.children('.value').val('<?php echo $ruledata['value']; ?>');
					</script>
				<?php
				}
				?>
			</div>
			<br>
			<button type="button" onclick="addRule(); return false;">Add rule</button>
			<br />
			<button type="button" onclick="saveRules(); return false;">Save rules</button>
		</div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
