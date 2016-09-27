<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_db.php
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

// Constants
define("SMART_GLOBE_BASE", INFUSIONS."smart_globe_panel/");
define("SMART_GLOBE_IMAGES", SMART_GLOBE_BASE."images/");
define("SMART_GLOBE_CLASSES", SMART_GLOBE_BASE."classes/");
define("SMART_GLOBE_SCRIPTS", SMART_GLOBE_BASE."scripts/");
define("SMART_GLOBE_STYLES", SMART_GLOBE_BASE."styles/");

// Databases
define("DB_SMART_GLOBE", DB_PREFIX."smart_globe");

// Language settings
if (file_exists(SMART_GLOBE_BASE."locale/".$settings['locale'].".php")) {
	include_once SMART_GLOBE_BASE."locale/".$settings['locale'].".php";
} else {
	include_once SMART_GLOBE_BASE."locale/English.php";
}
?>
