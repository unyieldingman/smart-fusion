<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: smart_doer.php
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

if (!checkrights("SSM") || !defined("iAUTH")) { die("Access denied!"); }

require_once INFUSIONS."smart_system/infusion_db.php";
require_once MY_SMART."smart_functions.php";

$smart_error = 1;
$smart_message = "";
if (isset($_GET['addon_id']) && isnum($_GET['addon_id'])) {
	$result = dbquery("SELECT addon_type, addon_path FROM ".DB_SMART_ADDONS." WHERE addon_id='".$_GET['addon_id']."'");
	if (dbrows($result)) {
		$data = dbarray($result);
		$addon_name = preg_replace("#(.+?)/index.php#i", "\\1", $data['addon_path'], 1);
		$path_parts = explode("/", $addon_name);
		$addon_name = end($path_parts);
		if ($data['addon_type'] == "1" && $addon_name == $settings['theme']) {
			$smart_message = $locale['smart_072'];
		} elseif ($data['addon_type'] == "2") {
			$rows = dbcount("(panel_id)", DB_PANELS, "panel_filename='".$addon_name."'");
			if ($rows != 0) {
				$smart_message = $locale['smart_073'];
			}
		} elseif ($data['addon_type'] == "3") {
			$rows = dbcount("(inf_id)", DB_INFUSIONS, "inf_folder='".$addon_name."'");
			if ($rows != 0) {
				$smart_message = $locale['smart_074'];
			}
		} elseif ($data['addon_type'] == "6" && $addon_name == $settings['locale']) {
			$smart_message = $locale['smart_075'];
		}
		if (empty($smart_message)) {
			switch ($data['addon_type']) {
				case "1": $main_folder = THEMES; break;
				case "2": $main_folder = INFUSIONS; break;
				case "3": $main_folder = INFUSIONS; break;
				case "6": $main_folder = LOCALE; break;
				default: $main_folder = ""; break;
			}
			if (isset($main_folder) && !empty($main_folder) && !empty($addon_name) && !empty($data['addon_path']) && file_exists($data['addon_path'])) {
				$unins_files = RemoveObject($main_folder.$addon_name);
				$smart_message = sprintf($locale['smart_076'], $unins_files.($unins_files == "1" ? $locale['smart_077'] : $locale['smart_078']));
			} elseif ($data['addon_type'] == "4") {
				$smart_message = $locale['smart_079'];
			} elseif ($data['addon_type'] == "5") {
				$smart_message = $locale['smart_080'];
			} else {
				$smart_message = $locale['smart_081'];
			}
			$smart_error = 0;
			$result = dbquery("DELETE FROM ".DB_SMART_ADDONS." WHERE addon_id='".$_GET['addon_id']."' LIMIT 1");
		}
	} else {
		$smart_message = $locale['smart_082'];
	}
} else {
	$smart_message = $locale['smart_083'];
}

header("Content-Type: text/html; charset=".$locale['charset']."");
echo $smart_error."||".$smart_message;
?>