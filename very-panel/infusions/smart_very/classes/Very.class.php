<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: Very.class.php
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
if (!defined("IN_FUSION")) { die("Access Denied"); }

require_once INFUSIONS."smart_very/infusion_db.php";

class Very {
	private $_parent = null;
	protected $_data = null;
	
	function __construct() {
		try {
			// Detect parent class
			$this->_parent = (get_class() == get_class($this) ? true : false);
			
			// Check if table exists
			$result = dbquery("SHOW TABLES LIKE '".DB_VERY_API."'");
			if (!dbrows($result)) {
				if (!$this->_parent) { $this->_callMessageRendering(); }
				throw new Exception("database '".DB_VERY_API."' does not exists.");
			}

			// Render error messages if state given
			if ($this->_parent && isset($_GET['status'])) { $this->_renderMessage(); }
			
			// Update settings if state given
			if ($this->_parent && isset($_POST['savesettings'])) { $this->_updateSettings(); }
			
			// Get Very parameters from the table
			$this->_data = dbarray(dbquery("SELECT very_panel_name, very_api_type, very_api_key, very_profile, very_mode, very_siteurl, very_synchronize, very_secureip, very_prune, very_cache, very_debug, very_theme FROM ".DB_VERY_API));
		} catch (Exception $e) {
			echo "<strong>Notice:</strong> {$e->getMessage()}\n";
		}
	}
	
	public function renderPage() {
		global $aidlink, $locale;
		
		$html = "<form name='inputform' method='post' action='".FUSION_SELF.$aidlink."'>\n";
		$html .= "<table cellpadding='0' cellspacing='0' width='600' class='center'>\n<tr>\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_006'].":</td>\n";
		$html .= "<td width='70%' class='tbl'><input type='text' name='very_panel_name' value='".$this->_data['very_panel_name']."' maxlength='240' class='textbox' style='width:230px;' /></td>\n";
		$html .= "</tr>\n<tr>\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_007'].":</td>\n";
		$html .= "<td width='70%' class='tbl'>\n<select name='very_api_type' id='very_api_type' class='textbox' style='width:230px;'>\n";
		$html .= "<option value='0'".($this->_data['very_api_type'] == "0" ? " selected='selected'" : "").">".$locale['very_008']."</option>\n";
		$html .= "<option value='1'".($this->_data['very_api_type'] == "1" ? " selected='selected'" : "").">".$locale['very_009']."</option>\n";
		$html .= "</select>\n</td>\n";
		$html .= "</tr>\n<tr id='api_key_input'".($this->_data['very_api_type'] == "0" ? " style='display:none;'" : "").">\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_010'].":</td>\n";
		$html .= "<td width='70%' class='tbl'><input type='text' name='very_api_key' value='".$this->_data['very_api_key']."' maxlength='10' class='textbox' style='width:230px;' /> <a href='http://smart-fusion.ru/api.php' target='_blank' title='".$locale['very_011']."'><strong>".$locale['very_012']."</strong></a></td>\n";
		$html .= "</tr>\n<tr>\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_013'].":</td>\n";
		$html .= "<td width='70%' class='tbl'>\n<select name='very_profile' id='very_profile' class='textbox' style='width:230px;'>\n";
		$html .= "<option value='0'".($this->_data['very_profile'] == "0" ? " selected='selected'" : "").">".$locale['very_014']."</option>\n";
		$html .= "<option value='1'".($this->_data['very_profile'] == "1" ? " selected='selected'" : "").">".$locale['very_015']."</option>\n";
		$html .= "</select>\n</td>\n";
		$html .= "</tr>\n<tr class='custom_profile'".($this->_data['very_profile'] == "0" ? " style='display:none;'" : "").">\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_016'].":</td>\n";
		$html .= "<td width='70%' class='tbl'>\n<select name='very_mode' class='textbox' style='width:230px;' />\n";
		$html .= "<option value='0'".($this->_data['very_mode'] == "0" ? " selected='selected'" : "").">".$locale['very_017']."</option>\n";
		$html .= "<option value='1'".($this->_data['very_mode'] == "1" ? " selected='selected'" : "").">".$locale['very_018']."</option>\n";
		$html .= "</select>\n</td>\n";
		$html .= "</tr>\n<tr class='custom_profile'".($this->_data['very_profile'] == "0" ? " style='display:none;'" : "").">\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_019'].":</td>\n";
		$html .= "<td width='70%' class='tbl'><input type='text' name='very_siteurl' value='".$this->_data['very_siteurl']."' maxlength='255' class='textbox' style='width:230px;' /><br /><span class='small'>".$locale['very_020']."</span></td>\n";
		$html .= "</tr>\n<tr class='custom_profile'".($this->_data['very_profile'] == "0" ? " style='display:none;'" : "").">\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_021'].":</td>\n";
		$html .= "<td width='70%' class='tbl'>\n<select name='very_synchronize' class='textbox'>\n";
		$html .= "<option value='0'".($this->_data['very_synchronize'] == "0" ? " selected='selected'" : "").">".$locale['very_022']."</option>\n";
		$html .= "<option value='1'".($this->_data['very_synchronize'] == "1" ? " selected='selected'" : "").">".$locale['very_023']."</option>\n";
		$html .= "</select>\n <a href='#' class='trigger'><img src='".VERY_IMAGES."help.png' style='border:none;vertical-align:middle;' alt='' /><span class='tbl tbl-border hint'>".$locale['very_024']."</span></a>\n</td>\n";
		$html .= "</tr>\n<tr class='custom_profile'".($this->_data['very_profile'] == "0" ? " style='display:none;'" : "").">\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_025'].":</td>\n";
		$html .= "<td width='70%' class='tbl'>\n<select name='very_secureip' class='textbox'>\n";
		$html .= "<option value='0'".($this->_data['very_secureip'] == "0" ? " selected='selected'" : "").">".$locale['very_022']."</option>\n";
		$html .= "<option value='1'".($this->_data['very_secureip'] == "1" ? " selected='selected'" : "").">".$locale['very_023']."</option>\n";
		$html .= "</select>\n <a href='#' class='trigger'><img src='".VERY_IMAGES."help.png' style='border:none;vertical-align:middle;' alt='' /><span class='tbl tbl-border hint'>".$locale['very_026']."</span></a>\n</td>\n";
		$html .= "</tr>\n<tr class='custom_profile'".($this->_data['very_profile'] == "0" ? " style='display:none;'" : "").">\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_027'].":</td>\n";
		$html .= "<td width='70%' class='tbl'>\n<select name='very_prune' class='textbox'>\n";
		$html .= "<option value='0'".($this->_data['very_prune'] == "0" ? " selected='selected'" : "").">".$locale['very_022']."</option>\n";
		$html .= "<option value='1'".($this->_data['very_prune'] == "1" ? " selected='selected'" : "").">".$locale['very_023']."</option>\n";
		$html .= "</select>\n <a href='#' class='trigger'><img src='".VERY_IMAGES."help.png' style='border:none;vertical-align:middle;' alt='' /><span class='tbl tbl-border hint'>".$locale['very_028']."</span></a>\n</td>\n";
		$html .= "</tr>\n<tr class='custom_profile'".($this->_data['very_profile'] == "0" ? " style='display:none;'" : "").">\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_029'].":</td>\n";
		$html .= "<td width='70%' class='tbl'>\n<select name='very_cache' class='textbox'>\n";
		$html .= "<option value='0'".($this->_data['very_cache'] == "0" ? " selected='selected'" : "").">".$locale['very_022']."</option>\n";
		$html .= "<option value='1'".($this->_data['very_cache'] == "1" ? " selected='selected'" : "").">".$locale['very_023']."</option>\n";
		$html .= "</select>\n <a href='#' class='trigger'><img src='".VERY_IMAGES."help.png' style='border:none;vertical-align:middle;' alt='' /><span class='tbl tbl-border hint'>".$locale['very_030']."</span></a>\n</td>\n";
		$html .= "</tr>\n<tr class='custom_profile'".($this->_data['very_profile'] == "0" ? " style='display:none;'" : "").">\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_031'].":</td>\n";
		$html .= "<td width='70%' class='tbl'>\n<select name='very_debug' class='textbox'>\n";
		$html .= "<option value='0'".($this->_data['very_debug'] == "0" ? " selected='selected'" : "").">".$locale['very_022']."</option>\n";
		$html .= "<option value='1'".($this->_data['very_debug'] == "1" ? " selected='selected'" : "").">".$locale['very_023']."</option>\n";
		$html .= "</select>\n <a href='#' class='trigger'><img src='".VERY_IMAGES."help.png' style='border:none;vertical-align:middle;' alt='' /><span class='tbl tbl-border hint'>".$locale['very_032']."</span></a>\n</td>\n";
		$html .= "</tr>\n<tr class='custom_profile'".($this->_data['very_profile'] == "0" ? " style='display:none;'" : "").">\n";
		$html .= "<td width='30%' class='tbl'>".$locale['very_036'].":</td>\n";
		$html .= "<td width='70%' class='tbl'>\n<select id='themeSelect' name='very_theme' class='textbox' />\n";
		$html .= "<option value='default'".($this->_data['very_theme'] == "default" ? " selected='selected'" : "").">".$locale['very_037']."</option>\n";
		$html .= "<option value='light'".($this->_data['very_theme'] == "light" ? " selected='selected'" : "").">".$locale['very_038']."</option>\n";
		$html .= "<option value='mono'".($this->_data['very_theme'] == "mono" ? " selected='selected'" : "").">".$locale['very_039']."</option>\n";
		$html .= "</select>\n<div id='preview' class='example {$this->_data['very_theme']}'></div>\n</td>\n";
		$html .= "</tr>\n<tr>\n";
		$html .= "<td align='center' colspan='2' class='tbl'><br />";
		$html .= "<input type='submit' name='savesettings' value='".$locale['very_033']."' class='button' /></td>\n";
		$html .= "</tr>\n</table>\n</form>\n";
		
		// Render panel
		opentable($locale['very_005']);
		echo $html;
		closetable();
	}
	
	public function renderScripts() {
		$js = "<script type='text/javascript'>\n$('#very_api_type').change(function(){\n";
		$js .= "if ($(this).val()==1) {\n$('#api_key_input').show();\n} else {\n";
		$js .= "$('#api_key_input').hide();\n}\n});\n$('#very_profile').change(function(){\n";
		$js .= "if ($(this).val()==1) {\n$('tr.custom_profile').show();\n} else {\n";
		$js .= "$('tr.custom_profile').hide();\n}\n});\n$('.trigger').hover(function(){\n";
		$js .= "tip = $(this).children('span');\ntip.stop().fadeIn(400);\n}, function() {\n";
		$js .= "tip.stop().hide();\n}).mousemove(function(e) {\nvar tipWidth = tip.width();\n";
		$js .= "var tipHeight = tip.height();\nvar mouse_x = e.pageX + 20;\nvar mouse_y = e.pageY - tipHeight - 20;\n";
		$js .= "var tipVisX = $(window).width() - (mouse_x - tipWidth);\nif (tipVisX < 20) { mouse_x = e.pageX - tipWidth - 20; }\n";
		$js .= "tip.css({top: mouse_y, left: mouse_x});\n});\n</script>\n";
		$js .= "<script type='text/javascript'>\n$('#themeSelect').change(function(){\n";
		$js .= "$('#preview').removeClass().addClass('example '+$(this).val());\n});\n</script>\n";
		
		// Output JavaScript code
		$this->_tryAddToFooter($js);
	}
	
	public function renderStyles() {
		$style = "<style type='text/css'>.trigger, .trigger:hover, .trigger img {border:none;}.hint {position:absolute;display:none;max-width:300px;padding:10px;z-index:9999;}</style>";
		$style .= "<style type='text/css'>.example{position:relative;top:5px;display:inline-block;background-image:url(images/very_examples.png);width:90px;height:21px;margin-left:10px}.example.default {background-position: left top}.example.light{background-position: left -22px}.example.mono{background-position:left -44px}</style>";
		
		// Output styles
		add_to_head($style);
	}
	
	private function _renderMessage() {
		global $locale;
		
		try {
			if ($_GET['status'] == "su") { throw new Exception($locale['very_004']); }
		} catch (Exception $e) {
			echo "<div id='close-message'><div class='admin-message'>".$e->getMessage()."</div></div>\n";
		}
	}
	
	private function _updateSettings() {
		global $aidlink;
		
		$very_panel_name = stripinput($_POST['very_panel_name']);
		$api_key_type = (isset($_POST['very_api_type']) && isnum($_POST['very_api_type']) ? $_POST['very_api_type'] : "0");
		$very_api_key = (strlen($_POST['very_api_key']) == 10 ? stripinput($_POST['very_api_key']) : "");
		if (empty($very_api_key)) { $api_key_type = "0"; }
		$very_profile = (isset($_POST['very_profile']) && isnum($_POST['very_profile']) ? $_POST['very_profile'] : "0");
		$very_mode = (isset($_POST['very_mode']) && isnum($_POST['very_mode']) ? $_POST['very_mode'] : "0");
		$very_siteurl = (preg_match("#^(http(s?):\/\/([a-zA-Z0-9-])+(\.([a-zA-Z0-9-])+)*(\.([a-zA-Z0-9~\/])+)+)?$#si", $_POST['very_siteurl']) ? cleanurl($_POST['very_siteurl']) : "");
		$very_synchronize = (isset($_POST['very_synchronize']) && isnum($_POST['very_synchronize']) ? $_POST['very_synchronize'] : "0");
		$very_secureip = (isset($_POST['very_secureip']) && isnum($_POST['very_secureip']) ? $_POST['very_secureip'] : "0");
		$very_prune = (isset($_POST['very_prune']) && isnum($_POST['very_prune']) ? $_POST['very_prune'] : "0");
		$very_cache = (isset($_POST['very_cache']) && isnum($_POST['very_cache']) ? $_POST['very_cache'] : "0");
		$very_debug = (isset($_POST['very_debug']) && isnum($_POST['very_debug']) ? $_POST['very_debug'] : "0");
		$very_theme = (isset($_POST['very_theme']) && preg_match("#[a-z]+#si", $_POST['very_theme']) ? $_POST['very_theme'] : "default");
		
		// Update database values
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
			very_debug = '".$very_debug."',
			very_theme = '".$very_theme."'
		");
		redirect(FUSION_SELF.$aidlink."&status=su");
	}
	
	protected function _tryAddToFooter($content) {
		if (function_exists("add_to_footer")) { add_to_footer($content);
		} else { echo $content; }
	}
	
	protected function _callMessageRendering() {}
}

class VeryButton extends Very {
	private $_initialized = false;
	private $_parameters = array();
	const API_KEY = "B8F183CEE7";
	
	public function renderButton() {
		global $locale;
		
		try {
			// Check if the button does not rendered earlier
			if ($this->_initialized) { throw new Exception("button has been already rendered"); }
			$this->_initialized = true;
			
			// Output handlers
			add_to_head("<link rel='stylesheet' type='text/css' href='http://smart-fusion.ru/api/very/exterior.css' media='all' />");
			add_to_head("<script type='text/javascript' src='http://smart-fusion.ru/api/very/jquery.very.api.js'></script>");
			
			// Set parameters
			$this->_addParam("mode", "'".($this->_data['very_mode'] == "0" ? "horizontal" : "vertical")."'");
			$this->_addParam("sync", ($this->_data['very_synchronize'] == "0" ? "false" : "true"));
			$this->_addParam("secureIp", ($this->_data['very_secureip'] == "0" ? "false" : "true"));
			$this->_addParam("prune", ($this->_data['very_prune'] == "0" ? "false" : "true"));
			$this->_addParam("cache", ($this->_data['very_cache'] == "0" ? "false" : "true"));
			$this->_addParam("debug", ($this->_data['very_debug'] == "0" ? "false" : "true"));
			if (!empty($this->_data['very_siteurl'])) { $this->_addParam("siteUrl", "'".$this->_data['very_siteurl']."'"); }
			if (!empty($this->_data['very_theme'])) { $this->_addParam("theme", "'".$this->_data['very_theme']."'"); }

			// Generate content
			$html = "<div style='text-align:center;margin-top:10px;'>".$locale['very_040']."</div>\n";
			$html .= "<center id='very_button' style='margin:10px auto;'><img src='".VERY_IMAGES.($this->_data['very_theme'] == "mono" ? "very_mono_disabled.png" : "very_disabled.png")."' style='border:none;' alt='' /></center>\n";
			
			// Output panel
			openside($this->_data['very_panel_name']);
			echo $html;
			closeside();
			
			// Render scripts
			$js = "<script type='text/javascript'>Very.Run('very_button', '".($this->_data['very_api_type'] == "1" ? $this->_data['very_api_key'] : self::API_KEY)."', {type: 'very'".$this->_getParamsLine()."});</script>\n";
		
			// Output scripts
			$this->_tryAddToFooter($js);
		} catch (Exception $e) {
			echo "<strong>Notice:</strong> {$e->getMessage()}\n";
		}
	}
	
	protected function _callMessageRendering() {
		global $locale;
		
		// Prevent reinitialization
		$this->_initialized = true;
		
		// Render content
		$title = (iADMIN ? $locale['very_034'] : $locale['very_035']);
		$html = "<div style='text-align:center;margin:10px auto;'><img src='".VERY_IMAGES."very_disabled.png' style='border:none;' alt='".$title."' title='".$title."' /></div>\n";
		
		// Output panel
		openside($locale['very_001']);
		echo $html;
		closeside();
	}
	
	private function _addParam($name, $value) {
		try {
			// Notice if parameter exists
			if (isset($this->_parameters[$name])) { throw new Exception("parameter '{$name}' already exists in this case"); }
			
			// Add a new one
			$this->_parameters[$name] = $value;
		} catch (Exception $e) {
			echo "<strong>Notice:</strong> {$e->getMessage()}\n";
		}
	}
	
	private function _getParamsLine() {
		$line = array(null);
		foreach ($this->_parameters as $key=>$value) {
			array_push($line, "{$key}: {$value}");
		}
		return implode(", ", $line);
	}
}
?>