<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2014 Nick Jones
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

if (!checkrights("MM") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) { redirect("../../index.php"); }

require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."mega_menu/classes/MegaMenu.class.php";

add_to_head("<script type='text/javascript' src='".INCLUDES."jquery/jquery-ui.js'></script>");
add_to_head("<link rel='stylesheet' href='".THEMES."templates/site_links.css' type='text/css' media='all' />");
add_to_head("<script type='text/javascript'>
$(document).ready(function() {
	$('.site-links').sortable({
		handle : '.mainhandle',
		placeholder: 'state-highlight',
		connectWith: '.connected',
		scroll: true,
		axis: 'y',
		update: function () {
			var ul = $(this),
				order = ul.sortable('serialize'),
				i = 0;
			$('#info').load('infusion_admin_updater.php".$aidlink."&'+order);
			ul.find('.num').each(function(i) {
				$(this).text(i+1);
			});
			ul.children('li').removeClass('tbl2').removeClass('tbl1');
			ul.children('li:odd').addClass('tbl2');
			ul.children('li:even').addClass('tbl1');
			window.setTimeout('closeDiv();',2500);
		}
	});
	$('.site-sublinks').sortable({
		handle : '.subhandle',
		placeholder: 'state-highlight',
		connectWith: '.subconnected',
		scroll: true,
		axis: 'y',
		update: function () {
			var ul = $(this),
				order = ul.sortable('serialize'),
				i = 0;
			$('#info').load('infusion_admin_updater.php".$aidlink."&'+order);
			ul.find('.subnum').each(function(i) {
				$(this).text(i+1);
			});
			ul.children('li').removeClass('tbl2').removeClass('tbl1');
			ul.children('li:odd').addClass('tbl2');
			ul.children('li:even').addClass('tbl1');
			window.setTimeout('closeDiv();',2500);
		}
	});
});
</script>");

if (isset($_GET['status']) && !isset($message)) {
	if ($_GET['status'] == "sn") {
		$message = $locale['mm_002'];
	} elseif ($_GET['status'] == "su") {
		$message = $locale['mm_003'];
	} elseif ($_GET['status'] == "del") {
		$message = $locale['mm_004'];
	}
	if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}

if ((isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['link_id']) && isnum($_GET['link_id']))) {
	$data = dbarray(dbquery("SELECT link_id, link_parent_id, link_order FROM ".DB_MEGA_MENU." WHERE link_id='".$_GET['link_id']."' LIMIT 1"));
	if ($data['link_parent_id'] == "0") { $result = dbquery("DELETE FROM ".DB_MEGA_MENU." WHERE link_parent_id='".$data['link_id']."'"); }
	$result = dbquery("UPDATE ".DB_MEGA_MENU." SET link_order=link_order-1 WHERE link_order>'".$data['link_order']."'");
	$result = dbquery("DELETE FROM ".DB_MEGA_MENU." WHERE link_id='".$_GET['link_id']."'");
	redirect(FUSION_SELF.$aidlink."&status=del");
} elseif (isset($_POST['savelink'])) {
	$link_name = stripinput($_POST['link_name']);
	$link_url = stripinput($_POST['link_url']);
	$link_parent_id = (isnum($_POST['link_parent_id']) ? $_POST['link_parent_id'] : "0");
	$link_columns = (isnum($_POST['link_columns']) && (int)$_POST['link_columns'] > 0 ? $_POST['link_columns'] : "1");
	$link_visibility = (isnum($_POST['link_visibility']) ? $_POST['link_visibility'] : "0");
	$link_order = (isnum($_POST['link_order']) ? $_POST['link_order'] : "");
	$link_window = isset($_POST['link_window']) ? $_POST['link_window'] : "0";
	if ($link_name && $link_url) {
		if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['link_id']) && isnum($_GET['link_id']))) {
			$old_link_info = dbarray(dbquery("SELECT link_parent_id, link_order FROM ".DB_MEGA_MENU." WHERE link_id='".$_GET['link_id']."'"));
			if ($old_link_info['link_parent_id'] != $link_parent_id) {
				$new_link_order = dbresult(dbquery("SELECT MAX(link_order) FROM ".DB_MEGA_MENU." WHERE link_parent_id='$link_parent_id'"), 0) + 1;
				if (!$link_order || $link_order > $new_link_order) { $link_order = $new_link_order; }
				$result = dbquery("UPDATE ".DB_MEGA_MENU." SET link_order=link_order+1 WHERE link_order>='$link_order' AND link_parent_id='$link_parent_id'");
				$result = dbquery("UPDATE ".DB_MEGA_MENU." SET link_order=link_order-1 WHERE link_order>='".$old_link_info['link_order']."' AND link_parent_id='".$old_link_info['link_parent_id']."'");
			} else {
				if ($link_order > $old_link_info['link_order']) {
					$result = dbquery("UPDATE ".DB_MEGA_MENU." SET link_order=link_order-1 WHERE link_order>'".$old_link_info['link_order']."' AND link_order<='$link_order' AND link_parent_id='".$old_link_info['link_parent_id']."'");
				} elseif ($link_order < $old_link_info['link_order']) {
					$result = dbquery("UPDATE ".DB_MEGA_MENU." SET link_order=link_order+1 WHERE link_order<'".$old_link_info['link_order']."' AND link_order>='$link_order' AND link_parent_id='".$old_link_info['link_parent_id']."'");
				}
			}
			$result = dbquery("UPDATE ".DB_MEGA_MENU." SET link_name='$link_name', link_url='$link_url', link_parent_id='$link_parent_id', link_visibility='$link_visibility', link_window='$link_window', link_order='$link_order', link_columns='$link_columns' WHERE link_id='".$_GET['link_id']."'");
			redirect(FUSION_SELF.$aidlink."&status=su");
		} else {
			$new_link_order = dbresult(dbquery("SELECT MAX(link_order) FROM ".DB_MEGA_MENU." WHERE link_parent_id='$link_parent_id'"), 0) + 1;
			if (!$link_order || $link_order > $new_link_order) { $link_order = $new_link_order; }
			$result = dbquery("UPDATE ".DB_MEGA_MENU." SET link_order=link_order+1 WHERE link_parent_id='$link_parent_id' AND link_order>='$link_order'");
			$result = dbquery("INSERT INTO ".DB_MEGA_MENU." (link_name, link_url, link_parent_id, link_visibility, link_window, link_order, link_columns) VALUES ('$link_name', '$link_url', '$link_parent_id', '$link_visibility', '$link_window', '$link_order', '$link_columns')");
			redirect(FUSION_SELF.$aidlink."&status=sn");
		}
	} else {
		redirect(FUSION_SELF.$aidlink);
	}
}
if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['link_id']) && isnum($_GET['link_id']))) {
	$result = dbquery("SELECT link_name, link_url, link_visibility, link_parent_id, link_columns, link_order, link_window FROM ".DB_MEGA_MENU." WHERE link_id='".$_GET['link_id']."'");
	if (dbrows($result)) {
		$data = dbarray($result);
		$link_name = $data['link_name'];
		$link_url = $data['link_url'];
		$link_visibility = $data['link_visibility'];
		$link_parent_id = $data['link_parent_id'];
		$link_columns = $data['link_columns'];
		$link_order = $data['link_order'];
		$window_check = ($data['link_window'] == "1" ? " checked='checked'" : "");
		$formaction = FUSION_SELF.$aidlink."&amp;action=edit&amp;link_id=".$_GET['link_id'];
		opentable($locale['mm_005']);
	} else {
		redirect(FUSION_SELF.$aidlink);
	}
} else {
	$link_name = "";
	$link_url = "";
	$link_visibility = "";
	$link_parent_id = "";
	$link_columns = "";
	$link_order = "";
	$window_check = "";
	$formaction = FUSION_SELF.$aidlink;
	opentable($locale['mm_006']);
}

// Render user group options
$visibility_opts = ""; $sel = "";
$user_groups = getusergroups();
while (list($key, $user_group) = each($user_groups)) {
	$sel = ($link_visibility == $user_group['0'] ? " selected='selected'" : "");
	$visibility_opts .= "<option value='".$user_group['0']."'$sel>".$user_group['1']."</option>\n";
}

// Render parent link options
$parent_opts = "<option value='0'>".$locale['mm_007']."</option>\n"; $sel = "";
$parent_links = MegaMenu::getParentLinks();
while (list($key, $link_param) = each($parent_links)) {
	$sel = ($link_parent_id == $link_param['0'] ? " selected='selected'" : "");
	$parent_opts .= "<option value='".$link_param['0']."'$sel>".parseubb($link_param['1'])."</option>\n";
}

require_once INCLUDES."bbcode_include.php";
echo "<form name='layoutform' method='post' action='".$formaction."'>\n";
echo "<table cellpadding='0' cellspacing='0' class='center'>\n<tr>\n";
echo "<td class='tbl'>".$locale['mm_008'].":</td>\n";
echo "<td class='tbl'><input type='text' name='link_name' value='".$link_name."' maxlength='100' class='textbox' style='width:240px;' /><br />\n";
echo "</td>\n</tr>\n<tr>\n";
echo "<td class='tbl'></td>\n<td class='tbl'>";
echo display_bbcodes("240px;", "link_name", "layoutform", "b|i|u|color|img")."\n";
echo "</td>\n</tr>\n<tr>\n";
echo "<td class='tbl'>".$locale['mm_009'].":</td>\n";
echo "<td class='tbl'><input type='text' name='link_url' value='".$link_url."' maxlength='200' class='textbox' style='width:240px;' /></td>\n";
echo "</tr>\n<tr>\n";
echo "<td class='tbl'>".$locale['mm_010'].":</td>\n";
echo "<td class='tbl'><select name='link_parent_id' class='textbox' style='width:150px;'>\n".$parent_opts."</select>\n";
if (empty($link_parent_id) || $link_parent_id == "0") {
	echo $locale['mm_011'].":\n<input type='number' name='link_columns' value='".$link_columns."' min='1' max='9' class='textbox' style='width:30px;' />";
} else {
	echo "<input type='hidden' name='link_columns' value='".$link_columns."' />\n";
}
echo "</td>\n</tr>\n<tr>\n";
echo "<td class='tbl'>".$locale['mm_012'].":</td>\n";
echo "<td class='tbl'><select name='link_visibility' class='textbox' style='width:150px;'>\n".$visibility_opts."</select>\n";
echo $locale['mm_013'].":\n<input type='number' name='link_order' value='".$link_order."' min='1' max='999' class='textbox' style='width:40px;' />";
echo "</td>\n</tr>\n<tr>\n";
echo "<td class='tbl'></td>\n";
echo "<td class='tbl'><hr /><label><input type='checkbox' name='link_window' value='1'".$window_check." /> ".$locale['mm_014']."</label></td>\n";
echo "</tr>\n<tr>\n";
echo "<td align='center' colspan='2' class='tbl'>\n";
echo "<input type='submit' name='savelink' value='".$locale['mm_015']."' class='button' /></td>\n";
echo "</tr>\n</table>\n</form>\n";
closetable();

opentable($locale['mm_022']);
echo "<div id='info'></div>\n";
echo "<div style='width:500px;' class='panels tbl-border center floatfix'><div class='tbl2'>\n";
echo "<div style='float:left; padding-left:30px;'><strong>".$locale['mm_008']."</strong></div>\n";
echo "<div style='float:right; width:100px; text-align:center;'><strong>".$locale['mm_016']."</strong></div>\n";
echo "<div style='float:right; width:60px; text-align:center;'><strong>".$locale['mm_011']."</strong></div>\n";
echo "<div style='float:right; width:60px; text-align:center;'><strong>".$locale['mm_013']."</strong></div>\n";
echo "<div style='float:right; width:110px; text-align:center;'><strong>".$locale['mm_012']."</strong></div>\n";
echo "<div style='clear:both;'></div>\n</div>\n";
echo "<ul id='site-links' class='site-links connected' style='list-style: none;'>\n";
$result = dbquery("SELECT link_id, link_name, link_url, link_parent_id, link_visibility, link_order, link_columns FROM ".DB_MEGA_MENU." WHERE link_parent_id='0' ORDER BY link_order");
if (dbrows($result)) {
	$i = 0;
	while($data = dbarray($result)) {
		$row_color = ($i % 2 == 0 ? "tbl1" : "tbl2");
		echo "<li id='listItem_".$data['link_id']."' class='".$row_color."' style='padding:0;'>\n";
		echo "<div style='float:left; width:30px; padding:5px;'><img src='".IMAGES."arrow.png' alt='move' class='handle mainhandle' /></div>\n";
		echo "<div style='float:left; padding:5px;'>\n";
		if ($data['link_name'] != "---" && $data['link_url'] == "---") {
			echo "<strong>".parseubb($data['link_name'], "b|i|u|color|img")."</strong>\n";
		} else if ($data['link_name'] == "---" && $data['link_url'] == "---") {
			echo "<hr />\n";
		} else {
			if (strstr($data['link_url'], "http://") || strstr($data['link_url'], "https://")) {
				echo "<a href='".$data['link_url']."'>".parseubb($data['link_name'], "b|i|u|color|img")."</a>\n";
			} else {
				echo "<a href='".BASEDIR.$data['link_url']."'>".parseubb($data['link_name'], "b|i|u|color|img")."</a>\n";
			}
		}
		echo "</div>\n";
		echo "<div style='float:right; width:100px; text-align:center; padding:5px;'>";
		echo "<a href='".FUSION_SELF.$aidlink."&amp;action=edit&amp;link_id=".$data['link_id']."'>".$locale['mm_017']."</a> -\n";
		echo "<a href='".FUSION_SELF.$aidlink."&amp;action=delete&amp;link_id=".$data['link_id']."' onclick=\"return confirm('".$locale['mm_019']."');\">".$locale['mm_018']."</a>\n";
		echo "</div>\n";
		echo "<div style='float:right; width:50px; text-align:center; padding:5px;'>".MegaMenu::buildVisualColumns($data['link_columns'])."</div>\n";
		echo "<div class='num' style='float:right; width:50px; text-align:center; padding:5px;'>".$data['link_order']."</div>\n";
		echo "<div style='float:right; width:100px; text-align:center; padding:5px;'>".getgroupname($data['link_visibility'])."</div>\n";
		echo "<div style='clear:both;'></div>\n";
		$result2 = dbquery("SELECT link_id, link_name, link_url, link_parent_id, link_visibility, link_order, link_columns FROM ".DB_MEGA_MENU." WHERE link_parent_id='".$data['link_id']."' ORDER BY link_order");
		if (dbrows($result2)) {
			$j = 0;
			echo "<ul id='site-sublinks' class='site-sublinks subconnected' style='list-style: none; margin: 0;'>\n";
			while($data2 = dbarray($result2)) {
				$row_color = ($j % 2 == 0 ? "tbl1" : "tbl2");
				echo "<li id='listSubItem_".$data2['link_id']."' class='".$row_color."' style='border-left:none; padding:0 0 0 15px;'>\n";
				echo "<div style='float:left; width:30px; padding:5px;'><img src='".IMAGES."arrow.png' alt='move' class='handle subhandle' /></div>\n";
				echo "<div style='float:left; padding:5px;'>\n";
				if ($data2['link_name'] != "---" && $data2['link_url'] == "---") {
					echo "<strong>".parseubb($data2['link_name'], "b|i|u|color|img")."</strong>\n";
				} else if ($data2['link_name'] == "---" && $data2['link_url'] == "---") {
					echo "<hr />\n";
				} else {
					if (strstr($data2['link_url'], "http://") || strstr($data2['link_url'], "https://")) {
						echo "<a href='".$data2['link_url']."'>".parseubb($data2['link_name'], "b|i|u|color|img")."</a>\n";
					} else {
						echo "<a href='".BASEDIR.$data2['link_url']."'>".parseubb($data2['link_name'], "b|i|u|color|img")."</a>\n";
					}
				}
				echo "</div>\n";
				echo "<div style='float:right; width:100px; text-align:center; padding:5px;'>";
				echo "<a href='".FUSION_SELF.$aidlink."&amp;action=edit&amp;link_id=".$data2['link_id']."'>".$locale['mm_017']."</a> -\n";
				echo "<a href='".FUSION_SELF.$aidlink."&amp;action=delete&amp;link_id=".$data2['link_id']."' onclick=\"return confirm('".$locale['mm_019']."');\">".$locale['mm_018']."</a>\n";
				echo "</div>\n";
				echo "<div class='subnum' style='float:right; width:110px; text-align:center; padding:5px;'>".$data2['link_order']."</div>\n";
				echo "<div style='float:right; width:100px; text-align:center; padding:5px;'>".getgroupname($data2['link_visibility'])."</div>\n";
				echo "<div style='clear:both;'></div>\n";
				echo "</li>\n";
				$j++;
			}
			echo "</ul>\n";
		}
		echo "</li>\n";
		$i++;
	}
echo "</ul>\n</div>";
} else {
	echo "<div style='text-align:center;margin-top:5px'>".$locale['mm_020']."</div>\n";
}
closetable();

require_once THEMES."templates/footer.php";
?>