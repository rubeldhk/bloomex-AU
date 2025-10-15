<?php
/**
* $Id: files_browser.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra, $mainframe, $mosConfig_list_limit, $protect, $hydra_template, $database, $hydra_sess;

echo $hydra->load('js', 'filebrowser');

$filebrowser = new FileBrowser();
$browserHTML = new browserHTML();

mosCommonHTML::loadOverlib();

if ($filebrowser->browse_mode == '1') {
    $filebrowser->browse();
}
elseif ($filebrowser->browse_mode == '2') {
	$filebrowser->browsePathway();
}

$option     = mosGetParam($_REQUEST, 'option');

$limit      = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );

require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
$pageNav = new mosPageNav( count($filebrowser->folders), $limitstart, $limit );

$cmd = '';
if ($protect->current_command == 'move_data') { $cmd = 'move_data'; }

$id = 0;
if ($protect->current_command == 'move_data') { $id = intval(mosGetParam($_POST, 'id', 0)); }

$type = '';
if ($protect->current_command == 'move_data') { $type = mosGetParam($_POST, 'data_type', ''); }

$mode = HL_BROWSER_MODE_SEEK;

if ($protect->current_command == 'move_data') { $mode = HL_BROWSER_MODE_MOVE_DATA; }

$th_names = array (HL_NAME, HL_CHANGE_DATE);
$querys   = array ('name', 'mdate');

$order_by  = $hydra_sess->profile('browser_table_order_by');
$order_dir = $hydra_sess->profile('browser_table_order_dir');

$hydra_sess->setProfile('browser_table_order_by',mosGetParam($_POST, 'order_by',$order_by)); 
$hydra_sess->setProfile('browser_table_order_dir',mosGetParam($_POST, 'order_dir',$order_dir));

$hydra_template->initTableOrdering($th_names, $querys, $order_by, $order_dir);

?>
<div class="tableContainer">
<div class="tableContainer_header"><?php echo HL_FILE_BROWSER;?> [<?php echo HL_BROWSER_MODE;?>: <?php echo $mode;?>]</div>
<div class="tableContainer_body">
<table class="formTable" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td align="left" style="width:32px;">
    <?php if ($filebrowser->current_folder >= 1) { ?>	
    <a href='javascript:browse(<?php echo $filebrowser->last_folder;?>);'><?php echo $hydra->load('img', '16_back.gif', "alt='".HL_BROWSE_UP."' title='".HL_BROWSE_UP."'");?></a>
    <?php } else { echo $hydra->load('img', '16_back_2.gif', "alt='".HL_ACTION_NOT_AVAILABLE."' title='".HL_ACTION_NOT_AVAILABLE."'"); }?>
    </td>
    <td align="left" style="width:32px;"><a href='javascript:browsePathway();'><?php echo $hydra->load('img', '16_files_browse.gif', "alt='".HL_BROWSE_PATH."' title='".HL_BROWSE_PATH."'");?></a></td>
    <td><?php echo HL_BROWSER_ROOT;?>/</td>
    <td align="left"><input type="text" name="pathway" id="pathway" value="<?php echo $filebrowser->pathway;?>" size="70"/></td>   
  </tr>
</table>

<table class="listTable" width="100%" cellpadding="0" cellspacing="0">
<tr>
  <!-- <th>#</th> -->
  <th><?php echo $hydra->load('img', '16_menu.gif', "alt='".HL_COMMENTS."' title='".HL_COMMENTS."'");?></th>
  <th>&nbsp;</th>
  <th width="50%" align="left"><?php echo $hydra_template->tableOrdering(0);?></th>
  <th width="20%" align="left"><?php echo HL_DATA_TYPE;?></th>
  <th width="10%" align="left"><?php echo HL_DATA_SIZE;?></th>
  <th width="20%" align="center"><?php echo $hydra_template->tableOrdering(1);?></th>
</tr>
<?php
$k = 0;
$x = 0;
for($i = 0, $n = count ($filebrowser->folders); $i < $n; $i++)
{
	$v = $filebrowser->folders[$i];
	
	// hide selected folder when moving
	if ($protect->current_command == 'move_data' AND ($id == $v['folder_id']) AND ($type == 'folder')) {
		continue;
	}
	

	if (intval($v['folder_access']) > $protect->my_usertype) {
		continue;
	}
	
	switch ($v['folder_type']) 
	{
		// folder is accessible to every one
		case '0':
         $browserHTML->browserRow('folder', $k, $x, $v);
         $x++;
			break;
		
		// folder is restricted to a certain project;		
		case '1':
			if (in_array($v['project'], $protect->my_projects) OR ($protect->my_usertype == 3)) {
			   $browserHTML->browserRow('folder', $k, $x, $v);
			   $x++;
			}
			break;
		
		// folder is restricted to a certain person		
		case '2':
			if ($protect->my_id == $v['uid'] OR ($protect->my_id == $v['creator']) OR ($protect->my_usertype == 3)) {		
			   $browserHTML->browserRow('folder', $k, $x, $v);
			   $x++;	
			}
			break;	
	}
	$k = 1 - $k;
}

$i2 = $x;
// load all documents
for($i = 0, $n = count ($filebrowser->documents); $i < $n; $i++)
{
	$v = $filebrowser->documents[$i];

	if (intval($v['doc_access']) > $protect->my_usertype) {
		continue;
	}
	
	$query = "SELECT COUNT(comment_id) FROM #__hydra_comments"
		    . "\n WHERE area = 'files' AND doc = '".$v['doc_id']."'";
		    $database->setQuery($query);
		    $comment = $database->loadResult();
	
	switch ($v['doc_type']) 
	{
		case '0':
			$browserHTML->browserRow('document', $k, $i2, $v, $comment);	
			$i2++;
			break;
			
		case '1':
			if (in_array($v['project'], $protect->my_projects) OR ($v['creator'] == $protect->my_id)  OR ($protect->my_usertype == 3)) {
			   $browserHTML->browserRow('document', $k, $i2, $v, $comment);
			   $i2++;	
			}
			break;
			
		case '2':
         if ($v['uid'] == $protect->my_id OR ($v['creator'] == $protect->my_id) OR ($protect->my_usertype == 3)) {
         	$browserHTML->browserRow('document', $k, $i2, $v, $comment);
         	$i2++;	
         }  
			break;		
	}
}

$i3 = $i2;
// load all files

for($i = 0, $n = count($filebrowser->files); $i < $n; $i++)
{
	$v = $filebrowser->files[$i];
	
	if (intval($v['file_access']) > $protect->my_usertype) {
		continue;
	}
	
	// comment button 
	$comment = $hydra->load('img', 'comment_gray.gif', "alt='".HL_ACTION_NOT_AVAILABLE."' title='".HL_ACTION_NOT_AVAILABLE."'");
	
	$query = "SELECT COUNT(comment_id) FROM #__hydra_comments"
		    . "\n WHERE area = 'files' AND data = '".$v['file_id']."'";
		    $database->setQuery($query);
		    $comment = $database->loadResult();
	
	switch ($v['file_type'])
	{
		case '0':
         $browserHTML->browserRow('file', $k, $i3, $v, $comment);
			$i3++;
			break;
			
		case '1':
			if (in_array($v['project'], $protect->my_projects) OR ($v['creator'] == $protect->my_id)  OR ($protect->my_usertype == 3)) {
			   $browserHTML->browserRow('file', $k, $i3, $v, $comment);
			   $i3++;
			}   
			break;
			
		case '2':
			if ($v['uid'] == $protect->my_id OR ($v['creator'] == $protect->my_id) OR ($protect->my_usertype == 3)) {
			   $browserHTML->browserRow('file', $k, $i3, $v, $comment);
			   $i3++;
			}   
			break;		
	}
}
?>
</table>
</div>
<div class="tableContainer_footer" align="center"></div>
</div>
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'files');
echo $hydra_template->drawInput('hidden', 'cmd', $cmd);
echo $hydra_template->drawInput('hidden', 'boxchecked', '');
echo $hydra_template->drawInput('hidden', 'folder', $filebrowser->current_folder);
echo $hydra_template->drawInput('hidden', 'id', $id);
echo $hydra_template->drawInput('hidden', 'last_folder', $filebrowser->last_folder);
echo $hydra_template->drawInput('hidden', 'browse_mode', '2');
echo $hydra_template->drawInput('hidden', 'data_type', $type);
echo $hydra_template->drawInput('hidden', 'order_by', $order_by);
echo $hydra_template->drawInput('hidden', 'order_dir', $order_dir);
?>
<script type="text/javascript" language="javascript">
function deleteFolder(del)
{
	if (confirm("<?php echo HL_CONFIRM_DELETE;?>")) {
	  document.adminForm.id.value  = del;
	  document.adminForm.data_type.value = 'folder';
	  document.adminForm.cmd.value = 'del_data';
	  document.adminForm.submit();
	}  
}


function deleteDoc(del)
{
	if (confirm("<?php echo HL_CONFIRM_DELETE;?>")) {
	  document.adminForm.id.value  = del;
	  document.adminForm.data_type.value = 'document';
	  document.adminForm.cmd.value = 'del_data';
	  document.adminForm.submit();
	}  
}


function deleteFile(del)
{
	if (confirm("<?php echo HL_CONFIRM_DELETE;?>")) {
	  document.adminForm.id.value        = del;
	  document.adminForm.data_type.value = 'data';
	  document.adminForm.cmd.value       = 'del_data';
	  document.adminForm.submit();
	}  
}
</script>
