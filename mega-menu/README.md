# Mega Menu

## Installation:
1. Include following class to your script
`require_once INFUSIONS."mega_menu/classes/MegaMenu.class.php";`
2. Build menu by calling static method
`string MegaMenu::buildMegaMenu ( void )`

### Example:
`echo "<div class='navigation'>\n".MegaMenu::buildMegaMenu()."</div>\n";`