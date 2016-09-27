<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: errors.php
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
require_once "maincore.php";
include THEME."theme.php";
require_once INCLUDES."output_handling_include.php";
require_once CLASSES."DocumentErrors.class.php";
include LOCALE.LOCALESET."exceptions.php";

$custom_settings = (function_exists("render_error_page") ? true : false);

echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n";
echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='".$locale['xml_lang']."' lang='".$locale['xml_lang']."'>\n";
echo "<head>\n";
echo "<title>".$settings['sitename']."</title>\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=".$locale['charset']."' />\n";
echo "<meta name='description' content='".$settings['description']."' />\n";
echo "<meta name='keywords' content='".$settings['keywords']."' />\n";
if (!$custom_settings) { echo "<style type='text/css'>html, body { height:100%; }</style>\n"; }
echo "<link rel='stylesheet' href='".THEME."styles.css' type='text/css' media='screen'/>\n";
echo "<link rel='shortcut icon' href='".IMAGES."favicon.ico' type='image/x-icon' />\n";
echo "</head>\n<body".(!$custom_settings ? " class='tbl2 setuser_body'" : "").">\n";

$documentError = new DocumentErrors();
$documentError->setHTTPStatus();
$error_info = $documentError->getErrorInfo();
if (!$custom_settings) { DocumentErrors::renderErrorPage($error_info);	
} else { render_error_page($error_info); }

echo "</body>\n</html>\n";

$output = ob_get_contents();
if (ob_get_length() !== FALSE){
	ob_end_clean();
}
echo handle_output($output);

if (ob_get_length() !== FALSE){
	ob_end_flush();
}

mysql_close($db_connect);
?>