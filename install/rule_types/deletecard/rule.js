function deletecard_evaluate($action_data, $config_column, $attribute_matching) {
	return !(($config_column == "ALL" || $config_column == $action_data["column"]) && !$attribute_matching);
}