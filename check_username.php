<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/db-settings.php");

global $mysqli,$db_table_prefix;

$username= mysql_real_escape_string($_REQUEST["username"]);

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
	echo 1;
}
else
{
	echo 0;
}
?>