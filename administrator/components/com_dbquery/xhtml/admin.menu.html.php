<?php

/***************************************
 * $Id: admin.menu.html.php,v 1.2 2005/05/25 14:16:38 tcp Exp $
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.2 $
 **/

defined( '_VALID_MOS' ) or die( _LANG_NO_ACCESS );

$actions = array('database', 'query', 'variable', 'substitution', 'config', 'stats', 'template', 'errors');

?>

<table width="60%">
  <tr>
<?php foreach( $actions as $action ) { ?>
    <td>
       <a href="<?php echo $obj->getUrl($action); ?>&task=show&limitstart=0">
       [<?php echo $action; ?>]
      </a>
    </td>
<?php } ?>
	<td><a href="#" onclick="javascript:window.open('<?php echo $obj->getUrl('HELP') ?>');return false">[documentation]</a>
	</td>
  <tr>
</table>
