<?php
/***************************************
 * $Id: PageNavigation.html.php,v 1.1.2.1 2005/07/29 13:06:29 tcp Exp $
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.1.2.1 $
 **/

defined('_VALID_MOS') or die(_LANG_TEMPLATE_NO_ACCESS);

global $dbq;
$pageNav =& $dbq->_pageNav;
$url = _DBQ_URL.'&task=ExecuteQuery&qid='.$dbq->id;

?>
<table align="center">
  <tr>
    <th style="text-align: center;"><?php echo  $pageNav->writePagesLinks($url) ?>    </th>
  </tr>

  <tr>
    <td style="text-align: center;"><?php echo  $pageNav->writePagesCounter() ?>    </td>
  </tr>

  <tr>
    <td style="text-align: center;"><?php echo  _LANG_TEMPLATE_DISPLAY ?> : <?php echo  $pageNav->writeLimitBox($url) ?>    </td>
  </tr>
</table>