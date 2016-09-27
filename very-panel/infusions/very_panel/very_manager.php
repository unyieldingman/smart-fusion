<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: very_manager.php
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

if (!checkrights("VAP") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) { redirect(BASEDIR); }

require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."very_panel/infusion_db.php";

if (isset($_GET['status'])) {
	if ($_GET['status'] == "su") {
		$message = $locale['very_004'];
	}
	if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}

if (isset($_POST['savesettings'])) {
	$very_panel_name = stripinput($_POST['very_panel_name']);
	$api_key_type = (isset($_POST['very_api_type']) && isnum($_POST['very_api_type']) ? $_POST['very_api_type'] : "0");
	$very_api_key = (strlen($very_api_key) == 10 ? stripinput($_POST['very_api_key']) : "");
	if (empty($very_api_key)) { $api_key_type = "0"; }
	$very_profile = (isset($_POST['very_profile']) && isnum($_POST['very_profile']) ? $_POST['very_profile'] : "0");
	$very_mode = (isset($_POST['very_mode']) && isnum($_POST['very_mode']) ? $_POST['very_mode'] : "0");
	$very_siteurl = (preg_match("#^(http(s?):\/\/([a-zA-Z0-9-])+(\.([a-zA-Z0-9-])+)*(\.([a-zA-Z0-9~\/])+)+)?$#si", $_POST['very_siteurl']) ? cleanurl($_POST['very_siteurl']) : "");
	$very_synchronize = (isset($_POST['very_synchronize']) && isnum($_POST['very_synchronize']) ? $_POST['very_synchronize'] : "0");
	$very_secureip = (isset($_POST['very_secureip']) && isnum($_POST['very_secureip']) ? $_POST['very_secureip'] : "0");
	$very_prune = (isset($_POST['very_prune']) && isnum($_POST['very_prune']) ? $_POST['very_prune'] : "0");
	$very_cache = (isset($_POST['very_cache']) && isnum($_POST['very_cache']) ? $_POST['very_cache'] : "0");
	$very_debug = (isset($_POST['very_debug']) && isnum($_POST['very_debug']) ? $_POST['very_debug'] : "0");
	$result = dbquery("UPDATE ".DB_VERY_API." SET
		very_panel_name = '".$very_panel_name."',
		very_api_type = '".$api_key_type."',
		very_api_key = '".$very_api_key."',
		very_profile = '".$very_profile."',
		very_mode = '".$very_mode."',
		very_siteurl = '".$very_siteurl."',
		very_synchronize = '".$very_synchronize."',
		very_secureip = '".$very_secureip."',
		very_prune = '".$very_prune."',
		very_cache = '".$very_cache."',
		very_debug = '".$very_debug."'
	");
	redirect(FUSION_SELF.$aidlink."&status=su");
} else {
	$data = dbarray(dbquery("SELECT very_panel_name, very_api_type, very_api_key, very_profile, very_mode, very_siteurl, very_synchronize, very_secureip, very_prune, very_cache, very_debug FROM ".DB_VERY_API.""));
	$very_panel_name = $data['very_panel_name'];
	$api_key_type = $data['very_api_type'];
	$very_api_key = $data['very_api_key'];
	$very_profile = $data['very_profile'];
	$very_mode = $data['very_mode'];
	$very_siteurl = $data['very_siteurl'];
	$very_synchronize = $data['very_synchronize'];
	$very_secureip = $data['very_secureip'];
	$very_prune = $data['very_prune'];
	$very_cache = $data['very_cache'];
	$very_debug = $data['very_debug'];
}

add_to_head("<style type='text/css'>.trigger, .trigger:hover, .trigger img {border:none;}.hint {position:absolute;display:none;max-width:300px;padding:10px;z-index:9999;}</style>");

opentable($locale['very_005']);
echo "<form name='inputform' method='post' action='".FUSION_SELF.$aidlink."'>\n";
echo "<table cellpadding='0' cellspacing='0' width='600' class='center'>\n<tr>\n";
echo "<td width='30%' class='tbl'>".$locale['very_006'].":</td>\n";
echo "<td width='70%' class='tbl'><input type='text' name='very_panel_name' value='".$very_panel_name."' maxlength='240' class='textbox' style='width:230px;' /></td>\n";
echo "</tr>\n<tr>\n";
echo "<td width='30%' class='tbl'>".$locale['very_007'].":</td>\n";
echo "<td width='70%' class='tbl'>\n<select name='very_api_type' id='very_api_type' class='textbox' style='width:230px;'>\n";
echo "<option value='0'".($api_key_type == "0" ? " selected='selected'" : "").">".$locale['very_008']."</option>\n";
echo "<option value='1'".($api_key_type == "1" ? " selected='selected'" : "").">".$locale['very_009']."</option>\n";
echo "</select>\n</td>\n";
echo "</tr>\n<tr id='api_key_input'".($api_key_type == "0" ? " style='display:none;'" : "").">\n";
echo "<td width='30%' class='tbl'>".$locale['very_010'].":</td>\n";
echo "<td width='70%' class='tbl'><input type='text' name='very_api_key' value='".$very_api_key."' maxlength='10' class='textbox' style='width:230px;' /> <a href='http://smart-fusion.ru/api.php' target='_blank' title='".$locale['very_011']."'><strong>".$locale['very_012']."</strong></a></td>\n";
echo "</tr>\n<tr>\n";
echo "<td width='30%' class='tbl'>".$locale['very_013'].":</td>\n";
echo "<td width='70%' class='tbl'>\n<select name='very_profile' id='very_profile' class='textbox' style='width:230px;'>\n";
echo "<option value='0'".($very_profile == "0" ? " selected='selected'" : "").">".$locale['very_014']."</option>\n";
echo "<option value='1'".($very_profile == "1" ? " selected='selected'" : "").">".$locale['very_015']."</option>\n";
echo "</select>\n</td>\n";
echo "</tr>\n<tr class='custom_profile'".($very_profile == "0" ? " style='display:none;'" : "").">\n";
echo "<td width='30%' class='tbl'>".$locale['very_016'].":</td>\n";
echo "<td width='70%' class='tbl'>\n<select name='very_mode' class='textbox' style='width:230px;' />\n";
echo "<option value='0'".($very_mode == "0" ? " selected='selected'" : "").">".$locale['very_017']."</option>\n";
echo "<option value='1'".($very_mode == "1" ? " selected='selected'" : "").">".$locale['very_018']."</option>\n";
echo "</select>\n</td>\n";
echo "</tr>\n<tr class='custom_profile'".($very_profile == "0" ? " style='display:none;'" : "").">\n";
echo "<td width='30%' class='tbl'>".$locale['very_019'].":</td>\n";
echo "<td width='70%' class='tbl'><input type='text' name='very_siteurl' value='".$very_siteurl."' maxlength='255' class='textbox' style='width:230px;' /><br /><span class='small'>".$locale['very_020']."</span></td>\n";
echo "</tr>\n<tr class='custom_profile'".($very_profile == "0" ? " style='display:none;'" : "").">\n";
echo "<td width='30%' class='tbl'>".$locale['very_021'].":</td>\n";
echo "<td width='70%' class='tbl'>\n<select name='very_synchronize' class='textbox'>\n";
echo "<option value='0'".($very_synchronize == "0" ? " selected='selected'" : "").">".$locale['very_022']."</option>\n";
echo "<option value='1'".($very_synchronize == "1" ? " selected='selected'" : "").">".$locale['very_023']."</option>\n";
echo "</select>\n <a href='#' class='trigger'><img src='".VERY_IMAGES."help.png' style='border:none;vertical-align:middle;' alt='' /><span class='tbl tbl-border hint'>".$locale['very_024']."</span></a>\n</td>\n";
echo "</tr>\n<tr class='custom_profile'".($very_profile == "0" ? " style='display:none;'" : "").">\n";
echo "<td width='30%' class='tbl'>".$locale['very_025'].":</td>\n";
echo "<td width='70%' class='tbl'>\n<select name='very_secureip' class='textbox'>\n";
echo "<option value='0'".($very_secureip == "0" ? " selected='selected'" : "").">".$locale['very_022']."</option>\n";
echo "<option value='1'".($very_secureip == "1" ? " selected='selected'" : "").">".$locale['very_023']."</option>\n";
echo "</select>\n <a href='#' class='trigger'><img src='".VERY_IMAGES."help.png' style='border:none;vertical-align:middle;' alt='' /><span class='tbl tbl-border hint'>".$locale['very_026']."</span></a>\n</td>\n";
echo "</tr>\n<tr class='custom_profile'".($very_profile == "0" ? " style='display:none;'" : "").">\n";
echo "<td width='30%' class='tbl'>".$locale['very_027'].":</td>\n";
echo "<td width='70%' class='tbl'>\n<select name='very_prune' class='textbox'>\n";
echo "<option value='0'".($very_prune == "0" ? " selected='selected'" : "").">".$locale['very_022']."</option>\n";
echo "<option value='1'".($very_prune == "1" ? " selected='selected'" : "").">".$locale['very_023']."</option>\n";
echo "</select>\n <a href='#' class='trigger'><img src='".VERY_IMAGES."help.png' style='border:none;vertical-align:middle;' alt='' /><span class='tbl tbl-border hint'>".$locale['very_028']."</span></a>\n</td>\n";
echo "</tr>\n<tr class='custom_profile'".($very_profile == "0" ? " style='display:none;'" : "").">\n";
echo "<td width='30%' class='tbl'>".$locale['very_029'].":</td>\n";
echo "<td width='70%' class='tbl'>\n<select name='very_cache' class='textbox'>\n";
echo "<option value='0'".($very_cache == "0" ? " selected='selected'" : "").">".$locale['very_022']."</option>\n";
echo "<option value='1'".($very_cache == "1" ? " selected='selected'" : "").">".$locale['very_023']."</option>\n";
echo "</select>\n <a href='#' class='trigger'><img src='".VERY_IMAGES."help.png' style='border:none;vertical-align:middle;' alt='' /><span class='tbl tbl-border hint'>".$locale['very_030']."</span></a>\n</td>\n";
echo "</tr>\n<tr class='custom_profile'".($very_profile == "0" ? " style='display:none;'" : "").">\n";
echo "<td width='30%' class='tbl'>".$locale['very_031'].":</td>\n";
echo "<td width='70%' class='tbl'>\n<select name='very_debug' class='textbox'>\n";
echo "<option value='0'".($very_debug == "0" ? " selected='selected'" : "").">".$locale['very_022']."</option>\n";
echo "<option value='1'".($very_debug == "1" ? " selected='selected'" : "").">".$locale['very_023']."</option>\n";
echo "</select>\n <a href='#' class='trigger'><img src='".VERY_IMAGES."help.png' style='border:none;vertical-align:middle;' alt='' /><span class='tbl tbl-border hint'>".$locale['very_032']."</span></a>\n</td>\n";
echo "</tr>\n<tr>\n";
echo "<td align='center' colspan='2' class='tbl'><br />";
echo "<input type='submit' name='savesettings' value='".$locale['very_033']."' class='button' /></td>\n";
echo "</tr>\n</table>\n</form>\n";
echo "<script type='text/javascript'>\n$('#very_api_type').change(function(){\n";
echo "if ($(this).val()==1) {\n$('#api_key_input').show();\n} else {\n";
echo "$('#api_key_input').hide();\n}\n});\n$('#very_profile').change(function(){\n";
echo "if ($(this).val()==1) {\n$('tr.custom_profile').show();\n} else {\n";
echo "$('tr.custom_profile').hide();\n}\n});\n$('.trigger').hover(function(){\n";
echo "tip = $(this).children('span');\ntip.stop().fadeIn(400);\n}, function() {\n";
echo "tip.stop().hide();\n}).mousemove(function(e) {\nvar tipWidth = tip.width();\n";
echo "var tipHeight = tip.height();\nvar mouse_x = e.pageX + 20;\nvar mouse_y = e.pageY - tipHeight - 20;\n";
echo "var tipVisX = $(window).width() - (mouse_x - tipWidth);\nif (tipVisX < 20) { mouse_x = e.pageX - tipWidth - 20; }\n";
echo "tip.css({top: mouse_y, left: mouse_x});\n});\n</script>\n";
closetable();

require_once THEMES."templates/footer.php";
?>