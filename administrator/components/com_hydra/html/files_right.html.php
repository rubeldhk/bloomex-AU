<?php
/**
* $Id: files_right.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra, $hydra_template, $protect;

switch($protect->current_command)
{
	// default right navigation
	default:
		rightDefault($hydra, $hydra_template, $protect);
		break;

	// navigation when creating a new folder	
	case 'new_folder':
		rightNewFolder($hydra, $hydra_template, $protect);
		break;

	// navigation when creating a new hydra document
	case 'new_document':
		rightNewDocument($hydra, $hydra_template, $protect);
		break;

	// navigation when creating reading a file
	case 'read_data':
		rightReadData($hydra, $hydra_template, $protect);
		break;

	// navigation when moving a file
	case 'move_data':
		rightMoveData($hydra, $hydra_template, $protect);
		break;	

	// navigation when creating a new comment
	case 'new_comment':
		rightNewComment($hydra, $hydra_template, $protect);
		break;	
		
	case 'create_files':
		rightCreateFiles($hydra, $hydra_template, $protect);
		break;

	case 'view_comments':
		rightViewComments($hydra, $hydra_template, $protect);
		break;		
}


/** 
* @author  Tobias Kuhn
* @name    rightDefault
* @param   object $hydra
* @param   object $hydra_template
* @param   object $protect
* @return  void
* @desc    prints the default navigation on the right side
**/
function rightDefault($hydra, $hydra_template, $protect)
{
	$body = '';
	
	// button for creating a new folder (depends on perm 'new_folder')
	$body .= $protect->perm('new_folder', $hydra_template->drawIcon(HL_NEW_FOLDER, '32_files_newfolder.gif', '', 'newFolder()'));
	// button for creating a new document (depends on perm 'new_document')
	$body .= $protect->perm('new_document', $hydra_template->drawIcon(HL_NEW_DOCUMENT, '32_files_newdocument.gif', '', 'newDocument()'));
	// upload button
	$body .= $protect->perm('create_files', $hydra_template->drawIcon(HL_NEW_UPLOAD, '32_files_upload.gif', '', 'newUpload()'));
	
	// print only when we have access to the files-area
   if ($protect->perm('*files')) {
	   echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
   } 
}


/** 
* @author  Tobias Kuhn
* @name    rightNewFolder
* @param   object $hydra
* @param   object $hydra_template
* @param   object $protect
* @return  void
* @desc    navigation when creating a new folder
**/
function rightNewFolder($hydra, $hydra_template, $protect)
{
	// catch folder id (perhaps we want to edit an existing folder)
	$id     = intval(mosGetParam($_REQUEST, 'id', 0));
	// catch the current folder for correct redirect
	$folder = intval(mosGetParam($_REQUEST, 'folder', 0));
	// lang-constant when cerating a new folder
	$lang   = HL_CREATE_FOLDER;
	
	// change lang if we have an id
	if ($id >= 1) { $lang = HL_EDIT_FOLDER; }
	
	$body = '';
	// button for creating a new folder (depends on perm 'new_folder')
	$body .= $protect->perm('new_folder', $hydra_template->drawIcon($lang, '32_submit.gif', '', 'validateCreate()'));
	// back button. no perms required
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=files&folder='.$folder);
	
	// print only if we have access
   if ($protect->perm('create_folder')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
   }
}


/** 
* @author  Tobias Kuhn
* @name    rightNewDocument
* @param   object $hydra
* @param   object $hydra_template
* @param   object $protect
* @return  void
* @desc    navigation when creating a new hydra document
**/
function rightNewDocument($hydra, $hydra_template, $protect)
{
	$folder = intval(mosGetParam($_REQUEST, 'folder', 0));
	$id     = intval(mosGetParam($_POST, 'id', 0));
	
	$lang = HL_CREATE_DOC;
	
	if ($id >= 1) { $lang = HL_EDIT_DOC; }
	
	$body = '';
	$body .= $protect->perm('new_document', $hydra_template->drawIcon($lang, '32_submit.gif', '', 'validateCreate()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=files&folder='.$folder);
	
    if ($protect->perm('create_folder')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
    }
	
}

/** 
* @author  Tobias Kuhn
* @name    rightReadData
* @param   object $hydra
* @param   object $hydra_template
* @param   object $protect
* @return  void
* @desc    navigation when reading a file
**/
function rightReadData($hydra, $hydra_template, $protect)
{
	$folder = intval(mosGetParam($_REQUEST, 'folder', 0));
	$id     = intval(mosGetParam($_REQUEST, 'id', 0));
	
	$body = '';
	$body .= $protect->perm('create_document', $hydra_template->drawIcon(HL_EDIT, '32_files_newdocument.gif', '',"editDoc($id)"));
	$body .= $protect->perm('create_comment', $hydra_template->drawIcon(HL_NEW_COMMENT, '32_comment.gif', '',"newComment()"));
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=files&folder='.$folder);
	
    if ($protect->perm('read_data')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
    }
}


/** 
* @author  Tobias Kuhn
* @name    rightMoveData
* @param   object $hydra
* @param   object $hydra_template
* @param   object $protect
* @return  void
* @desc    navigation when moving a file
**/
function rightMoveData($hydra, $hydra_template, $protect)
{
	$body = '';
	$body .= $protect->perm('move_confirm', $hydra_template->drawIcon(HL_MOVE_HERE,'move_data_big.gif', '', 'confirmMove()'));
	$body .= $hydra_template->drawIcon(HL_ABORT,'32_abort.gif', '', 'seekMode()');
	
    if ($protect->perm('move_data')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
    }
}


/** 
* @author  Tobias Kuhn
* @name    rightNewComment
* @param   object $hydra
* @param   object $hydra_template
* @param   object $protect
* @return  void
* @desc    navigation when creating a new comment
**/
function rightNewComment($hydra, $hydra_template, $protect)
{
	$comment = intval(mosGetParam($_POST, 'comment', 0));
	$lang = HL_CREATE_COMMENT;
	
	if ($comment >= 1) { $lang = HL_EDIT_COMMENT; }
	
	$body = '';
	
	$body .= $protect->perm('create_comment', $hydra_template->drawIcon($lang, '32_submit.gif', '', 'submitCreate()'));
	$body .= $hydra_template->drawIcon(HL_ABORT, '32_abort.gif', '', 'history.back()');
	
    if ($protect->perm('new_comment')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
    }
}


function rightCreateFiles($hydra, $hydra_template, $protect)
{
	$body   = '';
	$folder = intval(mosGetParam($_REQUEST, 'folder', 0));
	
	$body .= $protect->perm('upload_file', $hydra_template->drawIcon(HL_FILE_UPLOAD, '32_submit.gif', '', 'validateCreate()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=files&folder='.$folder);
	
	if ($protect->perm('create_files')) {
	  echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
   }
}

function rightViewComments($hydra, $hydra_template, $protect)
{
	$body   = '';
	$folder = intval(mosGetParam($_REQUEST, 'folder', 0));
	
	$body .= $protect->perm('create_comment', $hydra_template->drawIcon(HL_NEW_COMMENT, '32_comment.gif', '', 'newComment()'));
	$body .= $hydra_template->drawIcon(HL_GO_BACK, '32_back.gif', 'area=files&folder='.$folder);
	
    if ($protect->perm('view_comments')) {
	     echo $hydra_template->drawBox(HL_AVAILABLE_ACTIONS, $body);
    }
}
?>
