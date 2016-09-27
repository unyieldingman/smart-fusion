<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: smart_manager.php
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

if (!checkrights("SSM") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) { redirect(BASEDIR); }

require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."smart_system/infusion_db.php";
require_once INFUSIONS."smart_system/smart_functions.php";

if (isset($_GET['smart'])) {
	if ($_GET['smart'] == "update") {
		if (UpdateSmartStore()) {
			redirect(FUSION_SELF.$aidlink."&status=su");
		} else {
			redirect(FUSION_SELF.$aidlink."&status=se");
		}
	} elseif ($_GET['smart'] == "ownership") {
		if (isset($_POST['fix'])) {
			$addon_id = (isset($_POST['addon_id']) && isnum($_POST['addon_id']) ? $_POST['addon_id'] : "0");
			if (FixOwnership($addon_id)) {
				redirect(FUSION_SELF.$aidlink."&status=of");
			} else {
				redirect(FUSION_SELF.$aidlink."&status=oe");
			}
		} elseif (isset($_POST['cancel'])) {
			redirect(FUSION_SELF.$aidlink);
		} else {
			$result = dbquery("SELECT * FROM ".DB_SMART_ADDONS." WHERE addon_path!=''");
			$op_list = "<option value='0'>".$locale['smart_049']."</option>\n";
			while ($data = dbarray($result)) {
				$addon_name = preg_replace("#(.+?)/index.php#i", "\\1", $data['addon_path'], 1);
				$path_parts = explode("/", $addon_name);
				$addon_name = str_replace("_", " ", end($path_parts));
				$op_list .= "<option value='".$data['addon_id']."'>".ucwords($addon_name)."</option>\n";
			}
			opentable($locale['smart_050']);
			echo "<div style='text-align:center;'>\n<form name='fixownership' method='post' action='".FUSION_SELF.$aidlink."&amp;smart=ownership' onsubmit='return VerifyFixForm(this);'>\n";
			echo "<select name='addon_id' class='textbox' style='width:250px'>\n".$op_list."</select>\n";
			echo "<input type='submit' name='fix' value='".$locale['smart_051']."' class='button' />\n";
			echo "<input type='submit' name='cancel' value='".$locale['smart_052']."' onclick='return AbortActionSet();' class='button' />\n";
			echo "</form>\n</div>\n";
			closeside();
		}
	}
}

add_to_title($locale['smart_004'].$locale['smart_005']);
add_to_head("<link rel='stylesheet' type='text/css' href='".SMART_STYLES."interface.css' media='all' />");
add_to_head("<link rel='stylesheet' href='".INCLUDES."jquery/colorbox/colorbox.css' type='text/css' media='screen' />");
add_to_head("<script type='text/javascript' src='".INCLUDES."jquery/colorbox/jquery.colorbox.js'></script>");

if (isset($_GET['status'])) {
	if ($_GET['status'] == "su") {
		$message = $locale['smart_006'];
	} elseif ($_GET['status'] == "se") {
		$message = $locale['smart_007'];
	} elseif ($_GET['status'] == "of") {
		$message = $locale['smart_055'];
	} elseif ($_GET['status'] == "oe") {
		$message = $locale['smart_056'];
	} else {
		$message = $locale['smart_057'];
	}
	$s_error = true;
} else {
	$message = $locale['smart_008'];
	$s_error = false;
}

echo "<div class='admin-message".($s_error ? " smart_nohide" : "")."' id='Errors'>".$message."</div>\n";
echo "<div class='smart_autoload'>\n";
echo "<img src='".SMART_ICONS."spinner.gif' alt='' />\n";
echo "<img src='".SMART_ICONS."loader.gif' alt='' />\n";
echo "<img src='".SMART_ICONS."horizontal.gif' alt='' />\n";
echo "</div>\n";
opentable($locale['smart_009']);
$data = GetDataFromStorage();
echo "<div class='center smart_manager' id='SmartManager'>\n";
echo "<div class='smart_box_r'><strong>".$locale['smart_010']."</strong></div>\n";
echo "<div class='smart_box_l'><strong>".$locale['smart_011']."</strong></div>\n";
echo "<div class='smart_list_column'>\n<div class='smart_list_box' id='SmartListBox'>\n";
echo "<ul class='smart_list'>\n";
echo "<li class='tbl-border tbl2' unselectable='unselectable'><img src='".IMAGES."plus.gif' style='border:none;vertical-align:middle;' />".$locale['smart_012']." (".$data['lines']['1'].")\n";
echo "<ul id='Themes'>\n<li class='tbl2'><img src='".SMART_ICONS."themes.png' style='border:none;margin:0 3px;' align='left' alt='' />".$locale['smart_065']."</li>\n";
echo PrintDataMerge($data['1'], $data['lines']['1'], $locale['smart_015'])."</ul>\n</li>\n";
echo "<li class='tbl-border tbl2' unselectable='unselectable'><img src='".IMAGES."plus.gif' style='border:none;vertical-align:middle;' />".$locale['smart_013']." (".$data['lines']['2'].")\n";
echo "<ul id='Panels'>\n<li class='tbl2'><img src='".SMART_ICONS."panels.png' style='border:none;margin:0 3px;' align='left' alt='' />".$locale['smart_066']."</li>\n";
echo PrintDataMerge($data['2'], $data['lines']['2'], $locale['smart_016'])."</ul>\n</li>\n";
echo "<li class='tbl-border tbl2' unselectable='unselectable'><img src='".IMAGES."plus.gif' style='border:none;vertical-align:middle;' />".$locale['smart_014']." (".$data['lines']['3'].")\n";
echo "<ul id='Infusions'>\n<li class='tbl2'><img src='".SMART_ICONS."infusions.png' style='border:none;margin:0 3px;' align='left' alt='' />".$locale['smart_067']."</li>\n";
echo PrintDataMerge($data['3'], $data['lines']['3'], $locale['smart_017'])."</ul>\n</li>\n";
echo "<li class='tbl-border tbl2' unselectable='unselectable'><img src='".IMAGES."plus.gif' style='border:none;vertical-align:middle;' />".$locale['smart_062']." (".$data['lines']['4'].")\n";
echo "<ul id='BBCodes'>\n<li class='tbl2'><img src='".SMART_ICONS."bb-codes.png' style='border:none;margin:0 3px;' align='left' alt='' />".$locale['smart_068']."</li>\n";
echo PrintDataMerge($data['4'], $data['lines']['4'], $locale['smart_084'])."</ul>\n</li>\n";
echo "<li class='tbl-border tbl2' unselectable='unselectable'><img src='".IMAGES."plus.gif' style='border:none;vertical-align:middle;' />".$locale['smart_063']." (".$data['lines']['5'].")\n";
echo "<ul id='Updates'>\n<li class='tbl2'><img src='".SMART_ICONS."updates.png' style='border:none;margin:0 3px;' align='left' alt='' />".$locale['smart_069']."</li>\n";
echo PrintDataMerge($data['5'], $data['lines']['5'], $locale['smart_085'])."</ul>\n</li>\n";
echo "<li class='tbl-border tbl2' unselectable='unselectable'><img src='".IMAGES."plus.gif' style='border:none;vertical-align:middle;' />".$locale['smart_064']." (".$data['lines']['6'].")\n";
echo "<ul id='Locales'>\n<li class='tbl2'><img src='".SMART_ICONS."locales.png' style='border:none;margin:0 3px;' align='left' alt='' />".$locale['smart_070']."</li>\n";
echo PrintDataMerge($data['6'], $data['lines']['6'], $locale['smart_086'])."</ul>\n</li>\n";
echo "</ul>\n</div>\n";
echo "<div class='smart_list_controls'><div class='smart_install_button'><img src='".SMART_ICONS."loader.gif' id='InstallLoader' style='display:none;border:none;vertical-align:middle;' alt='".$locale['smart_018']."' title='".$locale['smart_018']."' /> \n";
echo "<input type='button' value='".$locale['smart_019']."' class='button' id='Install' style='display:none;' /></div>\n";
echo "<a href='javascript:void(0);' id='SlideDown'><img src='".SMART_ICONS."slide_down.png' style='border:none;vertical-align:middle;' alt='".$locale['smart_020']."' title='".$locale['smart_020']."' /></a>&nbsp;";
echo "<a href='javascript:void(0);' id='SlideUp'><img src='".SMART_ICONS."slide_up.png' style='border:none;vertical-align:middle;' alt='".$locale['smart_021']."' title='".$locale['smart_021']."' /></a>&nbsp;";
echo "<a href='javascript:void(0);' id='SlideToggle'><img src='".SMART_ICONS."slide_toggle.png' style='border:none;vertical-align:middle;' alt='".$locale['smart_022']."' title='".$locale['smart_022']."' /></a>";
echo "</div>\n</div>\n";
echo "<div class='smart_explorer' id='SmartExplorer'>\n";
echo "<div class='smart_preview'>\n";
echo "<img src='".SMART_ICONS."spinner.gif' class='smart_loader' id='Loader' alt='".$locale['smart_023']."' title='".$locale['smart_023']."' />\n";
echo "<a rel='panorama_main' href='http://".SMART_SERVER_NAME.SMART_SPLASH_URI."' id='Panorama'><img src='".SMART_ICONS."smart_logo.png' class='smart_preview' id='Preview' alt='".$locale['smart_024']."' title='".$locale['smart_024']."' /></a>\n";
echo "</div>\n";
echo "<div class='smart_info'><strong>".$locale['smart_025']."</strong><br />\n";
echo "<div class='smart_info_r'>\n";
echo $locale['smart_026']."<span id='Compatible'>7.xx</span><br />";
echo $locale['smart_027']."<span id='Size'>".$locale['smart_032']."</span><br />";
echo $locale['smart_028']."<span id='Type'>".$locale['smart_033']."</span>";
echo "</div>\n<div class='smart_info_l'>";
echo $locale['smart_029']."<span id='Title'>".$locale['smart_033']."</span><br />";
echo $locale['smart_030']."<span id='Author'>".$locale['smart_033']."</span><br />";
echo $locale['smart_031']."<span id='Pubdate'>00-00-0000</span>";
echo "<span id='Addon' style='display:none;'></span>";
echo "</div>\n</div>\n";
echo "<div class='smart_description'><strong>".$locale['smart_034']."</strong><br /><span id='Info'>".$locale['smart_035']."</span><br /><span id='Certificate'></span></div>";
echo "<div class='smart_stats'><strong>".$locale['smart_036']."</strong><br />\n";
echo $locale['smart_037']."<span id='Packages'>".dbcount("(addon_id)", DB_SMART_ADDONS)."</span><br />";
echo $locale['smart_038'].CountDataMerge($data)."<br />";
echo $locale['smart_039'].$data['storage']."<br />";
echo $locale['smart_040'].showdate("forumdate", (file_exists(SMART_BASES."addons.xml") ? filemtime(SMART_BASES."addons.xml") : 0))."<br />";
echo "<a href='".FUSION_SELF.$aidlink."&smart=update' class='smart_update_link'><img src='".SMART_ICONS."update.png' style='border:none;vertical-align:middle;' alt='".$locale['smart_041']."' title='".$locale['smart_041']."' /> ".$locale['smart_041']."</a><br />";
echo "<a href='".FUSION_SELF.$aidlink."&smart=ownership' class='smart_ownership_link'><img src='".SMART_ICONS."ownership.png' style='border:none;vertical-align:middle;' alt='".$locale['smart_050']."' title='".$locale['smart_050']."' /> ".$locale['smart_050']."</a>";
echo "</div>\n</div>\n</div>\n";
closetable();

InitCode("<script type='text/javascript' src='".SMART_JS."core.js'></script>");
InitCode("<script type='text/javascript'>$('a[rel^=\"panorama\"]').colorbox({width:'80%',height:'80%'})</script>");
InitCode("<script type='text/javascript'>
	smart.settings.pathSmart = '".MY_SMART."';
	smart.settings.pathImages = '".IMAGES."';
	smart.settings.verifyFixWarn = '".$locale['smart_053']."';
	smart.settings.verifyFixMess = '".$locale['smart_054']."';
	smart.settings.verifyDelMess = '".$locale['smart_058']."';
	smart.settings.sysVersion = '".$settings['version']."';
	smart.settings.verWarnMess = '".$locale['smart_059']."';
	smart.settings.statDeleting = '".$locale['smart_060']."';
	smart.settings.statInstalled = '".$locale['smart_061']."';
</script>");

require_once THEMES."templates/footer.php";
?>