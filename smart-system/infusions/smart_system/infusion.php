<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
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

require_once INFUSIONS."smart_system/infusion_db.php";

$inf_title = $locale['smart_001'];
$inf_description = $locale['smart_002'];
$inf_version = "3.0";
$inf_developer = "FDTD Designer (FILON)";
$inf_email = "supported@yandex.com";
$inf_weburl = "http://smart-fusion.ru/";

$inf_folder = "smart_system";

$inf_adminpanel[1] = array(
	"title" => $locale['smart_003'],
	"image" => "smart_system.gif",
	"panel" => "smart_manager.php",
	"rights" => "SSM"
);

$inf_newtable[1] = DB_SMART_ADDONS." (
  `addon_id` mediumint(8) NOT NULL default '0',
  `addon_type` int(2) NOT NULL default '0',
  `addon_path` varchar(250) NOT NULL default ''
) ENGINE=MyISAM;";

$inf_droptable[1] = DB_SMART_ADDONS;
?>