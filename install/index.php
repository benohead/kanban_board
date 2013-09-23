<?php
	require_once ("../models/db-settings.php");
	require_once ("../models/funcs.php");
	require_once ("../functions.php");
	?>
<?php
$dirname = $_SERVER['REQUEST_URI'];
if (!preg_match('/\/$/', $dirname)) {
	$dirname = dirname($dirname);
}
while ( preg_match('/\.php$/', $dirname) ) {
	$dirname = dirname($dirname);
}
$dirname = rtrim($dirname,"/");
$dirname = rtrim($dirname,"\\");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<title><?php echo translate('amazingweb Kanban'); ?></title>
		<?php echo CssCrush::tag($dirname.'/../styles/main.css'); ?>
	</head>
	<body>
		<div id='top'><div id='logo'></div></div>
		<div id='content'>
			<h1><?php echo translate('Installer'); ?></h1>	
<?php
			if (isset ($_GET["install"])) {
				$db_issue = false;
			
				$versions_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "versions` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`version_type` varchar(50) NOT NULL,
						`version_number` int(11) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
			
				$versions_entry = "
						INSERT INTO `" . $db_table_prefix . "versions` (`version_type`, `version_number`) VALUES
						('application',3),
						('db schema', 3);
						";
				
				$roles_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "roles` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`name` varchar(150) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
				
				$roles_entry = "
						INSERT INTO `" . $db_table_prefix . "roles` (`id`, `name`) VALUES
						(1, 'New Member'),
						(2, 'Administrator');
						";
			
				$users_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "users` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`user_name` varchar(50) NOT NULL,
						`display_name` varchar(50) NOT NULL,
						`password` varchar(225) NOT NULL,
						`email` varchar(150) NOT NULL,
						`activation_token` varchar(225) NOT NULL,
						`last_activation_request` int(11) NOT NULL,
						`lost_password_request` tinyint(1) NOT NULL,
						`active` tinyint(1) NOT NULL,
						`title` varchar(150) NOT NULL,
						`sign_up_stamp` int(11) NOT NULL,
						`last_sign_in_stamp` int(11) NOT NULL,
						`last_board_open` int(11),
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
			
				$user_roles_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "user_roles` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`user_id` int(11) NOT NULL,
						`role_id` int(11) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
			
				$user_roles_entry = "
						INSERT INTO `" . $db_table_prefix . "user_roles` (`id`, `user_id`, `role_id`) VALUES
						(1, 1, 2);
						";
			
				$configuration_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "configuration` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`name` varchar(150) NOT NULL,
						`value` varchar(150) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;
						";
			
				$configuration_entry = "
						INSERT INTO `" . $db_table_prefix . "configuration` (`id`, `name`, `value`) VALUES
						(1, 'website_name', 'awKanban'),
						(2, 'website_url', 'localhost/'),
						(3, 'email', 'noreply@ILoveAwKanban.com'),
						(4, 'activation', 'false'),
						(5, 'resend_activation_threshold', '0'),
						(6, 'template', 'models/site-templates/default.css');
						";
			
				$pages_sql = "CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "pages` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`page` varchar(150) NOT NULL,
						`private` tinyint(1) NOT NULL DEFAULT '0',
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;
						";
			
				$pages_entry = "INSERT INTO `" . $db_table_prefix . "pages` (`id`, `page`, `private`) VALUES
						(1, 'activate-account.php', 0),
						(2, 'forgot-password.php', 0),
						(3, 'index.php', 0),
						(4, 'left-nav.php', 0),
						(5, 'login.php', 0),
						(6, 'register.php', 0),
						(7, 'resend-activation.php', 0);
						";
			
				$kanban_projects_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "projects` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`project_name` varchar(50) NOT NULL,
						`display_name` varchar(50) NOT NULL,
						`active` tinyint(1) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
			
				$kanban_boards_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "boards` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`project_id` int(11) NOT NULL,
						`board_name` varchar(50) NOT NULL,
						`display_name` varchar(50) NOT NULL,
						`active` tinyint(1) NOT NULL,
						`board_css` longtext NOT NULL,
						`board_js` longtext NOT NULL,
						`card_attributes` longtext NOT NULL,
						`card_css` longtext NOT NULL,
						`card_js` longtext NOT NULL,
						`cards`longtext,
						`rules`longtext,
						`board_gens` TEXT,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
			
				$kanban_board_columns_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "board_columns` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`board_id` int(11) NOT NULL,
						`column_name` varchar(50) NOT NULL,
						`display_name` varchar(50) NOT NULL,
						`wip_limit` int(11) NOT NULL,					
						`order_nr` int(11) NOT NULL,					
						`description` TEXT,					
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
					
				$kanban_board_templates_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "board_templates` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`template_name` varchar(50) NOT NULL,
						`display_name` varchar(50) NOT NULL,
						`active` tinyint(1) NOT NULL,
						`board_css` longtext NOT NULL,
						`board_js` longtext NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
			
				$kanban_board_template_columns_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "board_template_columns` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`template_id` int(11) NOT NULL,
						`column_name` varchar(50) NOT NULL,
						`display_name` varchar(50) NOT NULL,
						`wip_limit` int(11) NOT NULL,
						`order_nr` int(11) NOT NULL,					
						`description` TEXT,					
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";	
			
				$kanban_cards_history_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "cards_history` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`board_id` int(11) NOT NULL,
						`cards` longtext NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
			
				$kanban_cards_history_forward_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "cards_history_forward` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`board_id` int(11) NOT NULL,
						`cards` longtext NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
			
				$kanban_card_templates_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "card_templates` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`template_name` varchar(50) NOT NULL,
						`display_name` varchar(50) NOT NULL,
						`active` tinyint(1) NOT NULL,
						`card_attributes` longtext NOT NULL,
						`card_css` longtext NOT NULL,
						`card_js` longtext NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
			
				$kanban_rule_types_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "rule_types` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`rule_name` varchar(50) NOT NULL,
						`display_name` varchar(50) NOT NULL,
						`active` tinyint(1) NOT NULL,
						`action` varchar(32) NOT NULL,
						`rule_js` longtext NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
			
				$user_projects_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "user_projects` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`user_id` int(11) NOT NULL,
						`project_id` int(11) NOT NULL,
						`access_type` char(1) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
			
				$user_boards_sql = "
						CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "user_boards` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`user_id` int(11) NOT NULL,
						`board_id` int(11) NOT NULL,
						`access_type` char(1) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
						";
	
				$kanban_generator_types_sql = "
					CREATE TABLE IF NOT EXISTS `" . $db_table_prefix . "generator_types` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`generator_name` varchar(50) NOT NULL,
					`display_name` varchar(50) NOT NULL,
					`active` tinyint(1) NOT NULL,
					`action` varchar(32) NOT NULL,
					`generator_js` longtext NOT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
					";
			
				$db_issue = $db_issue | install_table($versions_sql, "versions");
				$db_issue = $db_issue | install_basic_settings($versions_entry, "versions");
				$db_issue = $db_issue | install_table($configuration_sql, "configuration");
				$db_issue = $db_issue | install_basic_settings($configuration_entry, "configuration");
				$db_issue = $db_issue | install_table($roles_sql, "roles");
				$db_issue = $db_issue | install_basic_settings($roles_entry, "roles");
				$db_issue = $db_issue | install_table($user_roles_sql, "user_roles");
				$db_issue = $db_issue | install_basic_settings($user_roles_entry, "user_roles");
				$db_issue = $db_issue | install_table($pages_sql, "pages");
				$db_issue = $db_issue | install_basic_settings($pages_entry, "pages");
				$db_issue = $db_issue | install_table($users_sql, "users");
				$db_issue = $db_issue | install_table($kanban_projects_sql, "projects");
				$db_issue = $db_issue | install_table($kanban_boards_sql, "boards");
				$db_issue = $db_issue | install_table($kanban_board_columns_sql, "board_columns"); 
				$db_issue = $db_issue | install_table($kanban_board_templates_sql, "board_templates");
				$db_issue = $db_issue | install_table($kanban_board_template_columns_sql, "board_template_columns");
				$template_path = "templates";
				$board_templates_path = $template_path."/boards";
				$db_issue = $db_issue | import_board_templates($board_templates_path);
				$db_issue = $db_issue | install_table($kanban_cards_history_sql, "cards_history");
				$db_issue = $db_issue | install_table($kanban_cards_history_forward_sql, "cards_history_forward");
				$db_issue = $db_issue | install_table($kanban_card_templates_sql, "card_templates");
				$card_templates_path = $template_path."/cards";
				$db_issue = $db_issue | import_card_templates($card_templates_path);
				$db_issue = $db_issue | install_table($kanban_rule_types_sql, "rule_types");
				$rule_types_path = "rule_types";
				$db_issue = $db_issue | import_rule_types($rule_types_path);
				$generator_types_path = "generator_types";
				$db_issue = $db_issue | import_generator_types($generator_types_path);
				$db_issue = $db_issue | install_table($user_projects_sql, "user_projects");
				$db_issue = $db_issue | install_table($user_boards_sql, "user_boards");
				$db_issue = $db_issue | install_table($kanban_generator_types_sql, "generator_types");
			
				if (!$db_issue) {
?>
					<p><strong><a href='../register.php'><?php echo translate('Database setup complete, you can now register a user.'); ?></a></strong></p>
<?php
					touch('../.installed');
				} else {
?>
					<p><a href='?install=true'><?php echo translate('Try again'); ?></a></p>
<?php
			
				}
			} 
			else {
?>
				<a href='?install=true'><?php echo translate('Install now !'); ?></a>
<?php
			}
?>
		</div>
	</body>
</html>
