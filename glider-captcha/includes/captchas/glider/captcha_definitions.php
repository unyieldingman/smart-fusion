<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: captcha_definitions.php
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

// Path definitions for Glider
define("GLIDER_BASEDIR", INCLUDES."captchas/glider/");
define("GLIDER_CLASSES", GLIDER_BASEDIR."classes/");
define("GLIDER_JSCRIPTS", GLIDER_BASEDIR."jscripts/");
define("GLIDER_SHEETS", GLIDER_BASEDIR."sheets/");
define("GLIDER_LOCALE", GLIDER_BASEDIR."locale/");
?>