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
define("IN_SMART", true);
define("MY_SMART", INFUSIONS."smart_system/");
define("SMART_CLASSES", MY_SMART."classes/");
define("SMART_STYLES", MY_SMART."sheets/");
define("SMART_ICONS", MY_SMART."graphics/");
define("SMART_BASES", MY_SMART."storage/");
define("SMART_ADDONS", MY_SMART."addons/");
define("SMART_LOCALE", MY_SMART."locale/");
define("SMART_JS", MY_SMART."js/");

define("SMART_SERVER_NAME", "smart-fusion.ru");
define("SMART_SERVER_PORT", "80");
define("SMART_SERVER_PROTOCOL", "tcp");
define("SMART_ADDONS_LIST", $settings['locale']."/v3/smart-addons.xml");
define("SMART_SPLASH_URI", "/splashes/".$settings['locale']."/smart_splash.png");

// Databases
define("DB_SMART_ADDONS", DB_PREFIX."smart_addons");

// Language settings
if (file_exists(SMART_LOCALE.$settings['locale'].".php")) {
	include SMART_LOCALE.$settings['locale'].".php";
} else {
	include SMART_LOCALE."English.php";
}
?>
