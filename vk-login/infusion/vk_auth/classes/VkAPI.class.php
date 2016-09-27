<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2016 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: VkAPI.class.php
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

class VkAPI {
	const API_VERSION = 5.53;
	
	private $_settings;
	private $_redirectUrl;
	private $_error;
	
	function __construct() {
		global $infusion, $settings;
		
		$this->_settings = $infusion->getSettingsArray();
		$this->_redirectUrl = sprintf("%sinfusions/vk_auth/infusion_verify.php", $settings['siteurl']);
		$this->_error = null;
	}
	
	public function createLink() {
		$params = array(
			"client_id"		=>	$this->_settings['setting_client_id'],
			"redirect_uri"	=>	$this->_redirectUrl,
			"response_type"	=>	"code",
			"v"				=>	self::API_VERSION,
			"display"		=>	"page",
			"scope"			=>	"email"
		);
		
		return sprintf("http://oauth.vk.com/authorize?%s", http_build_query($params));
	}
	
	public function authenticate() {
		global $settings;
		
		try {
			
			// Clear error log
			$this->_error = null;
			
			// Detecting errors
			if (isset($_GET['error'])) { throw new Exception(isset($_GET['error_description']) ? urldecode($_GET['error_description']) : null); }
			
			// Detecting authentication result
			if (isset($_GET['code']) && preg_match("#^[a-z0-9]+$#si", $_GET['code'])) {
				$data = $this->_getAccessToken($_GET['code']);
				
				if (isset($data->access_token, $data->expires_in, $data->user_id)) {
					
					// Defining token expiration time
					define("TOKEN_EXPIRATION_TIME", time() + $data->expires_in);
					
					// Check if profile already exists
					$profile_exists = dbcount("(`user_id`)", DB_USERS, "user_uid='".$data->user_id."'");
					
					// Get user info
					$json = $this->_getUserInfo($data->user_id, $data->access_token);
					$user_data = $json->response[0];
					
					if ($profile_exists) {
						
						// Get account information
						$result = dbquery("SELECT user_id, user_token_id FROM ".DB_USERS." WHERE user_uid='".$data->user_id."' LIMIT 1");
						if (!$result) {
							throw new Exception("Could not get account details, database fail.");
						}
						$row = dbarray($result);
						
						// Update token expiration date
						$result = dbquery("UPDATE ".DB_TOKENS." SET token_token='".$data->access_token."', token_expires='".TOKEN_EXPIRATION_TIME."' WHERE token_id='".$row['user_token_id']."' LIMIT 1");
						if (!$result) {
							throw new Exception("Could not update token, database fail.");
						}
						
						// Autoupdate Vk user field
						$_POST['user_vk'] = $user_data->domain;
						
						// Require user fields
						$profile_method = "validate_update"; $db_values = "";
						$result = dbquery("SELECT * FROM ".DB_USER_FIELDS." ORDER BY field_order");
						if (dbrows($result)) {
							while ($field = dbarray($result)) {
								if (file_exists(LOCALE.LOCALESET."user_fields/".$field['field_name'].".php")) {
									include LOCALE.LOCALESET."user_fields/".$field['field_name'].".php";
								}
								if (file_exists(INCLUDES."user_fields/".$field['field_name']."_include.php")) {
									include INCLUDES."user_fields/".$field['field_name']."_include.php";
								}
							}
						}
						
						// Validate received user data
						$user_pass = $this->_generateUserPass($user_data->domain);
						
						// Synchronize user data
						$avatarname = $this->_saveAvatarImage($user_data->photo_100, $row['user_id']);
						$result = dbquery("UPDATE ".DB_USERS." SET user_name='".$user_data->domain."', user_password='".md5($user_pass)."'".($avatarname ? ", user_avatar='$avatarname'" : "")."$db_values WHERE user_id='".$row['user_id']."' LIMIT 1");
						if (!$result) {
							if ($avatarname) {
								@unlink(IMAGES."avatars/".$avatarname);
							}
							throw new Exception("Could not update user data, database fail.");
						}
						
						// Authenticate user
						$this->_authenticateUser($row['user_id'], $user_data->domain, $user_pass, TOKEN_EXPIRATION_TIME);
						
						return true;
					} else {
						
						// Checking if registration is enabled
						if (!$settings['enable_registration']) {
							throw new Exception("Registration disabled by administrator.");
						}
						
						// Add new token
						$result = dbquery("INSERT INTO ".DB_TOKENS." (`token_token`, `token_expires`) VALUES ('".$data->access_token."', '".TOKEN_EXPIRATION_TIME."')");
						if (!$result) {
							throw new Exception("Could not write new token, database fail.");
						}
						$token_id = mysql_insert_id();
						
						// Autocomplete Vk user field
						$_POST['user_vk'] = $user_data->domain;
						
						// Require user fields
						$profile_method = "validate_insert"; $db_fields = ""; $db_values = "";
						$result = dbquery("SELECT * FROM ".DB_USER_FIELDS." ORDER BY field_order");
						if (dbrows($result)) {
							while ($field = dbarray($result)) {
								if (file_exists(LOCALE.LOCALESET."user_fields/".$field['field_name'].".php")) {
									include LOCALE.LOCALESET."user_fields/".$field['field_name'].".php";
								}
								if (file_exists(INCLUDES."user_fields/".$field['field_name']."_include.php")) {
									include INCLUDES."user_fields/".$field['field_name']."_include.php";
								}
							}
						}
						
						// Validate received user data
						$user_pass = $this->_generateUserPass($user_data->domain);
						$user_status = $settings['admin_activation'] == "1" ? "2" : "0";
						
						// Create new profile
						$result = dbquery("INSERT INTO ".DB_USERS." (user_name, user_password, user_admin_password, user_email, user_hide_email, user_avatar, user_posts, user_threads, user_joined, user_lastvisit, user_ip, user_rights, user_groups, user_level, user_status, user_uid, user_token_id".$db_fields.") VALUES ('".$user_data->domain."', '".md5($user_pass)."', '', '', '1', '', '0', '0', '".time()."', '0', '".USER_IP."', '', '', '101', '$user_status', '".$data->user_id."', '$token_id'".$db_values.")");
						if (!$result) {
							throw new Exception("Could not create a new member, database fail.");
						}
						$user_id = mysql_insert_id();
						
						// Copy custom user avatar, not the default
						$avatarname = $this->_saveAvatarImage($user_data->photo_100, $user_id);
						if ($avatarname) {
							$result = dbquery("UPDATE ".DB_USERS." SET user_avatar='$avatarname' WHERE user_id='$user_id' LIMIT 1");
							if (!$result) {
								@unlink(IMAGES."avatars/".$avatarname);
							}
						}
						
						// Authenticate user
						$this->_authenticateUser($user_id, $user_data->domain, $user_pass, TOKEN_EXPIRATION_TIME);
						
						return true;
					}
				} else {
					throw new Exception(isset($data->error_description) ? $data->error_description : null);
				}
			} else {
				throw new Exception("Parameter 'code' is not defined.");
			}
		}
		catch (Exception $e) {
			$this->_error = $e->getMessage();
			return false;
		}
	}
	
	public function getErrorMessage() {
		return $this->_error;
	}
	
	public function _getUserInfo($user_id, $token) {
		try {
			$params = array(
				"user_id"		=>	$user_id,
				"fields"		=>	"photo_100,domain",
				"access_token" 	=>	$token
			);
			
			$client = new InternetConnection();
			$response = $client->get("https://api.vk.com/method/users.get", $params);
			
			return json_decode($response);
		}
		catch (Exception $e) {
			return null;
		}
	}
	
	private function _getAccessToken($code) {
		$params = array(
			"client_id"		=>	$this->_settings['setting_client_id'],
			"client_secret"	=>	$this->_settings['setting_secret_key'],
			"redirect_uri"	=>	$this->_redirectUrl,
			"code"			=>	$code
		);
		
		$client = new InternetConnection();
		$response = $client->get("https://oauth.vk.com/access_token", $params);
		
		return json_decode($response);
	}
	
	private function _authenticateUser($user_id, $user_name, $user_pass, $cookie_exp) {
		$cookie_value = $user_id.".".$user_pass;
		header("P3P: CP='NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM'");
		setcookie(COOKIE_PREFIX."user", $cookie_value, $cookie_exp, "/", "", "0");
		redirect(BASEDIR."setuser.php?user=".$user_name, true);
	}
	
	private function _saveAvatarImage($url, $user_id) {
		$output = false;
		if ($url != "http://vk.com/images/camera_100.png") {
			$avatarext = strrchr($url, ".");
			$avatarname = str_replace($url, "", basename($url));
			$avatarname = $avatarname."[".$user_id."]".$avatarext;
			if (copy($url, IMAGES."avatars/".$avatarname)) {
				chmod(IMAGES."avatars/".$avatarname, 0644);
				
				if (verify_image(IMAGES."avatars/".$avatarname)) {
					$output = $avatarname;
				} else {
					@unlink(IMAGES."avatars/".$avatarname);
				}
			}
		}
		return $output;
	}
	
	private function _generateUserPass($domain) {
		return md5($domain.($this->_settings['setting_client_id']).($this->_settings['setting_secret_key']));
	}
}
?>
