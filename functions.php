<?php
//--------------------------------------
//functions used throughout kanban board
//-----
require_once 'cssCrush/CssCrush.php';

function currentPageURL() {
	$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
	$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
	$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
	$url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
	return reconstructUrl($url);
}

function reconstructUrl($url){    
    $url_parts = parse_url($url);
    $constructed_url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];

    return $constructed_url;
}

function brToNewline($text) {
    //return str_replace("<br>","\r\n",$text);
    //return str_replace("<br>","&#13;&#10;",$text);
    return str_replace("<br>","&#10;",$text);
}

function unserialize_or_empty_array($str) {
	if (isNullOrEmptyString($str)) {
		return array();
	}
	$array = unserialize($str);
	if (!isset($array) || $array == NULL) {
		$array = array();
	}
	return $array;
}

function isNullOrEmptyString($str) {
	return (!isset($str) || $str == NULL || trim($str)==='');
}

//Execute an SQL statement and returns result
//TO DO supports only 1 returned column
function executeSQL($sql)
{
	global $mysqli,$db_table_prefix;
	$resArray = null;

	$sql = str_replace('###db_table_prefix###',$db_table_prefix,$sql);

	$resArray = array();
	$i=0;
	if ($result = $mysqli->query($sql)) {
		while ($row = $result->fetch_row()) {
			$resArray[$i++]=$row[0];
		}
		$result->close();
	}

	return $resArray;
}

function get_all_projects() {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$project_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			project_name,
			display_name,
			active
			FROM ".$db_table_prefix."projects");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $project_name, $display_name, $active);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'project_name' => $project_name, 'display_name' => $display_name, 'active' => $active);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function get_active_projects() {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$project_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			project_name,
			display_name,
			active
			FROM ".$db_table_prefix."projects
			WHERE active=1");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $project_name, $display_name, $active);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'project_name' => $project_name, 'display_name' => $display_name, 'active' => $active);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function get_active_projects_with_active_boards() {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$project_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$stmt = $mysqli->prepare("SELECT DISTINCT ".$db_table_prefix."projects.id,
			".$db_table_prefix."projects.project_name,
			".$db_table_prefix."projects.display_name,
			".$db_table_prefix."projects.active
			FROM ".$db_table_prefix."projects INNER JOIN ".$db_table_prefix."boards ON ".$db_table_prefix."projects.id = ".$db_table_prefix."boards.project_id
			WHERE ".$db_table_prefix."projects.active=1 AND ".$db_table_prefix."boards.active=1");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $project_name, $display_name, $active);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'project_name' => $project_name, 'display_name' => $display_name, 'active' => $active);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function fetchActiveBoards($projectid) {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$board_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			board_name,
			display_name,
			active
			FROM ".$db_table_prefix."boards
			WHERE active=1
			AND project_id=?");
	$stmt->bind_param("i", $projectid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $board_name, $display_name, $active);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'board_name' => $board_name, 'display_name' => $display_name, 'active' => $active);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function get_board_details($board_id) {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$board_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			board_name,
			display_name,
			active
			FROM ".$db_table_prefix."boards
			WHERE id=?");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $board_name, $display_name, $active);
	while ($stmt->fetch()){
		$board = array('id' => $id, 'board_name' => $board_name, 'display_name' => $display_name, 'active' => $active);
	}
	$stmt->close();
	if (isset($board)){
		return ($board);
	}
}

function get_board_display_name($board_id) {
	global $mysqli,$db_table_prefix;
	$display_name = NULL;
	$stmt = $mysqli->prepare("SELECT display_name
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($display_name);
	$stmt->fetch();
	$stmt->close();

	return $display_name;
}

function getBoardProjectId($board_id) {
	global $mysqli,$db_table_prefix;
	$project_id = NULL;
	$stmt = $mysqli->prepare("SELECT project_id
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($project_id);
	$stmt->fetch();
	$stmt->close();

	return $project_id;
}

function get_board_columns($board_id) {
	global $mysqli,$db_table_prefix;
	$column_name = NULL;
	$display_name = NULL;
	$wip_limit = NULL;
	$description = NULL;
	$stmt = $mysqli->prepare("SELECT
			column_name,
			display_name,
			wip_limit,
			description
			FROM ".$db_table_prefix."board_columns
			WHERE board_id=?
			ORDER BY order_nr ASC");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($column_name, $display_name, $wip_limit, $description);
	while ($stmt->fetch()){
		$row[$column_name] = array('display_name' => $display_name, 'wip_limit' => $wip_limit, 'description' => $description);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function get_card_attributes($board_id) {
	global $mysqli,$db_table_prefix;
	$card_attributes = NULL;
	$stmt = $mysqli->prepare("SELECT card_attributes
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($card_attributes);
	$stmt->fetch();
	$stmt->close();

	return $card_attributes;
}

function getCompleteCardAttributes($board_id) {
	global $standard_card_attributes;
	$cardattributes = unserialize_or_empty_array(get_card_attributes($board_id));	
	return array_merge($standard_card_attributes, $cardattributes);
}

function get_usersLastOpenBoard($userid) {
	global $mysqli,$db_table_prefix;
	$board_id = NULL;
	$stmt = $mysqli->prepare("SELECT last_board_open
			FROM ".$db_table_prefix."users
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($board_id);
	$stmt->fetch();
	$stmt->close();

	return $board_id;
}

function setUsersLastOpenBoard($userid, $board_id) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
			SET last_board_open=?
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("ii", $board_id, $userid);
	$stmt->execute();
	$stmt->close();
}

function fetchActiveTemplates()
{
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$template_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			template_name,
			display_name,
			active
			FROM ".$db_table_prefix."board_templates
			WHERE active=1");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $template_name, $display_name, $active);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'template_name' => $template_name, 'display_name' => $display_name, 'active' => $active);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function fetchRuleTypes() {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$rule_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$action = NULL;
	$rule_js = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			rule_name,
			display_name,
			active,
			action,
			rule_js
			FROM ".$db_table_prefix."rule_types");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $rule_name, $display_name, $active, $action, $rule_js);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'rule_name' => $rule_name, 'display_name' => $display_name, 'active' => $active, 'action' => $action, 'rule_js' => $rule_js);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function fetchBoardTemplates()
{
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$template_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$board_css = NULL;
	$board_js = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			template_name,
			display_name,
			active,
			board_css,
			board_js
			FROM ".$db_table_prefix."board_templates");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $template_name, $display_name, $active, $board_css, $board_js);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'template_name' => $template_name, 'display_name' => $display_name, 'active' => $active, 'board_css' => $board_css, 'board_js' => $board_js);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function getBoardTemplate($templateid)
{
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$template_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$board_css = NULL;
	$board_js = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			template_name,
			display_name,
			active,
			board_css,
			board_js
			FROM ".$db_table_prefix."board_templates
			WHERE id=?
			LIMIT 1");
	$stmt->bind_param("i", $templateid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $template_name, $display_name, $active, $board_css, $board_js);
	while ($stmt->fetch()){
		$row = array('id' => $id, 'template_name' => $template_name, 'display_name' => $display_name, 'active' => $active, 'board_css' => $board_css, 'board_js' => $board_js);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function getBoardTemplateColumns($templateid) {
	global $mysqli,$db_table_prefix;
	$column_name = NULL;
	$display_name = NULL;
	$wip_limit = NULL;
	$stmt = $mysqli->prepare("SELECT
			column_name,
			display_name,
			wip_limit
			FROM ".$db_table_prefix."board_template_columns
			WHERE template_id=?
			ORDER BY order_nr ASC");
	$stmt->bind_param("i", $templateid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($column_name, $display_name, $wip_limit);
	while ($stmt->fetch()){
		$row[$column_name] = array('display_name' => $display_name, 'wip_limit' => $wip_limit);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function fetchActiveCardTemplates()
{
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$template_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			template_name,
			display_name,
			active
			FROM ".$db_table_prefix."card_templates
			WHERE active=1");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $template_name, $display_name, $active);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'template_name' => $template_name, 'display_name' => $display_name, 'active' => $active);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function fetchCardTemplates()
{
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$template_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			template_name,
			display_name,
			active
			FROM ".$db_table_prefix."card_templates");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $template_name, $display_name, $active);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'template_name' => $template_name, 'display_name' => $display_name, 'active' => $active);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function add_cards_to_board_old($board_id, $cards) {
	global $mysqli,$db_table_prefix;
	$cards = serialize($cards + get_cards_on_board($board_id));
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."boards
			SET cards=?
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("si", $cards, $board_id);
	$stmt->execute();
	$stmt->close();
}

function remove_card_from_board_old($board_id, $cardid) {
	global $mysqli,$db_table_prefix;
	$cards = get_cards_on_board($board_id);
	unset($cards[$cardid]);
	$cards = serialize($cards);
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."boards
			SET cards=?
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("si", $cards, $board_id);
	$stmt->execute();
	$stmt->close();
}

function store_cards_on_board_old($board_id, $cards) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."boards
			SET cards=?
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("si", $cards, $board_id);
	$stmt->execute();
	$stmt->close();
}

function get_cards_on_board_old($board_id) {
	global $mysqli,$db_table_prefix;
	$cards = NULL;
	$stmt = $mysqli->prepare("SELECT cards
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($cards);
	$stmt->fetch();
	$stmt->close();
	if (isset($cards)){
		return unserialize_or_empty_array($cards);
	}
}

function getLastHistoryVersionId($board_id) {
	global $mysqli,$db_table_prefix;
	$cards = NULL;
	$stmt = $mysqli->prepare("SELECT MAX(id)
			FROM ".$db_table_prefix."cards_history
			WHERE
			board_id = ?
			");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($cards);
	$stmt->fetch();
	$stmt->close();
	if (isset($cards)){
		return ($cards);
	}
}

function getLastHistoryForwardVersionId($board_id) {
	global $mysqli,$db_table_prefix;
	$cards = NULL;
	$stmt = $mysqli->prepare("SELECT MAX(id)
			FROM ".$db_table_prefix."cards_history_forward
			WHERE
			board_id = ?
			");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($cards);
	$stmt->fetch();
	$stmt->close();
	if (isset($cards)){
		return ($cards);
	}
}

function addCardsToHistory($board_id) {
	global $mysqli,$db_table_prefix;
	$cards = serialize(get_cards_on_board($board_id));
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."cards_history(
			board_id,
			cards)
			VALUES(
			?,
			?)
			");
	$stmt->bind_param("is", $board_id, $cards);
	$stmt->execute();
	$stmt->close();
}

function addCardsToHistoryForward($board_id) {
	global $mysqli,$db_table_prefix;
	$cards = serialize(get_cards_on_board($board_id));
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."cards_history_forward(
			board_id,
			cards)
			VALUES(
			?,
			?)
			");
	$stmt->bind_param("is", $board_id, $cards);
	$stmt->execute();
	$stmt->close();
}

function emptyBoardForwardHistory($board_id) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."cards_history_forward
			WHERE
			board_id = ?
			");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->close();
}

function getCardsFromHistoryVersion($previousversion) {
	global $mysqli,$db_table_prefix;
	$cards = NULL;
	$stmt = $mysqli->prepare("SELECT cards
			FROM ".$db_table_prefix."cards_history
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $previousversion);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($cards);
	$stmt->fetch();
	$stmt->close();
	if (isset($cards)){
		return ($cards);
	}
}

function getCardsFromHistoryForwardVersion($previousversion) {
	global $mysqli,$db_table_prefix;
	$cards = NULL;
	$stmt = $mysqli->prepare("SELECT cards
			FROM ".$db_table_prefix."cards_history_forward
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $previousversion);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($cards);
	$stmt->fetch();
	$stmt->close();
	if (isset($cards)){
		return ($cards);
	}
}

function removeHistoryVersion($previousversion) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."cards_history
			WHERE
			id = ?
			");
	$stmt->bind_param("i", $previousversion);
	$stmt->execute();
	$stmt->close();
}

function removeHistoryForwardVersion($previousversion) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."cards_history_forward
			WHERE
			id = ?
			");
	$stmt->bind_param("i", $previousversion);
	$stmt->execute();
	$stmt->close();
}

function reactivateHistoryVersion($board_id, $previousversion) {
	$cardsOld = getCardsFromHistoryVersion($previousversion);
	removeHistoryVersion($previousversion);
	store_cards_on_board($board_id, $cardsOld);
}

function reactivateHistoryForwardVersion($board_id, $previousversion) {
	$cardsOld = getCardsFromHistoryForwardVersion($previousversion);
	removeHistoryForwardVersion($previousversion);
	store_cards_on_board($board_id, $cardsOld);
}

function get_board_css($board_id) {
	global $mysqli,$db_table_prefix;
	$board_css = NULL;
	$stmt = $mysqli->prepare("SELECT board_css
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($board_css);
	$stmt->fetch();
	$stmt->close();
	if (isset($board_css)){
		return ($board_css);
	}
}

function get_card_css($board_id) {
	global $mysqli,$db_table_prefix;
	$card_css = NULL;
	$stmt = $mysqli->prepare("SELECT card_css
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($card_css);
	$stmt->fetch();
	$stmt->close();
	if (isset($card_css)){
		return ($card_css);
	}
}

function get_board_generators($board_id) {
	global $mysqli,$db_table_prefix;
	$board_gens = NULL;
	$stmt = $mysqli->prepare("SELECT board_gens
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($board_gens);
	$stmt->fetch();
	$stmt->close();
	if (isset($board_gens)){
		return ($board_gens);
	}
}

function get_board_rules($board_id) {
	global $mysqli,$db_table_prefix;
	$rules = NULL;
	$stmt = $mysqli->prepare("SELECT rules
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($rules);
	$stmt->fetch();
	$stmt->close();
	if (isset($rules)){
		return ($rules);
	}
}

function get_board_javascript($board_id) {
	global $mysqli,$db_table_prefix;
	$board_js = NULL;
	$stmt = $mysqli->prepare("SELECT board_js
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($board_js);
	$stmt->fetch();
	$stmt->close();
	if (isset($board_js)){
		return ($board_js);
	}
}

function get_board_wip_limits($board_id) {
	global $mysqli,$db_table_prefix;
	$wip_limits = NULL;
	$stmt = $mysqli->prepare("SELECT wip_limits
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($wip_limits);
	$stmt->fetch();
	$stmt->close();
	if (isset($wip_limits)){
		return ($wip_limits);
	}
}

function get_card_javascript($board_id) {
	global $mysqli,$db_table_prefix;
	$card_js = NULL;
	$stmt = $mysqli->prepare("SELECT card_js
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($card_js);
	$stmt->fetch();
	$stmt->close();
	if (isset($card_js)){
		return ($card_js);
	}
}

function set_board_metadata($board_id, $board_css, $board_js, $card_attributes, $card_css, $card_js, $export_id_prefix) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."boards
			SET board_css=?, board_js=?, card_attributes=?, card_css=?, card_js=?, export_id_prefix=?
			WHERE
			id = ?");
	if ($stmt == false) {
		return false;
	}
	$stmt->bind_param("ssssssi", $board_css, $board_js, $card_attributes, $card_css, $card_js, $export_id_prefix, $board_id);
	if (!$stmt->execute()) {
		return false;
	}
	if ($stmt->errno) {
			return false;
	}
	$stmt->close();
	return true;
}

function set_board_card_attributes($board_id, $card_attributes) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."boards
			SET card_attributes=?
			WHERE
			id = ?");
	if ($stmt == false) {
		return false;
	}
	$stmt->bind_param("si", $card_attributes, $board_id);
	if (!$stmt->execute()) {
		return false;
	}
	if ($stmt->errno) {
			return false;
	}
	$stmt->close();
	return true;
}

function updateBoardStatistics($board_id, $statistics) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."statistics WHERE board_id = ?");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->close();
	foreach ($statistics as $statistics_name => $statistics_data) {
		$groups = isset($statistics_data['groups']) ? serialize($statistics_data['groups']) : "";
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."statistics(board_id, statistics_name, display_name, frequency, type, attribute_id, groups) VALUES(?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("issssss", $board_id, $statistics_name, $statistics_data['display_name'], $statistics_data['frequency'], $statistics_data['type'], $statistics_data['attribute_id'], $groups);
		$stmt->execute();
		$stmt->close();
	}
}

function set_board_columns($board_id, $columns) {
	global $mysqli,$db_table_prefix;
	$index = 0;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."board_columns WHERE board_id = ?");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->close();
	foreach ($columns as $column_name => $column_data) {
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."board_columns(board_id, column_name, display_name, wip_limit, order_nr, description) VALUES(?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("issiis", $board_id, $column_name, $column_data['display_name'], $column_data['wip_limit'], $index, $column_data['description']);
		$index++;
		$stmt->execute();
		$stmt->close();
	}
}

function updateBoardTemplateColumns($templateid, $columns) {
	global $mysqli,$db_table_prefix;
	$index = 0;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."board_template_columns WHERE template_id = ?");
	$stmt->bind_param("i", $templateid);
	$stmt->execute();
	$stmt->close();
	foreach ($columns as $column_name => $column_data) {
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."board_template_columns(template_id, column_name, display_name, wip_limit, order_nr) VALUES(?, ?, ?, ?, ?)");
		$stmt->bind_param("issii", $templateid, $column_name, $column_data['display_name'], $column_data['wip_limit'], $index);
		$index++;
		$stmt->execute();
		$stmt->close();
	}
}

function updateBoardTemplate($templateid, $template_data_css, $template_data_js) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."board_templates
			SET board_css=?, board_js=?
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("ssi", $template_data_css, $template_data_js, $templateid);
	$stmt->execute();
	$stmt->close();
}

function set_board_generators($board_id, $generators) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."boards
			SET board_gens=?
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("si", $generators, $board_id);
	$stmt->execute();
	$stmt->close();
}

function set_board_rules($board_id, $rules) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."boards
			SET rules=?
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("si", $rules, $board_id);
	$stmt->execute();
	$stmt->close();
}

function getDefaultAttributeValue($sourceType) {
	if ($sourceType == 'CHECKBOX') {
		return "false";
	}
	return "";
}

function getInputFieldCardAttribute($board_id, $attribute_id, $sourceType, $source, $value=null) {
	if ($sourceType == 'USERS') {
		return getInputFieldCardAttributeUsers($board_id, $attribute_id, $value);
	}
	else if ($sourceType == 'SQL') {
		return getInputFieldCardAttributeSql($attribute_id, $source, $value);
	}
	else if ($sourceType == 'RANGE') {
		return getInputFieldCardAttributeRange($attribute_id, $source, $value);
	}
	else if ($sourceType == 'TEXT') {
		return getInputFieldCardAttributeText($attribute_id, $value);
	}
	else if ($sourceType == 'STRING') {
		return getInputFieldCardAttributeString($attribute_id, $value);
	}
	else if ($sourceType == 'NUMERIC') {
		return getInputFieldCardAttributeNumeric($attribute_id, $value);
	}
	else if ($sourceType == 'DATE') {
		return getInputFieldCardAttributeDate($attribute_id, $value);
	}
	else if ($sourceType == 'CHECKBOX') {
		return getInputFieldCardAttributeCheckbox($attribute_id, $value);
	}
	return translate('Unknown attribute %1$s on board %2$d', $attribute_id, $board_id);
}

function getInputFieldCardAttributeCheckbox($attribute_id, $value="") {
	$el = '<input type="checkbox" id="'.$attribute_id.'" class="rounded"';
	if ($value == "true") {
		$el = $el . ' checked="checked"';
	}
	$el = $el . '"></input>';
	return $el;
}

function getInputFieldCardAttributeString($attribute_id, $value=null) {
	$el = '<input type="text" id="'.$attribute_id.'" class="rounded" style="width: 240px;" value="';
	if ($value != null) {
		$el = $el . str_replace(array("\'", '\"', '"', "'"), array("\\\'", '\\"', '\"', "\'"), $value);
	}
	$el = $el . '"></input>';
	return $el;
}

function getInputFieldCardAttributeDate($attribute_id, $value=null) {
	$el = '<input type="text" id="'.$attribute_id.'" class="rounded datepicker" style="width: 240px;" value="';
	if ($value != null) {
		$el = $el . str_replace(array("\'", '\"', '"', "'"), array("\\\'", '\\"', '\"', "\'"), $value);
	}
	$el = $el . '"></input>';
	return $el;
}

function getInputFieldCardAttributeNumeric($attribute_id, $value=null) {
	$el = '<input type="text" id="'.$attribute_id.'" class="rounded numeric" style="width: 240px;" value="';
	if ($value != null) {
		$el = $el . str_replace(array("\'", '\"', '"', "'"), array("\\\'", '\\"', '\"', "\'"), $value);
	}
	$el = $el . '"></input>';
	return $el;
}

function getInputFieldCardAttributeText($attribute_id, $value=null) {
	$el = '<textarea id="'.$attribute_id.'" class="rounded" style="height: 120px; width: 240px;">';
	if ($value != null) {
		$el = $el . $value;
	}
	$el = $el . '</textarea>';
	return $el;
}

function getInputFieldCardAttributeRange($attribute_id, $source, $value=null) {
	$values = explode(';',$source);
	$el = '<select size="1" id="'.$attribute_id.'" class="rounded" size=20>';
	foreach ($values as $v) {
		$el = $el . '<option ';
		if (($value != null) && ($v == $value)) {
			$el = $el . 'selected="selected"';
		}
		$el = $el . '>' . $v . '</option>';
	}
	$el = $el . '</select>';
	return $el;
}

function getInputFieldCardAttributeSql($attribute_id, $source, $value=null) {
	$values = executeSQL($source);
	$el = '<select size="1" id="'.$attribute_id.'" class="rounded" size=20>';
	foreach ($values as $v) {
		$el = $el . '<option ';
		if (($value != null) && ($v == $value)) {
			$el = $el . 'selected="selected"';
		}
		$el = $el . '>' . $v . '</option>';
	}
	$el = $el . '</select>';
	return $el;
}

function getInputFieldCardAttributeUsers($board_id, $attribute_id, $value) {
	global $db_table_prefix;
	$source = "SELECT u.display_name FROM ".$db_table_prefix."users u INNER JOIN ".$db_table_prefix."user_boards ub ON u.id=ub.user_id WHERE ub.board_id=".$board_id;
	$values = executeSQL($source);
	if (count($values) == 0) {
		$source = "SELECT u.display_name FROM ".$db_table_prefix."users u INNER JOIN ".$db_table_prefix."user_projects up ON u.id=up.user_id INNER JOIN ".$db_table_prefix."boards b ON up.project_id=b.project_id WHERE b.board_id=".$board_id;
		$values = executeSQL($source);
		if (count($values) == 0) {
			$source = "SELECT display_name FROM ".$db_table_prefix."users";
			$values = executeSQL($source);
		}
	}
	$values = array_merge(array(""), $values);
	$el = '<select size="1" id="'.$attribute_id.'" class="rounded" size=20>';
	foreach ($values as $v) {
		$el = $el . '<option ';
		if (($value != null) && ($v == $value)) {
			$el = $el . 'selected="selected"';
		}
		$el = $el . '>' . $v . '</option>';
	}
	$el = $el . '</select>';
	return $el;
}

function getActiveGeneratorTypes() {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$generator_name = NULL;
	$display_name = NULL;
	$action = NULL;
	$generator_js = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			generator_name,
			display_name,
			action,
			generator_js
			FROM ".$db_table_prefix."generator_types
			WHERE active=1");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $generator_name, $display_name, $action, $generator_js);
	while ($stmt->fetch()){
		$row[$generator_name] = array('id' => $id, 'generator_name' => $generator_name, 'display_name' => $display_name, 'action' => $action, 'generator_js' => $generator_js);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function getActiveRuleTypes() {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$rule_name = NULL;
	$display_name = NULL;
	$action = NULL;
	$rule_js = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			rule_name,
			display_name,
			action,
			rule_js
			FROM ".$db_table_prefix."rule_types
			WHERE active=1");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $rule_name, $display_name, $action, $rule_js);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'rule_name' => $rule_name, 'display_name' => $display_name, 'action' => $action, 'rule_js' => $rule_js);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function getRoleId($name) {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$stmt = $mysqli->prepare("SELECT
			id,
			name
			FROM ".$db_table_prefix."roles
			WHERE
			name = ?
			LIMIT 1");
	$stmt->bind_param("s", $name);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name);
	while ($stmt->fetch()){
		$row = array('id' => $id, 'name' => $name);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function get_project_users($project_id) {
	global $mysqli, $db_table_prefix;
	$user_id = NULL;
	$user_display_name = NULL;
	$access_type = NULL;
	$stmt = $mysqli->prepare("SELECT user.id, user.display_name, link.access_type
			FROM `".$db_table_prefix."users` AS user
			INNER JOIN `".$db_table_prefix."user_projects` AS link ON user.id=link.user_id
			INNER JOIN `".$db_table_prefix."projects` AS project ON link.project_id=project.id
			WHERE project.id = ?
			ORDER BY user.display_name");
	$stmt->bind_param("i", $project_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($user_id, $user_display_name, $access_type);
	while ($stmt->fetch()){
		$row[$user_id] = array('user_id' => $user_id, 'user_display_name' => $user_display_name, 'access_type' => $access_type);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}

}

function get_non_project_users($project_id) {
	global $mysqli, $db_table_prefix;
	$user_id = NULL;
	$user_display_name = NULL;
	$stmt = $mysqli->prepare("SELECT user.id, user.display_name
			FROM `".$db_table_prefix."users` AS user
			WHERE NOT EXISTS ( SELECT 1 FROM `".$db_table_prefix."user_projects` AS link WHERE user.id=link.user_id AND link.project_id = ? )
			ORDER BY user.display_name");
	$stmt->bind_param("i", $project_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($user_id, $user_display_name);
	while ($stmt->fetch()){
		$row[$user_id] = array('user_id' => $user_id, 'user_display_name' => $user_display_name);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}

}

function printRules($rules, $cancel_action){
	global $logged_in_user;
	if (!isset($rules)) {
		return;
	}
	foreach ($rules as $ruleid => $ruledata) {
?>
		$attribute_matching = 1;
<?php
		$attribute = $ruledata['attribute'];
		$value = $ruledata['value'];
		if ($attribute == 'loggedinuser') {
?>
			$attribute_matching = $attribute_matching && <?php echo (($logged_in_user->username == $value) || ($logged_in_user->displayname == $value)) ? "1" : "0"; ?>;
<?php
		}
		else if ($attribute == 'loggedinusersrole') {
			$role_id = getRoleId($value);
			?>
			$attribute_matching = $attribute_matching && <?php echo ($logged_in_user->checkRole(array($role_id))) ? "1": "0"; ?>;
<?php
		}
		else if ($attribute != 'ALL') {
?>
			$cardForRules.find('.<?php echo $attribute; ?>').each(function () { $attribute_matching = $attribute_matching && ($(this).html() == '<?php echo $value; ?>'); });
<?php						
		}
		echo 'if (!'.$ruledata['ruleType'].'_evaluate($action_data,"'.$ruledata['column'].'",$attribute_matching)) { alert("'.translate('A configured rule (%1$s) does not allow this.', $ruledata['ruleType']).'"); '.$cancel_action.' return; }'."\n";
	}
}

function getRulesPerAction($rules, $ruleTypes) {
	$rulesPerAction = array();
	if (isset($rules)) {
		foreach ($rules as $ruleid => $ruledata) {
			foreach ($ruleTypes as $ruleType) {
				$ruleTypeName = $ruleType['rule_name'];
				if ($ruleTypeName == $ruledata['ruleType']) {
					$ruleTypeAction = $ruleType['action'];
					if (!isset($rulesPerAction[$ruleTypeAction])) {
						$rulesPerAction[$ruleTypeAction] = array();
					}
					$rulesPerAction[$ruleTypeAction][$ruleid] = $ruledata;
				}
			}
		}
	}
	return $rulesPerAction;
}

function getGeneratorsPerTrigger($generators) {
	$generatorsPerTrigger = array();
	if (isset($generators)) {
		foreach ($generators as $generatorid => $generatordata) {
			$generatorTrigger = $generatordata['generatorTrigger'];
			if (!isset($generatorsPerTrigger[$generatorTrigger])) {
				$generatorsPerTrigger[$generatorTrigger] = array();
			}
			$generatorsPerTrigger[$generatorTrigger][] = $generatorid;
		}
	}
	return $generatorsPerTrigger;
}

function get_all_boards_in_project($projectid) {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$board_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$stmt = $mysqli->prepare("SELECT
		id,
		board_name,
		display_name,
		active
		FROM ".$db_table_prefix."boards
		WHERE project_id=?");
	$stmt->bind_param("i", $projectid);
	$stmt->execute();
	$stmt->bind_result($id, $board_name, $display_name, $active);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'board_name' => $board_name, 'display_name' => $display_name, 'active' => $active);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function getProjectDisplayName($projectid) {
	global $mysqli,$db_table_prefix;
	$display_name = NULL;
	$stmt = $mysqli->prepare("SELECT display_name
		FROM ".$db_table_prefix."projects
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("i", $projectid);
	$stmt->execute();
	$stmt->bind_result($display_name);
	$stmt->fetch();
	$stmt->close();

	return $display_name;
}

function createBoardTemplateFromBoard($board_id, $boardtemplatename, $boarddisplayname, $boardactive) {
	global $mysqli,$db_table_prefix;
	//first check whether the template name already exists
	$stmt = $mysqli->prepare("SELECT active
					FROM " . $db_table_prefix . "board_templates
					WHERE
					 	template_name = ?
					LIMIT 1");
	if ( false===$stmt ) {
		return 3;
	}
	$stmt->bind_param("s", $boardtemplatename);
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	if ($num_returns > 0) {
		return 1;
	}

	//Then check whether the template display name already exists
	$stmt = $mysqli->prepare("SELECT active
					FROM " . $db_table_prefix . "board_templates
					WHERE
					 	display_name = ?
					LIMIT 1");
	if ( false===$stmt ) {
		return 3;
	}
	$stmt->bind_param("s", $boarddisplayname);
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	if ($num_returns > 0) {
		return 2;
	}

	//Now copy the board data to a new template entry.
	$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "board_templates (
										template_name,
										display_name,
										active,
										board_css,
										board_js
								)
										SELECT
										?,
										?,
										?,
										board_css,
										board_js
										FROM " . $db_table_prefix . "boards
										WHERE " . $db_table_prefix . "boards.id = ?
										");
	if ( false===$stmt ) {
		return 3;
	}

	$stmt->bind_param("ssii", $boardtemplatename, $boarddisplayname, $boardactive, $board_id);
	$rc = $stmt->execute();
	if ( false===$rc ) {
		return 4;
	}
	$inserted_id = $mysqli->insert_id;
	$stmt->close();
	
	//Now copy the board colum data to a new template entry.
	$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "board_template_columns (
										template_id,
										column_name,
										display_name,
										wip_limit,
										order_nr
								)
										SELECT
										?,
										column_name,
										display_name,
										wip_limit,
										order_nr
										FROM " . $db_table_prefix . "board_columns
										WHERE " . $db_table_prefix . "board_columns.board_id = ?
										");
	if ( false===$stmt ) {
		return 3;
	}

	$stmt->bind_param("ii", $inserted_id, $board_id);
	$rc = $stmt->execute();
	if ( false===$rc ) {
		return 4;
	}
	$stmt->close();
}

function createCardTemplateFromBoard($board_id, $cardtemplatename, $carddisplayname, $cardactive) {
	global $mysqli,$db_table_prefix;
	//first check whether the template name already exists
	$stmt = $mysqli->prepare("SELECT active
					FROM " . $db_table_prefix . "card_templates
					WHERE
					 	template_name = ?
					LIMIT 1");
	if ( false===$stmt ) {
		return 3;
	}
	$stmt->bind_param("s", $cardtemplatename);
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	if ($num_returns > 0) {
		return 1;
	}

	//Then check whether the template display name already exists
	$stmt = $mysqli->prepare("SELECT active
					FROM " . $db_table_prefix . "card_templates
					WHERE
					 	display_name = ?
					LIMIT 1");
	if ( false===$stmt ) {
		return 3;
	}
	$stmt->bind_param("s", $carddisplayname);
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	if ($num_returns > 0) {
		return 2;
	}

	//Now copy the card data to a new template entry.
	$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "card_templates (
										template_name,
										display_name,
										active,
										card_attributes,
										card_css,
										card_js
								)
										SELECT
										?,
										?,
										?,
										card_attributes,
										card_css,
										card_js
										FROM " . $db_table_prefix . "boards
										WHERE " . $db_table_prefix . "boards.id = ?
										");
	if ( false===$stmt ) {
		return 3;
	}

	$stmt->bind_param("ssii", $cardtemplatename, $carddisplayname, $cardactive, $board_id);
	$rc = $stmt->execute();
	if ( false===$rc ) {
		return 4;
	}
	$stmt->close();
}

function getCurrentApplicationVersion() {
	global $mysqli,$db_table_prefix;
	$version=NULL;
	//first check whether the template name already exists
	$stmt = $mysqli->prepare("SELECT version_number FROM " . $db_table_prefix . "versions WHERE version_type='application' LIMIT 1");
	if ( false===$stmt ) {
		return false;
	}
	$stmt->execute();
	$stmt->bind_result($version);
	if (!$stmt->fetch()) {
		$version = 1;
	}
	$stmt->close();
	return $version;
}

function getUpdateList($directory) {
	$updates = glob($directory . "*", GLOB_ONLYDIR);
	sort($updates, SORT_NUMERIC);
	$updates_to_run = array();
	//print each file name
	foreach ($updates as $update){
		$update_version = intval($update);
		if ($update > getCurrentApplicationVersion()) {
			array_push($updates_to_run, $update);
		}
	}
	return $updates_to_run;
}


function checkUpdates() {
	$directory = "updates/";
	$updates = glob($directory . "*", GLOB_ONLYDIR);
	foreach ($updates as $update){
		$update_version = intval(basename($update));
		if ($update_version > getCurrentApplicationVersion()) {
			return true;
		}
	}
	return false;
}

function addCardsToArchive($board_id, $cards) {
	//first load the existing archive for this board
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$currentCards = NULL;
	$stmt = $mysqli->prepare("SELECT id, cards
		FROM ".$db_table_prefix."card_archive
		WHERE
		board_id = ?
		LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $currentCards);
	$stmt->fetch();
	$stmt->close();
	if (!isset($currentCards)) {
		$currentCards = $cards;
	}
	else {
		$currentCards = array_merge(unserialize_or_empty_array($currentCards), $cards);
	}
	storeCardsInBoardArchive($currentCards, $board_id, $id);
}

function storeCardsInBoardArchive($cards, $board_id, $id) {
	global $mysqli,$db_table_prefix;
	if (isset($id) && $id>0) {
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."card_archive 
			SET cards=?
			WHERE board_id = ? AND id = ?");
		$serialize_cards = serialize($cards);
		$stmt->bind_param("sii", $serialize_cards, $board_id, $id);
		$stmt->execute();
		$stmt->close();
	}
	else {
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."card_archive(
				board_id,
				cards)
				VALUES(
				?,
				?)
				");
		$serialize_cards = serialize($cards);
		$stmt->bind_param("is", $board_id, $serialize_cards);
		$stmt->execute();
		$stmt->close();
	}
}

function getArchivedCards($board_id) {
	global $mysqli,$db_table_prefix;
	$cards = NULL;
	$stmt = $mysqli->prepare("SELECT cards
		FROM ".$db_table_prefix."card_archive
		WHERE
		board_id = ?
		LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($cards);
	$stmt->fetch();
	$stmt->close();
	return $cards;
}

function moveCardsFromArchiveToBoard($cards_to_add, $board_id) {
	//first load the existing archive for this board
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$currentCards = NULL;
	$stmt = $mysqli->prepare("SELECT id, cards
		FROM ".$db_table_prefix."card_archive
		WHERE
		board_id = ?
		LIMIT 1");
	if (!$stmt) {
		error_log("moveCardsFromArchiveToBoard: error creating prepared statement");
		return 1;
	}
	$stmt->bind_param("i", $board_id);
	$rc = $stmt->execute();
	if ( false===$rc ) {
		error_log("moveCardsFromArchiveToBoard: error executing prepared statement");
		error_log($stmt->error);
		return 2;
	}
	$stmt->store_result();
	$stmt->bind_result($id, $currentCards);
	$stmt->fetch();
	$stmt->close();
	if (!isset($currentCards)) {
		error_log("moveCardsFromArchiveToBoard: no card archive found");
		return 3;
	}
	$cards_on_board = get_cards_on_board($board_id);
	$archived_cards = unserialize_or_empty_array($currentCards);
	foreach ($cards_to_add as $key => $cardid) {
		$cards_on_board[$cardid] = $archived_cards[$cardid];
		unset($archived_cards[$cardid]);
	}
	store_cards_on_board($board_id, serialize($cards_on_board));
	storeCardsInBoardArchive($archived_cards, $board_id, $id);	
}

function jsAddSlashes($str) {
	$pattern = array(
			"/\\\\/"  , "/\n/"    , "/\r/"    , "/\"/"    ,
			"/\'/"    , "/&/"     , "/</"     , "/>/"
	);
	$replace = array(
			"\\\\\\\\", "\\n"     , "\\r"     , "\\\""    ,
			"\\'"     , "\\x26"   , "\\x3C"   , "\\x3E"
	);
	return preg_replace($pattern, $replace, $str);
}

function extractArrayFromArray($array, $subKey) {
	$newArray = array();
	foreach ($array as $key => $value) {
		$newArray[$key] = $value[$subKey];
	}
	return $newArray;
}

function createDropDownFromArray($array, $selectedKey, $classes, $multiple = FALSE) {
	$combobox = '<select class="'.$classes.'" '.($multiple ? 'multiple' : '').'>';
	foreach ($array as $key => $value) {
		if(is_array($selectedKey)) {
			$isSelected = in_array($key, $selectedKey);
		}
		else {
			$isSelected = $key == $selectedKey;
		}
		$combobox = $combobox . '<option value="' . $key . '"'.($isSelected ? 'selected="selected"' : '').'>' . $value . '</option>';
	}
	$combobox = $combobox . '</select>';
	return $combobox;
}

function getBoardStatistics($board_id) {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$statistics_name = NULL;
	$display_name = NULL;
	$active = NULL;
	$board_id = NULL;
	$frequency = NULL;
	$type = NULL;
	$attribute_id = NULL;
	$groups = NULL;
	$conditions = NULL;
	$stmt = $mysqli->prepare("SELECT `id`, `statistics_name`, `display_name`, `active`, `board_id`, `frequency`, `type`, `attribute_id`, `groups`, `conditions` 
			FROM `".$db_table_prefix."statistics` 
			WHERE board_id=?");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $statistics_name, $display_name, $active, $board_id, $frequency, $type, $attribute_id, $groups, $conditions);
	while ($stmt->fetch()){
		$row[$id] = array('id' => $id, 'statistics_name' => $statistics_name, 'display_name' => $display_name, 'active' => $active, 'board_id' => $board_id, 'frequency' => $frequency, 'type' => $type, 'attribute_id' => $attribute_id, 'groups' => unserialize_or_empty_array($groups), 'conditions' => $conditions);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function gatherBoardStatistics($board_id) {
	global $mysqli,$db_table_prefix;
	$statistics = getBoardStatistics($board_id);
	$cards = get_cards_on_board($current_board);
	foreach ($statistics as $stat_id => $stat_data) {
		switch ($stat_data['frequency']) {
			case 'H':
				$date = date('Y.m.d H');
				break;
			case 'W':
				$date = date('Y#W');
				break;
			case 'M':
				$date = date('Y.m');
				break;
			case 'Y':
				$date = date('Y');
				break;
			default:
				$date = date('Y.m.d');
				break;
		}
	}
	$result = array();
	foreach ($cards as $card_id => $card_data) {
		//do something
	}
	$stmt = $mysqli->prepare("DELETE FROM `".$db_table_prefix."statistics_results` WHERE board_id=? AND stat_time=?");
	$stmt->bind_param("is", $board_id, $date);
	$stmt->execute();
	$stmt = $mysqli->prepare("INSERT INTO `".$db_table_prefix."statistics_results`(statistics_id, stat_time, card_group, card_count) VALUES(?, ?, ?, ?)");
	$stmt->bind_param("is", $stat_id, $date, $group, $count);
	$stmt->execute();
}

function decompress($compressed) {
	$dictSize = 256;
	$dictionary = array();
	for ($i = 1; $i < 256; $i++) {
		$dictionary[$i] = chr($i);
	}
	$w = chr($compressed[0]);
	$result = $w;
	for ($i = 1; $i < count($compressed); $i++) {
		$entry = "";
		$k = $compressed[$i];
		if (isset($dictionary[$k])) {
			$entry = $dictionary[$k];
		} else if ($k == $dictSize) {
			$entry = $w.charAt($w, 0);
		} else {
			return null;
		}
		$result .= $entry;
		$dictionary[$dictSize++] = $w.charAt($entry, 0);
		$w = $entry;
	}
	return $result;
}
function charAt($string, $index){
	if($index < mb_strlen($string)){
		return mb_substr($string, $index, 1);
	} else{
		return -1;
	}
}

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
			* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}

// DO NOT USE IT. ONLY TO BE USED IN UPDATE 9
function getCardsOnAllBoards_Update9() {
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$stmt = $mysqli->prepare("SELECT id, cards FROM ".$db_table_prefix."boards");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $cards);
	while ($stmt->fetch()){
		$row[$id] = unserialize_or_empty_array($cards);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
	return array();
}


// DO NOT USE IT. ONLY TO BE USED IN UPDATE 11
function getCardsOnAllBoards_Update11() {
	global $mysqli,$db_table_prefix;
	$board_id = NULL;
	$stmt = $mysqli->prepare("SELECT id FROM ".$db_table_prefix."boards");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($board_id);
	while ($stmt->fetch()){
		$row[$board_id] = get_cards_on_board_update11($board_id);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
	return array();
}

function store_cards_on_board($board_id, $cards) {
	global $mysqli,$db_table_prefix;
	
	$stmt1 = $mysqli->prepare("DELETE FROM ".$db_table_prefix."board_cards WHERE board_id = ? ");
	$stmt1->bind_param("i", $board_id);
	$stmt1->execute();
	$stmt1->close();
	add_cards_to_board($board_id, $cards);
}

function migrate_board_cards() {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."boards SET cards = NULL WHERE id = ?");
	$boards = getCardsOnAllBoards_Update9();

	foreach($boards as $board_id => $cards) {
		echo translate('Migrating cards of board %1$d  (%2$d cards)', $board_id, count($cards)).'<br/><br/>';
		store_cards_on_board($board_id, $cards);
		$stmt->bind_param("i", $board_id);
		$stmt->execute();
	}
}

function migrate_board_card_attributes() {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."board_cards SET attributes = NULL WHERE board_id = ?");
	$boards = getCardsOnAllBoards_Update11();

	foreach($boards as $board_id => $cards) {
		echo translate('Migrating cards of board %1$d  (%2$d cards)', $board_id, count($cards)).'<br/><br/>';
		store_cards_on_board($board_id, $cards);
		$stmt->bind_param("i", $board_id);
		$stmt->execute();
	}
}

function add_cards_to_board($board_id, $cards) {
	global $mysqli,$db_table_prefix;

	foreach ($cards as $card_id => $card_data) {
		$stmt = $mysqli->prepare("SELECT id FROM ".$db_table_prefix."board_cards WHERE board_id = ? AND card_name = ? LIMIT 1");
		$stmt->bind_param("is", $board_id, $card_id);
		if (!$stmt->execute()) {
			error_log($stmt->error);
		}
		$stmt->bind_result($id);
		if (!$stmt->fetch()) {
			$id = 0;
		}
		$stmt->close();
		if (isset($id) && $id>0) {
			//Do nothing, just update the attributes
		}
		else {
			$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."board_cards(board_id, card_name, order_nr)
								SELECT ?, ?, COALESCE((MAX(order_nr)+1),0) FROM ".$db_table_prefix."board_cards WHERE board_id = ?");
			$stmt->bind_param("isi", $board_id, $card_id, $board_id);
			if ($stmt->execute()) {
				error_log($stmt->error);
			}
			$stmt->close();
		}		
		remove_card_attributes($board_id, $card_id);
		foreach ($card_data as $name => $value) {
			$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."board_card_attributes(board_id, card_name, attribute_name, attribute_value)
											VALUES(?, ?, ?, ?)");
			$stmt->bind_param("isss", $board_id, $card_id, $name, $value);
			if ($stmt->execute()) {
				error_log($stmt->error);
			}
			$stmt->close();
		}
	}
}

function remove_card_attributes($board_id, $cardid) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."board_card_attributes WHERE board_id = ? AND card_name = ?");
	$stmt->bind_param("is", $board_id, $cardid);
	$stmt->execute();
	$stmt->close();
}

function remove_card_from_board($board_id, $cardid) {
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."board_cards WHERE board_id = ? AND card_name = ? LIMIT 1");
	$stmt->bind_param("is", $board_id, $cardid);
	$stmt->execute();
	$stmt->close();
}

function get_cards_on_board($board_id) {
	global $mysqli,$db_table_prefix;
	$cards = NULL;
	$stmt = $mysqli->prepare("SELECT ".$db_table_prefix."board_cards.card_name, attribute_name, attribute_value
	                        FROM ".$db_table_prefix."board_cards
	                        JOIN ".$db_table_prefix."board_card_attributes
	                        ON ".$db_table_prefix."board_cards.board_id=".$db_table_prefix."board_card_attributes.board_id
	                        AND ".$db_table_prefix."board_cards.card_name=".$db_table_prefix."board_card_attributes.card_name
	                        WHERE ".$db_table_prefix."board_cards.board_id = ?
	                        ORDER BY order_nr ASC");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name, $value);
	while ($stmt->fetch()){
		$cards[$id][$name] = $value;
	}
	$stmt->close();
	if (isset($cards)) {
		return ($cards);
	}
}

function get_cards_on_board_update11($board_id) {
	global $mysqli,$db_table_prefix;
	$cards = NULL;
	$stmt = $mysqli->prepare("SELECT card_name, attributes
			FROM ".$db_table_prefix."board_cards
			WHERE
			board_id = ?
			ORDER BY order_nr ASC");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($card_id, $card_data);
	while ($stmt->fetch()){
		$row[$card_id] = unserialize_or_empty_array($card_data);
	}
	$stmt->close();
	if (isset($row)) {
		return ($row);
	}
}

function get_board_export_id_prefix($board_id) {
	global $mysqli,$db_table_prefix;
	$prefix = NULL;
	$stmt = $mysqli->prepare("SELECT export_id_prefix
			FROM ".$db_table_prefix."boards
			WHERE
			id = ?
			LIMIT 1");
	$stmt->bind_param("i", $board_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($prefix);
	$stmt->fetch();
	$stmt->close();

	return $prefix;
}

?>
