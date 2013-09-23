<?php

require_once("db-settings.php"); //Require DB connection

//Retrieve settings
$stmt = $mysqli->prepare("SELECT id, name, value
		FROM ".$db_table_prefix."configuration");
$stmt->execute();
$stmt->bind_result($id, $name, $value);

while ($stmt->fetch()){
	$settings[$name] = array('id' => $id, 'name' => $name, 'value' => $value);
}
$stmt->close();

//Set Settings
$email_activation = $settings['activation']['value'];
$mail_templates_dir = "models/mail-templates/";
$website_name = $settings['website_name']['value'];
$website_url = $settings['website_url']['value'];
$email_address = $settings['email']['value'];
$resend_activation_threshold = $settings['resend_activation_threshold']['value'];
$email_date = date('dmy');
$template = $settings['template']['value'];

$master_account = -1;

$default_hooks = array("#WEBSITENAME#","#WEBSITEURL#","#DATE#");
$default_replace = array($website_name,$website_url,$email_date);


//Pages to require
require_once("class.mail.php");
require_once("class.project.php");
require_once("class.board.php");
require_once("class.user.php");
require_once("class.newuser.php");
require_once("funcs.php");

session_start();

//Global User Object Var
//logged_in_user can be used globally if constructed
if(isset($_SESSION["kanbanUser"]) && is_object($_SESSION["kanbanUser"]))
{
	$logged_in_user = $_SESSION["kanbanUser"];
}

?>
