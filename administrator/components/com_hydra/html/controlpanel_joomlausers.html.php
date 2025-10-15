<?php
/**
* $Id: controlpanel_joomlausers.html.php 16 2007-04-15 12:18:46Z eaxs $
* @package   Project Fork
* @copyright Copyright (C) 2006-2007 Tobias Kuhn. All rights reserved.
* @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*
*
* Project Fork is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
**/

defined ( '_VALID_MOS' ) OR DIE( 'Direct access is not allowed' );

global $hydra_template, $hydra, $mainframe, $mosConfig_list_limit, $protect;

$limit      = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$protect->current_area}{$protect->current_command}limitstart", 'limitstart', 0 ) );

if ($limit == 0) { $limit = $mosConfig_list_limit; }

$controlpanel = new Controlpanel;

$joomla_users = $controlpanel->getJoomlaUsers($limit, $limitstart);
$total        = $joomla_users['total'];
$joomla_users = $joomla_users['users'];

require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
$pageNav = new mosPageNav( $total, $limitstart, $limit );

?>
<div class="tableContainer">
<div class="tableContainer_header"><div style="display:block;float:left"><?php echo HL_JOOMLAUSERS;?></div><div style="display:block;float:right"><?php echo $pageNav->getLimitBox($hydra->link('area=controlpanel&cmd=show_users'));?></div></div>
<div class="tableContainer_body">
<table class="listTable" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <th align="center">#</th>
    <th align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $total; ?>);" /></th>
    <th align="left" width="40%"><?php echo HL_NAME;?></th>
    <th align="left" width="30%"><?php echo HL_USERNAME;?></th>
    <th align="left" width="30%"><?php echo HL_EMAIL;?></th>
  </tr>
  <?php
  if ($total < 1) { 
  	 echo "<tr class='row0'><td colspan='5'>".$hydra_template->drawInfo(HL_NO_JOOMLAUSERS)."</td></tr>"; 
    $joomla_users = array();
  }
  $k = 0;
  $i = 0;
  foreach ($joomla_users AS $key => $v)
  {
  	  ?>
  	  <tr class="row<?php echo $k;?>">
  	    <td align="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
  	    <td align="center"><?php echo mosHTML::idBox( $i, $v['id'] ); ?></td>
  	    <td align="left" width="40%"><?php echo $v['name']; ?></td>
  	    <td align="left" width="30%"><?php echo $v['username']; ?></td>
  	    <td align="left" width="30%"><?php echo $v['email']; ?></td>
  	  </tr>
  	  <?php
  	  // forgot that in 0.6.0 - fixed by Giller
  	  $i++;
  	  $k = 1 - $k;
  }
  ?>
</table>
</div>
<div class="tableContainer_footer" align="center"><?php echo $pageNav->getPagesLinks(); ?></div>
</div>
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'cmd', 'show_joomlausers');
echo $hydra_template->drawInput('hidden', 'boxchecked', '');
?>
<script type="text/javascript" language="javascript">
function validateImport()
{
	if (document.adminForm.boxchecked.value == '') {
		alert ('<?php echo HL_IMPORT_JOOMLA_USERS_WARN;?>');
	}
	else {
	  document.adminForm.cmd.value =	'setup_import';
	  document.adminForm.submit();
	}
}
</script>