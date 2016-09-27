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

require_once INFUSIONS."smart_globe_panel/infusion_db.php";

$inf_title = $locale['globe_000'];
$inf_description = $locale['globe_001'];
$inf_version = "1.0";
$inf_developer = "FDTD Designer (FILON)";
$inf_email = "supported@yandex.com";
$inf_weburl = "http://smart-fusion.ru/";

$inf_folder = "smart_globe_panel";

$inf_adminpanel[1] = array(
	"title" => $locale['globe_000'],
	"image" => INFUSIONS."smart_globe_panel/images/globe.gif",
	"panel" => "smart_globe_admin.php",
	"rights" => "SGLB"
);

$inf_newtable[1] = DB_SMART_GLOBE." (
  `globe_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `globe_site_url` varchar(255) NOT NULL DEFAULT '',
  `globe_title` varchar(255) NOT NULL DEFAULT '',
  `globe_location` varchar(255) NOT NULL DEFAULT '',
  `globe_language` varchar(255) NOT NULL DEFAULT '',
  `globe_info` text NOT NULL,
  `globe_population` varchar(20) NOT NULL DEFAULT '',
  `globe_born` int(4) NOT NULL DEFAULT '0',
  `globe_icon` varchar(240) NOT NULL DEFAULT '',
  `globe_draft` int(1) NOT NULL DEFAULT '0',
  `globe_backlink` int(1) NOT NULL DEFAULT '0',
  `globe_last_check` int(15) NOT NULL DEFAULT '0',
  `globe_privilegies` int(1) NOT NULL DEFAULT '0',
  `globe_orientation` int(1) NOT NULL DEFAULT '0',
  `globe_xaxis` int(4) NOT NULL DEFAULT '0',
  `globe_yaxis` int(4) NOT NULL DEFAULT '0',
  `globe_datestamp` int(15) NOT NULL DEFAULT '0',
  PRIMARY KEY (`globe_id`)
) ENGINE=MyISAM";

$inf_droptable[1] = DB_SMART_GLOBE;
?>