<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_admin_updater.php
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
require_once "../../maincore.php";
require_once INFUSIONS."mega_menu/infusion_db.php";

if (!checkrights("MM") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) { redirect("../../index.php"); }

if ((isset($_GET['listItem']) && is_array($_GET['listItem'])) || (isset($_GET['listSubItem']) && is_array($_GET['listSubItem']))) {
	$list_item = (isset($_GET['listItem']) ? $_GET['listItem'] : $_GET['listSubItem']);
	foreach ($list_item as $position => $item) {
		if (isnum($position) && isnum($item)) {
			dbquery("UPDATE ".DB_MEGA_MENU." SET link_order='".($position+1)."' WHERE link_id='".$item."'");
		}
	}
	header("Content-Type: text/html; charset=".$locale['charset']."\n");
	echo "<div id='close-message'><div class='admin-message'>".$locale['mm_021']."</div></div>";
}
?>