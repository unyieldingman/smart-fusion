<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2014 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_admin.php
| Author: FDTD Designer
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

if (!checkrights("NFP") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) { redirect(BASEDIR); }

require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."news_feed_pipe/infusion_db.php";

if (isset($_GET['status']) && !isset($message)) {
	if ($_GET['status'] == "tu") {
		$message = $locale['pipe_002'];
	} elseif ($_GET['status'] == "tc") {
		$message = $locale['pipe_003'];
	} elseif ($_GET['status'] == "fs") {
		$message = $locale['pipe_004'];
	} elseif ($_GET['status'] == "fu") {
		$message = $locale['pipe_005'];
	} elseif ($_GET['status'] == "fd") {
		$message = $locale['pipe_006'];
	} elseif ($_GET['status'] == "nu") {
		$message = $locale['pipe_007'];
	}
	if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}

if (isset($_GET['error']) && !isset($message)) {
	if ($_GET['error'] == 1) {
		$message = $locale['pipe_008'];
	} elseif ($_GET['error'] == 2) {
		$message = $locale['pipe_009'];
	}
	if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}

if (isset($_POST['save_tags'])) {
	$tag_category_id = isset($_POST['tag_category_id']) && isnum($_POST['tag_category_id']) ? $_POST['tag_category_id'] : 0;
	$tag_names = isset($_POST['tag_names']) && !empty($_POST['tag_names']) ? explode(",", $_POST['tag_names']) : array();
	array_filter($tag_names);
	
	$insert_values = array();
	foreach ($tag_names as $tag_name) {
		$insert_values[] = "('".$tag_category_id."', '".stripinput($tag_name)."')";
	}
	$insert_values = implode(",", $insert_values);
	
	// Delete previous tags
	$result = dbquery("DELETE FROM ".DB_PIPE_TAGS." WHERE tag_category_id='".$tag_category_id."'");
	
	// Insert new tags
	if (!empty($insert_values)) {
		$result = dbquery("INSERT INTO ".DB_PIPE_TAGS." (`tag_category_id`, `tag_name`) VALUES ".$insert_values);
	}
	redirect(FUSION_SELF.$aidlink."&amp;status=tu");
}
// Deleting tags for all non-existing news categories
elseif (isset($_POST['clear_tags_history'])) {
	$result = dbquery(
		"DELETE FROM ".DB_PIPE_TAGS." WHERE tag_category_id NOT IN(
			SELECT DISTINCT news_cat_id FROM ".DB_NEWS_CATS."
		)"
	);
	redirect(FUSION_SELF.$aidlink."&amp;status=tc");
}
// Deleting existing feed
elseif (isset($_POST['delete_feed'])) {
	$feed_id = isset($_POST['feed_id']) && isnum($_POST['feed_id']) ? $_POST['feed_id'] : 0;
	$result = dbquery("DELETE FROM ".DB_PIPE_FEEDS." WHERE feed_id='".$feed_id."' LIMIT 1");
	redirect(FUSION_SELF.$aidlink."&amp;status=fd");
}
// Submitting new feed
elseif (isset($_POST['save_feed'])) {
	$feed_url = isset($_POST['feed_url']) ? $_POST['feed_url'] : "";
	
	$error = false;
	if (!preg_match("#https?\:\/\/.*?\..*?\/.*?$#si", $feed_url)) { $error = 1;
	} elseif (!($xml_string = @file_get_contents($feed_url))) { $error = 2; }
	
	if ($error) {
		redirect(FUSION_SELF.$aidlink."&amp;error=".$error);
	} else {
		$xml = simplexml_load_string($xml_string);
		$feed_title = stripinput(isset($xml->channel->title) ? iconv("utf-8", $locale['charset']."//IGNORE", $xml->channel->title) : preg_replace("#https?\:\/\/(.*?)\.(.*?)\/.*?$#si", "\\1.\\2", $feed_url));

		if (isset($_POST['feed_id']) && isnum($_POST['feed_id'])) {
			$result = dbquery("UPDATE ".DB_PIPE_FEEDS." SET feed_title='".$feed_title."', feed_url='".stripinput($feed_url)."' WHERE feed_id='".$_POST['feed_id']."' LIMIT 1");
			redirect(FUSION_SELF.$aidlink."&amp;status=fu");
		} else {
			$result = dbquery("INSERT INTO ".DB_PIPE_FEEDS." (`feed_title`, `feed_url`) VALUES ('".$feed_title."', '".stripinput($feed_url)."')");
			redirect(FUSION_SELF.$aidlink."&amp;status=fs");
		}
	}
}
// Parsing news feeds
elseif (isset($_POST['parse_news'])) {
	// Get list of associated tags
	$result = dbquery("SELECT tag_category_id, tag_name FROM ".DB_PIPE_TAGS);
	$tag_indexer = array(); $tag = array();
	while ($data = dbarray($result)) {
		$cat_id = (int)$data['tag_category_id'];
		if (!isset($tag[$cat_id])) { $tag[$cat_id] = array(); }
		$tag[$cat_id][] = $data['tag_name'];
	}
	$tag_indexer = array_keys($tag);
	array_unique($tag_indexer);
	
	// Parse RSS-feeds
	$result = dbquery("SELECT DISTINCT feed_url FROM ".DB_PIPE_FEEDS." ORDER BY feed_id ASC");
	while ($feed = dbarray($result)) {
		if (!($xml_string = @file_get_contents($feed['feed_url']))) { continue; }
		$xml = simplexml_load_string($xml_string);
		if (!isset($xml->channel)) { continue; }
		
		// Feed informatiom
		$feed_source = preg_replace("#https?\:\/\/(.*?)\.(.*?)\/.*?$#si", "\\1.\\2", $feed['feed_url']);
		foreach ($xml->channel->item as $item) {
			
			// Parse title and description
			$title = isset($item->title) ? stripinput(iconv("utf-8", $locale['charset']."//IGNORE", $item->title)) : NULL;
			$description = isset($item->description) ? strip_tags(stripinput(iconv("utf-8", $locale['charset']."//IGNORE", $item->description))) : NULL;
			
			// Get category
			$category_id = NULL;
			foreach ($tag_indexer as $cat_id) {
				$overlap = false;
				foreach ($tag[$cat_id] as $keyword) {
					if (stripos($title, $keyword) !== false || stripos($description, $keyword) !== false) {
						$overlap = true;
						break;
					}
				}
				if ($overlap) {
					$category_id = $cat_id;
					break;
				}
			}
			
			// Submit news to the database
			if ($category_id != NULL && $title != NULL && $description != NULL && !dbcount("(`news_id`)", DB_NEWS, "news_subject='".$title."'")) {
				$result2 = dbquery("INSERT INTO ".DB_NEWS." (news_subject, news_cat, news_news, news_extended, news_breaks, news_name, news_datestamp, news_start, news_end, news_image, news_image_t1, news_image_t2, news_visibility, news_draft, news_sticky, news_reads, news_allow_comments, news_allow_ratings, news_source) VALUES ('$title', '$category_id', '$description', '$description', 'y', '1', '".time()."', '0', '0', '', '', '', '0', '0', '0', '0', '1', '1', '$feed_source')");
			}
		}
		unset($xml_string, $xml);
	}
	redirect(FUSION_SELF.$aidlink."&amp;status=nu");
} else {
	// Update message
	$rows = dbcount("(`feed_id`)", DB_PIPE_FEEDS);
	opentable($locale['pipe_010']);
	echo "<form name='inputform' method='post' action='".FUSION_SELF.$aidlink."'>\n";
	echo "<table cellpadding='0' cellspacing='0' width='100%' class='center'>\n<tr>\n";
	echo "<td width='80%' class='tbl' valign='top'><h3>".sprintf($locale['pipe_011'], $rows)."</h3>".($rows ? sprintf($locale['pipe_012'], $locale['pipe_014']) : $locale['pipe_013'])."</td>\n";
	echo "<td class='tbl' valign='middle'>\n";
	echo "<input type='submit' name='parse_news' value='".$locale['pipe_014']."' class='button'".(!$rows ? " disabled='disabled'" : "")." /></td>\n";
	echo "</tr>\n</table>\n</form>\n";
	closetable();
	
	// List of available news categories
	$result = dbquery("SELECT news_cat_id, news_cat_name FROM ".DB_NEWS_CATS." ORDER BY news_cat_name");
	if (dbrows($result)) {
		$select_list = ""; $category_name = ""; $current_category_id = isset($_POST['category_id']) && isnum($_POST['category_id']) ? $_POST['category_id'] : 0;
		while ($data = dbarray($result)) {
			if ($data['news_cat_id'] == $current_category_id) { $category_name = $data['news_cat_name']; }
			$select_list .= "<option value='".$data['news_cat_id']."'".($data['news_cat_id'] == $current_category_id ? " selected='selected'" : "").">".$data['news_cat_name']."</option>\n";
		}
		opentable($locale['pipe_015']);
		echo "<div style='text-align:center'>\n<form name='inputform' method='post' action='".FUSION_SELF.$aidlink."'>\n";
		echo "<select name='category_id' class='textbox' style='width:250px'>\n".$select_list."</select>\n";
		echo "<input type='submit' name='edit_category_tags' value='".$locale['pipe_016']."' class='button' />\n";
		echo "<input type='submit' name='clear_tags_history' value='".$locale['pipe_017']."' class='button' />\n";
		echo "</form>\n</div>\n";
		closetable();
	}

	// Editing tags for selected news category
	if (isset($_POST['edit_category_tags']) && isset($_POST['category_id']) && isnum($_POST['category_id'])) {
		$result = dbquery("SELECT tag_name FROM ".DB_PIPE_TAGS." WHERE tag_category_id='".$_POST['category_id']."' ORDER BY tag_id ASC");
		$tags = array();
		while ($data = dbarray($result)) {
			$tags[] = $data['tag_name'];
		}
		add_to_head("<link rel='stylesheet' type='text/css' href='".INFUSION_HOME."plugins/tagsinput/styles.css'>");
		add_to_head("<script type='text/javascript' src='".INFUSION_HOME."plugins/tagsinput/jquery.tagsinput.min.js'></script>");
		opentable($locale['pipe_018']);
		echo "<form name='inputform' method='post' action='".FUSION_SELF.$aidlink."'>\n";
		echo "<table cellpadding='0' cellspacing='0' width='400' class='center'>\n<tr>\n";
		if (isset($category_name) && !empty($category_name)) {
			echo "<td width='130' class='tbl'>".$locale['pipe_019'].":</td>\n";
			echo "<td class='tbl'>".$category_name."</td>\n";
			echo "</tr>\n<tr>\n";
		}
		echo "<td width='130' class='tbl'>".$locale['pipe_020'].":</td>\n";
		echo "<td class='tbl'><textarea name='tag_names' rows='5' class='textbox' style='width:98%'>".implode(",", $tags)."</textarea></td>\n";
		echo "</tr>\n<tr>\n";
		echo "<td align='center' colspan='2' class='tbl'><br />\n";
		echo "<input type='hidden' name='tag_category_id' value='".$_POST['category_id']."' />\n";
		echo "<input type='submit' name='save_tags' value='".$locale['pipe_021']."' class='button' /></td>\n";
		echo "</tr>\n</table>\n</form>\n";
		echo "<script type='text/javascript'>\n$(function(){\n";
		echo "$('textarea[name=tag_names]').tagsInput({width:'98%',height:'auto'});\n";
		echo "});\n</script>";
		closetable();
	}
	
	// List of available news categories
	$result = dbquery("SELECT feed_id, feed_title, feed_url FROM ".DB_PIPE_FEEDS." ORDER BY feed_id DESC");
	if (dbrows($result)) {
		$select_list = ""; $current_feed = isset($_POST['feed_id']) && isnum($_POST['feed_id']) ? $_POST['feed_id'] : 0; $feed_url = "";
		while ($data = dbarray($result)) {
			if ($data['feed_id'] == $current_feed) { $feed_url = $data['feed_url']; }
			$select_list .= "<option value='".$data['feed_id']."'".($data['feed_id'] == $current_feed ? " selected='selected'" : "").">".$data['feed_title']."</option>\n";
		}
		opentable($locale['pipe_022']);
		echo "<div style='text-align:center'>\n<form name='inputform' method='post' action='".FUSION_SELF.$aidlink."'>\n";
		echo "<select name='feed_id' class='textbox' style='width:250px'>\n".$select_list."</select>\n";
		echo "<input type='submit' name='edit_feed' value='".$locale['pipe_016']."' class='button' />\n";
		echo "<input type='submit' name='delete_feed' value='".$locale['pipe_023']."' class='button' onclick='return DeleteFeed();' />\n";
		echo "</form>\n</div>\n";
		echo "<script type='text/javascript'>\nfunction DeleteFeed(){\n";
		echo "return confirm(\"".$locale['pipe_024']."\");\n}\n</script>\n";
		closetable();
	}
	
	// Editing feeds
	if (!isset($_POST['edit_feed']) || !isset($feed_url)) { $feed_url = ""; }
	opentable($locale['pipe_025']);
	echo "<form name='inputform' method='post' action='".FUSION_SELF.$aidlink."'>\n";
	echo "<table cellpadding='0' cellspacing='0' width='400' class='center'>\n<tr>\n";
	echo "<td width='130' class='tbl'>".$locale['pipe_026'].":</td>\n";
	echo "<td class='tbl'><input type='text' name='feed_url' value='".$feed_url."' class='textbox' style='width:200px;' /><br /><span class='small'>".$locale['pipe_027']."</span></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td align='center' colspan='2' class='tbl'><br />\n";
	if (isset($_POST['feed_id']) && isnum($_POST['feed_id'])) {
		echo "<input type='hidden' name='feed_id' value='".$_POST['feed_id']."' />\n";
	}
	echo "<input type='submit' name='save_feed' value='".$locale['pipe_021']."' class='button' /></td>\n";
	echo "</tr>\n</table>\n</form>\n";
	closetable();
}

require_once THEMES."templates/footer.php";
?>