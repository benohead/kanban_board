<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/db-settings.php");
require_once("functions.php");
require_once("models/header.php");

?>
<body>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1><?php echo $website_name; ?></h1>
<h2><?php echo translate('Register board'); ?></h2>	
<?php
//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$projectid = trim($_POST["projectid"]);
	$boardname = trim($_POST["boardname"]);
	$displayname = trim($_POST["displayname"]);
	$templateid = trim($_POST["templateid"]);
	$card_template_id = trim($_POST["card_template_id"]);
	if (isset($_POST["active"])) {
		$active = trim($_POST["active"]);	
	}
	else {
		$active = 0;
	}
	
	if(min_max_range(1,50,$boardname)) {
		$errors[] = translate('The board name must have between %1$d and %2$d characters.', 1, 50);
	}
	if(!ctype_alnum($boardname)) {
		$errors[] = translate('The board name must only contain alphanumeric characters.');
	}
	if(min_max_range(1,50,$displayname)) {
		$errors[] = translate('The display name must have between %1$d and %2$d characters.', 1, 50);
	}

	//End data validation
	if(count($errors) == 0)
	{	
		//Construct a user object
		$board = new board($projectid,$boardname,$displayname,$active,$templateid,$card_template_id,0,0);
		
		//Checking this flag tells us whether there were any errors such as possible data duplication occured
		if(!$board->status)
		{
			if($board->boardname_taken) $errors[] = translate('Board name already taken');
			if($board->displayname_taken) $errors[] = translate('Display name already taken');
		}
		else
		{
			if(!$board->addboard())
			{
				if($board->sql_failure)  $errors[] = translate('Error inserting the board in the database');
			}
			else {
				$successes[] = translate('Board "%1$s" successfully created.', $board->displayname);
			}
		}
	}
}
else {
	$projectid = trim($_GET["projectid"]);
}
include("left-nav.php");
?>
<div id='main'>
<?php
echo resultBlock($errors,$successes);
?>
<div id='regbox'>
<form name='newboard' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>

<p>
<label><?php echo translate('Board Name:'); ?></label>
<input type='text' name='boardname' placeholder='<?php echo translate('Board Name'); ?>'/>
</p>
<p>
<label><?php echo translate('Display Name:'); ?></label>
<input type='text' name='displayname' placeholder='<?php echo translate('Display Name'); ?>'/>
</p>
<p>
<label><?php echo translate('Activate:'); ?></label>
<input type = 'checkbox' name ='active' value=1/>
</p>
<p>
<label><?php echo translate('Board Template:'); ?></label>
<select name ='templateid'>
<?php
$templates = fetchActiveTemplates();
foreach ($templates as $template) {
?>
	<option value='<?php echo $template['id']; ?>'><?php echo $template['display_name']; ?></option>
<?php
}
?>
</select>
</p>
<p>
<label><?php echo translate('Card Template:'); ?></label>
<select name ='card_template_id'>
<?php
$card_templates = fetchActiveCardTemplates();
foreach ($card_templates as $card_template) {
?>
	<option value='<?php echo $card_template['id']; ?>'><?php echo $card_template['display_name']; ?></option>
<?php
}
?>
</select>
</p>
<br>
<input type='hidden' name='projectid' value='<?php echo $projectid; ?>'/>
<p>
<input type='submit' value='<?php echo translate('Register'); ?>'/>
</p>

</form>
<a href='display_project.php?id=<?php echo $projectid; ?>'><?php echo translate('Back to the project details'); ?></a>
</div>

</div>
<div id='bottom'></div>
</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
</body>
</html>
