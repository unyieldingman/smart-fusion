<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2014 Nick Jones
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

require_once INFUSIONS."mega_menu/infusion_db.php";

$inf_title = $locale['mm_000'];
$inf_description = $locale['mm_001'];
$inf_version = "1.0";
$inf_developer = "FDTD Designer (FILON)";
$inf_email = "supported@yandex.com";
$inf_weburl = "http://smart-fusion.ru/";

$inf_folder = "mega_menu";

$inf_adminpanel[1] = array(
	"title" => $locale['mm_000'],
	"image" => "mega_menu.gif",
	"panel" => "infusion_admin.php",
	"rights" => "MM"
);

$inf_newtable[1] = DB_MEGA_MENU." (
  `link_id` mediumint(8) NOT NULL auto_increment,
  `link_name` varchar(100) NOT NULL default '',
  `link_url` varchar(255) NOT NULL default '',
  `link_parent_id` mediumint(8) NOT NULL default '0',
  `link_visibility` tinyint(3) NOT NULL default '0',
  `link_window` tinyint(1) NOT NULL default '0',
  `link_order` smallint(2) NOT NULL default '0',
  `link_columns` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`link_id`),
  KEY (`link_parent_id`)
) ENGINE=MyISAM;";

$inf_droptable[1] = DB_MEGA_MENU;
?>