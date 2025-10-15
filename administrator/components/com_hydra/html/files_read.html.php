<?php
/**
* $Id: files_read.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra, $protect, $database, $mosConfig_absolute_path, $hydra, $hydra_template;

$filebrowser = new FileBrowser;

$type = mosGetParam($_POST, 'data_type','');
$id   = intval(mosGetParam($_POST, 'id', 0));
 
$data = $filebrowser->loadData($id, $type);

switch ($type)
{
	case 'document':
		$user_info = $hydra->getUserDetails($data->creator);
		echo $hydra->load('js', 'filebrowser');
		?>
		<table class="sheet" cellpadding="0" cellspacing="0" align="center" width="100%">
          <tr>
            <td class="sheet_tl"></td>
            <td class="sheet_tc"></td>
            <td class="sheet_tr"></td>
          </tr>
          <tr>
            <td class="sheet_cl"></td>
            <td class="sheet_cc">
            
            <!-- START -->
            
            <table cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td width="60%" class="sheet_title" valign="top"><?php echo stripslashes($data->doc_title);?></td>
                <td width="40%" align="right" valign="top">
                <?php 
                echo HL_CREATED_BY.": ".$user_info->name." (".hydraDate($data->cdate).")";
                ?>
                </td> 
              </tr>
              <tr>
                <td class="sheet_text" colspan="2"><?php echo stripslashes($data->doc_text);?></td>
              </tr>
            </table>  
              
            <!-- FINISH -->
              
            </td>
            <td class="sheet_cr"></td>
          </tr>
          <tr>
            <td class="sheet_bl"></td>
            <td class="sheet_bc"></td>
            <td class="sheet_br"></td>
          </tr>
        </table>    
		<?php
		echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
        echo $hydra_template->drawInput('hidden', 'area', 'files');
        echo $hydra_template->drawInput('hidden', 'cmd', $cmd);
        echo $hydra_template->drawInput('hidden', 'folder', $filebrowser->current_folder);
        echo $hydra_template->drawInput('hidden', 'id', $id);
        echo $hydra_template->drawInput('hidden', 'last_folder', $filebrowser->last_folder);
        echo $hydra_template->drawInput('hidden', 'browse_mode', '2');
        
        if($protect->perm('view_comments')) {
        	require_once($hydra->load('html', 'files_commentlist'));
        }
		break;

		
	// file download	
	case 'data':
		$settings = $hydra->loadSettings();
		
		$file  = null;
		$query = "SELECT file_realname, file_location, mime_type FROM #__hydra_files WHERE file_id = '$id'";
		       $database->setQuery($query);
		       $database->loadObject($file);

		$mime_type     = $file->mime_type;
		$name          = $file->file_realname;
		$file_location = $file->file_location;

		if (!file_exists($file_location)) {
			echo "<script type='text/javascript' language='javascript'>"
			   . "\n alert('".HL_FILE_NOT_EXISTS."');"
			   . "\n document.adminForm.id.value = '0';"
      	       . "\n document.adminForm.cmd.value = ' ';"
      	       . "\n document.adminForm.data_type.value = ' ';"
			   . "\n </script>";
		}
		else {
			
			header("Content-Type: $mime_type");
            header("Content-Disposition: attachment; filename=$name");
			
		}
		
		
        if (!readfile($file_location)) {
      	   echo "<script type='text/javascript' language='javascript'>"
      	      . "\n alert('".HL_FILE_NOT_READABLE."');"
      	      . "\n document.adminForm.id.value = '0';"
      	      . "\n document.adminForm.cmd.value = ' ';"
      	      . "\n document.adminForm.data_type.value = ' ';"
      	      . "\n </script>";
        }
        
		break;	
}
?>