<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: smart_globe_infusion.php
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
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."smart_globe_panel/infusion_db.php";
require_once SMART_GLOBE_CLASSES."SmartGlobe.class.php";

$globe = new SmartGlobe();
$globe->processingRequest();
add_to_title($locale['globe_009'].$locale['globe_010']);
opentable($locale['globe_011']);
echo $globe->renderMeta();
closetable(); 
if (isset($_GET['status']) && $_GET['status'] == "sa") {
	opentable($locale['globe_013']);
	echo $globe->renderMessage();
	closetable();
} else {
	opentable($locale['globe_016']);
	echo $globe->renderPage();
	closetable();
}

require_once THEMES."templates/footer.php";
?>