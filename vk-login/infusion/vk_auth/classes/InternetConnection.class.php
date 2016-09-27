<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2016 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: InternetConnection.class.php
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

class InternetConnection {
	protected $_userAgent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
	private $_curl;
	
	function __construct() {
		$this->_curl = curl_init();
	}
	
	function __destruct() {
		curl_close($this->_curl);
	}
	
	public function get($url, $params = array()) {
		$query = http_build_query($params);
		
		$options = array(
			CURLOPT_URL				=>	empty($query) ? $url : sprintf("%s?%s", $url, $query),
			CURLOPT_RETURNTRANSFER	=>	true,
			CURLOPT_CONNECTTIMEOUT	=>	30,
			CURLOPT_USERAGENT		=>	$this->_userAgent,
			CURLOPT_FOLLOWLOCATION	=>	true
		);
		
		curl_setopt_array($this->_curl, $options);
		
		return curl_exec($this->_curl);
	}
}
?>
