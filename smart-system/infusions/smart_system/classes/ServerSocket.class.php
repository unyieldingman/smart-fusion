<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: ServerSocket.class.php
| Author: Tatar
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/

class ServerSocket {
	var $socket_ping;
	var $socket_info;
	var $buffer;

	function get_microtime() { 
		list($usec, $sec) = explode(" ", microtime()); 
		return ((float)$usec + (float)$sec); 
	}

	function CheckSocket($server_transport, $server_address, $server_port, $server_command = "\xff\xff\xff\xffping") {
		$start = $this->get_microtime();
		$fp = @stream_socket_client($server_transport."://".$server_address.":".$server_port, $errno, $errstr, 1);
		$end = $this->get_microtime();
		if($fp && $server_transport != "udp") {
			$this->socket_info = @stream_get_meta_data($fp);
			@fclose($fp);
			$this->socket_ping = ($end - $start) * 1000;
			return true;
		} elseif ($fp && $server_transport == "udp") {
			@stream_set_timeout($fp, 1);
			@fwrite($fp, $server_command, strlen($server_command));
	    $buffer = @fread($fp, 1);
			$info = @stream_get_meta_data($fp);
    	$buffer .= @fread($fp, $info["unread_bytes"]);
			@fclose($fp);
			if ($buffer == "") {
				$this->socket_ping = "";
				$this->socket_info = "";
				return false;
			} elseif ($info["timed_out"] == "1") {
				$this->socket_ping = "";
				$this->socket_info = "";
				return false;
			} else {
				$this->socket_info = $info;
				$this->socket_ping = ($end - $start) * 1000;
				$this->buffer = $buffer;
				return true;
			}
		} else {
			$this->socket_ping = "";
			$this->socket_info = "";
			return false;
		}
	}
}
?>