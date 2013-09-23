<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}
require_once("models/header.php");
?>
<body>
	<div id='wrapper'>
		<div id='top'>
			<div id='logo'></div>
		</div>
		<div id='content'>
			<h1>
				<?php echo $website_name; ?>
			</h1>
			<h2>
				<?php echo translate('Account'); ?>
			</h2>
				<?php
				include("left-nav.php");

				?>
			<div id='main'>
				<?php echo translate('Hey, %1$s. Just so you know, your title at the moment is %2$s, and that can be changed in the admin panel. You registered this account on %3$s.', $logged_in_user->displayname, $logged_in_user->title, date("M d, Y", $logged_in_user->signupTimeStamp())); ?>
			</div>
			<div id='bottom'></div>
		</div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
</body>
</html>
