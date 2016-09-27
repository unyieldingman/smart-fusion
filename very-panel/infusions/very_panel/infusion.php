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

require_once INFUSIONS."very_panel/infusion_db.php";

$inf_title = $locale['very_001'];
$inf_description = $locale['very_002'];
$inf_version = "1.0";
$inf_developer = "FDTD Designer (FILON)";
$inf_email = "supported@yandex.com";
$inf_weburl = "http://smart-fusion.ru/";

$inf_folder = "very_panel";

$inf_adminpanel[1] = array(
	"title" => $locale['very_003'],
	"image" => "very_panel.gif",
	"panel" => "very_manager.php",
	"rights" => "VAP"
);

$inf_newtable[1] = DB_VERY_API." (
  `very_panel_name` varchar(240) NOT NULL default '',
  `very_api_type` int(1) NOT NULL default '0',
  `very_api_key` varchar(10) NOT NULL default '',
  `very_profile` int(1) NOT NULL default '0',
  `very_mode` int(1) NOT NULL default '0',
  `very_siteurl` varchar(255) NOT NULL default '0',
  `very_synchronize` int(1) NOT NULL default '0',
  `very_secureip` int(1) NOT NULL default '0',
  `very_prune` int(1) NOT NULL default '0',
  `very_cache` int(1) NOT NULL default '0',
  `very_debug` int(1) NOT NULL default '0'
) ENGINE=MyISAM;";

$inf_insertdbrow[1] = DB_VERY_API." (`very_panel_name`, `very_api_type`, `very_api_key`, `very_profile`, `very_mode`, `very_siteurl`, `very_synchronize`, `very_secureip`, `very_prune`, `very_cache`, `very_debug`) VALUES ('".$locale['very_001']."', '0', '', '0', '0', '', '0', '1', '0', '0', '0')";

$inf_droptable[1] = DB_VERY_API;
?>