<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: smart_globe_admin.php
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

if (!checkrights("SGLB") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) { redirect("../index.php"); }

require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."smart_globe_panel/infusion_db.php";
require_once SMART_GLOBE_CLASSES."SmartGlobe.class.php";

// Initializing
$globe = new SmartGlobe();

// Number of items on page
$sites_per_page = 20;

if (isset($_GET['error']) && isnum($_GET['error'])) {
	if ($_GET['error'] == 1) {
		$message = $locale['globe_026'];
	} elseif ($_GET['error'] == 2) {
		$message = sprintf($locale['globe_027'], parsebytesize($settings['news_photo_max_b']));
	} elseif ($_GET['error'] == 3) {
		$message = $locale['globe_028'];
	} elseif ($_GET['error'] == 4) {
		$message = sprintf($locale['globe_029'], $settings['news_photo_max_w'], $settings['news_photo_max_h']);
	} elseif ($_GET['error'] == 5) {
		$message = $locale['globe_030'];
	} elseif ($_GET['error'] == 6) {
		$message = $locale['globe_031'];
	}
	if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}

if (isset($_GET['status'])) {
	if ($_GET['status'] == "sd") {
		$message = $locale['globe_032'];
	} elseif ($_GET['status'] == "su") {
		$message = $locale['globe_033'];
	} elseif ($_GET['status'] == "sc") {
		$message = $locale['globe_034'];
	}
	if ($message) { echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}

if (isset($_GET['action']) && !empty($_GET['action']) && isset($_GET['globe_id']) && isnum($_GET['globe_id'])) {
	if ($_GET['action'] == "checklink") {
		$result = dbquery("SELECT globe_site_url FROM ".DB_SMART_GLOBE." WHERE globe_id='".$_GET['globe_id']."'");
		if (dbrows($result)) {
			$data = dbarray($result);
			$backlink = $globe->checkBacklink($data['globe_site_url']);
			$status = ($backlink == "0" ? "1" : "0");
			$result = dbquery("UPDATE ".DB_SMART_GLOBE." SET globe_backlink='".$status."', globe_last_check='".time()."'".($status == "0" ? ", globe_draft='1'" : "")." WHERE globe_id='".$_GET['globe_id']."'");
			redirect(FUSION_SELF.$aidlink."&".($backlink != "0" ? "error=".($backlink + 4)."" : "status=sc"));
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	} elseif ($_GET['action'] == "delete") {
		$result = dbquery("SELECT globe_icon FROM ".DB_SMART_GLOBE." WHERE globe_id='".$_GET['globe_id']."'");
		if (dbrows($result)) {
			$data = dbarray($result);
			$icon_path = SMART_GLOBE_IMAGES."partners/".$data['globe_icon'];
			if (!empty($data['globe_icon']) && file_exists($icon_path)) { unlink($icon_path); }
			$result = dbquery("DELETE FROM ".DB_SMART_GLOBE." WHERE globe_id='".$_GET['globe_id']."' LIMIT 1");
			redirect(FUSION_SELF.$aidlink."&amp;status=sd");
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	} elseif ($_GET['action'] == "edit") {
		if (isset($_POST['globe_return'])) {
			redirect(FUSION_SELF.$aidlink);
		} elseif (isset($_POST['globe_save'])) {
			$globe_site_url = stripinput($_POST['globe_site_url']);
			if (!$globe->checkURL($globe_site_url)) {
				$globe_site_url = "http://".$globe_site_url;
			}
			$globe_title = stripinput($_POST['globe_title']);
			$globe_location = stripinput($_POST['globe_location']);
			$globe_language = stripinput($_POST['globe_language']);
			$globe_info = stripinput($_POST['globe_info']);
			$globe_population = (isnum($_POST['globe_population']) ? $_POST['globe_population'] : "0");
			$globe_born = (isnum($_POST['globe_born']) ? $_POST['globe_born'] : date("Y"));
			$globe_orientation = ($_POST['globe_orientation'] == "0" ? "0" : "1");
			$globe_xaxis = (isnum($_POST['globe_xaxis']) ? $_POST['globe_xaxis'] : "0");
			$globe_yaxis = (isnum($_POST['globe_yaxis']) ? $_POST['globe_yaxis'] : "0");
			
			$error = false;
			if (isset($_FILES['globe_icon']) && is_uploaded_file($_FILES['globe_icon']['tmp_name'])) {
				require_once INCLUDES."photo_functions_include.php";

				$image = $_FILES['globe_icon'];
				$image_name = stripfilename(str_replace(" ", "_", strtolower(substr($image['name'], 0, strrpos($image['name'], ".")))));
				$image_ext = strtolower(strrchr($image['name'],"."));

				if ($image_ext == ".gif") { $filetype = 1;
				} elseif ($image_ext == ".jpg") { $filetype = 2;
				} elseif ($image_ext == ".png") { $filetype = 3;
				} else { $filetype = false; }

				if (!preg_match("/^[-0-9A-Z_\.\[\]]+$/i", $image_name)) {
					$error = 1;
				} elseif ($image['size'] > $settings['news_photo_max_b']){
					$error = 2;
				} elseif (!$filetype) {
					$error = 3;
				} else {
					$image_t1 = image_exists(SMART_GLOBE_IMAGES."partners/", $image_name."_t1".$image_ext);
					$image_full = image_exists(SMART_GLOBE_IMAGES."partners/", $image_name.$image_ext);
					
					move_uploaded_file($_FILES['globe_icon']['tmp_name'], SMART_GLOBE_IMAGES."partners/".$image_t1);
					$imagefile = @getimagesize(SMART_GLOBE_IMAGES."partners/".$image_t1);
					if ($imagefile[0] > $settings['news_photo_max_w'] || $imagefile[1] > $settings['news_photo_max_h']) {
						$error = 4;
						@unlink(SMART_GLOBE_IMAGES."partners/".$image_t1);
					} else {
						createthumbnail($filetype, SMART_GLOBE_IMAGES."partners/".$image_t1, SMART_GLOBE_IMAGES."partners/".$image_full, 170, round(170 * $imagefile[1] / $imagefile[0]));
						if (function_exists("chmod")) { chmod(SMART_GLOBE_IMAGES."partners/".$image_full, 0644); }
						@unlink(SMART_GLOBE_IMAGES."partners/".$image_t1);
					}
				}
				if (!$error) {
					$globe_icon = $image_full;
				} else {
					$globe_icon = "";
				}
			} else {
				$globe_icon = (isset($_POST['globe_icon']) ? $_POST['globe_icon'] : "");
			}
			$globe_privilegies = (isset($_POST['globe_privilegies']) && $_POST['globe_privilegies'] == "yes" ? "1" : "0");
			$globe_draft = (isset($_POST['globe_draft']) && $_POST['globe_draft'] == "yes" ? "1" : "0");
			$del_icon = (isset($_POST['del_icon']) && $_POST['del_icon'] == "yes" ? "1" : "0");
			
			if ($del_icon) {
				$image_path = SMART_GLOBE_IMAGES."partners/".$globe_icon;
				if (!empty($globe_icon) && file_exists($image_path)) { unlink($image_path); }
				$globe_icon = "";
			}
			
			$result = dbquery("UPDATE ".DB_SMART_GLOBE." SET globe_site_url='".$globe_site_url."', globe_title='".$globe_title."', globe_location='".$globe_location."', globe_language='".$globe_language."', globe_info='".$globe_info."', globe_population='".$globe_population."', globe_born='".$globe_born."', globe_icon='".$globe_icon."', globe_draft='".$globe_draft."', globe_privilegies='".$globe_privilegies."', globe_orientation='".$globe_orientation."', globe_xaxis='".$globe_xaxis."', globe_yaxis='".$globe_yaxis."' WHERE globe_id=".$_POST['globe_id']."");
			redirect(FUSION_SELF.$aidlink."&action=edit&globe_id=".$_POST['globe_id']."&".($error ? "error=$error" : "status=su"));
		}
		
		$result = dbquery("SELECT * FROM ".DB_SMART_GLOBE." WHERE globe_id='".$_GET['globe_id']."'");
		if (dbrows($result)) {
			$data = dbarray($result);
			opentable($locale['globe_035']);
			echo "<form name='inputform' method='post' action='".FUSION_SELF.$aidlink."&amp;action=edit&amp;globe_id=".$data['globe_id']."' enctype='multipart/form-data' onsubmit='return VerifyForm(this);'>\n";
			echo "<table cellpadding='0' cellspacing='0' width='400' class='center'>\n<tr>\n";
			echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['globe_064'].":</td>\n";
			echo "<td class='tbl'><input type='text' name='globe_title' value='".$data['globe_title']."' class='textbox' style='width:250px;' /></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['globe_017'].":</td>\n";
			echo "<td class='tbl'><input type='text' name='globe_site_url' value='".$data['globe_site_url']."' class='textbox' style='width:250px;' /></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['globe_018'].":</td>\n";
			echo "<td class='tbl'><input type='text' name='globe_location' value='".$data['globe_location']."' class='textbox' style='width:250px;' /></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['globe_036'].":</td>\n";
			echo "<td class='tbl'><input type='text' name='globe_language' value='".$data['globe_language']."' class='textbox' style='width:250px;' /></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='1%' class='tbl' valign='top' style='white-space:nowrap'>".$locale['globe_063'].":</td>\n";
			echo "<td class='tbl'><textarea name='globe_info' rows='3' class='textbox' style='width:250px;'>".$data['globe_info']."</textarea></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['globe_037'].":</td>\n";
			echo "<td class='tbl'><input type='text' name='globe_population' value='".$data['globe_population']."' class='textbox' style='width:250px;' /></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['globe_065'].":</td>\n";
			echo "<td class='tbl'><input type='text' name='globe_born' value='".$data['globe_born']."' class='textbox' style='width:250px;' /></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['globe_038'].":</td>\n";
			echo "<td class='tbl'>\n";
			if (!empty($data['globe_icon']) && file_exists(SMART_GLOBE_IMAGES."partners/".$data['globe_icon'])) {
				echo "<img src='".SMART_GLOBE_IMAGES."partners/".$data['globe_icon']."' style='border:none;' alt='' /><br />\n";
				echo "<input type='hidden' name='globe_icon' value='".$data['globe_icon']."' />\n";
				echo "<label><input type='checkbox' name='del_icon' value='yes' /> ".$locale['globe_039']."</label>";
			} else {
				echo "<input type='file' name='globe_icon' value='' class='textbox' style='width:250px;' />\n";
			}
			echo "</td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['globe_040'].":</td>\n";
			echo "<td class='tbl'>\n<select name='globe_orientation' class='textbox' style='width:250px;'>\n";
			echo "<option value='0'".($data['globe_orientation'] == "0" ? " selected='selected'" : "").">".$locale['globe_041']."</option>\n";
			echo "<option value='1'".($data['globe_orientation'] == "1" ? " selected='selected'" : "").">".$locale['globe_042']."</option>\n";
			echo "</select>\n</td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['globe_043'].":</td>\n";
			echo "<td class='tbl'>X: <input type='text' name='globe_xaxis' value='".$data['globe_xaxis']."' class='textbox' style='width:100px;' /> Y: <input type='text' name='globe_yaxis' value='".$data['globe_yaxis']."' class='textbox' style='width:100px;' /></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['globe_044'].":</td>\n";
			echo "<td class='tbl'>\n";
			echo "<label><input type='checkbox' name='globe_privilegies' value='yes'".($data['globe_privilegies'] == "1" ? " checked='checked'" : "")." /> ".$locale['globe_045']."</label><br />\n";
			echo "<label><input type='checkbox' name='globe_draft' value='yes'".($data['globe_draft'] == "1" ? " checked='checked'" : "")." /> ".$locale['globe_046']."</label>\n";
			echo "</td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td align='center' class='tbl' colspan='2'>\n";
			echo "<input type='hidden' name='globe_id' value='".$data['globe_id']."' />\n";
			echo "<input type='hidden' name='globe_backlink' value='".$data['globe_backlink']."' />\n";
			echo "<input type='hidden' name='globe_last_check' value='".$data['globe_last_check']."' />\n";
			echo "<input type='submit' name='globe_return' value='".$locale['globe_047']."' class='button' />\n";
			echo "&nbsp;<input type='submit' name='globe_save' value='".$locale['globe_048']."' class='button' />\n";
			echo "</td>\n";
			echo "</tr>\n</table>\n</form>\n";
			closetable();
			echo "<script type='text/javascript'>
			function VerifyForm(frm) {
				if (frm.globe_site_url==\"\") {
					alert(\"".$locale['globe_049']."\");
					return false;
				}
			}
			</script>\n";
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	} else {
		redirect(FUSION_SELF.$aidlink);
	}
} else {
	$rows = dbcount("(globe_id)", DB_SMART_GLOBE);
	if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }
	opentable($locale['globe_050']);
		if ($rows) {
			$result = dbquery("SELECT * FROM ".DB_SMART_GLOBE." ORDER BY globe_draft ASC, globe_privilegies DESC, globe_last_check DESC, globe_datestamp DESC LIMIT ".$_GET['rowstart'].",$sites_per_page");
			echo "<table cellpadding='0' cellspacing='1' width='100%' class='center tbl-border'>\n<tr>\n";
			echo "<td class='tbl2'><strong>".$locale['globe_017']."</strong></td>\n";
			echo "<td class='tbl2'><strong>".$locale['globe_018']."</strong></td>\n";
			echo "<td class='tbl2'><strong>".$locale['globe_036']."</strong></td>\n";
			echo "<td class='tbl2'><strong>".$locale['globe_051']."</strong></td>\n";
			echo "<td class='tbl2'><strong>".$locale['globe_052']."</strong></td>\n";
			echo "<td class='tbl2'><strong>".$locale['globe_044']."</strong></td>\n";
			while ($data = dbarray($result)) {
				$class = ($data['globe_draft'] == "1" ? "tbl2" : "tbl");
				echo "</tr>\n<tr>\n";
				echo "<td class='".$class."'><img src='".(($data['globe_last_check'] + 604800 < time()) || ($data['globe_backlink'] == "0") ? SMART_GLOBE_IMAGES."globe/warning.png" : SMART_GLOBE_IMAGES."globe/success.png")."' style='border:none;vertical-align:middle;' alt='".$locale['globe_053']."' /> <a href='".$data['globe_site_url']."' title='".$locale['globe_054'].": ".$data['globe_site_url']."' target='_blank'>".trimlink($data['globe_site_url'], 20)."</a></td>\n";
				echo "<td class='".$class."'>".$data['globe_location']."</td>\n";
				echo "<td class='".$class."'>".$data['globe_language']."</td>\n";
				echo "<td class='".$class."'>\n";
				echo "<img src='".($data['globe_draft'] == "1" ? SMART_GLOBE_IMAGES."globe/draft_on.png" : SMART_GLOBE_IMAGES."globe/draft_off.png")."' style='border:none;vertical-align:middle;' alt='".$locale['globe_055']."' title='".$locale['globe_055']."' />&nbsp;";
				echo "<img src='".($data['globe_privilegies'] == "1" ? SMART_GLOBE_IMAGES."globe/previlegies_on.png" : SMART_GLOBE_IMAGES."globe/previlegies_off.png")."' style='border:none;vertical-align:middle;' alt='".$locale['globe_056']."' title='".$locale['globe_056']."' />&nbsp;";
				echo "<img src='".($data['globe_backlink'] == "1" ? SMART_GLOBE_IMAGES."globe/backlink_on.png" : SMART_GLOBE_IMAGES."globe/backlink_off.png")."' style='border:none;vertical-align:middle;' alt='".$locale['globe_057']."' title='".$locale['globe_057']."' />";
				echo "</td>\n";
				echo "<td class='".$class."'>".showdate("shortdate", $data['globe_datestamp'])."</td>\n";
				echo "<td class='".$class."'><a href='".FUSION_SELF.$aidlink."&action=edit&globe_id=".$data['globe_id']."'><img src='".SMART_GLOBE_IMAGES."globe/edit.png' style='border:none;vertical-align:middle;' alt='".$locale['globe_058']."' title='".$locale['globe_058']."' /></a> <a href='".FUSION_SELF.$aidlink."&action=delete&globe_id=".$data['globe_id']."' onclick='return DeleteGlobelink()'><img src='".SMART_GLOBE_IMAGES."globe/delete.png' style='border:none;vertical-align:middle;' alt='".$locale['globe_059']."' title='".$locale['globe_059']."' /></a> <a href='".FUSION_SELF.$aidlink."&action=checklink&globe_id=".$data['globe_id']."'><img src='".SMART_GLOBE_IMAGES."globe/refresh.png' style='border:none;vertical-align:middle;' alt='".$locale['globe_060']."' title='".$locale['globe_060']."' /></a></td>\n";
			}
			echo "</tr>\n</table>\n";
			echo "<script type='text/javascript'>\nfunction DeleteGlobelink() {\n
			return confirm(\"".$locale['globe_061']."\");\n}\n
			</script>\n";
			if ($rows > $sites_per_page) echo "<div align='center' style='margin-top:5px;'>\n".makepagenav($_GET['rowstart'], $sites_per_page, $rows, 3, FUSION_SELF.$aidlink."&amp;")."\n</div>\n";
		} else {
			echo "<div style='text-align:center;'><br />".$locale['globe_062']."<br /><br /></div>\n";
		}
	closetable();
}

require_once THEMES."templates/footer.php";
?>