<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2016 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_verify.php
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
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."vk_auth/infusion_db.php";
require_once INFUSION_CLASSES."InternetConnection.class.php";
require_once INFUSION_CLASSES."VkAPI.class.php";

// Initialize VK API
$vk = new VkAPI();
$vk->authenticate();

// Redirect to home page
redirect(BASEDIR);

require_once THEMES."templates/footer.php";
?>
