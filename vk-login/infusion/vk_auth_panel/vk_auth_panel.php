<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2016 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: vk_auth_panel.php
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
if (!defined("IN_FUSION")) { die("Access Denied"); }

// Definititons
if (!defined("VK_AUTH_XDATA")) {
	define("VK_AUTH_XDATA", INFUSIONS."vk_auth_panel/xdata/");
}

// Initialize panel styles
add_to_head("<link rel='stylesheet' type='text/css' href='".VK_AUTH_XDATA."_css/basis.css' />");

try {
	
	// Checking if registration is enabled
	if (!$settings['enable_registration']) {
		throw new Exception($locale['vk_auth_015']);
	}
	
	// Checking infusion installed
	$rows = dbcount("(`inf_id`)", DB_INFUSIONS, "inf_folder = 'vk_auth'");
	if (!$rows) {
		throw new Exception($locale['vk_auth_016']);
	}
	
	// Try to include settings file
	$infusion_db_path = INFUSIONS."vk_auth/infusion_db.php";
	if (!file_exists($infusion_db_path)) {
		throw new Exception($locale['vk_auth_017']);
	}
	require_once $infusion_db_path;
	
	// Including API client
	require_once INFUSION_CLASSES."VkAPI.class.php";
	
	// Initialize API
	$vk = new VkAPI();
	
	// Output panel
	if (iMEMBER && isset($userdata['user_uid']) && $userdata['user_uid'] != 0) {
		echo "<div class='vkontakte-panel--profile'>\n";
		echo "<img src='".(empty($userdata['user_avatar']) ? VK_AUTH_XDATA."_graphics/camera_100.png" : IMAGES."avatars/".$userdata['user_avatar'])."' class='vkontakte-panel-photo' alt='' />\n";
		echo "<div class='vkontakte-panel-link'>\n".$locale['vk_auth_011'].",<br />";
		echo "<a href='http://vk.com/".$userdata['user_name']."' target='_blank'>".$userdata['user_name']."</a>\n";
		echo "</div>\n<div class='vkontakte-panel-logout'>\n";
		echo "<a href='".BASEDIR."setuser.php?logout=yes'>".$locale['vk_auth_012']."</a>\n";
		echo "</div>\n</div>\n";
	} else {
		$auth_link = $vk->createLink();
		echo "<div class='vkontakte-panel--auth'>\n";
		echo "<img src='".VK_AUTH_XDATA."_graphics/vk_logo_medium.png' class='vkontakte-panel-icon' alt='' />\n";
		echo "<div class='vkontakte-panel-link'>\n";
		echo "<a href='".$auth_link."'>".$locale['vk_auth_013']."</a>\n";
		echo "</div>\n</div>\n";
	}
}
catch (Exception $e) {
	echo "<div class='vkontakte-panel--error'>\n";
	echo "<img src='".VK_AUTH_XDATA."_graphics/vk_logo_small.png' class='vkontakte-panel-icon' alt='' />\n";
	echo "<div class='vkontakte-panel-message'>\n";
	echo sprintf($locale['vk_auth_014'], $e->getMessage());
	echo "</div>\n";
}
?>
