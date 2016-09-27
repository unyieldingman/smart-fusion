<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2014 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_db.php
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

// Database tables
if (!defined("DB_PIPE_TAGS")) { define("DB_PIPE_TAGS", DB_PREFIX."pipe_tags"); }
if (!defined("DB_PIPE_FEEDS")) { define("DB_PIPE_FEEDS", DB_PREFIX."pipe_feeds"); }

// Folders
if (!defined("INFUSION_HOME")) { define("INFUSION_HOME", INFUSIONS."news_feed_pipe/"); }

// Language settings
if (file_exists(INFUSION_HOME."locale/".$settings['locale'].".php")) {
	include INFUSION_HOME."locale/".$settings['locale'].".php";
} else {
	include INFUSION_HOME."locale/English.php";
}
?>