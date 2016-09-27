<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2014 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
| Author: FDTD Designer
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

require_once INFUSIONS."news_feed_pipe/infusion_db.php";

// Infusion general information
$inf_title = $locale['pipe_000'];
$inf_description = $locale['pipe_001'];
$inf_version = "1.1";
$inf_developer = "FDTD Designer";
$inf_email = "supported@yandex.com";
$inf_weburl = "http://smart-fusion.ru";

$inf_folder = "news_feed_pipe";

$inf_adminpanel[1] = array(
	"title" => $locale['pipe_000'],
	"image" => "news_pipe.gif",
	"panel" => "infusion_admin.php",
	"rights" => "NFP"
);

$inf_newtable[1] = DB_PIPE_TAGS." (
	`tag_id` mediumint(8) unsigned NOT NULL auto_increment,
	`tag_category_id` mediumint(8) unsigned NOT NULL default '0',
	`tag_name` varchar(255) NOT NULL default '',
	PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM;";

$inf_newtable[2] = DB_PIPE_FEEDS." (
	`feed_id` mediumint(8) unsigned NOT NULL auto_increment,
	`feed_title` varchar(255) NOT NULL default '',
	`feed_url` varchar(255) NOT NULL default '',
	PRIMARY KEY (`feed_id`)
) ENGINE=MyISAM;";

$inf_droptable[1] = DB_PIPE_TAGS;
$inf_droptable[2] = DB_PIPE_FEEDS;

// Core tables modification
if (isset($_POST['infuse']) && isset($_POST['infusion'])) {
	$result = dbquery("ALTER TABLE ".DB_NEWS." ADD `news_source` varchar(255) NULL default '' AFTER `news_allow_ratings`");
} elseif (isset($_GET['defuse'])) {
	$result = dbquery("ALTER TABLE ".DB_NEWS." DROP `news_source`");
}
?>