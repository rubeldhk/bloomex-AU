<?php

/***************************************
 * $Id: invalidInput.html.php,v 1.2 2005/06/05 10:12:44 tcp Exp $
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.2 $
 **/

defined( '_VALID_MOS' ) or die( _LANG_TEMPLATE_NO_ACCESS );

?>
<table>
  <?php 
  while (list($key, $value) = each($invalidInput) ) {
  	$key = $obj->displayName($key);
  ?>

  <tr>
    <td><?php echo  $key ?>: </td>
    <td><?php echo  $value->comment ?></td>
  </tr>
  <?php 
  } 
  ?>
</table>
