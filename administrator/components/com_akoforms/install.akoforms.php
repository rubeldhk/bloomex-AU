<?php 
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.2
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze. All rights reserved!
* @license http://www.konze.de/ Copyrighted Commercial Software
*/

function com_install() {
  global $database, $mosConfig_absolute_path;

  # Show installation result to user
  ?>
  <center>
  <table width="100%" border="0">
    <tr>
      <td><img src="components/com_akoforms/images/logo.png"></td>
      <td>
        <strong>AkoForms - A Mambo Form Generator Component</strong><br/>
        <font class="small">&copy; Copyright 2004 by Arthur Konze<br/>
        This component is copyrighted software. Distribution is prohibited.</font><br/>
      </td>
    </tr>
    <tr>
      <td background="E0E0E0" style="border:1px solid #999;" colspan="2">
        <code>Installation Process:<br />
        <?php
          # Set up new icons for admin menu
          $database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/categories.png' WHERE admin_menu_link='option=com_akoforms&task=forms'");
          $iconresult[1] = $database->query();
          $database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/edit.png' WHERE admin_menu_link='option=com_akoforms&task=fields'");
          $iconresult[2] = $database->query();
          $database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/db.png' WHERE admin_menu_link='option=com_akoforms&task=data'");
          $iconresult[3] = $database->query();
          $database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/config.png' WHERE admin_menu_link='option=com_akoforms&task=settings'");
          $iconresult[4] = $database->query();
          $database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/user.png' WHERE admin_menu_link='option=com_akoforms&task=language'");
          $iconresult[5] = $database->query();
          $database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/credits.png' WHERE admin_menu_link='option=com_akoforms&task=about'");
          $iconresult[6] = $database->query();
          foreach ($iconresult as $i=>$icresult) {
            if ($icresult) {
              echo "<font color='green'>FINISHED:</font> Image of menu entry $i has been corrected.<br />";
            } else {
              echo "<font color='red'>ERROR:</font> Image of menu entry $i could not be corrected.<br />";
            }
          }

        ?>
        <font color="green"><b>Installation finished.</b></font></code>
      </td>
    </tr>
  </table>
  </center>
  <?php
}
?>