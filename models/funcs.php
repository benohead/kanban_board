<?php

//Functions that do not interact with DB
//------------------------------------------------------------------------------

//Retrieve a list of all .css files in models/site-templates 
function getTemplateFiles()
{
	$directory = "models/site-templates/";
	$templates = glob($directory . "*.css");
	//print each file name
	return $templates;
}

//Retrieve a list of all .php files in root files folder
function getPageFiles()
{
	$directory = "";
	$pages = glob($directory . "*.php");
	//print each file name
	foreach ($pages as $page){
		$row[$page] = $page;
	}
	return $row;
}

//Destroys a session as part of logout
function destroySession($name)
{
	if(isset($_SESSION[$name]))
	{
		$_SESSION[$name] = NULL;
		unset($_SESSION[$name]);
	}
}

//Generate a unique code
function getUniqueCode($length = "")
{	
	$code = md5(uniqid(rand(), true));
	if ($length != "") return substr($code, 0, $length);
	else return $code;
}

//Generate an activation key
function generate_activation_token($gen = null)
{
	do
	{
		$gen = md5(uniqid(mt_rand(), false));
	}
	while(validateActivationToken($gen));
	return $gen;
}

//@ Thanks to - http://phpsec.org
function generate_hash($plainText, $salt = null)
{
	if ($salt === null)
	{
		$salt = substr(md5(uniqid(rand(), true)), 0, 25);
	}
	else
	{
		$salt = substr($salt, 0, 25);
	}
	
	return $salt . sha1($salt . $plainText);
}

//Checks if an email is valid
function isValidEmail($email)
{
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	}
	else {
		return false;
	}
}

//Checks if a string is within a min and max length
function min_max_range($min, $max, $what)
{
	if(strlen(trim($what)) < $min)
		return true;
	else if(strlen(trim($what)) > $max)
		return true;
	else
	return false;
}

//Replaces hooks with specified text
function replaceDefaultHook($str)
{
	global $default_hooks,$default_replace;	
	return (str_replace($default_hooks,$default_replace,$str));
}

//Displays error and success messages
function resultBlock($errors,$successes){
	//Error block
	if(count($errors) > 0)
	{
?>
		<div id='error'>
		<a style="text-decoration: none;" href='#' onclick="document.getElementById('error').style.display = 'none';">[X]</a>
		<ul>
<?php		
		foreach($errors as $error)
		{
?>
			<li><?php echo $error; ?></li>
<?php
		}
?>
		</ul>
		</div>
<?php
	}
	//Success block
	if(count($successes) > 0)
	{
?>
		<div id='success'>
		<a style="text-decoration: none;" href='#' onclick="document.getElementById('success').style.display = 'none';">[X]</a>
		<ul>
<?php
		foreach($successes as $success)
		{
?>
			<li><?php echo $success; ?></li>
<?php
		}
?>
		</ul>
		</div>
<?php
	}
}

//Completely sanitizes text
function sanitize($str)
{
	return strtolower(strip_tags(trim(($str))));
}

//Functions that interact mainly with .users table
//------------------------------------------------------------------------------

//Delete a defined array of users
function deleteUsers($users) {
	global $mysqli,$db_table_prefix; 
	$i = 0;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."users 
		WHERE id = ?");
	$stmt2 = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_roles 
		WHERE user_id = ?");
	foreach($users as $id){
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt2->bind_param("i", $id);
		$stmt2->execute();
		$i++;
	}
	$stmt->close();
	$stmt2->close();
	return $i;
}

//Check if a display name exists in the DB
function displayname_exists($displayname)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT active
		FROM ".$db_table_prefix."users
		WHERE
		display_name = ?
		LIMIT 1");
	$stmt->bind_param("s", $displayname);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Check if an email exists in the DB
function email_exists($email)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT active
		FROM ".$db_table_prefix."users
		WHERE
		email = ?
		LIMIT 1");
	$stmt->bind_param("s", $email);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Check if a user name and email belong to the same user
function emailUsernameLinked($email,$username)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT active
		FROM ".$db_table_prefix."users
		WHERE user_name = ?
		AND
		email = ?
		LIMIT 1
		");
	$stmt->bind_param("ss", $username, $email);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Retrieve information for all users
function fetchAllUsers()
{
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$user = NULL;
	$display = NULL;
	$password = NULL;
	$email = NULL;
	$token = NULL;
	$activationRequest = NULL;
	$passwordRequest = NULL;
	$active = NULL;
	$title = NULL;
	$signUp = NULL;
	$signIn = NULL; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		user_name,
		display_name,
		password,
		email,
		activation_token,
		last_activation_request,
		lost_password_request,
		active,
		title,
		sign_up_stamp,
		last_sign_in_stamp
		FROM ".$db_table_prefix."users");
	$stmt->execute();
	$stmt->bind_result($id, $user, $display, $password, $email, $token, $activationRequest, $passwordRequest, $active, $title, $signUp, $signIn);
	
	while ($stmt->fetch()){
		$row[] = array('id' => $id, 'user_name' => $user, 'display_name' => $display, 'password' => $password, 'email' => $email, 'activation_token' => $token, 'last_activation_request' => $activationRequest, 'lost_password_request' => $passwordRequest, 'active' => $active, 'title' => $title, 'sign_up_stamp' => $signUp, 'last_sign_in_stamp' => $signIn);
	}
	$stmt->close();
	return ($row);
}

//Retrieve complete user information by username, token or ID
function fetchUserDetails($username=NULL,$token=NULL, $id=NULL)
{
	$user = NULL;
	$display = NULL;
	$password = NULL;
	$email = NULL;
	$token = NULL;
	$activationRequest = NULL;
	$passwordRequest = NULL;
	$active = NULL;
	$title = NULL;
	$signUp = NULL;
	$signIn = NULL;
	if($username!=NULL) {
		$column = "user_name";
		$data = $username;
	}
	elseif($token!=NULL) {
		$column = "activation_token";
		$data = $token;
	}
	elseif($id!=NULL) {
		$column = "id";
		$data = $id;
	}
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		user_name,
		display_name,
		password,
		email,
		activation_token,
		last_activation_request,
		lost_password_request,
		active,
		title,
		sign_up_stamp,
		last_sign_in_stamp
		FROM ".$db_table_prefix."users
		WHERE
		$column = ?
		LIMIT 1");
		$stmt->bind_param("s", $data);
	
	$stmt->execute();
	$stmt->bind_result($id, $user, $display, $password, $email, $token, $activationRequest, $passwordRequest, $active, $title, $signUp, $signIn);
	while ($stmt->fetch()){
		$row = array('id' => $id, 'user_name' => $user, 'display_name' => $display, 'password' => $password, 'email' => $email, 'activation_token' => $token, 'last_activation_request' => $activationRequest, 'lost_password_request' => $passwordRequest, 'active' => $active, 'title' => $title, 'sign_up_stamp' => $signUp, 'last_sign_in_stamp' => $signIn);
	}
	$stmt->close();
	return ($row);
}

//Toggle if lost password request flag on or off
function flagLostPasswordRequest($username,$value)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET lost_password_request = ?
		WHERE
		user_name = ?
		LIMIT 1
		");
	$stmt->bind_param("ss", $value, $username);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

//Check if a user is logged in
function isUserLoggedIn()
{
	global $logged_in_user,$mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT 
		id,
		password
		FROM ".$db_table_prefix."users
		WHERE
		id = ?
		AND 
		password = ? 
		AND
		active = 1
		LIMIT 1");
	$stmt->bind_param("is", $logged_in_user->user_id, $logged_in_user->hash_pw);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if($logged_in_user == NULL)
	{
		return false;
	}
	else
	{
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			destroySession("kanbanUser");
			return false;	
		}
	}
}

//Change a user from inactive to active
function setUserActive($token)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET active = 1
		WHERE
		activation_token = ?
		LIMIT 1");
	$stmt->bind_param("s", $token);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Change a user's display name
function updateDisplayName($id, $display)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET display_name = ?
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("si", $display, $id);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

//Update a user's email
function updateEmail($id, $email)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET 
		email = ?
		WHERE
		id = ?");
	$stmt->bind_param("si", $email, $id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Input new activation token, and update the time of the most recent activation request
function updateLastActivationRequest($new_activation_token,$username,$email)
{
	global $mysqli,$db_table_prefix; 	
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET activation_token = ?,
		last_activation_request = ?
		WHERE email = ?
		AND
		user_name = ?");
	$stmt->bind_param("ssss", $new_activation_token, time(), $email, $username);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Generate a random password, and new token
function updatePasswordFromToken($pass,$token)
{
	global $mysqli,$db_table_prefix;
	$new_activation_token = generate_activation_token();
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET password = ?,
		activation_token = ?
		WHERE
		activation_token = ?");
	$stmt->bind_param("sss", $pass, $new_activation_token, $token);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Update a user's title
function updateTitle($id, $title)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET 
		title = ?
		WHERE
		id = ?");
	$stmt->bind_param("si", $title, $id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;	
}

//Check if a user ID exists in the DB
function userIdExists($id)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT active
		FROM ".$db_table_prefix."users
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("i", $id);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Checks if a username exists in the DB
function usernameExists($username)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT active
		FROM ".$db_table_prefix."users
		WHERE
		user_name = ?
		LIMIT 1");
	$stmt->bind_param("s", $username);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Check if activation token exists in DB
function validateActivationToken($token,$lostpass=NULL)
{
	global $mysqli,$db_table_prefix;
	if($lostpass == NULL) 
	{	
		$stmt = $mysqli->prepare("SELECT active
			FROM ".$db_table_prefix."users
			WHERE active = 0
			AND
			activation_token = ?
			LIMIT 1");
	}
	else 
	{
		$stmt = $mysqli->prepare("SELECT active
			FROM ".$db_table_prefix."users
			WHERE active = 1
			AND
			activation_token = ?
			AND
			lost_password_request = 1 
			LIMIT 1");
	}
	$stmt->bind_param("s", $token);
	$stmt->execute();
	$stmt->store_result();
		$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Functions that interact mainly with .roles table
//------------------------------------------------------------------------------

//Create a role in DB
function createRole($role) {
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."roles (
		name
		)
		VALUES (
		?
		)");
	$stmt->bind_param("s", $role);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Delete a role from the DB
function deleteRole($role) {
	global $mysqli,$db_table_prefix,$errors; 
	$i = 0;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."roles 
		WHERE id = ?");
	$stmt2 = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_roles 
		WHERE role_id = ?");
	foreach($role as $id){
		if ($id == 1){
			$errors[] = translate('You cannot delete the default "new user" group');
		}
		elseif ($id == 2){
			$errors[] = translate('You cannot delete the default "admin" group');
		}
		else{
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$stmt2->bind_param("i", $id);
			$stmt2->execute();
			$i++;
		}
	}
	$stmt->close();
	$stmt2->close();
	return $i;
}

//Retrieve information for all roles
function fetchAllRoles()
{
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$name = NULL; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		name
		FROM ".$db_table_prefix."roles");
	$stmt->execute();
	$stmt->bind_result($id, $name);
	while ($stmt->fetch()){
		$row[] = array('id' => $id, 'name' => $name);
	}
	$stmt->close();
	return ($row);
}

//Retrieve information for a single role
function fetchRoleDetails($id)
{
	global $mysqli,$db_table_prefix; 
	$name = NULL; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		name
		FROM ".$db_table_prefix."roles
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($id, $name);
	while ($stmt->fetch()){
		$row = array('id' => $id, 'name' => $name);
	}
	$stmt->close();
	return ($row);
}

//Check if a role ID exists in the DB
function roleIdExists($id)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT id
		FROM ".$db_table_prefix."roles
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("i", $id);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Check if a role name exists in the DB
function roleNameExists($role)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT id
		FROM ".$db_table_prefix."roles
		WHERE
		name = ?
		LIMIT 1");
	$stmt->bind_param("s", $role);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Change a role's name
function updateRoleName($id, $name)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."roles
		SET name = ?
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("si", $name, $id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;	
}

//Functions that interact mainly with .user_roles table
//------------------------------------------------------------------------------

//Match role(s) with user(s)
function addRole($role, $user) {
	global $mysqli,$db_table_prefix; 
	$i = 0;
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."user_roles (
		role_id,
		user_id
		)
		VALUES (
		?,
		?
		)");
	if (is_array($role)){
		foreach($role as $id){
			$stmt->bind_param("ii", $id, $user);
			$stmt->execute();
			$i++;
		}
	}
	elseif (is_array($user)){
		foreach($user as $id){
			$stmt->bind_param("ii", $role, $id);
			$stmt->execute();
			$i++;
		}
	}
	else {
		$stmt->bind_param("ii", $role, $user);
		$stmt->execute();
		$i++;
	}
	$stmt->close();
	return $i;
}

//Retrieve information for all user/role matches
function fetchAllMatches()
{
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$user = NULL;
	$role = NULL; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		user_id,
		role_id
		FROM ".$db_table_prefix."user_roles");
	$stmt->execute();
	$stmt->bind_result($id, $user, $role);
	while ($stmt->fetch()){
		$row[] = array('id' => $id, 'user_id' => $user, 'role_id' => $role);
	}
	$stmt->close();
	return ($row);	
}

//Retrieve list of roles a user has
function fetchUserRoles($user_id)
{
	global $mysqli,$db_table_prefix; 
	$id = NULL;
	$role = NULL;
	$stmt = $mysqli->prepare("SELECT
		id,
		role_id
		FROM ".$db_table_prefix."user_roles
		WHERE user_id = ?
		");
	$stmt->bind_param("i", $user_id);	
	$stmt->execute();
	$stmt->bind_result($id, $role);
	while ($stmt->fetch()){
		$row[$role] = array('id' => $id, 'role_id' => $role);
	}
	$stmt->close();
	if (isset($row)){
		return ($row);
	}
}

//Retrieve list of users who have a role
function fetchRoleUsers($role_id)
{
	global $mysqli,$db_table_prefix;
	$id = NULL;
	$user = NULL;
	$stmt = $mysqli->prepare("SELECT id, user_id
		FROM ".$db_table_prefix."user_roles
		WHERE role_id = ?
		");
	$stmt->bind_param("i", $role_id);	
	$stmt->execute();
	$stmt->bind_result($id, $user);
	while ($stmt->fetch()){
		$row[$user] = array('id' => $id, 'user_id' => $user);
	}
	$stmt->close();
	if (isset($row)){
		return ($row);
	}
}

//Unmatch role(s) from user(s)
function removeRole($role, $user) {
	global $mysqli,$db_table_prefix; 
	$i = 0;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_roles 
		WHERE role_id = ?
		AND user_id =?");
	if (is_array($role)){
		foreach($role as $id){
			$stmt->bind_param("ii", $id, $user);
			$stmt->execute();
			$i++;
		}
	}
	elseif (is_array($user)){
		foreach($user as $id){
			$stmt->bind_param("ii", $role, $id);
			$stmt->execute();
			$i++;
		}
	}
	else {
		$stmt->bind_param("ii", $role, $user);
		$stmt->execute();
		$i++;
	}
	$stmt->close();
	return $i;
}

//Functions that interact mainly with .configuration table
//------------------------------------------------------------------------------

//Update configuration table
function update_config($id, $value)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."configuration
		SET 
		value = ?
		WHERE
		id = ?");
	foreach ($id as $cfg){
		$stmt->bind_param("si", $value[$cfg], $cfg);
		$stmt->execute();
	}
	$stmt->close();	
}

//------------------------------------------------------------------------------

//Check if a user has access to a page
function securePage($uri){
	
	//Separate document name from uri
	$tokens = explode('/', $uri);
	$page = $tokens[sizeof($tokens)-1];
	global $mysqli,$db_table_prefix,$logged_in_user,$master_account;
	$role = NULL;
	$id = NULL;
	$private = NULL;
	//retrieve page details
	$stmt = $mysqli->prepare("SELECT 
		id,
		page,
		private
		FROM ".$db_table_prefix."pages
		WHERE
		page = ?
		LIMIT 1");
	$stmt->bind_param("s", $page);
	$stmt->execute();
	$stmt->bind_result($id, $page, $private);
	while ($stmt->fetch()){
		$pageDetails = array('id' => $id, 'page' => $page, 'private' => $private);
	}
	$stmt->close();
	
	//If page does not exist in DB only allow access to logged in users
	if (empty($pageDetails)){
		if (!isUserLoggedIn()) {
			header("Location: login.php");
			return false;			
		}
		return true;
	}
	//If page is public, allow access
	elseif ($pageDetails['private'] == 0) {
		return true;	
	}
	//If user is not logged in, deny access
	elseif(!isUserLoggedIn()) {
		header("Location: login.php");
		return false;
	}
	//private page in database and user logged in => allow access
	return true;
}

function translate() {
	$parameters = func_get_args();
	$parameters[0] = _($parameters[0]);
	return call_user_func_array('sprintf', $parameters);
}

	
function install_basic_settings($prepare_stmt, $table_name) {
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("truncate table " . $db_table_prefix . $table_name);
	if ($stmt->execute()) {
?>
		<p><?php echo translate('Truncated table %1$s.....', $db_table_prefix.$table_name); ?></p>
<?php
		$stmt = $mysqli->prepare($prepare_stmt);
		if ($stmt->execute()) {
?>
			<p><?php echo translate('Inserted basic config settings into table %1$s.....', $db_table_prefix.$table_name); ?></p>
<?php
		} else {
?>
			<p><?php echo translate('Error inserting basic config settings into table %1$s.', $table_name); ?></p>
<?php
			return true;
		}
	} else {
?>
		<p><?php echo translate('Error truncating table %1$s.', $table_name); ?></p>
<?php
		return true;
	}
	return false;
}

function install_table($prepare_stmt, $table_name) {
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare($prepare_stmt);
	if ($stmt->execute()) {
		?>
		<p><?php echo translate('%1$s table created.....', $db_table_prefix.$table_name); ?></p>
<?php
	} else {
?>
		<p><?php echo translate('Error constructing %1$s table.', $table_name); ?></p>
<?php
		return true;
	}
	return false;
}

function update_table($prepare_stmt, $table_name) {
	global $mysqli, $db_table_prefix;
	if ($mysqli->query($prepare_stmt) || $mysqli->errno==1060) {
		?>
		<p><?php echo translate('%1$s table updated.....', $db_table_prefix.$table_name); ?></p>
<?php
	}
	else {
?>
		<p><?php echo translate('Error updating %1$s table.', $table_name); ?></p>
<?php
		return true;
	}
	return false;
}

function import_rule_types($rule_types_path) {
	$d = dir($rule_types_path);
?>
		<p><?php echo translate('Importing rule types from %1$s.', realpath($d->path)); ?></p>
<?php
	while (false !== ($entry = $d->read())) {
		if ($entry != "." && $entry != "..") {
			import_rule_type($entry, $rule_types_path);
		}
	}
	$d->close();
}

function import_rule_type($rule_type_name, $rule_types_path) {
	global $mysqli, $db_table_prefix;
	if (!file_exists($rule_types_path."/".$rule_type_name."/display_name.txt")) {
		?>
		<p><?php echo translate('Skipping %1$s as it is not a rule type.', $rule_type_name); ?></p>
<?php
	}
	else {
?>
		<p><?php echo translate('Importing rule type: %1$s.', $rule_type_name); ?></p>
<?php
		$display_name = file_get_contents($rule_types_path."/".$rule_type_name."/display_name.txt");
		$action = file_get_contents($rule_types_path."/".$rule_type_name."/action.txt");
		$rule_js = file_get_contents($rule_types_path."/".$rule_type_name."/rule.js");
		$stmt = $mysqli->prepare("DELETE FROM " . $db_table_prefix . "rule_types WHERE rule_name=?");
		if ( false===$stmt ) {
			return true;
		}
	
		$stmt->bind_param("s", $rule_type_name);
		$stmt->execute();
		$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "rule_types (
											rule_name,
											display_name,
											active,
											action,
											rule_js
									)
									VALUES (?,
											?,
											1,
											?,
											?)");
		if ( false===$stmt ) {
			return true;
		}
	
		$stmt->bind_param("ssss", $rule_type_name, $display_name, $action, $rule_js);
		$rc = $stmt->execute();
		if ( false===$rc ) {
			return true;
		}
		$inserted_id = $mysqli->insert_id;
		$stmt->close();
	}
	return false;
}

function import_generator_types($generator_types_path) {
	$d = dir($generator_types_path);
?>
		<p><?php echo translate('Importing generator types from %1$s.', realpath($d->path)); ?></p>
<?php
	while (false !== ($entry = $d->read())) {
		if ($entry != "." && $entry != "..") {
			import_generator_type($entry, $generator_types_path);
		}
	}
	$d->close();
}

function import_generator_type($generator_type_name, $generator_types_path) {
	global $mysqli, $db_table_prefix;
	if (!file_exists($generator_types_path."/".$generator_type_name."/display_name.txt")) {
		?>
		<p><?php echo translate('Skipping %1$s as it is not a generator type.', $generator_type_name); ?></p>
<?php
	}
	else {
?>
		<p><?php echo translate('Importing generator type: %1$s.', $generator_type_name); ?></p>
<?php
		$display_name = file_get_contents($generator_types_path."/".$generator_type_name."/display_name.txt");
		$action = file_get_contents($generator_types_path."/".$generator_type_name."/action.txt");
		$generator_js = file_get_contents($generator_types_path."/".$generator_type_name."/generator.js");
		$stmt = $mysqli->prepare("DELETE FROM " . $db_table_prefix . "generator_types WHERE generator_name=?");
		if ( false===$stmt ) {
			return true;
		}
	
		$stmt->bind_param("s", $generator_type_name);
		$stmt->execute();
		$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "generator_types (
											generator_name,
											display_name,
											active,
											action,
											generator_js
									)
									VALUES (?,
											?,
											1,
											?,
											?)");
		if ( false===$stmt ) {
			return true;
		}
	
		$stmt->bind_param("ssss", $generator_type_name, $display_name, $action, $generator_js);
		$rc = $stmt->execute();
		if ( false===$rc ) {
			return true;
		}
		$inserted_id = $mysqli->insert_id;
		$stmt->close();
	}
	return false;
}

function import_board_templates($board_templates_path) {
	$d = dir($board_templates_path);
?>
		<p><?php echo translate('Importing board templates from %1$s.', realpath($d->path)); ?></p>
<?php
	while (false !== ($entry = $d->read())) {
		if ($entry != "." && $entry != "..") {
			import_board_template($entry, $board_templates_path);
		}
	}
	$d->close();
}

function import_board_template($template_name, $board_templates_path) {
	global $mysqli, $db_table_prefix;
	if (!file_exists($board_templates_path."/".$template_name."/display_name.txt")) {
?>
		<p><?php echo translate('Skipping %1$s as it is not a board template.', $template_name); ?></p>
<?php
	}
	else {
?>
		<p><?php echo translate('Importing board template: %1$s.', $template_name); ?></p>
<?php
		$display_name = file_get_contents($board_templates_path."/".$template_name."/display_name.txt");
		$board_css = file_get_contents($board_templates_path."/".$template_name."/board.css");
		$board_js = file_get_contents($board_templates_path."/".$template_name."/board.js");
		$board_columns = unserialize(file_get_contents($board_templates_path."/".$template_name."/columns.txt"));
		$stmt = $mysqli->prepare("DELETE FROM " . $db_table_prefix . "board_templates WHERE template_name=?");
		if ( false===$stmt ) {
			return true;
		}
	
		$stmt->bind_param("s", $template_name);
		$stmt->execute();
		$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "board_templates (
											template_name,
											display_name,
											active,
											board_css,
											board_js
									)
									VALUES (?,
											?,
											1,
											?,
											?)");
		if ( false===$stmt ) {
			return true;
		}
	
		$stmt->bind_param("ssss", $template_name, $display_name, $board_css, $board_js);
		$rc = $stmt->execute();
		if ( false===$rc ) {
			return true;
		}
		$inserted_id = $mysqli->insert_id;
		$stmt->close();
		$order_nr = 0;
		foreach($board_columns as $column_name => $column_data) {
			$description = isset($column_data['description']) ? $column_data['description'] : ""; 
			$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "board_template_columns (
													template_id,
													column_name,
													display_name,
													wip_limit,
													order_nr,
													description
											)
											VALUES (?,
													?,
													?,
													?,
													?,
													?)");
			if ( false===$stmt ) {
				return true;
			}
			$wip_limit = isset($column_data['wip_limit']) ? $column_data['wip_limit'] : 0;			
			$stmt->bind_param("issiis", $inserted_id, $column_name, $column_data['display_name'], $wip_limit, $order_nr, $description);
			$order_nr++;
			$rc = $stmt->execute();
			if ( false===$rc ) {
				return true;
			}
			$stmt->close();
		}
	}
	return false;
}

function import_card_templates($card_templates_path) {
	$d = dir($card_templates_path);
?>
		<p><?php echo translate('Importing card templates from %1$s.', realpath($d->path)); ?></p>
<?php
	while (false !== ($entry = $d->read())) {
		if ($entry != "." && $entry != "..") {
			import_card_template($entry, $card_templates_path);
		}
	}
	$d->close();
}

function import_card_template($template_name, $card_templates_path) {
	global $mysqli, $db_table_prefix;
	if (!file_exists($card_templates_path."/".$template_name."/display_name.txt")) {
?>
		<p><?php echo translate('Skipping %1$s as it is not a card template.', $template_name); ?></p>
<?php
	}
	else {
?>
		<p><?php echo translate('Importing card template: %1$s.', $template_name); ?></p>
<?php
		$display_name = file_get_contents($card_templates_path."/".$template_name."/display_name.txt");
		$card_css = file_get_contents($card_templates_path."/".$template_name."/card.css");
		$card_js = file_get_contents($card_templates_path."/".$template_name."/card.js");
		$card_attributes = file_get_contents($card_templates_path."/".$template_name."/attributes.txt");
		$stmt = $mysqli->prepare("DELETE FROM " . $db_table_prefix . "card_templates WHERE template_name=?");
		if ( false===$stmt ) {
			return true;
		}
	
		$stmt->bind_param("s", $template_name);
		$stmt->execute();
		$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "card_templates (
											template_name,
											display_name,
											active,
											card_css,
											card_js,
											card_attributes
									)
									VALUES (?,
											?,
											1,
											?,
											?,
											?)");
		if ( false===$stmt ) {
			return true;
		}
	
		$stmt->bind_param("sssss", $template_name, $display_name, $card_css, $card_js, $card_attributes);
		$rc = $stmt->execute();
		if ( false===$rc ) {
			return true;
		}
		$inserted_id = $mysqli->insert_id;
		$stmt->close();
	}
	return false;
}
?>
