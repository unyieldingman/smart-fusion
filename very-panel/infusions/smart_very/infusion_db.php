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
define("VERY_PANEL", INFUSIONS."smart_very/");
define("VERY_IMAGES", VERY_PANEL."images/");

// Databases
define("DB_VERY_API", DB_PREFIX."very_api");

// Language settings
if (file_exists(VERY_PANEL."locale/".$settings['locale'].".php")) {
	include VERY_PANEL."locale/".$settings['locale'].".php";
} else {
	include VERY_PANEL."locale/English.php";
}
?>
