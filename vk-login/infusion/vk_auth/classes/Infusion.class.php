<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2016 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: Infusion.class.php
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

class Infusion {
	const BOUNDARY_CORE_VERSION = 70200;
	
	private $_settings;
	
	function __construct() {
		
		// Directories definitions
		$this->_addDefinition("INFUSION_FOLDER", "vk_auth");
		$this->_addDefinition("INFUSION_BASEDIR", sprintf("%s%s/", INFUSIONS, INFUSION_FOLDER));
		$this->_addDefinition("INFUSION_CLASSES", sprintf("%sclasses/", INFUSION_BASEDIR));
		$this->_addDefinition("INFUSION_LOCALE", sprintf("%slocale/", INFUSION_BASEDIR));
		
		// Tables definitions
		$this->_addDefinition("DB_TOKENS", DB_PREFIX."tokens");
		$this->_addDefinition("DB_SETTINGS_INF", DB_PREFIX."settings_inf");
		
		// Declare settings
		$this->_settings = array();
		
		// Include localization files
		$this->_includeLocale();
	}
	
	public function getSettingsArray($force_update = false) {
		
		if (empty($this->_settings) || $force_update === true) {
			$this->_updateSettings();
		}
		
		return $this->_settings;
	}
	
	public function setSettingsArray($settings) {
		$status_code = 0;
		if (is_array($settings)) {
			foreach ($settings as $key => $value) {
				$key = stripinput($key);
				$value = stripinput($value);
				
				$result = dbquery(
					"UPDATE ".DB_SETTINGS_INF." SET
						settings_value = '".$value."'
					WHERE settings_name = '".$key."' AND settings_inf = '".INFUSION_FOLDER."'
					LIMIT 1"
				);
				
				if ($result) {
					$this->_settings[$key] = $value;
				} else {
					$status_code = 1;
				}
			}
		}
		return $status_code;
	}
	
	public function getCoreVersionNumber() {
		global $settings;
		
		return (int)str_replace(".", "", $settings['version']);
	}
	
	private function _updateSettings() {
		$result = dbquery("SELECT `settings_name`, `settings_value` FROM ".DB_SETTINGS_INF." WHERE settings_inf = '".INFUSION_FOLDER."'");
		while ($data = dbarray($result)) {
			$this->_settings[$data['settings_name']] = $data['settings_value'];
		}
	}
	
	private function _addDefinition($name, $value) {
		if (!defined($name)) {
			define($name, $value);
		}
	}
	
	private function _includeLocale() {
		global $settings, $locale;
		
		if (file_exists(INFUSION_LOCALE.$settings['locale'].".php")) {
			include_once INFUSION_LOCALE.$settings['locale'].".php";
		} else {
			include_once INFUSION_LOCALE."English.php";
		}
	}
}
?>
