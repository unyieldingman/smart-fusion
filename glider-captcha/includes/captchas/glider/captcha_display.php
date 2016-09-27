<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: captcha_display.php
| Author: FDTD Designer (FILON)
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

require_once INCLUDES."captchas/glider/captcha_definitions.php";
if (file_exists(GLIDER_LOCALE.$settings['locale'].".php")) {
	include GLIDER_LOCALE.$settings['locale'].".php";
} else {
	include GLIDER_LOCALE."English.php";
}
require_once GLIDER_CLASSES."Glider.class.php";

// Hide extra input
$_CAPTCHA_HIDE_INPUT = true;

// Render Glider
$captcha = new Glider();
$captcha->render();
?>