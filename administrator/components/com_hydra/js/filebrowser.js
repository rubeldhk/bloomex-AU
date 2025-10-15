/**
* @package   Hydra
* @copyright Copyright (C) 2006 Tobias Kuhn. All rights reserved.
* @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*
*
* Hydra is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
**/

/**
* @author  Tobias Kuhn
* @name    browse
* @version 1.0 
* @param   int id
* @return  void
* @desc    browse by clicking on a folder
**/
function browse(id)
{
   var current_folder = document.adminForm.folder;
   
   document.adminForm.browse_mode.value = "1";
   
   document.adminForm.area.value         = 'files';
   
   if (document.adminForm.cmd.value == 'read_data') {
      document.adminForm.data_type.value   = ' ';
      document.adminForm.cmd.value         = ' ';
   }   
   current_folder.value = id;  
   document.adminForm.submit();	
}

/**
* @author  Tobias Kuhn
* @name    browsePathway
* @version 1.0 
* @param   void
* @return  void
* @desc    browse by entering something in the addressbar
**/
function browsePathway()
{
	var pathway = document.adminForm.pathway;

	document.adminForm.browse_mode.value = "2";
	document.adminForm.data_type.value   = ' ';
	document.adminForm.cmd.value         = ' ';
	
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    newFolder
* @version 1.0 
* @param   void
* @return  void
* @desc    changes command to 'new_folder'
**/
function newFolder()
{	
	document.adminForm.cmd.value = 'new_folder';
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    editFolder
* @version 1.0 
* @param   int edit
* @return  void
* @desc    changes command to 'new_folder' for editing
**/
function editFolder(edit)
{
	document.adminForm.id.value  = edit;
	document.adminForm.cmd.value = 'new_folder';
	document.adminForm.submit();	
}

/**
* @author  Tobias Kuhn
* @name    newDocument
* @version 1.0 
* @param   void
* @return  void
* @desc    changes command to 'new_document'
**/
function newDocument()
{
	document.adminForm.cmd.value = 'new_document';
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    readDoc
* @version 1.1
* @param   int doc
* @return  void
* @desc    open a hydra document
**/
function readDoc(doc)
{
	document.adminForm.cmd.value = 'read_data';
	document.adminForm.area.value = 'files';
	
	document.adminForm.id.value        = doc;
	document.adminForm.data_type.value = 'document';
	
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    editDoc
* @version 1.0 
* @param   int edit
* @return  void
* @desc    changes command to 'new_document' for editing a hydra-doc
**/
function editDoc(edit)
{
	document.adminForm.id.value  = edit;
	document.adminForm.cmd.value = 'new_document';
	document.adminForm.submit();	
}

/**
* @author  Tobias Kuhn
* @name    moveDoc
* @version 1.0 
* @param   int move
* @return  void
* @desc    enables move-mode for a documents
**/
function moveDoc(move)
{
	document.adminForm.id.value = move;
	document.adminForm.browse_mode.value = '1';
	document.adminForm.data_type.value = 'document';
	document.adminForm.cmd.value = 'move_data';
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    moveFolder
* @version 1.0 
* @param   int move
* @return  void
* @desc    enables move-mode for a folder
**/
function moveFolder(move)
{
	document.adminForm.id.value = move;
	document.adminForm.browse_mode.value = '1';
	document.adminForm.data_type.value = 'folder';
	document.adminForm.cmd.value = 'move_data';
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    moveFolder
* @version 1.0 
* @param   int move
* @return  void
* @desc    submit the form to change the data-position
**/
function confirmMove()
{
	document.adminForm.cmd.value = 'move_confirm';
	document.adminForm.browse_mode.value = '1';
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    seekMode
* @version 1.0 
* @param   void
* @return  void
* @desc    
**/
function seekMode()
{
	document.adminForm.cmd.value = '';
	document.adminForm.browse_mode.value = '1';
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    newDocComment
* @version 1.0 
* @param   int doc
* @return  void
* @desc    
**/
function newDocComment(doc)
{
	document.adminForm.cmd.value = 'new_comment';
	document.adminForm.data_type.value = 'document';
	document.adminForm.id.value = doc;
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    newDataComment
* @version 1.0 
* @param   string data
* @return  void
* @desc    
**/
function newDataComment(data)
{
	document.adminForm.cmd.value = 'new_comment';
	document.adminForm.data_type.value = 'data';
	document.adminForm.id.value = data;
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    newUpload
* @version 1.0 
* @param   void
* @return  void
* @desc    
**/
function newUpload()
{
    document.adminForm.id.value  = 0;
	document.adminForm.cmd.value = 'create_files';
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    editFile
* @version 1.0 
* @param   int edit
* @return  void
* @desc    
**/
function editFile(edit)
{
	document.adminForm.cmd.value = 'create_files';
	document.adminForm.id.value  = edit;
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    moveFile
* @version 1.0 
* @param   int move
* @return  void
* @desc    
**/
function moveFile(move)
{
	document.adminForm.id.value = move;
	document.adminForm.browse_mode.value = '1';
	document.adminForm.data_type.value = 'data';
	document.adminForm.cmd.value = 'move_data';
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    readFile
* @version 1.0 
* @param   int file
* @return  void
* @desc    
**/
function readFile(file)
{
	document.adminForm.id.value  = file;
	document.adminForm.data_type.value = 'data';
	document.adminForm.cmd.value  = 'read_data';
	document.adminForm.area.value = 'files';
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    viewHydraComment
* @version 1.0 
* @param   int doc_id
* @return  void
* @desc    
**/
function viewHydraComment(doc_id)
{
	document.adminForm.id.value  = doc_id;
	document.adminForm.data_type.value = 'document';
	document.adminForm.cmd.value = 'view_comments';
	document.adminForm.submit();
}

/**
* @author  Tobias Kuhn
* @name    viewDataComment
* @version 1.0 
* @param   int file_id
* @return  void
* @desc    
**/
function viewDataComment(file_id)
{
	document.adminForm.id.value  = file_id;
	document.adminForm.data_type.value = 'data';
	document.adminForm.cmd.value = 'view_comments';
	document.adminForm.submit();
}