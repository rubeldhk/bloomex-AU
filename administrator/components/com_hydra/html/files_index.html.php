<?php
/**
* $Id: files_index.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra, $protect;

require_once($hydra->load('class', 'filebrowser'));

switch ($protect->current_command)
{
	default:
		require_once($hydra->load('class', 'mime_type'));
		require_once($hydra->load('html', 'files_browser'));
		break;
		
	case 'new_folder':
		require_once($hydra->load('html', 'files_newfolder'));
		break;
		
	case 'create_folder':
		$filebrowser = new FileBrowser();
		$filebrowser->createFolder();
		break;

	case 'del_data':
		$filebrowser = new FileBrowser();
		$filebrowser->deleteData();
		break;

	case 'new_document':
		require_once($hydra->load('html', 'files_newdoc'));
		break;

	case 'create_document':
		$filebrowser = new FileBrowser;
		$filebrowser->createDocument();
		break;

	case 'read_data':
		require_once($hydra->load('html', 'files_read'));
		break;
		
	case 'move_confirm':
		$filebrowser = new FileBrowser;
		$filebrowser->moveData();
		break;	
		
	case 'new_comment':
		require_once($hydra->load('html', 'files_newcomment'));
		break;

	case 'create_comment':
		$filebrowser = new FileBrowser;
		$filebrowser->createComment();
		break;

	case 'create_files':
		require_once($hydra->load('html', 'files_upload'));
		break;

	case 'upload_file':
		$filebrowser = new FileBrowser;
		$filebrowser->uploadFile();
		break;

	case 'view_comments':
		require_once($hydra->load('html', 'files_commentlist'));
		break;

	case 'del_comment':
	   $filebrowser = new FileBrowser;
		$filebrowser->deleteComment();
		break;			
			
}
?>