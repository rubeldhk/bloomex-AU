<?php

/***************************************
 * $Id: web.html.php,v 1.1 2005/05/10 14:47:28 tcp Exp $
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.1 $
 **/

?>

<table>
  <tr>
    <td style="text-align: right">
      <?php echo _LANG_PAGE_COMPANY ?>
    </td>
    <td>
      <a href="javascript:void window.open('<?php echo $obj->getURL('COMPANY') ?>')"><?php echo $obj->getURL('COMPANY') ?></a>
    </td>
  </tr>
  <tr>
    <td style="text-align: right">
      <?php echo _LANG_PAGE_SUPPORT ?>
    </td>
    <td>
      <a href="javascript:void window.open('<?php echo $obj->getURL('SUPPORT') ?>')"><?php echo $obj->getURL('SUPPORT') ?></a>
    </td>
  </tr>
  <tr>
    <td style="text-align: right">
      <?php echo _LANG_PAGE_FORUM ?>
    </td>
    <td>
      <a href="javascript:void window.open('<?php echo $obj->getURL('FORUM') ?>')"><?php echo $obj->getURL('FORUM') ?></a>
    </td>
  </tr>
  <tr>
    <td style="text-align: right">
      <?php echo _LANG_PAGE_DOWNLOAD ?>
    </td>
    <td>
      <a href="javascript:void window.open('<?php echo $obj->getURL('DOWNLOAD') ?>')"><?php echo $obj->getURL('DOWNLOAD') ?></a>
    </td>
  </tr>
</table>

