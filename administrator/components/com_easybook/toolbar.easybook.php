<?php
/**
* EasyBook - A Joomla Guestbook Component
* @version 1.1 Stable
* @package EasyBook
* Based on AkoBook
* @license Released under the terms of the GNU General Public License (see LICENSE.php in the Joomla! root directory)
* @Achim Raji (aka cybergurk) - David Jardin (aka SniperSister) - Cedric May - Siegmund Langsch (aka langsch2)
**/

defined( '_VALID_MOS' ) or die( 'Direkter Zugriff ist nicht erlaubt.' );

require_once( $mainframe->getPath( 'toolbar_html' ) );
require_once( $mainframe->getPath( 'toolbar_default' ) );

switch ($task) {
  case "new":
    menueasybook::NEW_MENU();
    break;

  case "overview":
    break;

  case "edit":
    menueasybook::EDIT_MENU();
    break;

  case "config":
    menueasybook::CONFIG_MENU();
    break;

  case "about":
    menueasybook::ABOUT_MENU();
    break;

  case "convert":
    menueasybook::CONVERT_MENU();
    break;

  case "convert3.42":
    menueasybook::CONVERT_MENU();
    break;

  case "convertyah":
    menueasybook::CONVERT_MENU();
    break;

  case "language";
    menueasybook::LANG_MENU();
    break;

case "words";
    menueasybook::WORD_MENU();
    break;

case "view";
      $obj = new MENU_Default();
    break;

  default:
   break;
}
?>
