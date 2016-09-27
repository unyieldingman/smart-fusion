<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: very_panel.php
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

require_once INFUSIONS."very_panel/infusion_db.php";

$result = dbquery("SHOW TABLES LIKE '".DB_VERY_API."'");
if (dbrows($result)) {
	add_to_head("<link rel='stylesheet' type='text/css' href='http://smart-fusion.ru/api/very/exterior.css' media='all' />");
	add_to_head("<script type='text/javascript' src='http://smart-fusion.ru/api/very/jquery.very.api.js'></script>");
	$data = dbarray(dbquery("SELECT very_panel_name, very_api_type, very_api_key, very_profile, very_mode, very_siteurl, very_synchronize, very_secureip, very_prune, very_cache, very_debug FROM ".DB_VERY_API.""));
	$parameters = "";
	if ($data['very_profile'] == "1") {
		$parameters .= ", mode: '".($data['very_mode'] == "0" ? "horizontal" : "vertical")."'";
		$parameters .= (!empty($data['very_siteurl']) ? ", siteUrl: '".$data['very_siteurl']."'" : "");
		$parameters .= ", sync: ".($data['very_synchronize'] == "0" ? "false" : "true")."";
		$parameters .= ", secureIp: ".($data['very_secureip'] == "0" ? "false" : "true")."";
		$parameters .= ", prune: ".($data['very_prune'] == "0" ? "false" : "true")."";
		$parameters .= ", cache: ".($data['very_cache'] == "0" ? "false" : "true")."";
		$parameters .= ", debug: ".($data['very_debug'] == "0" ? "false" : "true")."";
	}
	openside($data['very_panel_name']);
	echo "<center id='very_button' style='margin:10px auto;'><img src='".VERY_IMAGES."very_disabled.png' style='border:none;' alt='' /></center>\n";
	echo "<script type='text/javascript'>Very.Run('very_button', '".($data['very_api_type'] == "1" ? $data['very_api_key'] : "EB7CD6EA17")."', {type: 'very'".$parameters."});</script>\n";
	closeside();
} else {
	openside($locale['very_001']);
	$title = (iADMIN ? $locale['very_034'] : $locale['very_035']);
	echo "<div style='text-align:center;margin:10px auto;'><img src='".VERY_IMAGES."very_disabled.png' style='border:none;' alt='".$title."' title='".$title."' /></div>\n";
	closeside();
}
?>