<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: Glider.class.php
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

class Glider {
	private $_hash;
	const CODE_WORD = DB_PREFIX;
	
	function __construct() {
		// Generate captcha hash
		$length = $this->_getCodeLength();
		$this->_hash = $this->_generateSalt($length);
		$this->_hash .= $this->_cryptXOR(self::CODE_WORD, $this->_hash);
	}
	
	function render($settings=array()) {
		global $locale;
		
		// Get random class name
		$class = $this->_getRandomClass();
		// Trick for PHP-Fusion 7.00.xx support
		$func = (function_exists("add_to_footer") ? "add_to_footer" : "echo");
		// Output HTML
		echo "<div class='{$class}'></div>\n";
		echo "<input type='hidden' name='captcha_code' value='' />"; // For comments system correct working
		// Adding resources to head section
		add_to_head("<link rel='stylesheet' type='text/css' href='".GLIDER_SHEETS."styles.css' />");
		add_to_head("<script type='text/javascript' src='".GLIDER_JSCRIPTS."jquery.cookie.min.js'></script>");
		add_to_head("<script type='text/javascript' src='".GLIDER_JSCRIPTS."jquery.ui.min.js'></script>");
		add_to_head("<script type='text/javascript' src='".GLIDER_JSCRIPTS."jquery.ui.touch.js'></script>");
		add_to_head("<script type='text/javascript' src='".GLIDER_JSCRIPTS."jquery.glider.min.js'></script>");
		// Check settings
		$this->_checkSetting($settings['theme'], "silverglide");
		$this->_checkSetting($settings['width'], "296", false);
		$this->_checkSetting($settings['locale'], array($locale['glider_000'], $locale['glider_001'], $locale['glider_002']));
		// Build settings line
		$this->_buildSettingsLine($settings);
		// Adding script initialization
		$func("<script type='text/javascript'>Glider.Init('.{$class}', '{$this->_hash}', {{$settings}});</script>");
	}
	
	public static function validate() {
		try {
			// Check cookie existing
			if (!isset($_COOKIE['gl_token']) || empty($_COOKIE['gl_token'])) { throw new Exception(); }
			// Get salt and crypted hash from $_COOKIE
			$salt_length = self::_getCodeLength();
			$salt = substr($_COOKIE['gl_token'], 0, $salt_length);
			$hash = substr($_COOKIE['gl_token'], $salt_length, (strlen($_COOKIE['gl_token']) - $salt_length + 1));
			// Test hash encoding
			if (!base64_decode($hash, true)) { throw new Exception(); }
			// Encrypt code word
			$code_word = self::_cryptXOR($hash, self::CODE_WORD, -1);
			// Check salt correctness
			if ($code_word != $salt) { throw new Exception(); }
			// Clear cookies
			unset($_COOKIE['gl_token']);
			setcookie("gl_token", "", (time() - 3600));
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	private static function _getCodeLength() {
		$code_line = hash("adler32", "{$_SERVER['HTTP_USER_AGENT']}{$_SERVER['LOCAL_ADDR']}{$_SERVER['REMOTE_ADDR']}");
		preg_match_all("#[0-9]#", $code_line, $numbers);
		return array_sum($numbers[0]);
	}
	
	private function _generateSalt($length) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
		$size = strlen($chars); $salt = "";
		for($i=0;$i<$length;$i++) {
			$salt .= $chars[rand(0, ($size - 1))];
		}
		return $salt;
	}
	
	private static function _cryptXOR($key, $salt, $direction=1) {
		$func = array(); $xor_string = "";
		if ($direction == 1) { array_push($func, "strrev", "base64_encode");
		} else { array_push($func, "base64_decode", "strrev"); }
		$key = $func[0]($key); $salt = strrev($salt);
		$max_length = max(strlen($key), strlen($salt));
		for ($i=0;$i<$max_length;$i++) {
			$xor_string .= chr(((isset($key{$i}) ? ord($key{$i}) : 0) ^ (isset($salt{$i}) ? ord($salt{$i}) : 0)));
		}
		return $func[1]($xor_string);
	}
	
	private function _getRandomClass($prefix="glider_") {
		return uniqid($prefix);
	}
	
	private function _checkSetting(&$setting, $default="", $quotes=true) {
		$var = (isset($setting) ? "setting" : "default");
		if ($quotes) {
			if (is_array(${$var})) {
				foreach (${$var} as $key=>$value) {
					${$var}[$key] = "'{$value}'";
				}
			} else {
				${$var} = "'".${$var}."'";
			}
		}
		$setting = (isset($setting) ? $setting : $default);
	}
	
	private function _buildSettingsLine(&$settings) {
		foreach ($settings as $key=>$value) {
			$settings[$key] = "{$key}: ".(is_array($value) ? "[".implode(", ", $value)."]" : $value);
		}
		$settings = implode(", ", $settings);
	}
}
?>