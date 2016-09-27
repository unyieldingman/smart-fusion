<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2016 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: user_vk_include.php
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

if ($profile_method == "input") {
	echo "<tr>\n";
	echo "<td class='tbl'>".$locale['uf_vk'].":</td>\n";
	echo "<td class='tbl'><input type='text' name='user_vk' value='".(isset($user_data['user_vk']) ? $user_data['user_vk'] : "")."' maxlength='100' class='textbox' style='width:200px;' /></td>\n";
	echo "</tr>\n";
} elseif ($profile_method == "display") {
	if ($user_data['user_vk']) {
		echo "<tr>\n";
		echo "<td width='1%' class='tbl1' style='white-space:nowrap'>".$locale['uf_vk']."</td>\n";
		echo "<td align='right' class='tbl1'>".$user_data['user_vk']."</td>\n";
		echo "</tr>\n";
	}
} elseif ($profile_method == "validate_insert") {
	$db_fields .= ", user_vk";
	$db_values .= ", '".(isset($_POST['user_vk']) ? stripinput(trim($_POST['user_vk'])) : "")."'";
} elseif ($profile_method == "validate_update") {
	$db_values .= ", user_vk='".(isset($_POST['user_vk']) ? stripinput(trim($_POST['user_vk'])) : "")."'";
}
?>
