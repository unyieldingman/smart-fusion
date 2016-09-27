<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: smart_response.php
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

$smart_error = false;
if (isset($_GET['addon_id']) && isnum($_GET['addon_id'])) {
	$xml = ParseXMLResponse();
	if ($xml == "") {
		$smart_error = $locale['smart_042'];
	}
} else {
	$smart_error = $locale['smart_043'];
}

header("Content-Type: text/html; charset=".$locale['charset']."");
$request = array();
$request[] = ($smart_error ? "1" : "0");
$request[] = ($smart_error != "" ? $smart_error : "");
if (!$smart_error) {
	foreach ($xml->addon as $addon) {
		if ($addon['id'] == $_GET['addon_id']) {
			$request[] = ConvertCharset($addon->title);
			$request[] = ConvertCharset(nl2br($addon->description));
			$request[] = $addon->autolink;
			$request[] = $addon->category;
			$request[] = ConvertCharset($addon->author);
			$request[] = $addon->pubdate;
			$request[] = $addon->compatible;
			$request[] = parsebytesize($addon->size);
			$request[] = ConvertCharset($addon->type);
			$request[] = $addon->preview;
			$request[] = $addon['id'];
			$request[] = $addon->panorama;
			$request[] = $addon->certificate;
		}
	}
}
foreach ($request as $data_line) {
	echo $data_line."||";
}
?>