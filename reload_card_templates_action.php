<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");
	require_once("models/funcs.php");

	if (isset($_REQUEST["verbose"])) {
		$verbose = trim($_REQUEST["verbose"]);
	}
	else {
		$verbose = 0;
	}
	
	ob_start();
	
	$template_path = "install/templates";
	$card_templates_path = $template_path."/cards";
	if (import_card_templates($card_templates_path)) {
		$errors[] = translate('Error importing the card templates');
		$errors[] = ob_get_contents();
	}
	else {
		$successes[] = translate('Successfully imported the card templates');
		$successes[] = ob_get_contents();
	}
	
	ob_end_clean();
	
	if(count($errors) == 0) {
		$results = array(
				"error" => false,
				"messages" => $successes);
	}
	else {
		$results = array(
				"error" => true,
				"messages" => $errors);
	}
	if($verbose != 0) {
		echo json_encode($results);
	}
?>