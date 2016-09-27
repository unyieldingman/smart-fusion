<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: DocumentErrors.class.php
| Author: FILON
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

class RewriteHtaccess {
	private $_handle	= "";
	private $_content	= "";
	
	public function __construct($mode="a+") {
		$this->_handle = fopen(BASEDIR.".htaccess", $mode);
		$this->_content = file_get_contents(BASEDIR.".htaccess");
		return $this->_handle;
	}
	
	public function updateErrorRecord($error, $new_error, $new_url) {
		$this->_content = preg_replace("#ErrorDocument[\s+]".$error."[\s+]\S+[\r\n]?#si", "ErrorDocument ".$new_error." /".$new_url, $this->_content);
		return true;
	}
	
	public function addErrorRecord($error, $url) {
		$this->_content .= "\r\nErrorDocument ".$error." /".$url;
		return true;
	}
	
	public function deleteErrorRecord($error) {
		$this->_content = preg_replace("#ErrorDocument[\s+]".$error."[\s+]\S+(\r\n)?#si", "", $this->_content);
		return true;
	}
	
	public function __destruct() {
		if (!empty($this->_content)) {
			ftruncate($this->_handle, 0);
			fwrite($this->_handle, $this->_content);
		}
		fclose($this->_handle);
		unset($this->_handle, $this->_content);
	}
}

class DocumentErrors {
	private $_code			= "";
	private static $_html	= "";

	public function __construct() {
		global $settings;
		$codeSended = $this->_checkCodeCorrectness($_GET['error_code']);
		$this->_code = ($codeSended ? $_GET['error_code'] : (isset($_SERVER['REDIRECT_STATUS']) ? $_SERVER['REDIRECT_STATUS'] : "200"));
		if (!$codeSended) { redirect($settings['siteurl']."errors.php?error_code=".$this->_code); }
		return true;
	}
	
	public function setHTTPStatus() {
		switch ($this->_code) {
			// Client errors
			case "400": $instruct = "HTTP/1.1 400 Bad Request"; break;
			case "401": $instruct = "HTTP/1.1 401 Unauthorized"; break;
			case "402": $instruct = "HTTP/1.1 402 Payment Required"; break;
			case "403": $instruct = "HTTP/1.1 403 Forbidden"; break;
			case "404": $instruct = "HTTP/1.1 404 Not Found"; break;
			case "405": $instruct = "HTTP/1.1 405 Method Not Allowed"; break;
			case "406": $instruct = "HTTP/1.1 406 Not Acceptable"; break;
			case "407": $instruct = "HTTP/1.1 407 Proxy Authentication Required"; break;
			case "408": $instruct = "HTTP/1.1 408 Request Time-out"; break;
			case "409": $instruct = "HTTP/1.1 409 Conflict"; break;
			case "410": $instruct = "HTTP/1.1 410 Gone"; break;
			case "411": $instruct = "HTTP/1.1 411 Length Required"; break;
			case "412": $instruct = "HTTP/1.1 412 Precondition Failed"; break;
			case "413": $instruct = "HTTP/1.1 413 Request Entity Too Large"; break;
			case "414": $instruct = "HTTP/1.1 414 Request-URI Too Large"; break;
			case "415": $instruct = "HTTP/1.1 415 Unsupported Media Type"; break;
			case "416": $instruct = "HTTP/1.1 416 Requested Range Not Satisfiable"; break;
			case "417": $instruct = "HTTP/1.1 417 Expectation Failed"; break;
			// Server errors
			case "500": $instruct = "HTTP/1.1 500 Internal Server Error"; break;
			case "501": $instruct = "HTTP/1.1 501 Not Implemented"; break;
			case "502": $instruct = "HTTP/1.1 502 Bad Gateway"; break;
			case "503": $instruct = "HTTP/1.1 503 Service Unavailable"; break;
			case "504": $instruct = "HTTP/1.1 504 Gateway Time-out"; break;
			case "505": $instruct = "HTTP/1.1 505 HTTP Version Not Supported"; break;
			default: $instruct = "HTTP/1.1 200 OK"; break;
		}
		header($instruct);
		return true;
	}
	
	public function getErrorInfo() {
		global $locale;
		$error_info = array();
		$error_info['code'] = $this->_code;
		$error_info['message'] = (isset($locale['error_'.$this->_code]) ? $locale['error_'.$this->_code] : $locale['error_200']);
		return $error_info;
	}
	
	public static function renderErrorPage($error_info) {
		global $locale;
		DocumentErrors::$_html .= "<table style='width:100%;height:100%'>\n<tr>\n<td>\n";
		DocumentErrors::$_html .= "<table cellpadding='0' cellspacing='1' width='80%' class='tbl-border center'>\n<tr>\n";
		DocumentErrors::$_html .= "<td class='tbl1'>\n<table cellspacing='0' cellpadding='0' width='100%' class='center'>\n<tr>\n";
		DocumentErrors::$_html .= "<td align='right' class='tbl1' style='width:40%;'>\n";
		DocumentErrors::$_html .= "<img src='".IMAGES."error_message.png' alt='".$locale['error_000']."' />\n";
		DocumentErrors::$_html .= "</td>\n<td align='left' class='tbl1' style='padding-left:40px;width:60%;'>\n";
		DocumentErrors::$_html .= "<span style='font-size:48px;'>".$locale['error_001'].$error_info['code']."</span><br />".$error_info['message'];
		DocumentErrors::$_html .= "<br /><br />".THEME_BULLET." <a href='".BASEDIR."index.php'>".$locale['error_002']."</a>\n";
		DocumentErrors::$_html .= "</td>\n</tr>\n</table>\n<br /><br />";
		DocumentErrors::$_html .= "<div style='text-align:center;'>Powered by <a href='http://www.php-fusion.co.uk'>PHP-Fusion</a> &copy; 2003 - ".date("Y")."</div>";
		DocumentErrors::$_html .= "<br /><br />\n</td>\n</tr>\n</table>\n";
		DocumentErrors::$_html .= "</td>\n</tr>\n</table>\n";
		echo DocumentErrors::$_html;
	}
	
	private function _checkCodeCorrectness($error_code) {
		$status = (isset($error_code) && isnum($error_code) && (strlen($error_code) == 3) ? true : false);
		return $status;
	}
}
?>