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

require_once INFUSIONS."smart_very/infusion_db.php";

$inf_title = $locale['very_003'];
$inf_description = $locale['very_002'];
$inf_version = "2.0";
$inf_developer = "FDTD Designer (FILON)";
$inf_email = "supported@yandex.com";
$inf_weburl = "http://smart-fusion.ru/";

$inf_folder = "smart_very";

$inf_adminpanel[1] = array(
	"title" => $locale['very_003'],
	"image" => "smart_very.gif",
	"panel" => "smart_admin.php",
	"rights" => "SVP"
);

$inf_newtable[1] = DB_VERY_API." (
  `very_panel_name` varchar(240) NOT NULL default '',
  `very_api_type` tinyint(1) NOT NULL default '0',
  `very_api_key` varchar(10) NOT NULL default '',
  `very_profile` tinyint(1) NOT NULL default '0',
  `very_mode` tinyint(1) NOT NULL default '0',
  `very_siteurl` varchar(255) NOT NULL default '0',
  `very_synchronize` tinyint(1) NOT NULL default '0',
  `very_secureip` tinyint(1) NOT NULL default '0',
  `very_prune` tinyint(1) NOT NULL default '0',
  `very_cache` tinyint(1) NOT NULL default '0',
  `very_debug` tinyint(1) NOT NULL default '0',
  `very_theme` varchar(20) NOT NULL default ''
) ENGINE=MyISAM;";

$inf_insertdbrow[1] = DB_VERY_API." (`very_panel_name`, `very_api_type`, `very_api_key`, `very_profile`, `very_mode`, `very_siteurl`, `very_synchronize`, `very_secureip`, `very_prune`, `very_cache`, `very_debug`, `very_theme`) VALUES ('".$locale['very_001']."', '0', '', '0', '0', '', '0', '1', '0', '0', '0', 'mono')";

$inf_insertdbrow[2] = DB_PANELS." (`panel_name`, `panel_filename`, `panel_content`, `panel_side`, `panel_order`, `panel_type`, `panel_access`, `panel_display`, `panel_status`, `panel_url_list`, `panel_restriction`) VALUES ('".$locale['very_001']."', '".$inf_folder."', '', '4', '0', 'file', '0', '0', '0', '', '0')";

$inf_droptable[1] = DB_VERY_API;

$inf_deldbrow[1] = DB_PANELS." WHERE panel_filename='".$inf_folder."'";
?>