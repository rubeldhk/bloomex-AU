<?php
/**
* EasyBook - A Joomla Guestbook Component
* @version 1.1 Stable
* @package EasyBook
* Based on AkoBook
* @license Released under the terms of the GNU General Public License (see LICENSE.php in the Joomla! root directory)
* @Achim Raji (aka cybergurk) - David Jardin (aka SniperSister) - Cedric May - Siegmund Langsch (aka langsch2)
**/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
include($mosConfig_absolute_path.'/configuration.php');
if (file_exists($mosConfig_absolute_path.'/components/com_easybook/languages/'.$mosConfig_lang.'.php')) {
      include($mosConfig_absolute_path.'/components/com_easybook/languages/'.$mosConfig_lang.'.php');
} else {
      include($mosConfig_absolute_path.'/components/com_easybook/languages/english.php');
}
function com_install() {
  global $database, $mosConfig_absolute_path;

  # Show installation result to user
  ?>
  <center>
  <table width="100%" border="0">
    <tr>
      <td><img src="components/com_easybook/images/logo.png"></td>
      <td>
        <strong><?php echo _GUESTBOOK_INSTALL_NAME ?></strong><br/>
        <?php echo _GUESTBOOK_INSTALL_LICENSE ?><br/>
      </td>
    </tr>
    <tr>
      <td background="E0E0E0" style="border:1px solid #999;" colspan="2">
        <code><?php echo _GUESTBOOK_INSTALL_PROCESS ?><br />
        <?php
          # Set up new icons for admin menu
          $database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/edit.png' WHERE admin_menu_link='option=com_easybook&task=view'");
          $iconresult[0] = $database->query();
          $database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/config.png' WHERE admin_menu_link='option=com_easybook&task=config'");
          $iconresult[1] = $database->query();
          $database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/language.png' WHERE admin_menu_link='option=com_easybook&task=language'");
          $iconresult[2] = $database->query();
          $database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/credits.png' WHERE admin_menu_link='option=com_easybook&task=about'");
          $iconresult[3] = $database->query();
          $database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/edit.png' WHERE admin_menu_link='option=com_easybook&task=Words'");
          $iconresult[4] = $database->query();
          foreach ($iconresult as $i=>$icresult) {
            if ($icresult) {
              echo _GUESTBOOK_INSTALL_IMAGEOKAY . $i . "<br />";
            } else {
              echo _GUESTBOOK_INSTALL_IMAGEFAILED . $i . "<br />";
            }
          }

        ?>
        <font color="green"><b><?php echo _GUESTBOOK_INSTALL_FINISHED ?></b></font></code>
      </td>
    </tr>
  </table>
  </center>
	<div align="center"><img src="components/com_easybook/images/logo_big.jpg"></div>
  <?php
}
?>