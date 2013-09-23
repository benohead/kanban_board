<?php
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){
		die();
	}
	require_once("models/db-settings.php");
	require_once("functions.php");

	// When executed in a browser, this script will prompt for download 
	// of 'test.xls' which can then be opened by Excel or OpenOffice.
	
	require 'php-export-data.class.php';
	
	$current_board = trim($_REQUEST["boardid"]);
	$board = get_board_details($current_board);
	$boardname = $board['board_name'];
	
	// 'browser' tells the library to stream the data directly to the browser.
	// other options are 'file' or 'string'
	// 'test.xls' is the filename that the browser will use when attempting to 
	// save the download
	$exporter = new ExportDataExcel('browser', $boardname.'.xls');
	
	$exporter->initialize(); // starts streaming data to web browser
	



	$boardcolumns = get_board_columns($current_board);
	$cardattributes = getCompleteCardAttributes($current_board);
	$cards = get_cards_on_board($current_board);
	$prefix = get_board_export_id_prefix($current_board);
	
	if (!isset($cards)) {
		$cards = array();
	}

	$row = array();
	array_push($row, translate('Column'));
	foreach ($cardattributes as $attribute_id => $attribute_name) {
			array_push($row, brToNewline($attribute_name['name']));
	}
	$exporter->addRow($row);
	$row = array();
	foreach ($cards as $card_id => $card_data) {
		array_push($row, brToNewline($card_data['board']));

		foreach ($cardattributes as $attribute_id => $attribute_name) {
			$data = $card_data[$attribute_id];
			if ($attribute_id == "cardNumber") {
				$data = $prefix . $data;
			}
			array_push($row, brToNewline($data));
		}
		$exporter->addRow($row);
		$row = array();
	}

	$exporter->finalize(); // writes the footer, flushes remaining data to browser.
	
	exit(); // all done

?>