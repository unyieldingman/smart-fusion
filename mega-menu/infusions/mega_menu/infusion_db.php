<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2014 Nick Jones
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

// Database constants
if (!defined("DB_MEGA_MENU")) { define("DB_MEGA_MENU", DB_PREFIX."mega_menu"); }

// Path constants
if (!defined("MEGA_BASEDIR")) { define("MEGA_BASEDIR", INFUSIONS."mega_menu/"); }
if (!defined("MEGA_CLASSES")) { define("MEGA_CLASSES", MEGA_BASEDIR."classes/"); }
if (!defined("MEGA_IMAGES")) { define("MEGA_IMAGES", MEGA_BASEDIR."images/"); }
if (!defined("MEGA_STYLES")) { define("MEGA_STYLES", MEGA_BASEDIR."styles/"); }
if (!defined("MEGA_JSCRIPTS")) { define("MEGA_JSCRIPTS", MEGA_BASEDIR."jscripts/"); }
if (!defined("MEGA_LOCALE")) { define("MEGA_LOCALE", MEGA_BASEDIR."locale/"); }

// Language settings
if (file_exists(MEGA_LOCALE.$settings['locale'].".php")) {
	include MEGA_LOCALE.$settings['locale'].".php";
} else {
	include MEGA_LOCALE."English.php";
}
?>
