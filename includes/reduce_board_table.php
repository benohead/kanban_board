<?php
$remove_attributes = array();
if (!isset($_REQUEST["reduce"]) || $_REQUEST["reduce"] == "1") {
	foreach ($cardattributes as $attribute_id => $attribute_data) {
		$value_array = array();
		foreach ($cards as $card_id => $card_data) {
			if (isset($card_data[$attribute_id])) {
				if (!isset($value_array[$card_data[$attribute_id]])) {
					$value_array[$card_data[$attribute_id]] = 0;
				}
				$value_array[$card_data[$attribute_id]]++;
			}
			else {
				if (!isset($value_array[''])) {
					$value_array[''] = 0;
				}
				$value_array['']++;
			}
		}
		if (count($cards) > 1 && count($value_array) < 2) {
			array_push($remove_attributes, $attribute_id);
		}
	}
}
?>