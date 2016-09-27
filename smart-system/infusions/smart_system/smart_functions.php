<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: smart_functions.php
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
if (!defined("IN_FUSION") || !defined("IN_SMART")) { die("Access Denied"); }

function CheckDefaultServerSocket() {
	require_once SMART_CLASSES."ServerSocket.class.php";
	$server = new ServerSocket();
	if ($server->CheckSocket(SMART_SERVER_PROTOCOL, SMART_SERVER_NAME, SMART_SERVER_PORT)) {
		return true;
	} else {
		return false;
	}
}

function ParseXMLResponse() {
	$xml_data = "";
	$xml_document = SMART_BASES."addons.xml";
	if (file_exists($xml_document)) { $xml_data = simplexml_load_file($xml_document); }
	return $xml_data;
}

function GetDataFromStorage($template="<li class='tbl{index} {class}' id='{id}' title='{alt}'>{loader}{title}</li>\n") {
	global $locale;
	
	$xml_data = ParseXMLResponse();
	$stack = array();
	for ($i=1;$i<=6;$i++) {
		$stack[$i][] = "";
		$stack['lines'][$i] = 0;
	}
	$stack['storage'] = "00-00-0000";
	if (!empty($xml_data)) {
		$stack['storage'] = $xml_data['update'];
		$installed = array();
		$result = dbquery("SELECT `addon_id` FROM ".DB_SMART_ADDONS."");
		while ($data = dbarray($result)) {
			$installed[] = $data['addon_id'];
		}
		foreach ($xml_data->addon as $addon) {
			$addon_cat = $addon->category;
			$addon->title = ConvertCharset($addon->title);
			$addon_title = (in_array($addon['id'], $installed) ? "<div class='smart_status' title='".$locale['smart_060']."'>".$locale['smart_061']."</div>".trimlink($addon->title, 15) : trimlink($addon->title, 25));
			$class_index = ($stack['lines']["".$addon_cat.""] % 2 == "0" ? "1" : "2");
			$class_state = (in_array($addon['id'], $installed) ? "unclickable" : "clickable");
			$smart_waiter = "<img src='".SMART_ICONS."horizontal.gif' id='Waiter_".$addon['id']."' class='smart_waiter' alt='' />".(isnum($addon->certificate) && $addon->certificate != "0" ? "<img src='".SMART_ICONS."certified.png' class='smart_valign_fix' style='margin:0 3px;' alt='".$locale['smart_071']."' title='".$locale['smart_071']."' />" : "");
			$values = array('{loader}', '{index}', '{class}', '{id}', '{title}', '{alt}');
			$replacement = array($smart_waiter, $class_index, $class_state, $addon['id'], $addon_title, $addon->title);
			$templ = str_replace($values, $replacement, $template);
			$stack["".$addon_cat.""][] = $templ;
			$stack['lines']["".$addon_cat.""]++;
		}
	}
	return $stack;
}

function PrintDataMerge($data, $addons, $noname) {
	$lines = "";
	if ($addons == 0) {
		$lines .= "<li class='tbl'>".$noname."</li>\n";
	} else {
		foreach ($data as $line) {
			$lines .= $line;
		}
	}
	return $lines;
}

function CountDataMerge($data) {
	$installed = 0;
	for ($i=1;$i<=6;$i++) {
		$installed = $installed + $data['lines'][$i];
	}
	return $installed;
}

function UpdateSmartStore() {
	$normal = false;
	if (CheckDefaultServerSocket()) {
		$addons_list = "http://".SMART_SERVER_NAME."/".SMART_ADDONS_LIST;
		$result = file_get_contents($addons_list);
		$handle = fopen(SMART_BASES."addons.xml", "w");
		fwrite($handle, $result);
		fclose($handle);
		$normal = true;
	}
	return $normal;
}

function ConvertCharset($text) {
	global $locale;
	
	return iconv("utf-8", $locale['charset']."//TRANSLIT//IGNORE", $text);
}

function SetCHMODRights($path, $perm) {
	$error = true;
	if (file_exists($path)) {
		$path = str_replace("/index.php", "", $path);
		$handle = opendir($path);
		while (($file = readdir($handle)) !== false) {
			if (($file !== "..")) {
				if (chmod($path."/".$file, $perm)) { $error = false; }
				if (!is_file($path."/".$file) && ($file !== ".")) { SetCHMODRights($path."/".$file, $perm); }
			}
		}
		closedir($handle);
	}
	return $error;
}

function FixOwnership($addon_id) {
	$error = false;
	$result = dbquery("SELECT addon_path FROM ".DB_SMART_ADDONS." WHERE addon_id='".$addon_id."'");
	if (dbrows($result)) {
		$data = dbarray($result);
		if (SetCHMODRights($data['addon_path'], 0777) === false) {
			$error = true;
		}
	}
	return $error;
}

function RemoveObject($path, $entries=0, $parent=true) {
	$dir = dir($path);
	while ($entry = $dir->read()) {
		if ($entry != "." && $entry != "..") {
			if (is_dir($path."/".$entry)) {
				$entries = RemoveObject($path."/".$entry, $entries, false);
				rmdir($path."/".$entry);
			} else {
				unlink($path."/".$entry);
				$entries++;
			}
		}
	}
	$dir->close();
	if ($parent && is_dir($path)) { rmdir($path); }
	
	return $entries;
}

function InitCode($code) {
	if (function_exists("add_to_footer")) {
		add_to_footer($code);
	} else {
		echo $code;
	}
}
?>