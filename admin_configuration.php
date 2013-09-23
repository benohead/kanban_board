<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){
	die();
}

//Forms posted
if(!empty($_POST))
{
	$config_id = array();
	$new_settings = $_POST['settings'];

	//Validate new site name
	if ($new_settings[1] != $website_name) {
		$new_website_name = $new_settings[1];
		if(min_max_range(1,150,$new_website_name))
		{
			$errors[] = translate('Site name must be between %1$d and %2$d characters in length',1,150);
		}
		else if (count($errors) == 0) {
			$config_id[] = 1;
			$config_value[1] = $new_website_name;
			$website_name = $new_website_name;
		}
	}

	//Validate new URL
	if ($new_settings[2] != $website_url) {
		$new_website_url = $new_settings[2];
		if(min_max_range(1,150,$new_website_url))
		{
			$errors[] = translate('Site name must be between %1$d and %2$d characters in length',1,150);
		}
		else if (substr($new_website_url, -1) != "/"){
			$errors[] = translate('Please include the ending / in your site\'s URL');
		}
		else if (count($errors) == 0) {
			$config_id[] = 2;
			$config_value[2] = $new_website_url;
			$website_url = $new_website_url;
		}
	}

	//Validate new site email address
	if ($new_settings[3] != $email_address) {
		$new_email = $new_settings[3];
		if(min_max_range(1,150,$new_email))
		{
			$errors[] = translate('Site name must be between %1$d and %2$d characters in length',1,150);
		}
		elseif(!isValidEmail($new_email))
		{
			$errors[] = translate('The email you have entered is not valid');
		}
		else if (count($errors) == 0) {
			$config_id[] = 3;
			$config_value[3] = $new_email;
			$email_address = $new_email;
		}
	}

	//Validate email activation selection
	if ($new_settings[4] != $email_activation) {
		$new_activation = $new_settings[4];
		if($new_activation != "true" AND $new_activation != "false")
		{
			$errors[] = translate('Email activation must be either "true" or "false"');
		}
		else if (count($errors) == 0) {
			$config_id[] = 4;
			$config_value[4] = $new_activation;
			$email_activation = $new_activation;
		}
	}

	//Validate new email activation resend threshold
	if ($new_settings[5] != $resend_activation_threshold) {
		$new_resend_activation_threshold = $new_settings[5];
		if($new_resend_activation_threshold > 72 OR $new_resend_activation_threshold < 0)
		{
			$errors[] = translate('Activation Threshold must be between %1$d and %2$d hours',0,72);
		}
		else if (count($errors) == 0) {
			$config_id[] = 5;
			$config_value[5] = $new_resend_activation_threshold;
			$resend_activation_threshold = $new_resend_activation_threshold;
		}
	}

	//Validate new template selection
	if ($new_settings[6] != $template) {
		$newTemplate = $new_settings[6];
		if(min_max_range(1,150,$template))
		{
			$errors[] = translate('Template path must be between %1$d and %2$d characters in length',1,150);
		}
		elseif (!file_exists($newTemplate)) {
			$errors[] = translate('There is no file for the template key "%1$s"',$newTemplate);
		}
		else if (count($errors) == 0) {
			$config_id[] = 6;
			$config_value[6] = $newTemplate;
			$template = $newTemplate;
		}
	}

	//Update configuration table with new settings
	if (count($errors) == 0 AND count($config_id) > 0) {
		update_config($config_id, $config_value);
		$successes[] = translate('Your site\'s configuration has been updated. You may need to load a new page for all the settings to take effect');
	}
}

$templates = getTemplateFiles(); //Retrieve list of template files
$roleData = fetchAllRoles(); //Retrieve list of all roles
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
				<?php echo translate('Admin Configuration'); ?>
			</h2>
				<?php
				include("left-nav.php");
				?>
			<div id='main'>
				<?php
				echo resultBlock($errors,$successes);
				?>
				<div id='regbox'>
					<form name='adminConfiguration' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
						<p>
							<label><?php echo translate('Website Name:'); ?> </label>
							<input type='text' name='settings[<?php echo $settings['website_name']['id']; ?>]' value='<?php echo $website_name; ?>' placeholder='<?php echo translate('Website Name'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('Website URL:'); ?> </label>
							<input type='text' name='settings[<?php echo $settings['website_url']['id']; ?>]' value='<?php echo $website_url; ?>' placeholder='<?php echo translate('Website URL'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('Email:'); ?> </label>
							<input type='text' name='settings[<?php echo $settings['email']['id']; ?>]' value='<?php echo $email_address; ?>' placeholder='<?php echo translate('Email Address'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('Activation Threshold:'); ?> </label>
							<input type='text' name='settings[<?php echo $settings['resend_activation_threshold']['id']; ?>]' value='<?php echo $resend_activation_threshold; ?>' placeholder='<?php echo translate('Activation Threshold'); ?>'/>
						</p>
						<p>
							<label><?php echo translate('Email Activation:'); ?> </label> 
							<select name='settings[<?php echo $settings['activation']['id']; ?>]'>
								<?php
								//Display email activation options
								if ($email_activation == "true"){
?>
									<option value='true' selected>								
										<?php echo translate('True'); ?>
									</option>
									<option value='false'>
										<?php echo translate('False'); ?>
									</option>
							<?php
}
else {
?>
								<option value='true'>
									<?php echo translate('True'); ?>
								</option>
								<option value='false' selected>
									<?php echo translate('False'); ?>
								</option>
							<?php
}
?>
							</select>
						</p>
						<p>
							<label><?php echo translate('Template:'); ?> </label> <select
								name='settings[<?php echo $settings['template']['id']; ?>]'>
								<?php
								//Display template options
								foreach ($templates as $temp){
	if ($temp == $template){
?>
								<option value='<?php echo $temp; ?>' selected>
									<?php echo $temp; ?>
								</option>
								<?php
	}
	else {
?>
								<option value='<?php echo $temp; ?>'>
									<?php echo $temp; ?>
								</option>
								<?php
	}
}
?>
							</select>
						</p>
						<input type='submit' name='Submit' value='<?php echo translate('Submit'); ?>' />
					</form>
				</div>
			</div>
			<div id='bottom'></div>
		</div>
	</div>
	<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
