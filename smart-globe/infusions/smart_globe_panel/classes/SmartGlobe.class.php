<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: SmartGlobe.class.php
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

class SmartGlobe {
	private $_globe_links		= array();
	private $_globe_patterns	= array();
	private $_data				= array();
	private $_html				= "";
	
	public function __construct($state=null) {
		$this->_data['site_url'] = "";
		$this->_data['site_location'] = "";
		$this->_data['site_language'] = "";
		$this->_data['site_info'] = "";
		if (is_null($state)) { $this->initDefaults(); }
	}
	
	public function addLink($pattern) {
		if (!empty($pattern)) {
			$this->_globe_links[] = "<!--smart_globe_auth-->".$pattern;
			return true;
		}
		return false;
	}
	
	public function addPattern($name, $value) {
		if (!isset($this->_globe_patterns[0][$name])) {
			$this->_globe_patterns[0][] = "{".$name."}";
			$this->_globe_patterns[1][] = $value;
			return true;
		}
		return false;
	}
	
	public function initDefaults() {
		global $settings;
		// Add patterns
		$this->addPattern("site_url", $settings['siteurl']);
		$this->addPattern("site_name", $settings['sitename']);
		$this->addPattern("site_banner", $settings['sitebanner']);
		// Add links
		$this->addLink("<a href='{site_url}' title='{site_name}'>{site_name}</a>");
		$this->addLink("<a href='{site_url}' title='{site_name}'><img src='{site_url}{site_banner}' alt='{site_name}' title='{site_name}' /></a>");
	}
	
	public function getLink($offset=0) {
		if (isset($this->_globe_links[$offset])) {
			return $this->_globe_links[$offset];
		}
		return null;
	}
	
	public function processingRequest() {
		global $locale;
		if (isset($_POST['globalize_site'])) {
			$this->_data['site_url'] = stripinput($_POST['site_url']);
			if (!empty($this->_data['site_url']) && !$this->checkURL($this->_data['site_url'])) {
				$this->_data['site_url'] = "http://".$this->_data['site_url'];
			}
			$this->_data['site_location'] = stripinput($_POST['site_location']);
			$this->_data['site_language'] = stripinput($_POST['site_language']);
			$this->_data['site_info'] = stripinput($_POST['site_info']);
			
			// Check errors
			$error = false;
			$backlink = (iSUPERADMIN ? "0" : $this->checkBacklink($this->_data['site_url']));
			if (empty($this->_data['site_url'])) { $error = 1;
			} elseif (empty($this->_data['site_location'])) { $error = 2;
			} elseif (empty($this->_data['site_language'])) { $error = 3;
			} elseif ($backlink == "1") { $error = 4;
			} elseif ($backlink == "2") { $error = 5; }
			
			if (!$error) {
				$result = dbquery("INSERT INTO ".DB_SMART_GLOBE." (`globe_site_url`, `globe_title`, `globe_location`, `globe_language`, `globe_info`, `globe_population`, `globe_born`, `globe_icon`, `globe_draft`, `globe_backlink`, `globe_last_check`, `globe_privilegies`, `globe_orientation`, `globe_xaxis`, `globe_yaxis`, `globe_datestamp`) VALUES ('".$this->_data['site_url']."', '', '".$this->_data['site_location']."', '".$this->_data['site_language']."', '".$this->_data['site_info']."', '0', '0', '', '1', '1', '".time()."', '".(iSUPERADMIN ? "1" : "0")."', '0', '0', '0', '".time()."')");
				redirect(FUSION_SELF."?status=sa");
			} else {
				if ($error == "1") { $message = $locale['globe_004'];
				} elseif ($error == "2") { $message = $locale['globe_005'];
				} elseif ($error == "3") { $message = $locale['globe_006'];
				} elseif ($error == "4") { $message = $locale['globe_007'];
				} elseif ($error == "5") { $message = $locale['globe_008']; }
				if (isset($message) && !empty($message)) { echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
			}
		}
	}
	
	public function renderMeta() {
		global $locale;
		$this->_html = "<table cellpadding='0' cellspacing='0' width='100%' class='center'>\n<tr>\n";
		$this->_html .= "<td class='tbl' width='80' valign='top'><img src='".SMART_GLOBE_BASE."images/site_globalize.png' style='border:none;' alt='".$locale['globe_000']."' title='".$locale['globe_000']."' /></td>\n";
		$this->_html .= "<td class='tbl'>".$locale['globe_012']."</td>\n";
		$this->_html .= "</tr>\n</table>\n";
		return $this->_html;
	}
	
	public function renderMessage() {
		global $locale;
		$this->_html = "<div style='text-align:center;'>".$locale['globe_014']."<br /><br /><a href='".FUSION_SELF."'>".$locale['globe_015']."</a></div>\n";
		return $this->_html;
	}
	
	public function renderPage() {
		global $locale;
		$this->_html = "<form name='globalize_site' method='POST' action='".FUSION_SELF."'>\n";
		$this->_html .= "<table cellpadding='0' cellspacing='1' width='500' class='center'>\n<tr>\n";
		$this->_html .= "<td width='100' class='tbl'>".$locale['globe_017'].":</td>\n";
		$this->_html .= "<td class='tbl'><input type='text' name='site_url' value='".$this->_data['site_url']."' class='textbox' style='width:250px;' /></td>\n";
		$this->_html .= "</tr>\n<tr>\n";
		$this->_html .= "<td width='100' class='tbl'>".$locale['globe_018'].":</td>\n";
		$this->_html .= "<td class='tbl'><input type='text' name='site_location' value='".$this->_data['site_location']."' class='textbox' style='width:250px;' /><br /><span class='small'>".$locale['globe_019']."</span></td>\n";
		$this->_html .= "</tr>\n<tr>\n";
		$this->_html .= "<td width='100' class='tbl'>".$locale['globe_020'].":</td>\n";
		$this->_html .= "<td class='tbl'><input type='text' name='site_language' value='".$this->_data['site_language']."' class='textbox' style='width:250px;' /></td>\n";
		$this->_html .= "</tr>\n<tr>\n";
		$this->_html .= "<td width='100' class='tbl' valign='top'>".$locale['globe_063'].":</td>\n";
		$this->_html .= "<td class='tbl'><textarea name='site_info' rows='3' class='textbox' style='width:250px;'>".$this->_data['site_info']."</textarea></td>\n";
		$this->_html .= "</tr>\n<tr>\n";
		$this->_html .= "<td class='tbl2' colspan='2'><strong>".$locale['globe_021']."</strong></td>\n";
		$this->_html .= "</tr>\n<tr>\n";
		$this->_html .= "<td class='tbl' colspan='2'>".$locale['globe_022']."</td>\n";
		$this->_html .= $this->_parseLinks();
		$this->_html .= "</tr>\n<tr>\n";
		$this->_html .= "<td align='center' class='tbl' colspan='2'><input type='submit' name='globalize_site' value='".$locale['globe_025']."' class='button' /></td>\n";
		$this->_html .= "</tr>\n</table>\n</form>\n";
		return $this->_html;
	}
	
	public function checkURL($url) {
		if (preg_match("#http(s)?://.*?#si", $url)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function checkBacklink($page) {
		$page_content = $this->_getPageContent($page);
		$error = 2;
		if ($page_content) {
			foreach ($this->_globe_links as $link) {
				$link = str_replace($this->_globe_patterns[0], $this->_globe_patterns[1], $link);
				if (preg_match("#".$link."#si", $page_content)) {
					$error = false;
					break;
				}
			}
		} else {
			$error = 1;
		}
		return $error;
	}
	
	private function _parseLinks() {
		global $locale; $html_code = ""; $i = 1;
		foreach ($this->_globe_links as $link) {
			$link = str_replace($this->_globe_patterns[0], $this->_globe_patterns[1], $link);
			$html_code .= "</tr>\n<tr>\n";
			$html_code .= "<td width='100' class='tbl' valign='top'>".sprintf($locale['globe_023'], $i).":</td>\n";
			$html_code .= "<td class='tbl'><textarea rows='4' class='textbox' style='width:250px;' readonly='readonly'>".$link."</textarea><br /><span class='small'>".$locale['globe_024']."</span><br /><br /><strong>".$locale['globe_068'].":</strong><br />".$link."</td>\n";
			$i++;
		}
		return $html_code;
	}
	
	private function _getPageContent($url) {
		try {
			$curl = curl_init();
			curl_setopt_array($curl, array(
					CURLOPT_URL				=> $url,
					CURLOPT_RETURNTRANSFER	=> 1,
					CURLOPT_POST			=> 0,
					CURLOPT_USERAGENT		=> "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)",
					CURLOPT_FOLLOWLOCATION	=> 0
				)
			);
			$max_redirects = 5; $code = 1;
			$new_url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
			$redir = curl_copy_handle($curl); 
			curl_setopt($redir, CURLOPT_HEADER, true); 
			curl_setopt($redir, CURLOPT_NOBODY, true); 
			curl_setopt($redir, CURLOPT_FORBID_REUSE, false); 
			curl_setopt($redir, CURLOPT_RETURNTRANSFER, true);
			do {
				curl_setopt($redir, CURLOPT_URL, $new_url);
				$data = curl_exec($redir); 
				if (curl_errno($redir)) { 
					$code = 0; 
				} else { 
					$code = curl_getinfo($redir, CURLINFO_HTTP_CODE); 
					if ($code == 301 || $code == 302) { 
						preg_match('/Location:(.*?)\n/', $data, $matches); 
						$new_url = trim(array_pop($matches)); 
						if (!$this->checkURL($new_url)) {
							$prefix = (substr($url, -1, 1) == "/" ? "" : "/");
							$new_url = $url.$prefix.$new_url;
						}
					} else { 
						$code = 0; 
					} 
				}
			} while ($code && --$max_redirects);
			curl_setopt($curl, CURLOPT_URL, $new_url);
			if(!($html = curl_exec($curl))) { throw new Exception(); }
			curl_close($curl);
			return $html;
		} catch(Exception $e) {
			return false;
		}
	}
	
	public static function initHeaders() {
		add_to_head("<link rel='stylesheet' type='text/css' href='".SMART_GLOBE_STYLES."exterior.css' media='screen' />");
		add_to_head("<script type='text/javascript' src='".SMART_GLOBE_SCRIPTS."jquery.pin.js'></script>");
		return true;
	}
	
	public static function drawMapItems() {
		global $locale; $html = "";
		$result = dbquery("SELECT * FROM ".DB_SMART_GLOBE." WHERE globe_draft='0' ORDER BY globe_datestamp ASC");
		$html .= "<div class='world-map century' id='worldMap'></div>\n";
		while ($data = dbarray($result)) {
			$html .= "<div class='pin ".($data['globe_orientation'] == "0" ? "pin-up" : "pin-down")."' data-xpos='".$data['globe_xaxis']."' data-ypos='".$data['globe_yaxis']."'>\n";	  
			$html .= "<h2>".$data['globe_title']."</h2>\n";
			$html .= "<ul>\n";
			if (!empty($data['globe_icon'])) { $html .= "<li><img src='".SMART_GLOBE_IMAGES."partners/".$data['globe_icon']."' style='border:none;' alt='".$data['globe_title']." ".$locale['globe_069']."' /></li>\n"; }
			$html .= "<li class='description'>".$data['globe_info']."</li>\n";
			if (isnum($data['globe_born']) && $data['globe_born'] != "0") { $html .= "<li><b>".$locale['globe_065'].":</b> ".$data['globe_born']."</li>\n"; }
			if (isnum($data['globe_population']) && $data['globe_population'] != "0") { $html .= "<li><b>".$locale['globe_037'].":</b> ".$data['globe_population']."</li>\n"; }
			if (!empty($data['globe_location'])) { $html .= "<li><b>".$locale['globe_018'].":</b> ".$data['globe_location']."</li>\n"; }
			if (!empty($data['globe_language'])) { $html .= "<li><b>".$locale['globe_036'].":</b> ".$data['globe_language']."</li>\n"; }
			$html .= "<li class='pagelink'><a href='".$data['globe_site_url']."' target='_blank'>".$locale['globe_070']."</a></li>\n";
			$html .= "</ul>\n</div>\n";
		}
		$html .= "<a href='".SMART_GLOBE_BASE."smart_globe_infusion.php' class='globalize-button'></a>\n";
		return $html;
	}
}
?>