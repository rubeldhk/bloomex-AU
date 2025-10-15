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
class menueasybook {

  function NEW_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save();
    mosMenuBar::cancel();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

  function EDIT_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save();
    mosMenuBar::cancel(view);
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

  function CONVERT_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

  function CONFIG_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save( 'savesettings', _GUESTBOOK_ASAVE );
    mosMenuBar::custom('overview','back.png','back_f2.png', _GUESTBOOK_ABACK,false);
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

  function ABOUT_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back(_GUESTBOOK_ABACK, 'index2.php?option=com_easybook');
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

  function WORD_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save( 'savewords', _GUESTBOOK_ASAVE );
    mosMenuBar::cancel();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }


   function LANG_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save( 'savelanguage', _GUESTBOOK_ASAVE );
    mosMenuBar::cancel();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

  function DEFAULT_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::addNew();
    mosMenuBar::editList();
    mosMenuBar::deleteList();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

}
?>
