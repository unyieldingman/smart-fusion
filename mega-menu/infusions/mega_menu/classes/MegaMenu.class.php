<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: MegaMenu.class.php
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

require_once INFUSIONS."mega_menu/infusion_db.php";

class MegaMenu {
	private function __construct() {}
	
	public static function getParentLinks() {
		$options = array();
		$result = dbquery("SELECT link_id, link_name FROM ".DB_MEGA_MENU." WHERE link_parent_id='0' AND link_name!='---' ORDER BY link_order ASC");
		while ($data = dbarray($result)) {
			array_push($options, array($data['link_id'], $data['link_name']));
		}
		return $options;
	}
	
	public static function buildVisualColumns($size) {
		$stack = "";
		for ($i = 0; $i < $size; $i++) {
			$stack .= "<img src='".MEGA_IMAGES."column.png' alt='' />";
		}
		return $stack;
	}
	
	public static function buildMegaMenu() {
		// Extras
		add_to_head("<link rel='stylesheet' type='text/css' href='".MEGA_STYLES."styles.css' />");
		add_to_footer("<script type='text/javascript' src='".MEGA_JSCRIPTS."jquery.megamenu.min.js'></script>");
		
		$res = "";
		$result = dbquery(
			"SELECT link_id, link_name, link_url, link_window, link_columns FROM ".DB_MEGA_MENU."
			WHERE ".groupaccess('link_visibility')." AND link_parent_id='0'
			ORDER BY link_order ASC"
		);
		if (dbrows($result)) {
			$res .= "<ul id='mega_menu' class='mega-menu'>\n";
			while ($data = dbarray($result)) {
				$link_target = ($data['link_window'] == "1" ? " target='_blank'" : "");
				$li_class = (START_PAGE == $data['link_url'] ? "active" : "");
				if ($data['link_name'] == "---" && $data['link_url'] == "---") {
					$res .= "<li class='delimiter'><span></span>";
				} elseif ($data['link_url'] == "---") {
					$res .= "<li class='label'><span class='text'>".$data['link_name']."</span>";
				} elseif (preg_match("!^(ht|f)tp(s)?://!i", $data['link_url'])) {
					$res .= "<li".($li_class ? " class='".$li_class."'" : "")."><a href='".$data['link_url']."'".$link_target.">\n";
					$res .= "<span>".parseubb($data['link_name'], "b|i|u|color|img")."</span></a>";
				} else {
					$res .= "<li".($li_class ? " class='".$li_class."'" : "")."><a href='".BASEDIR.$data['link_url']."'".$link_target.">\n";
					$res .= "<span>".parseubb($data['link_name'], "b|i|u|color|img")."</span></a>";
				}
				$result2 = dbquery(
					"SELECT link_name, link_url, link_window, link_columns FROM ".DB_MEGA_MENU."
					WHERE ".groupaccess('link_visibility')." AND link_parent_id='".$data['link_id']."'
					ORDER BY link_order ASC"
				);
				$rows = dbrows($result2);
				if ($rows) {
					$i = 1; $links_per_column = ceil($rows / $data['link_columns']);
					$res .= "<div class='drop-down'>\n<ul>\n";
					while ($data2 = dbarray($result2)) {
						$link_target = ($data2['link_window'] == "1" ? " target='_blank'" : "");
						$li_class = (START_PAGE == $data2['link_url'] ? "active" : "");
						if ($data2['link_name'] == "---" && $data2['link_url'] == "---") {
							$res .= "<li class='delimiter'><hr /></li>";
						} elseif ($data2['link_url'] == "---") {
							$res .= "<li class='label'><span>".$data2['link_name']."</span></li>\n";
						} elseif (preg_match("!^(ht|f)tp(s)?://!i", $data2['link_url'])) {
							$res .= "<li".($li_class ? " class='".$li_class."'" : "")."><a href='".$data2['link_url']."'".$link_target.">\n";
							$res .= "<span>".parseubb($data2['link_name'], "b|i|u|color|img")."</span></a></li>";
						} else {
							$res .= "<li".($li_class ? " class='".$li_class."'" : "")."><a href='".BASEDIR.$data2['link_url']."'".$link_target.">\n";
							$res .= "<span>".parseubb($data2['link_name'], "b|i|u|color|img")."</span></a></li>";
						}
						if ($i % $links_per_column == 0 && $i != $rows) { $res .= "</ul>\n<ul>\n"; }
						$i++;
					}
					$res .= "</ul>\n</div>\n";
				}
				$res .= "</li>\n";
			}
			$res .= "</ul>\n";
			return $res;
		}
	}
}
?>