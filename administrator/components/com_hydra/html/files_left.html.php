<?php
/**
* $Id: files_left.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra_template, $hydra, $protect;

switch($protect->current_command)
{
	default:
	   leftFolderDetails($hydra_template, $protect);
	   break;
}

function leftFolderDetails($hydra_template, $protect)
{
	global $database, $hydra_sess;
	
    $body   = '';
    $id     = intval(mosGetParam($_REQUEST, 'folder', $hydra_sess->profile('browser_current_folder', 0, true)));
    $cmd    = $protect->current_command;
    
    $current_folder = intval(mosGetParam($_REQUEST, 'folder', 0));
    
    if ($cmd AND ($cmd != 'move_data')) { return; }
    if (!$current_folder) { return; }
    
    $query = "SELECT f.folder_id,f.folder_name,f.folder_name,f.folder_type,f.project,f.uid, f.cdate, f.mdate, f.folder_active"
    	     . "\n FROM #__hydra_folders AS f WHERE f.folder_id = '$id'";
    	     $database->setQuery($query);
    	     $database->loadObject($folder);
    
    $folder_name   = $folder->folder_name;
    $folder_status = $folder->folder_active;
    
    if ($folder_status == 1) { 
    	$folder_status = '&nbsp;'; 
    } 
    else {
    	$folder_status = " <div class='small'>".HL_INACTIVE."</div>";
    	
    	if (!$id) { $folder_status = '&nbsp;'; } 
    }
    
    $folder_type = HL_PUBLIC;
    $sub_info    = '&nbsp;';
    $cdate_lbl   = HL_CREATE_DATE;
    $mdate_lbl   = HL_CHANGE_DATE;
    $cdate       = date('d m Y, H:i', hydraTime($folder->cdate));
    $mdate       = date('d m Y, H:i', hydraTime($folder->mdate)); 
    
    if (!$id) { 
    	$folder_name = HL_BROWSER_ROOT; 
      $cdate_lbl   = '&nbsp;';
      $mdate_lbl   = '&nbsp;';
      $cdate       = '&nbsp;';
      $mdate       = '&nbsp;';
    }
    
    // project name ?          
    if ($folder->folder_type == '1') {
    	 $query = "SELECT project_name FROM #__hydra_project WHERE project_id = '".$folder->project."'";
    		     $database->setQuery($query);
    		     $sub_info = $database->loadResult();

    	 $folder_type = HL_PROJECT;       
    }
    	
    // if it's a private folder, get the username
    if ($folder->folder_type == '2') {
    	 $query = "SELECT u.name FROM #__users AS u, #__hydra_users AS h"
    		     . "\n WHERE h.id = '".$folder->uid."'"
    		     . "\n AND h.jid = u.id";
    		     $database->setQuery($query);
    		     $sub_info = $database->loadResult();

    	 $folder_type =  HL_PRIVATE;	          
    }
                  
    
    $body = "<table width='100%'>"
          . "\n <tr>"
          . "\n <td colspan='2'><strong>$folder_name</strong></td>"
          . "\n </tr>"
          . "\n <tr>"
          . "\n <td colspan='2'>$folder_status</td>"
          . "\n </tr>"
          . "\n <tr>"
          . "\n <td width='40%'><strong>".$folder_type."</strong></td>"
          . "\n <td>".$sub_info."</td>"
          . "\n </tr>"
          . "\n <tr>"
          . "\n <tr>"
          . "\n <td width='40%'><strong>".$cdate_lbl."</strong></td>"
          . "\n <td>".$cdate."</td>"
          . "\n </tr>"
          . "\n <tr>"
          . "\n <td width='40%'><strong>".$mdate_lbl."</strong></td>"
          . "\n <td>".$mdate."</td>"
          . "\n </tr>"
          . "\n </table>";
	
	 echo $hydra_template->drawBox(HL_DETAILS, $body);
}
?>