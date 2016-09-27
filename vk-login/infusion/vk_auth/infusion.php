<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2016 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
| Author: FILON (FDTD Designer)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION") || !checkrights("I")) { header("Location:index.php"); exit; }

require_once INFUSIONS."vk_auth/infusion_db.php";

$inf_title = $locale['vk_auth_000'];
$inf_description = $locale['vk_auth_001'];
$inf_version = "1.0";
$inf_developer = "FDTD Designer (FILON)";
$inf_email = "supported@yandex.com";
$inf_weburl = "http://smart-fusion.ru/";

$inf_folder = INFUSION_FOLDER;

$inf_adminpanel[1] = array(
	"title" => $locale['vk_auth_002'],
	"image" => "vk_auth.png",
	"panel" => "infusion_admin.php",
	"rights" => "VK"
);

$inf_newtable[1] = DB_TOKENS." (
  `token_id` mediumint(8) unsigned NOT NULL auto_increment,
  `token_token` varchar(255) NOT NULL DEFAULT '',
  `token_expires` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`token_id`)
) ENGINE=MyISAM";

$inf_insertdbrow[1] = DB_SETTINGS_INF." (`settings_name`, `settings_value`, `settings_inf`) VALUES
('setting_client_id', '', '".$inf_folder."'),
('setting_secret_key', '', '".$inf_folder."')";

$inf_droptable[1] = DB_TOKENS;

$inf_deldbrow[1] = DB_SETTINGS_INF." WHERE settings_inf = '".$inf_folder."'";

if (isset($_POST['infuse']) && isset($_POST['infusion'])) {
	$result = dbquery(
		"ALTER TABLE ".DB_USERS."
		ADD `user_uid` int(10) unsigned NOT NULL DEFAULT '0' AFTER `user_id`,
		ADD `user_token_id` mediumint(8) unsigned NOT NULL DEFAULT '0' AFTER `user_uid`,
		ADD CONSTRAINT fk_token_id FOREIGN KEY (user_token_id) REFERENCES ".DB_TOKENS."(token_id) ON DELETE CASCADE"
	);
} elseif (isset($_GET['defuse']) && isnum($_GET['defuse'])) {
	$result = dbquery(
		"ALTER TABLE ".DB_USERS."
		DROP `user_uid`,
		DROP `user_token_id`,
		DROP FOREIGN KEY `fk_token_id`"
	);
}

// Adding support for oldest PHP-Fusion
$result = dbquery("SHOW TABLES LIKE '".DB_SETTINGS_INF."'");
if (!dbrows($result)) {
	$inf_newtable[2] = DB_SETTINGS_INF." (
	  `settings_name` varchar(200) NOT NULL DEFAULT '',
	  `settings_value` text NOT NULL,
	  `settings_inf` varchar(200) NOT NULL DEFAULT ''
	) ENGINE=MyISAM";
} 
?>
