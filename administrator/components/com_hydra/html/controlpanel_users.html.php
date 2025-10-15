<?php
/**
* $Id: controlpanel_users.html.php 27 2007-04-16 18:50:09Z eaxs $
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

mosCommonHTML::loadOverlib();

global $hydra_template, $database, $hydra, $mainframe, $mosConfig_list_limit, $protect;

$limit      = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );

// thanks to Giller for fixing $limitstart 
$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$protect->current_area}{$protect->current_command}limitstart", 'limitstart', 0 ) );

if ($limit == 0) { $limit = $mosConfig_list_limit; }

$controlpanel = new Controlpanel;
$hydra_users  = $controlpanel->getHydraUsers($limit, $limitstart);
$total        = $hydra_users['total'];
$hydra_users  = $hydra_users['users'];


require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
$pageNav = new mosPageNav( $total, $limitstart, $limit );

?>
<div class="tableContainer">
<div class="tableContainer_header"><div style="display:block;float:left"><?php echo HL_USERS;?></div><div style="display:block;float:right"><?php echo $pageNav->getLimitBox($hydra->link('area=controlpanel&cmd=show_users'));?></div></div>
<div class="tableContainer_body">
<table class="listTable" width="100%" cellpadding="0" cellspacing="0">
<tr>
  <th align="center">#</th>
  <th align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $total; ?>);" /></th>
  <th align="center">Id</th>
  <th align="left" width="50%"><?php echo HL_NAME;?></th>
  <th align="left" width="45%"><?php echo HL_USER_TYPE;?></th>
  <th align="center" width="5%"><?php echo $hydra->load('img', '16_group.gif', "alt='".HL_GROUPS."' title='".HL_GROUPS."'");?></th>
  </tr>
  <?php
  if ($total < 1) { 
  	 echo "<tr class='row0'><td colspan='5'>".$hydra_template->drawInfo(HL_NO_HYDRAUSERS)."</td></tr>"; 
    $hydra_users = array();
  }
  
  $k = 0;
  for($i = 0, $n = count($hydra_users); $i< $n; $i++)
  {
  	  $v = $hydra_users[$i];
  	  
  	  $grouplist  = '';
  	  $usergroups = array();
  	  $checkbox   = mosHTML::idBox( $i, $v['id'] );
  	  
     $query = "SELECT g.group_id, g.group_name FROM #__hydra_groups AS g"
            . "\n INNER JOIN #__hydra_group_members AS m ON '".$v['id']."' = m.uid"
            . "\n WHERE g.group_id = m.gid";
            $database->setQuery($query);
            $groups = $database->loadAssocList();
            $total_groups = count($groups);
            
     foreach ($groups AS $k2 => $v2)
  	  {
  	  	  $grouplist   .= htmlspecialchars(stripslashes($v2['group_name']), ENT_QUOTES);
  	  	  $usergroups[] = $v2['group_id']; 
  	  }
  	        
     $user_in_groups = "<a href='#' onmouseover=\"return overlib('<table>".$grouplist."</table>', CAPTION, '".htmlspecialchars(HL_GROUPS, ENT_QUOTES)."', BELOW, RIGHT);\" onmouseout=\"return nd();\">"
                     . $hydra->load('img', '16_group.gif', "alt='' title=''")
                     . "</a>";
                     
     if ($total_groups < 1) { $user_in_groups = $hydra->load('img', '16_group_2.gif', "alt='' title=''"); }

     if ($protect->my_usertype != 3) {
     	  foreach($usergroups AS $k2 => $v2) 
     	  {
     	  	  if (!in_array($v2, $protect->my_groups)) {
     	  	  	  $checkbox = '&nbsp;';
     	  	  }
     	  }
     }
     elseif($v['id'] == $protect->my_id) {
     	  $checkbox = '&nbsp;';
     }
     
     $edit = "<a href='".$hydra->link('section=controlpanel&cmd=add_users&id='.$v['id'])."'>".$hydra->load('img', 'edit.gif', "alt='".HL_EDIT."' title='".HL_EDIT."'")."</a>"   	
  	  ?>
  	  <tr class="row<?php echo $k;?>">
  	    <td align="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
  	    <td align="center"><?php echo $checkbox; ?></td>
  	    <td align="center" ><?php echo $v['id'];?></td>
  	    <td align="left" width="50%"><?php echo $v['name'];?></td>
  	    <td align="left" width="45%"><?php echo $hydra->formatUserType($v['user_type'], "menu_".$i, $v['id']);?></td>
  	    <td align="center" width="5%"><?php echo $user_in_groups;?></td>
  	  </tr>
  	  <?php
  	  $k = 1 - $k;
  }
  ?>
</table>
</div>
<div class="tableContainer_footer" align="center"><?php echo $pageNav->getPagesLinks(); ?></div>
</div>
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'cmd', 'show_users');
echo $hydra_template->drawInput('hidden', 'id', '');
echo $hydra_template->drawInput('hidden', 'user_type', '');
echo $hydra_template->drawInput('hidden', 'boxchecked', '');
?>
<script type="text/javascript" language="javascript">
function validateDelete()
{
	if (document.adminForm.boxchecked.value == '') {
		alert ('<?php echo HL_DELETE_USERS_WARN;?>');
	}
	else {
	  document.adminForm.cmd.value =	'del_users';
	  document.adminForm.submit();
	}
}

function changeType(user, usertype)
{
	document.adminForm.cmd.value = 'change_usertype';
	document.adminForm.id.value = user;
	document.adminForm.user_type.value = usertype;
	document.adminForm.submit();
}
</script>