<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: smart_installer.php
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
require_once SMART_CLASSES."PclZip.class.php";

$smart_error = "";
$install_error = true;
if (isset($_GET['addon_id']) && isnum($_GET['addon_id'])) {
	if (CheckDefaultServerSocket()) {
		$xml = ParseXMLResponse();
		if ($xml != "") {
			foreach ($xml->addon as $addon) {
				if ($addon['id'] == $_GET['addon_id']) {
					$link = $addon->autolink;
					$type = $addon->category;
					break;
				}
			}
			$parts = explode(".", $link);
			$extension = end($parts);
			$filename = md5(time()).".".$extension;
			if (copy($link, SMART_ADDONS.$filename)) {
				$full_filepath = SMART_ADDONS.$filename;
				$archive = new PclZip($full_filepath);
				switch ($type) {
					case "1": $extract = THEMES; break;
					case "2": $extract = INFUSIONS; break;
					case "3": $extract = INFUSIONS; break;
					case "4": $extract = BASEDIR; break;
					case "5": $extract = BASEDIR; break;
					case "6": $extract = LOCALE; break;
				}
				if ($type != "4" && $type != "5") { $folders = makefilelist($extract, ".|..|index.php", false, "folders"); }
				if ($archive->extract($extract) != 0) {
					$smart_error = $locale['smart_044'];
					$install_error = false;
					if ($type == "4" || $type == "5") {
						$folder_name = "";
					} else {
						$unpacks = makefilelist($extract, ".|..|index.php", false, "folders");
						foreach ($unpacks as $pack) {
							if (!in_array($pack, $folders)) {
								$folder_name = $pack;
								break;
							}
						}
					}
					$result = dbquery("INSERT INTO ".DB_SMART_ADDONS." (`addon_id`, `addon_type`, `addon_path`) VALUES
						('".$_GET['addon_id']."', '".$type."', '".(!empty($folder_name) ? $extract.$folder_name."/index.php" : "")."')
					");
					$smart_error = $locale['smart_044'];
					unlink(SMART_ADDONS.$filename);
				} else {
					$smart_error = $locale['smart_045'];
					unlink(SMART_ADDONS.$filename);
				}
			} else {
				$smart_error = $locale['smart_046'];
			}
		} else {
			$smart_error = $locale['smart_047'];
		}
	} else {
		$smart_error = $locale['smart_048'];
	}
}

header("Content-Type: text/html; charset=".$locale['charset']."");
echo ($install_error === true ? "1" : "0")."||".$smart_error;
?>