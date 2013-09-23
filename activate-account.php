<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Get token param
if(isset($_GET["token"]))
{	
	$token = $_GET["token"];	
	if(!isset($token))
	{
		$errors[] = translate('Your activation token is not valid');
	}
	else if(!validateActivationToken($token)) //Check for a valid token. Must exist and active must be = 0
	{
		$errors[] = translate('Token does not exist / Account is already activated');
	}
	else
	{
		//Activate the users account
		if(!setUserActive($token))
		{
			$errors[] = translate('Fatal SQL error');
		}
	}
}
else
{
	$errors[] = translate('Your activation token is not valid');
}

if(count($errors) == 0) {
	$successes[] = translate('You have successfully activated your account. You can now login <a href="login.php">here</a>.');
}

require_once("models/header.php");

?>
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1><?php echo $website_name; ?></h1>
<h2><?php translate('Activate Account'); ?></h2>

<?php
include("left-nav.php");

?>
<div id='main'>

<?php echo resultBlock($errors,$successes); ?>

</div>
<div id='bottom'></div>
</div>
</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
</body>
</html>
