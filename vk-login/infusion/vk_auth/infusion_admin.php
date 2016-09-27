<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2016 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_admin.php
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

if (!checkrights("VK") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) { redirect("../../index.php"); }

require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."vk_auth/infusion_db.php";

if (isset($_GET['result']) && isnum($_GET['result']) && !isset($message)) {
	if ($_GET['result'] == 0) {
		$message = $locale['vk_auth_009'];
	} elseif ($_GET['result'] == 1) {
		$message = $locale['vk_auth_010'];
	}
	if (isset($message)) {
		echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n";
	}
}

if (isset($_POST['savesettings'])) {
	$settings_inf = array(
		"setting_client_id"		=>	(isset($_POST['setting_client_id']) ? $_POST['setting_client_id'] : ""),
		"setting_secret_key"	=>	(isset($_POST['setting_secret_key']) ? $_POST['setting_secret_key'] : "")
	);
	$result = $infusion->setSettingsArray($settings_inf);
	redirect(FUSION_SELF.$aidlink."&result=".$result);
}

$settings2 = $infusion->getSettingsArray();
$backlink = sprintf("%sinfusions/%s/infusion_verify.php", $settings['siteurl'], INFUSION_FOLDER);

opentable($locale['vk_auth_003']);
echo "<form name='settingsform' method='post' action='".FUSION_SELF.$aidlink."'>\n";
echo "<table cellpadding='0' cellspacing='0' width='500' class='center'>\n<tr>\n";
echo "<td width='50%' class='tbl'>".$locale['vk_auth_004'].":</td>\n";
echo "<td width='50%' class='tbl'><input type='text' name='setting_client_id' value='".$settings2['setting_client_id']."' class='textbox' style='width:100px;' /><br /><a href='https://vk.com/editapp?act=create' target='_blank'>".$locale['vk_auth_005']."</a></td>\n";
echo "</tr>\n<tr>\n";
echo "<td width='50%' class='tbl'>".$locale['vk_auth_006'].":</td>\n";
echo "<td width='50%' class='tbl'><input type='text' name='setting_secret_key' value='".$settings2['setting_secret_key']."' class='textbox' style='width:200px;' /></td>\n";
echo "</tr>\n<tr>\n";
echo "<td colspan='2' class='tbl'><br />\n";
echo "<span class='alt'>".$locale['vk_auth_007'].":<br /><a href='javascript:alert(\"".$backlink."\");'>".$backlink."</a></span>\n";
echo "<td>\n";
echo "</tr>\n<tr>\n";
echo "<td align='center' colspan='2' class='tbl'><br />\n";
echo "<input type='submit' name='savesettings' value='".$locale['vk_auth_008']."' class='button' />\n</td>\n";
echo "</tr>\n</table>\n</form>\n";
closetable();

require_once THEMES."templates/footer.php";
?>