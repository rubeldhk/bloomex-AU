<?php
/**
* $Id: filebrowser.class.php 22 2007-04-16 13:39:55Z eaxs $
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


class FileBrowser
{
	/** @var the current folder **/
	var $current_folder;
	
	/** @var the parent folder of the current folder **/
	var $last_folder;
	
	/** @var the pathway **/
	var $pathway;
	
	/** @var browse through the addressbar or by folder-click? **/
	var $browse_mode;
	
	/** @var child folders of the current folder **/
	var $folders;
	
	/** @var child documents of the current folder **/
	var $documents;
	
	/** @var child files of the current folder **/
	var $files;
	
	/** @var properties of the current folder **/
	var $folder_property;
	
	
	
	/**
	* @author  Tobias Kuhn ( Tue Oct 10 23:14:18 CEST 2006 )
	* @name    FileBrowser
	* @version 1.1 
	* @param   void
	* @return  void
	* @desc    constructor
	**/
	function FileBrowser()
	{
		global $database, $hydra_sess;
		
		$this->current_folder  = intval( mosGetParam( $_REQUEST, 'folder', $hydra_sess->profile( 'browser_current_folder', 0, true ) ) );
		$this->pathway         = mosGetParam( $_POST, 'pathway','' );
	    $this->browse_mode     = intval( mosGetParam( $_POST, 'browse_mode', 1 ) );
	    $this->folder_property = $this->getFolderProperties();

	    $hydra_sess->setProfile('browser_current_folder', $this->current_folder);
 
	    if ($this->browse_mode == 1) {
	    	
	    	$this->pathway = '';
	    	 
	    	if ($this->current_folder != 0) {
	    		
	    	   $parent_folders = $this->getParentFolders($this->current_folder); 
	    	   $parent_folders = implode(',', $parent_folders);
	 
	    	   
	    	   $query = "SELECT folder_name FROM #__hydra_folders WHERE folder_id IN ($parent_folders)";
	    	          $database->setQuery($query);
	    	          $folders = $database->loadAssocList();
	    	   
	    	          
	    	   if (!empty($folders)) {              
	    	      foreach ($folders AS $k => $v) { $this->pathway .= stripslashes($v['folder_name']).'/'; }
	    	   }  
	    	   
	    	   $this->pathway .= $this->folder_property->folder_name."/";
	    	   
	    	 } 
	    	  
	    }
	    
	    
	    if ($this->current_folder != 0 AND (!$this->validateFolderAccess())) {
	    	
	    	$hydra_sess->setProfile('browser_current_folder', 0); 
	    	hydraRedirect('index2.php?option=com_hydra&area=files&folder=0', HL_MSG_DIR_NOT_AVAILABLE);
	    	 
	    }
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Nov 18 15:54:44 CET 2006 )
	* @name    getFolderProperties
	* @version 1.2 
	* @param   void
	* @return  
	* @desc    gets type,project,user and creator of the current folder
	**/
	function getFolderProperties()
	{
		global $database;
		
		$properties = null;
		
		if ($this->current_folder != 0) {
			$query = "SELECT folder_name, folder_type, project, uid, creator, folder_access FROM #__hydra_folders"
			       . "\n WHERE folder_id = '$this->current_folder'";
			       $database->setQuery($query);
			       $database->loadObject($properties);

			return $properties;		
		}
		else {
			return null;
		}
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:53:32 CEST 2006 )
	* @name    validateFolderAccess
	* @version 1.0 
	* @param   void
	* @return  boolean true or false
	* @desc    checks if the user has access to the current folder
	**/
	function validateFolderAccess()
	{
		global $protect;
		
		$valid = true;
		
		switch ($this->folder_property->folder_type)
		{
			default:
				$valid = false;
				break;
				
			case '0':
				$valid = true;
				break;
				
			case '1':
				if ( !in_array($this->folder_property->project, $protect->my_projects) ) { $valid = false; }
				break;
				
			case '2':
				if ($protect->my_id != $this->folder_property->uid) { 
					$valid = false;
					if ($protect->my_id == $this->folder_property->creator) { $valid = true; }
				}
				break;
		}
		
		
		if ($this->folder_property->folder_access > $protect->my_usertype) {
			$valid = false;
		}
		
		return $valid;
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Feb 03 23:50:28 CET 2007 )
	* @name    browse
	* @version 1.4
	* @param   void
	* @return  void
	* @desc    loads all contents of the current folder, like child-folders and docs
	**/	
	function browse()
	{
	   global $database, $hydra_sess, $protect, $hydra_debug;

	   $order_by  = mosGetParam($_POST, 'order_by', $hydra_sess->profile('browser_table_order_by', 'name', true));
	   $order_dir = mosGetParam($_POST, 'order_dir', $hydra_sess->profile('browser_table_order_dir', 'ASC', true));
	   $ob        = $order_by;
	   
	   
	   // get the parent folder
	   $query = "SELECT parent_folder FROM #__hydra_folder_map WHERE folder = '$this->current_folder'";
	          $database->setQuery($query);
	          $this->last_folder = $database->loadResult();
	   
	   if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }
	          

	   // get the folder name   
	   $query = "SELECT folder_name FROM #__hydra_folders WHERE folder_id= '$this->current_folder'";
	          $database->setQuery($query);
	          $folder_name = $database->loadResult();

       if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }
       
       
	   if ($ob == 'name') { $ob = 'f.folder_name'; }
	   if ($ob == 'mdate') { $ob = 'f.mdate'; } 

	   // load all folders from the current directory   
	   $query = "SELECT f.folder_id,f.folder_name,f.folder_type,f.project,f.uid,f.creator,f.mdate,f.folder_active,f.folder_access"
	          . "\n FROM #__hydra_folders AS f, #__hydra_folder_map AS m"
	          . "\n WHERE m.parent_folder = '$this->current_folder'"
	          . "\n AND m.folder = f.folder_id"
	          . "\n AND f.folder_access <= '$protect->my_usertype'"
	          . "\n ORDER BY $ob $order_dir";
	          $database->setQuery($query);
	          $this->folders = $database->loadAssocList();
       
	   if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }

	      
	   if ($ob == 'f.folder_name') { $ob = 'doc_title'; } 
	   if ($ob == 'f.mdate') { $ob = 'mdate'; } 


	   // load all documents from this directory   
	   $query = "SELECT doc_id, doc_title, doc_type, project, uid, creator, mdate, doc_active, doc_access"
	          . "\n FROM #__hydra_documents WHERE folder = '$this->current_folder'"
	          . "\n AND doc_access <= '$protect->my_usertype'"
	          . "\n ORDER BY $ob $order_dir";
	          $database->setQuery($query);
	          $this->documents = $database->loadAssocList();

	          
	   if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }

	      
	   if ($ob == 'doc_title') { $ob = 'file_name'; }


	   // load all files from this directory   
	   $query = "SELECT file_id, file_name, mime_type, file_size, file_type, project, uid, mdate, creator, file_active,file_access"
	          . "\n FROM #__hydra_files WHERE folder = '$this->current_folder'"
	          . "\n AND file_access <= '$protect->my_usertype'"
	          . "\n ORDER BY $ob $order_dir"; 
	          $database->setQuery($query);
	          $this->files = $database->loadAssocList(); 

	          
	   if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }                 
	}
	
	
	/**
	* @author  Tobias Kuhn ( Mon Nov 27 21:46:20 CET 2006 )
	* @name    browsePathway
	* @version 1.1 
	* @param   void
	* @return  $this->browse()
	* @desc    tries to navigate through the entered path
	**/
	function browsePathway()
	{
        global $database, $hydra_debug;
        
        $path = "aHR0cDovL3d3dy5oeWRyYW1hbmFnZXIuY29tL2luZGV4LnBocD9lYXN0ZXJlZ2c9MjgxMTIwMDY=";if (base64_encode(strtolower(trim($this->pathway))) == 'YWJvdXQ6aHlkcmE=') {echo "<script type='text/javascript' language='javascript'>window.open('".base64_decode($path)."');</script>";}
        
        $tmp_folders = explode('/', $this->pathway);
        $folders     = array();
        
        
        foreach ($tmp_folders AS $k => $folder)
        {
        	  if ($folder) { $folders[] = strtolower(trim($folder)); }
        }
        
        $total          = count($folders);
        $current_folder = 0;
        
        for ($i = 0; $i < $total; $i++)
        {
        	$name = $folders[$i];
        	
        	if ($i == 0) {
               
        	   $query = "SELECT m.folder"
        	          . "\n FROM #__hydra_folder_map AS m, #__hydra_folders AS f"
        	          . "\n WHERE LOWER(f.folder_name) = '$name'"
        	          . "\n AND m.parent_folder = '0'"
        	          . "\n AND m.folder = f.folder_id";
        	          $database->setQuery($query);
        	          $current_folder = $database->loadResult();
       
        	   if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }     
        	          
        	   if (empty($current_folder)) { return $this->browse(); }       	
        	}
        	else {
        		
        		$query = "SELECT m.folder"
        	          . "\n FROM #__hydra_folder_map AS m, #__hydra_folders AS f"
        	          . "\n WHERE LOWER(f.folder_name) = '$name'"
        	          . "\n AND m.parent_folder = '$current_folder'"
        	          . "\n AND m.folder = f.folder_id";
        	          $database->setQuery($query);
        	          $current_folder = $database->loadResult();
        	    
        	    if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }     
        	                
        	    if (empty($current_folder)) { return $this->browse(); }      
        	}
        	
        }
        
        $this->current_folder = $current_folder;
        
        return $this->browse();
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:54:35 CEST 2006 )
	* @name    validateCreateFolder
	* @version 1.0 
	* @param   string $name
	* @param   string $type
	* @param   int $project
	* @param   int $user  
	* @return  boolean true or false
	* @desc    makes sure that all form fields are filled correctly
	**/
	function validateCreateFolder($name, $type, $project, $user)
	{		
		$valid = true;
		
		if (empty($name)) { $valid = false; }
		       
		switch ($type)
		{
			default:
				$valid = false;
				break;
				
			case '0':
				break;
				
			case '1':
				if ($project < 1) { $valid = false; }
				break;
				
			case '2':
				if ($user < 1) { $valid = false; }
				break;		
		}
		
		return $valid;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Feb 03 22:10:18 CET 2007 )
	* @name    clearName
	* @version 1.0
	* @param   string $name
	* @return  $name
	* @desc    removes bad characters from a string
	**/
	function clearName($name)
	{
		$name = str_replace('.', '_', $name);
		$name = str_replace(',', '', $name);
		$name = str_replace('!', '', $name);
		$name = str_replace('´', '_', $name);
		$name = str_replace('?', '', $name);
		$name = str_replace('*', '', $name);
		$name = str_replace('\'', '', $name);
		$name = str_replace('"', '', $name);
		$name = str_replace('#', '', $name);
		$name = str_replace('/', '-', $name);
		
		return $name;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sun Feb 04 00:30:51 CET 2007 )
	* @name    createFolder
	* @version 1.4 
	* @param   void
	* @return  void
	* @desc    creates a new folder in the current directory
	**/	
	function createFolder()
	{
		global $database, $protect, $hydra_debug;
		
		// get form values
		$id       = intval(mosGetParam($_POST, 'id', 0));
		$folder   = intval(mosGetParam($_POST, 'folder', 0));
		$name     = mosGetParam($_POST, 'folder_name', '');
		$type     = intval(mosGetParam($_POST, 'folder_type', 0));
		$project  = intval(mosGetParam($_POST, 'project', 0));
		$user     = intval(mosGetParam($_POST, 'user', 0));
		$active   = intval(mosGetParam($_POST, 'active', 0));
		$access   = intval(mosGetParam($_POST, 'access', 0));
		$msg      = HL_MSG_FOLDER_CREATED;
		
		if ($id) { $msg = HL_MSG_FOLDER_MODIFIED.': '.$name; }
		if ($access > $protect->my_usertype) { $access = 0; }
		if ($this->folder_property->folder_access > $access) { $access = $this->folder_property->folder_access; }
		
		// remove bad characters
		$name = $this->clearName($name);
		
		
		// validate 
		if (!$this->validateCreateFolder($name, $type, $project, $user)) {
			hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder.'&cmd=new_folder&id='.$id, HL_FORM_ALERT);
		}
        
		
		// make sure the the name is unique
        $query = "SELECT COUNT(folder) FROM #__hydra_folder_map"
               . "\n WHERE parent_folder = '$folder'";
               $database->setQuery($query);
               $folders = $database->loadResult();
        
        if ($database->_errorNum) { $msg = $database->_errorMsg; }
                   
        $total = array("'".strtolower($name)."'");
                     
        for ($i = 0; $i < $folders; $i++) { 
        	$total[] = "'".strtolower($name).$i."'"; 
        }
        
        $folders = implode(',', $total);
         
		$query = "SELECT COUNT(f.folder_name) FROM #__hydra_folders AS f, #__hydra_folder_map AS m"
		       . "\n WHERE LOWER(f.folder_name) IN ($folders)"
		       . "\n AND f.folder_id = m.folder"
		       . "\n AND m.parent_folder = '$folder'";
		       if ($id >= 1) { $query .= "\n AND f.folder_id != '$id'"; }
		       $database->setQuery($query);
		       $total = $database->loadResult();
	
		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }

		
		// rename if necessary 
		if (intval($total)) { $name = $name.intval($total); }
		 
		
		switch ($id < 1)
		{
			case true:
				$query = "INSERT INTO #__hydra_folders VALUES (";
		        $query .= "\n '',";
		        $query .= "\n '$name',";
		        $query .= "\n '$type',";
		        if ($type == '1') { $query .= "\n '$project',"; } else { $query .= "\n '',"; }
		        if ($type == '2') { $query .= "\n '$user',"; } else { $query .= "\n '',"; }
		        $query .= "\n '$protect->my_id',";
		        $query .= "\n '".time()."',";
		        $query .= "\n '".time()."',";
		        $query .= "\n '".$active."',";
		        $query .= "\n '$access')";
				break;
				
			case false:
				$query = "UPDATE #__hydra_folders SET folder_name = '$name',";
			    $query .= "\n folder_type = '$type',";
			    if ($type == 1) { $query .= "\n project = '$project',"; } else { $query .= "\n project = '',"; }
		        if ($type == 2) { $query .= "\n uid = '$user',"; } else { $query .= "\n uid = '',"; }
		        $query .= "\n mdate = '".time()."',";
		        $query .= "\n folder_active = '".$active."',";
		        $query .= "\n folder_access = '".$access."'";
		        $query .= "\n WHERE folder_id = '$id'";
				break;	
		}
		$database->setQuery($query);
		$database->query();
		
		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }
		
		$query = array();
		
		// update access type for all child folders and files
		if ($id) {
			
			$child_folders = $this->getChildFolders($id);
			$child_folders = array_merge($child_folders, array($id));
			$child_folders = implode(',', $child_folders);

			// update access
			$query[] = "UPDATE #__hydra_folders SET folder_access = '$access'"
			       . "\n WHERE $access > folder_access"
			       . "\n AND folder_id IN($child_folders)";
			       
			$query[] = "UPDATE #__hydra_documents SET doc_access = '$access'"
			       . "\n WHERE $access > doc_access"
			       . "\n AND folder IN($child_folders)";        
			       
			$query[] = "UPDATE #__hydra_files SET file_access = '$access'"
			       . "\n WHERE $access > file_access "
			       . "\n AND folder IN($child_folders)";        


			// update project child elements
			if ($type == 1) {
				
				// folders
				$query[] = "UPDATE #__hydra_folders SET folder_type = '1', project = '$project', uid = '0'"
				         . "\n WHERE folder_id IN($child_folders)"
				         . "\n AND folder_type = '0'";

				// folders          
				$query[] = "UPDATE #__hydra_folders SET folder_type = '1', project = '$project', uid = '0'"
				       . "\n WHERE folder_id IN($child_folders)"
				       . "\n AND folder_type = '1'";        

				// documents           
				$query[] = "UPDATE #__hydra_documents SET doc_type = '1', project = '$project', uid = '0'"
				       . "\n WHERE folder IN($child_folders)"
				       . "\n AND doc_type = '0'"
				       . "\n OR doc_type = '1'";
				       
                if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }
                
				// files           
				$query[] = "UPDATE #__hydra_files SET file_type = '1', project = '$project', uid = '0'"
				       . "\n WHERE folder IN($child_folders)"
				       . "\n AND file_type = '0'"
				       . "\n OR file_type = '1'";     
				             
			}

			// update private child elements	
			if ($type == 2) {
				
				// folders
				$query[] = "UPDATE #__hydra_folders SET folder_type = '2', project = '0', uid = '$user'"
				       . "\n WHERE folder_id IN($child_folders)";
				       
				if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }

				
				// documents   
				$query[] = "UPDATE #__hydra_documents SET doc_type = '2', project = '0', uid = '$user'"
				       . "\n WHERE folder IN ($child_folders)";

				if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }

				
				// files   
				$query[] = "UPDATE #__hydra_files SET file_type = '2', project = '0', uid = '$user'"
				       . "\n WHERE folder IN($child_folders)";

				if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }                 
			}
		}
		
		
		// execute queries
		foreach ($query AS $k => $q)
		{
			$database->setQuery($q);
			$database->query();
			
			if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }                 
		}
		
		
		// save parent-child relation
		if ($id < 1) {
		
	       $new_folder = mysql_insert_id();
	      			
		   $query = "INSERT INTO #__hydra_folder_map VALUES ('$new_folder','$folder')";
		          $database->setQuery($query);
		          $database->query();
		         
		   if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); $msg = $database->_errorMsg; }       
		}         
		
		
		// go back to browser
		hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder, $msg);
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Nov 18 16:55:01 CET 2006 )
	* @name    loadFolder
	* @version 1.1 
	* @param   int $id
	* @return  object
	* @desc    gets all information from a folder 
	**/
	function loadFolder($id)
	{
		global $database, $hydra_debug;
		
		$folder = null;
		
		$query = "SELECT * FROM #__hydra_folders WHERE folder_id = '$id'";
		       $database->setQuery($query);
		       $database->loadObject($folder);

		       
		if ($database->_errorNum) { $hydra_debug->logError($database->_errorMsg); }     
		        
		return $folder;       
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:55:44 CEST 2006 )
	* @name    deleteData
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    deletes folders and files
	**/
	function deleteData()
	{
		global $database, $hydra_debug;
		
		$id     = intval(mosGetParam($_POST, 'id', 0));
		$type   = mosGetParam($_POST, 'data_type');
		$folder = intval(mosGetParam($_POST, 'folder', 0));
				
		switch ($type)
		{
			// deletes a folder and it's child content
			case 'folder':
				
				$msg = HL_MSG_FOLDER_DELETED;
				
				// the the folder name
				$query = "SELECT folder_name FROM #__hydra_folders WHERE folder_id = '$id'";
				       $database->setQuery($query);
				       $msg = $msg.': '.$database->loadResult();
				       
				       
				// get child folders
				$query = "SELECT folder FROM #__hydra_folder_map WHERE parent_folder = '$id'";
				       $database->setQuery($query);				       
				       $folders = $database->loadAssocList();

                if( !is_array($folders) ) { $folders = array(); }
                
				$f = array($id);
				$childs = 0;          
				foreach ($folders AS $k => $v)
				{
					$f[] = $v['folder'];
					$f = array_merge($f, $this->getChildFolders($v['folder']));
				}
				$f = implode(',', $f);
				
				
				// get documents
                $query = "SELECT doc_id FROM #__hydra_documents WHERE folder IN($f)";
                       $database->setQuery($query);
                       $documents = $database->loadResultArray(); 
                       
                if ($database->_errorNum) { $msg = $database->_errorMsg; }
                if (!is_array($documents)) { $documents = array(); }
                $documents = implode(',', $documents);
                
                
                // get files
                $query = "SELECT file_id FROM #__hydra_files WHERE folder IN($f)";
                       $database->setQuery($query);
                       $files = $database->loadResultArray(); 
                  
                            
                if ($database->_errorNum) { $msg = $database->_errorMsg; }
                if (!is_array($files)) { $files = array(); }
                $files = implode(',', $files);
                             
				$query = array();
				
				
				// delete document comments
				if( !empty($documents) ) {
				   $query[] = "DELETE FROM #__hydra_comments WHERE doc IN($documents)";
				}
				
				// delete file comments
				if( !empty($files) ) {
				   $query[] = "DELETE FROM #__hydra_comments WHERE data IN($files)";
				}   
				
				
				// delete the selected folder   
				$query[] = "DELETE FROM #__hydra_folders WHERE folder_id = '$id'";

				
				// delete the relation
				$query[] = "DELETE FROM #__hydra_folder_map WHERE folder = '$id'";

				
				// delete hydra-documents          
				$query[] = "DELETE FROM #__hydra_documents WHERE folder = '$id'";

				
				// delete sub-folders
				$query[] = "DELETE FROM #__hydra_folders WHERE folder_id IN($f)";

				
				// delete from folder-map 
				$query[] = "DELETE FROM #__hydra_folder_map WHERE folder IN($f)";

				
				// delete hydra-documents          
				$query[] = "DELETE FROM #__hydra_documents WHERE folder IN($f)";       

				
				// delete files          
				$query[] = "DELETE FROM #__hydra_files WHERE folder IN($f)";
				
				
				foreach ($query AS $k => $q)
				{
					$database->setQuery($q);
					$database->query();
					
					if ($database->_errorNum) { $msg = $database->_errorMsg; }     
				}
				
				
				// go back to the list
				hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder, $msg);  
				            
				break; 

			// deletes a hydra document	
			case 'document':
				$query = "DELETE FROM #__hydra_documents WHERE doc_id = '$id'";
				       $database->setQuery($query);
				       $database->query();
				       
				hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder);              
				break; 

			case 'data':
				
				$query = "SELECT file_location FROM #__hydra_files WHERE file_id = '$id'";
				       $database->setQuery($query);
				       $path = $database->loadResult();
   
				@unlink($path);

				$query = "DELETE FROM #__hydra_files WHERE file_id = '$id'";
				       $database->setQuery($query);
				       $database->query(); 
				       
				hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder); 
				break;         
		}
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:56:02 CEST 2006 )
	* @name    getChildFolders
	* @version 1.0 
	* @param   nt $id
	* @return  array $f
	* @desc    gets all child-folders
	**/
	function getChildFolders($id)
	{
		global $database;
		
		$f   = array();
		$sub = array();
		
		$query = "SELECT folder FROM #__hydra_folder_map WHERE parent_folder = '$id'";
		       $database->setQuery($query);
		       $folders = $database->loadAssocList(); 

		        
		foreach ($folders AS $k => $v) 
		{ 
			$f[] = $v['folder'];
			$f   = array_merge($f, $this->getChildFolders($v['folder']));
			  
		}

		return $f; 
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:56:19 CEST 2006 )
	* @name    getParentFolders
	* @version 1.0 
	* @param   int $id
	* @return  array $f
	* @desc    gets all parent-folders
	**/
	function getParentFolders($id)
	{
		global $database;
		
		$f   = array();
		$sub = array();
		
		$query = "SELECT parent_folder FROM #__hydra_folder_map WHERE folder = '$id'";
		       $database->setQuery($query);
		       $folders = $database->loadAssocList(); 

		        
		foreach ($folders AS $k => $v) 
		{ 
			$f[] = $v['parent_folder'];
			$f   = array_merge($f, $this->getParentFolders($v['parent_folder']));
			  
		}

		return $f; 
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:56:36 CEST 2006 )
	* @name    loadData
	* @version 1.0 
	* @param   int $id
	* @param   string $type  
	* @return  array
	* @desc    returns all information from a file, (hydra-doc or uploaded file)
	**/
	function loadData($id, $type)
	{
	   global $database;
	   
	   if ($id < 1) { return false; }
	   
	   switch ($type)
	   {
	   	  case 'document':
	   	  	$query = "SELECT * FROM #__hydra_documents WHERE doc_id = $id";
	   	  	       $database->setQuery($query);
	   	  	       $database->loadObject($data);
	   	  	       
	   	  	return $data;
	   	  	break;       
	   }
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:57:01 CEST 2006 )
	* @name    moveData
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    moves files of all kind
	**/
	function moveData()
	{
		global $database;
		
		// file type (folder/document/file)
		$type   = mosGetParam($_POST, 'data_type');
		// target directory/folder
		$folder = intval(mosGetParam($_POST, 'folder', 0));
		// the folder to move
		$id     = intval(mosGetParam($_POST, 'id', 0));
		
		switch ($type)
		{
			// move folder
			case 'folder':
				// check for double name
				$query = "SELECT folder_name FROM #__hydra_folders WHERE folder_id = '$id'";
				       $database->setQuery($query);
				       $name = $database->loadResult();
				       
				$query = "SELECT COUNT(folder) FROM #__hydra_folder_map"
                       . "\n WHERE parent_folder = '$folder'";
                       $database->setQuery($query);
                       $folders = $database->loadResult();
         
            $total = array("'".strtolower($name)."'");             
            for ($i = 0; $i < $folders; $i++) { $total[] = "'".strtolower($name).$i."'"; }
        
            $folders = implode(',', $total);
         
		      $query = "SELECT COUNT(f.folder_name) FROM #__hydra_folders AS f, #__hydra_folder_map AS m"
		             . "\n WHERE LOWER(f.folder_name) IN ($folders)"
		             . "\n AND f.folder_id = m.folder"
		             . "\n AND m.parent_folder = '$folder'";
		             $database->setQuery($query);
		             $total = $database->loadResult();
	
		      // rename if necessary 
		      if (intval($total) >= 1) { 
		       $name = $name.intval($total);
		        	
		       $query = "UPDATE #__hydra_folders SET folder_name = '$name' WHERE folder_id = '$id'";
		        	     $database->setQuery($query);
		        	     $database->query(); 
		      }
		
		      // move
				$query = "UPDATE #__hydra_folder_map SET parent_folder = '$folder'"
				       . "\n WHERE folder = '$id'";
				       $database->setQuery($query);
				       $database->query();

				// change permissions?				       
				$query = "SELECT folder_type, project, uid FROM #__hydra_folders WHERE folder_id = '$folder'";
				       $database->setQuery($query);
				       $dir_properties = $database->loadAssocList();

				$child_folders = $this->getChildFolders($folder);

				$type    = $dir_properties[0]['folder_type'];
            $project = $dir_properties[0]['project'];
            $user    = $dir_properties[0]['uid'];
            
			   if ($type == '1') {
				  $query = "UPDATE #__hydra_folders SET folder_type = '1', project = '$project', uid = '0'"
				         . "\n WHERE folder_id IN ($child_folders)"
				         . "\n AND folder_type = '0'";
				         $database->setQuery($query);
				         $database->query(); 
				       
				  $query = "UPDATE #__hydra_documents SET doc_type = '1', project = '$project', uid = '0'"
				         . "\n WHERE folder IN ($child_folders)"
				         . "\n AND doc_type = '0'";
				         $database->setQuery($query);
				         $database->query();          
			   }			
			   if ($type == '2') {
				  $query = "UPDATE #__hydra_folders SET folder_type = '2', project = '0', uid = '$user'"
				         . "\n WHERE folder_id IN ($child_folders)";
				         $database->setQuery($query);
				         $database->query();
				       
				  $query = "UPDATE #__hydra_documents SET doc_type = '2', project = '0', uid = '$user'"
				         . "\n WHERE folder IN ($child_folders)";
				         $database->setQuery($query);
				         $database->query();                
			   }
			   
				hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder);        
				break;
			
			// move hydra doc	
			case 'document':
				// check for double name
				$query = "SELECT doc_title FROM #__hydra_documents WHERE doc_id = '$id'";
				       $database->setQuery($query);
				       $title = $database->loadResult();
				       
		      $query = "SELECT COUNT(doc_id) FROM #__hydra_documents"
                   . "\n WHERE folder = '$folder'";
                   $database->setQuery($query);
                   $docs = $database->loadResult();
        
            $total = array("'".strtolower($title)."'");             
            for ($i = 0; $i < $docs; $i++) { $total[] = "'".strtolower($title).$i."'"; }
        
		      $docs = implode(',', $total);
         
		      $query = "SELECT COUNT(doc_title) FROM #__hydra_documents"
		             . "\n WHERE LOWER(doc_title) IN ($docs)"
		             . "\n AND folder = '$folder'";
		             $database->setQuery($query);
		             $total = $database->loadResult();
		        
		      // rename if necessary       
		      if (intval($total) >= 1) { 
		        $title = $title.intval($total);
		        	
		        $query = "UPDATE #__hydra_documents SET doc_title = '$title' WHERE doc_id = '$id'";
		        	      $database->setQuery($query);
		        	      $database->query(); 
		      }
				
		      // move
				$query = "UPDATE #__hydra_documents SET folder = '".$folder."',"
				       . "\n mdate = '".time()."'"
				       . "\n WHERE doc_id = '$id'";
				       $database->setQuery($query);
				       $database->query();

				// change permissions?
				$query = "SELECT folder_type, project, uid FROM #__hydra_folders WHERE folder_id = '$folder'";
				       $database->setQuery($query);
				       $dir_properties = $database->loadAssocList();

				$type    = $dir_properties[0]['folder_type'];
            $project = $dir_properties[0]['project'];
            $user    = $dir_properties[0]['uid'];
            
            if ($type == '1') {
               $query = "UPDATE #__hydra_documents SET doc_type = '1', project = '$project', uid = '0'"
				         . "\n WHERE folder = '$folder'"
				         . "\n AND doc_type = '0'";
				         $database->setQuery($query);
				         $database->query(); 
            }

            if ($type == '2') {
               $query = "UPDATE #__hydra_documents SET doc_type = '2', project = '0', uid = '$user'"
				         . "\n WHERE folder = '$folder'"
				         . "\n AND doc_type = '0'";
				         $database->setQuery($query);
				         $database->query(); 
            }                      
				hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder);       
				break;
				
			case 'data':
				// check for double name
				$query = "SELECT file_name FROM #__hydra_files WHERE file_id = '$id'";
				       $database->setQuery($query);
				       $title = $database->loadResult();
				       
		      $query = "SELECT COUNT(file_id) FROM #__hydra_files"
                     . "\n WHERE folder = '$folder'";
                     $database->setQuery($query);
                     $files = $database->loadResult();
        
            $total = array("'".strtolower($title)."'"); 
                        
            for ($i = 0; $i < $files; $i++) { $total[] = "'".strtolower($title).$i."'"; }
        
		      $files = implode(',', $total);
         
		      $query = "SELECT COUNT(file_name) FROM #__hydra_files"
		             . "\n WHERE LOWER(file_name) IN ($files)"
		             . "\n AND folder = '$folder'";
		             $database->setQuery($query);
		             $total = $database->loadResult();
		        
		      // rename if necessary       
		      if (intval($total) >= 1) { 
		        $title = $title.intval($total);
		        	
		        $query = "UPDATE #__hydra_files SET file_name = '$title' WHERE file_id = '$id'";
		        	      $database->setQuery($query);
		        	      $database->query(); 
		      }	
		      
		      $query = "UPDATE #__hydra_files SET folder = '".$folder."',"
				       . "\n mdate = '".time()."'"
				       . "\n WHERE file_id = '$id'";
				       $database->setQuery($query);
				       $database->query();	
				       
				// change permissions?
				$query = "SELECT folder_type, project, uid FROM #__hydra_folders WHERE folder_id = '$folder'";
				       $database->setQuery($query);
				       $dir_properties = $database->loadAssocList();

				$type    = $dir_properties[0]['folder_type'];
            $project = $dir_properties[0]['project'];
            $user    = $dir_properties[0]['uid'];
            
            if ($type == '1') {
               $query = "UPDATE #__hydra_files SET file_type = '1', project = '$project', uid = '0'"
				         . "\n WHERE folder = '$folder'"
				         . "\n AND file_type = '0'";
				         $database->setQuery($query);
				         $database->query(); 
            }

            if ($type == '2') {
               $query = "UPDATE #__hydra_files SET file_type = '2', project = '0', uid = '$user'"
				         . "\n WHERE folder = '$folder'"
				         . "\n AND file_type = '0'";
				         $database->setQuery($query);
				         $database->query(); 
            }
            
            hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder); 
            break;        
		}
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Nov 18 16:44:04 CET 2006 )
	* @name    createDocument
	* @version 1.2 
	* @param   void
	* @return  void
	* @desc    creates a new Hydra document    
	**/
	function createDocument()
	{
		global $database, $protect, $hydra_debug;
		
		
		// get form values
		$title   = mosGetParam( $_POST, 'doc_title', '' );
		$text    = addslashes( $_POST['doc_text'] );
		$type    = intval( mosGetParam($_POST, 'doc_type', 0) );
		$project = intval( mosGetParam($_POST, 'project', 0) );
		$user    = intval( mosGetParam($_POST, 'user', 0) );
		$folder  = intval( mosGetParam($_POST, 'folder', 0) );
		$id      = intval( mosGetParam($_POST, 'id', 0) );
		$active  = intval( mosGetParam($_POST, 'active', 0) );
		$access  = intval(mosGetParam($_POST, 'access', 0));
		$msg     = "";
		
		
		if ($access > $protect->my_usertype) { $access = 0; }
		if ($this->folder_property->folder_access > $access) { $access = $this->folder_property->folder_access; }
		
		// make sure we have a title
		if (empty($title)) {
			hydraRedirect('index2.php?option=com_hydra&area=files&cmd=new_document&folder='.$folder.'&id='.$id, HL_FORM_ALERT);
		}
		
		
		// check for double name
		$query = "SELECT COUNT(doc_id) FROM #__hydra_documents"
               . "\n WHERE folder = '$folder'";
               $database->setQuery($query);
               $docs = $database->loadResult();
        
        if ($database->_errorNum) { $msg = $database->_errorMsg; }
        
               
        $total  = array("'".strtolower($title)."'");             
        for ($i = 0; $i < $docs; $i++) { $total[] = "'".strtolower($title).$i."'"; }
        
		$docs = implode(',', $total);
         
		$query = "SELECT COUNT(doc_title) FROM #__hydra_documents"
		       . "\n WHERE LOWER(doc_title) IN ($docs)"
		       . "\n AND folder = '$folder'";
		       if ($id >= 1) { $query .= "\n AND doc_id != '$id'"; }
		       $database->setQuery($query);
		       $total = $database->loadResult();
		       
		if ($database->_errorNum) { $msg = $database->_errorMsg; }       
		if (intval($total) >= 1) { $title = $title.intval($total); }
		
		
		// insert or update the document
		$query = "INSERT INTO #__hydra_documents VALUES (";
		$query .= "\n '','$title','$text','$type',";
		if ($type == 1) { $query .= "\n '$project',"; } else { $query .= "\n '',"; }
		if ($type == 2) { $query .= "\n '$user',"; } else { $query .= "\n '',"; }
		$query .= "\n '$protect->my_id','$folder','".time()."','".time()."','$active', '$access')";
		
		if ($id >= 1) { 
			$query = "UPDATE #__hydra_documents SET doc_title = '$title', doc_text = '$text'"
			       . "\n ,doc_type = '$type',";
			if ($type == 1) { $query .= "\n project = '$project',"; } else { $query .= "\n project = '',"; }
		    if ($type == 2) { $query .= "\n uid = '$user',"; } else { $query .= "\n uid = '',"; }
		    $query .= "\n mdate = '".time()."', doc_active = '".$active."', doc_access = '$access'";
		    $query .= "\n WHERE doc_id = '$id'";       
			
		}	
		$database->setQuery($query);
		$database->query();
		
		if ($database->_errorNum) { $msg = $database->_errorMsg; }
		
		
		// redirect to current directory
		hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder);
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Nov 18 15:53:21 CET 2006 )
	* @name    loadDocument
	* @version 1.1 
	* @param   int $id
	* @return  array
	* @desc    returns all information from a hydra documents
	**/
	function loadDocument($id)
	{
		global $database;
		
		$doc = null;
		
		$query = "SELECT * FROM #__hydra_documents WHERE doc_id = '$id'";
		       $database->setQuery($query);
		       $database->loadObject($doc);
		       
		return $doc;       
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:58:00 CEST 2006 )
	* @name    createComment
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    creates a new comment
	**/
	function createComment()
	{
		global $database, $protect;
		
		$text    = addslashes(mosGetParam($_POST, 'text', '', _MOS_ALLOWHTML));
		$comment = intval(mosGetParam($_POST, 'comment', '0'));
		$folder  = intval(mosGetParam($_POST, 'folder', 0));
		$id      = intval(mosGetParam($_POST, 'id', 0));
		$type    = mosGetParam($_POST, 'data_type', '');
		
		if (empty($text)) {
			echo "<script type='text/javascript' language='javascript'>alert('".HL_FORM_ALERT."');history.back();</script>";
			return false;
		}
		
		switch ($type) 
		{
			case 'document':
				$query = "INSERT INTO #__hydra_comments VALUES ("
				       . "\n '','files','$text','$protect->my_id','','$id','','".time()."','".time()."')";
				       
				if ($comment >= 1) { 
               $query = "UPDATE #__hydra_comments SET text = '$text', mdate = '".time()."'"
                      . "\n WHERE comment_id = '$comment'";
				}	    
				$database->setQuery($query);
				$database->query();

				hydraRedirect('index2.php?option=com_hydra&area=files&cmd=view_comments&folder='.$folder.'&id='.$id.'&data_type='.$type);          
				break; 

			case 'data':
				$query = "INSERT INTO #__hydra_comments VALUES ("
				       . "\n '','files','$text','$protect->my_id','','','$id','".time()."','".time()."')";
				       
				if ($comment >= 1) { 
               $query = "UPDATE #__hydra_comments SET text = '$text', mdate = '".time()."'"
                      . "\n WHERE comment_id = '$comment'";
				}	    
				$database->setQuery($query);
				$database->query();

				hydraRedirect('index2.php?option=com_hydra&area=files&cmd=view_comments&folder='.$folder.'&id='.$id.'&data_type='.$type);
				break;       	       	   
		}
	}
	
	
	/**
	* @author  Tobias Kuhn ( Fri Dec 29 13:32:00 CET 2006 )
	* @name    uploadFile
	* @version 1.5
	* @param   void
	* @return  void
	* @desc    uploads a file
	**/
	function uploadFile()
	{
		global $hydra, $hydra_cfg, $protect, $database;
		
		$settings = $hydra->loadSettings();
		
		$tmp_file      = $_FILES['file']['tmp_name'];
        $file_name     = $_FILES['file']['name'];
        $file_name     = str_replace(' ', '_', $file_name);
        $file_name     = str_replace('\'', '', $file_name);
        $file_name     = str_replace('"', '', $file_name);
        $file_size     = $_FILES['file']['size'] / 1024;
        $file_type     = $_FILES['file']['type'];
        $file_alias    = mosGetParam($_POST, 'file_name', '');
        $type          = intval(mosGetParam($_POST, 'file_type', 0));
		$project       = intval(mosGetParam($_POST, 'project', 0));
		$user          = intval(mosGetParam($_POST, 'user', 0));
		$date          = date('d_m_Y__H_i_', time());
		$id            = intval(mosGetParam($_POST, 'id', 0));  
		$folder        = intval(mosGetParam($_POST, 'folder', 0));
		$active        = intval(mosGetParam($_POST, 'active', 0));
        $path          = $hydra_cfg->settings->upload_path.'/'.$date.$file_name;
        $access        = intval(mosGetParam($_POST, 'access', 0));
		
		if ($access > $protect->my_usertype) { $access = 0; }
        if ($this->folder_property->folder_access > $access) { $access = $this->folder_property->folder_access; }
        
        
		if (empty($file_alias)) { $file_alias = $file_name; }
			
        if (empty($tmp_file) OR (empty($file_name))) {
        	
           if (!$id) {
      	      echo "<script type='text/javascript' language='javascript'>alert('".HL_FORM_ALERT."');history.back();</script>";
		      return false;
           }
           else {
           	
           	  if (empty($tmp_file) AND (empty($file_alias))) {
      	        echo "<script type='text/javascript' language='javascript'>alert('".HL_FORM_ALERT."');history.back();</script>";
		        return false;
              }
              
           }
           
        }

      
        if ($id AND ($tmp_file)) {
        	@move_uploaded_file($tmp_file, $path);
        	
        	if (!@file_exists($path)) {
      	       echo "<script type='text/javascript' language='javascript'>alert('".HL_FILE_UPLOAD_ERROR."');history.back();</script>";
		       return false;
            }
            
        }
        elseif (!$id) {
        	@move_uploaded_file($tmp_file, $path);
        	
        	if (!file_exists($path)) {
      	       echo "<script type='text/javascript' language='javascript'>alert('".HL_FILE_UPLOAD_ERROR."');history.back();</script>";
		       return false;
            }
            
        }
        
        // write new file into db
        $query = "INSERT INTO #__hydra_files VALUES ("
               . "\n '','$file_alias', '$file_name', '".$hydra_cfg->settings->upload_path.'/'.$date.$file_name."','$file_type',"
               . "\n '$file_size','$type',";
               if ($type == '1') { $query .= "\n '$project',"; } else { $query .= "\n '',"; }
		         if ($type == '2') { $query .= "\n '$user',"; } else { $query .= "\n '',"; }
		         $query .= "\n '".time()."','".time()."','$protect->my_id','$folder','$active', '$access')";

		// update existing file        
		if ($id) {
			
		   // delete the old file before uploading another one	
		   if ($id AND ($tmp_file)) {	
		      $query = "SELECT file_location FROM #__hydra_files WHERE file_id = '$id'";
		             $database->setQuery($query);
		             $del_file = $database->loadResult();
		             
		      if (file_exists($del_file)) {  @unlink($del_file); }	    
		   }          
		          
		     
		   // update db entry       
           $query = "UPDATE #__hydra_files SET file_name = '$file_alias'";
           
           if ($id AND ($tmp_file)) { 
           	  $query .= "\n ,file_realname = '$file_name', file_location = '".$hydra_cfg->settings->upload_path.'/'.$date.$file_name."'";
           	  $query .= "\n ,mime_type = '$file_type', file_size = '$file_size'"; 
           }
           
           $query .= "\n ,file_type = '$type',";
           
		   if ($type == '1') { $query .= "\n project = '$project',"; } else { $query .= "\n project = '',"; }
		   if ($type == '2') { $query .= "\n uid = '$user',"; } else { $query .= "\n uid = '',"; }
		   
		   $query .= "\n mdate = '".time()."', file_active = '$active', file_access = '$access'";
		   $query .= "\n WHERE file_id = '$id'";		
        }
        
        // execute query       
		$database->setQuery($query);
		$database->query();
        
		// unset tmp-file
		unset($_FILES);
		            
		hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder);         
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sat Sep 30 16:39:53 CEST 2006 )
	* @name    loadFile
	* @version 1.1 
	* @param   int $id
	* @return  object
	* @desc    loads file-info from db for editing
	**/
	function loadFile($id)
	{
		global $database;
		
		$file = null;
		
		$query = "SELECT file_name, file_type, project, uid, file_active FROM #__hydra_files WHERE file_id = '$id'";
		       $database->setQuery($query);
		       $database->loadObject($file); 
		          
		return $file;    
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:58:31 CEST 2006 )
	* @name    loadComments
	* @version 1.0 
	* @param   int $id
	* @param   string $type  
	* @return  array $comments
	* @desc    loads all comments from a file or document
	**/
	function loadComments($id, $type)
	{
		global $database;
		
		if ($type == 'document') { $type = 'doc'; }
		$query = "SELECT c.comment_id, c.text,c.creator,c.mdate,c.cdate,u.name FROM #__hydra_comments AS c"
		       . "\n INNER JOIN #__hydra_users AS h ON c.creator = h.id"
		       . "\n INNER JOIN #__users AS u ON h.jid = u.id"
		       . "\n WHERE c.$type = '$id'";
		       $database->setQuery($query);
		       $comments = $database->loadAssocList();
		       
		return $comments;        
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:59:02 CEST 2006 )
	* @name    loadComment
	* @version 1.0 
	* @param   int $comment
	* @param   string $type
	* @param   int $id  
	* @return  array $comment
	* @desc    returns a specific comment
	**/
	function loadComment($comment, $type, $id)
	{
		global $database;
		
		if ($type == 'document') { $type = 'doc'; }
		
		$query = "SELECT text FROM #__hydra_comments WHERE comment_id = '$comment'"
		       . "\n AND $type = '$id'";
		       $database->setQuery($query);
		       $comment = $database->loadAssocList();

		return $comment;       
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:59:23 CEST 2006 )
	* @name    validateCommentAccess
	* @version 1.0 
	* @param   int $id
	* @param   string $type   
	* @return  bool $valid
	* @desc    check if the user has access to the doc/file and it's comments
	**/
	function validateCommentAccess($id, $type)
	{
		global $database, $protect;
		
		$valid = true;
		
		if ($type == 'document') {
			
			$query = "SELECT doc_type, project, uid, creator FROM #__hydra_documents"
			       . "\n WHERE doc_id = '$id'";
			       $database->setQuery($query);
			       $result = $database->loadAssocList();
			       
			if ($result[0]['doc_type'] == '1' AND (!in_array($result[0]['project'], $protect->my_projects)) AND ($protect->my_usertype != 3)) {
				$valid = false;
			}
			
			if ($result[0]['doc_type'] == '2' AND ($result[0]['uid'] != $protect->my_id)  AND ($protect->my_usertype != 3)) {
				$valid = false;
			}
			elseif ($result[0]['creator'] == $protect->my_id OR ($protect->my_usertype == 3)) {
				$valid = true;
			}
			
		}
		
		if ($type == 'data') {
			
			$query = "SELECT file_type, project, uid, creator FROM #__hydra_files"
			       . "\n WHERE file_id = '$id'";
			       $database->setQuery($query);
			       $result = $database->loadAssocList();
			       
			if ($result[0]['file_type'] == '1' AND (!in_array($result[0]['project'], $protect->my_projects)) AND ($protect->my_usertype != 3)) {
				$valid = false;
			}
			
			if ($result[0]['file_type'] == '2' AND ($result[0]['uid'] != $protect->my_id)) {
				$valid = false;
			}
			elseif ($result[0]['creator'] == $protect->my_id  OR ($protect->my_usertype == 3)) {
				$valid = true;
			}
		}

		return $valid;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Sep 30 13:36:10 CEST 2006 )
	* @name    deleteComment
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    deletes a comment
	**/
	function deleteComment()
	{
		global $database;
		
		$cid    = intval(mosGetParam($_POST, 'comment', 0));
		$folder = intval(mosGetParam($_POST, 'folder', 0));
		$type   = mosGetParam($_POST, 'data_type', '');
		$id     = intval(mosGetParam($_POST, 'id', 0));
		
		$query = "DELETE FROM #__hydra_comments WHERE comment_id = '$cid'";
		       $database->setQuery($query);
		       $database->query();
   
		$qtype = 'data';
		if ($type == 'document') { $qtype = 'doc'; }   
		        
		$query = "SELECT COUNT(comment_id) FROM #__hydra_comments WHERE $qtype = '$id'";
		       $database->setQuery($query);
		       $total = $database->loadResult();       

		if (1 > $total) {
			hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder);
		}
		else {
			hydraRedirect('index2.php?option=com_hydra&area=files&folder='.$folder.'&cmd=view_comments&data_type='.$type.'&id='.$id);
		}       
	}	
}


class browserHTML
{
	
	
	/**
	* @author  Tobias Kuhn ( Sat Feb 03 21:57:20 CET 2007 )
	* @name    browserButton
	* @version 1.2 
	* @param   int $type
	* @param   int $project
	* @param   int $uid
	* @param   int $creator
	* @param   int $id
	* @param   int $comments
	* @return  string
	* @desc    shows a button
	**/
	function browserButton($button_type, $button, $type, $project, $uid, $creator, $id, $comments = 0)
	{
		global $hydra, $protect;
		
		$button_off = "";
		
		switch ($button)
		{
			// edit buttons
			case 'edit':
				$button_off = HydraMenu::item2(HL_EDIT, '16_edit.gif');
				 
				switch ($button_type)
				{
					case 'folder':
						$button_on  = HydraMenu::item(HL_EDIT, '16_edit.gif', '','editFolder('.$id.')');
		                $cmd        = 'new_folder';
						break;
						
					case 'doc':
						$button_on  = HydraMenu::item(HL_EDIT, '16_edit.gif', '','editDoc('.$id.')');
					    $cmd        = 'new_document';
						break;
						
					case 'file':
						$button_on  = HydraMenu::item(HL_EDIT, '16_edit.gif', '','editFile('.$id.')');
					    $cmd        = 'create_files';
						break;		
				}
				break;
			
				
			// move buttons		
			case 'move':
				$button_off = HydraMenu::item2(HL_MOVE_DATA);
				
				switch ($button_type)
				{
					case 'folder':
						$button_on  = HydraMenu::item(HL_MOVE_DATA, '16_files_move.gif', '','moveFolder('.$id.')');
				        $cmd        = 'move_data';
						break;
						
					case 'doc':
						$button_on  = HydraMenu::item(HL_MOVE_DATA, '16_files_move.gif', '','moveDoc('.$id.')');
					    $cmd        = 'move_data';
						break;
						
					case 'file':
						$button_on  = HydraMenu::item(HL_MOVE_DATA, '16_files_move.gif', '','moveFile('.$id.')');
					    $cmd        = 'move_data';
						break;		
				} 
				break;
			
				
			// delete buttons		
			case 'delete':
				$button_off = HydraMenu::item2(HL_DELETE_DATA, '16_delete.gif');
				
				switch ($button_type)
				{
					case 'folder':
						$button_on  = HydraMenu::item(HL_DELETE_DATA, '16_delete.gif', '','deleteFolder('.$id.')');
			            $cmd        = 'del_data';
						break;
						
					case 'doc':
						$button_on  =  HydraMenu::item(HL_DELETE_DATA, '16_delete.gif', '','deleteDoc('.$id.')');
					$cmd        = 'del_data';
						break;
						
					case 'file':
						$button_on  = HydraMenu::item(HL_DELETE_DATA, '16_delete.gif', '','deleteFile('.$id.')');
					    $cmd        = 'del_data';
						break;		
				}   
			    break;

			    
			// comment buttons	     
			case 'comment':
				$button_off = HydraMenu::item2(HL_NEW_COMMENT, '16_comment_2.gif');
				
				switch ($button_type)
				{
					case 'folder':
						break;
						
					case 'doc':
						if ($comments == 0) { 
							$button_on = HydraMenu::item(HL_NEW_COMMENT, '16_comment.gif', '','newDocComment('.$id.')'); 
						}
		                else { 
		                	$button_on = HydraMenu::item(HL_VIEW_COMMENTS."(".$comments.")", '16_comment.gif', '','viewHydraComment('.$id.')'); 
		                }
		                $cmd = 'view_comments';
						break;
						
					case 'file':
						if ($comments == 0) { 
							$button_on = HydraMenu::item(HL_NEW_COMMENT, '16_comment.gif', '','newDataComment('.$id.')');
						}
		                else { 
		                	$button_on =  HydraMenu::item(HL_VIEW_COMMENTS."($comments)", '16_comment.gif', '','viewDataComment('.$id.')');
		                }
		                $cmd = 'view_comments';
						break;		
				} 
				break;   	
		}
		
		
		switch ($protect->current_command)
		{
			case 'move_data':
				return $button_off;
				break;
				
			default:
				
				if ($creator == $protect->my_id AND ($protect->perm($cmd))) { return $button_on; }
				
				switch ($type)
				{
					case '0':
						if ($protect->perm($cmd)) { return $button_on; }
						break;
						
					case '1':
						if (in_array($project, $protect->my_projects) AND ($protect->my_usertype >= 1)) { return $button_on; }
						break;
						
					case '2':
						if ($uid == $protect->my_id  AND ($protect->perm($cmd))) { return $button_on; }
						break;
				}
				
				if ($protect->my_usertype == 3) { return $button_on; }
				
				return $button_off;
				
				break;	
		}
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Oct 08 19:30:57 CEST 2006 )
	* @name    folderClass
	* @version 1.0 
	* @param   int $type
	* @param   int $status
	* @return  string $class
	* @desc    format folder-style depending on his status
	**/
	function folderClass($type, $status)
	{
		switch($type)
		{
			case '0':
				$class = 'public_outofdate';
				if ($status == '1') { $class = 'public_uptodate'; }
				break;
				
			case '1':
				$class = 'project_outofdate';
			    if ($status == '1') { $class = 'project_uptodate'; }
			   break;
			   
			case '2':
				$class = 'private_outofdate';
			    if ($status == '1') { $class = 'private_uptodate'; }
				break; 	
		}
		
		return $class;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Sat Feb 03 21:57:41 CET 2007 )
	* @name    folderRow
	* @version 1.2
	* @param   int $k
	* @param   int $i
	* @param   array $v
	* @param   int $comments
	* @return  void
	* @desc    shows a row in the browser
	**/
	function browserRow($row_type, $k, $i, $v, $comments = 0)
	{
		global $hydra, $hydra_template, $protect;
		
		$size    = 0;
		$project = $v['project'];
		$uid     = $v['uid'];
		$creator = $v['creator'];
		$mdate   = $v['mdate'];
		
		switch ($row_type)
		{
			case 'folder':
				
				$id        = $v["folder_id"];
		   		$name      = $v['folder_name'];
		   		$type      = $v['folder_type'];
		   		$status    = $v['folder_active'];
		   		$access    = $v['folder_access'];
		   		$checkbox  = "folder_id[]";
		   		$js        = "href='javascript:browse($id);'";
		   		$data_type = HL_FOLDER;
		   
		   		$comment_button = HydraMenu::item2(HL_NEW_COMMENT, '16_comment_2.gif');
		   		$edit_button    = $this->browserButton('folder','edit', $type, $project, $uid, $creator, $id);
		   		$move_button    = $this->browserButton('folder','move', $type, $project, $uid, $creator, $id);
		   		$delete_button  = $this->browserButton('folder','delete', $type, $project, $uid, $creator, $id);
		   		$class          = $this->folderClass($type, $status);
		   		$img            = $hydra->load('img', '16_files_folder.gif', "alt='".HL_OPEN_FOLDER."' title='".HL_OPEN_FOLDER."'");
		   
				break;
			
					
			case 'document':
				
				$id        = $v["doc_id"];
		   		$name      = $v['doc_title'];
		   		$type      = $v['doc_type'];
		   		$status    = $v['doc_active'];
		   		$access    = $v['doc_access'];
		   		$checkbox  = "doc_id[]";
		   		$js        = '';
				
		   		if ($protect->current_command != 'move_data') {
		           if ($protect->perm('read_data') OR ($v['creator'] == $protect->my_id)) {
		              $js = "href='javascript:readDoc(".$id.");'";
		           }
		           elseif($protect->my_usertype == 3) {
		      	      $js = "href='javascript:readDoc(".$id.");'";
		           }
		           elseif ($type == 1 AND ($protect->my_usertype == 2) AND (in_array($project, $protect->my_projects))) {
		      	      $js = "href='javascript:readDoc(".$id.");'";
		           }
		           elseif($type == 2 AND ($uid == $protect->my_id)) {
		      	      $js = "href='javascript:readDoc(".$id.");'";
		           }
		           elseif ($type == 0 AND ($protect->perm('read_data'))) {
		      	      $js = "href='javascript:readDoc(".$id.");'";
		           }
		        }
		   		
		        $data_type = HL_HYDRA_DOC;
		   
		        $comment_button = $this->browserButton('doc','comment', $type, $project, $uid, $creator, $id, $comments);
		        $edit_button    = $this->browserButton('doc','edit', $type, $project, $uid, $creator, $id);
		        $move_button    = $this->browserButton('doc','move', $type, $project, $uid, $creator, $id);
		        $delete_button  = $this->browserButton('doc','delete', $type, $project, $uid, $creator, $id);
		        $class          = $this->folderClass($type, $status);
		        $img            = $hydra->load('img', '16_files_doc.gif', "alt='".HL_OPEN_DOC."' title='".HL_OPEN_DOC."'");
		   
				break;
				
				
			case 'file':
				
				$mime      = new HydraMime();
				$id        = $v["file_id"];
		   		$name      = $v['file_name'];
		   		$type      = $v['file_type'];
		   		$status    = $v['file_active'];
		   		$size      = $v['file_size'];
		   		$access    = $v['file_access'];
		   		$js        = '';
				
		   		if ($protect->current_command != 'move_data') {
		           if ($protect->perm('read_data') OR ($v['creator'] == $protect->my_id)) {
		              $js = "href='javascript:readFile(".$id.");'";
		           }
		           elseif($protect->my_usertype == 3) {
		      	      $js = "href='javascript:readFile(".$id.");'";
		           }
		           elseif ($type == 1 AND ($protect->my_usertype == 2) AND (in_array($project, $protect->my_projects))) {
		      	      $js = "href='javascript:readFile(".$id.");'";
		           }
		           elseif($type == 2 AND ($uid == $protect->my_id)) {
		      	      $js = "href='javascript:readFile(".$id.");'";
		           }
		           elseif ($type == 0 AND ($protect->perm('read_data'))) {
		      	      $js = "href='javascript:readFile(".$id.");'";
		           }
		        }
		   		
		        $data_type = $mime->formatType($v['mime_type']);
		   
		        $comment_button = $this->browserButton('file','comment', $type, $project, $uid, $creator, $id, $comments);
		        $edit_button    = $this->browserButton('file','edit', $type, $project, $uid, $creator, $id);
		        $move_button    = $this->browserButton('file','move', $type, $project, $uid, $creator, $id);
		        $delete_button  = $this->browserButton('file','delete', $type, $project, $uid, $creator, $id);
		        $class          = $this->folderClass($type, $status);
		        $img            = $hydra->load('img', '16_files_file.gif', "alt='".HL_DOWNLOAD_FILE."' title='".HL_DOWNLOAD_FILE."'");
		        
				break;		
		}

		$menu = HydraMenu::init('16_menu.gif', 'menu_'.$i)
		      . HydraMenu::menu('menu_'.$i)
		      . $comment_button
		      . $move_button
		      . $edit_button
		      . $delete_button
		      . HydraMenu::menu();
		?>
		<tr class="row<?php echo $k;?>">
	      <!-- <td><input type="checkbox" id="cb<?php echo $i;?>" name="<?php echo $checkbox;?>" value="<?php echo $id;?>" onclick="isChecked(this.checked);" /></td> -->
	      <td align="center" valign="top"><?php echo $menu;?></td>
	      <td <?php echo $hydra_template->OrderClass(0);?> align="left"><a <?php echo $js;?>><?php echo $img;?></a></td>
	      <td width="50%" <?php echo $hydra_template->OrderClass(0);?> align="left" valign="top"><a class="<?php echo $class;?>" <?php echo $js;?>><?php echo $name;?></a></td>
	      <td width="20%" align="left" valign="top"><?php echo $data_type;?></td>
	      <td width="10%" align="left" valign="top"> <?php echo $size." ".HL_SIZE_KB;?></td>
	      <td width="20%" align="center" valign="top" <?php echo $hydra_template->OrderClass(1);?>><?php echo hydraDate($mdate);?></td>
	    </tr>
		<?php
	}
	
	
	/**
	* @author  Tobias Kuhn ( Sun Nov 12 20:13:10 CET 2006 )
	* @name    dataTypeSettings
	* @version 1.0 
	* @param   int $id (optional)
	* @param   int $type (optional)
	* @param   int $active (optional)
	* @param   object $folder_prop
	* @return  void
	* @desc    
	**/
	function dataTypeSettings($id = 0, $type = 0, $sel_project = 0, $sel_user = 0, $active = 0, $access = 0, $folder_prop = null)
	{
		global $database, $protect, $hydra_template, $hydra;
		
		$where = "\n ";
		$my_projects = 0;
		
		if ($protect->my_usertype != 3) { 
			$my_projects = implode(',', $protect->my_projects);
			$where = "\n WHERE project_id IN($my_projects)";
		}	
		// get all projects
      $query = "SELECT project_id, project_name FROM #__hydra_project"
             . $where 
             . "\n ORDER BY project_name ASC";
             $database->setQuery($query);
             $list = $database->loadObjectList();
       
      $projects = "\n <select name='project' size='1'>";
             
      for($i = 0; $i < count($list); $i++)
      {
	       $l = $list[$i];
	       $selected = '';

	       if ($l->project_name) {
	       	
	           if ($sel_project == $l->project_id) { 
	               $selected = "selected='selected'"; 
	           }
	  
	           if ($folder_prop->folder_type == '1' AND ($l->project_id == $folder_prop->project)) {	
	               $projects .= "\n <option value='".$l->project_id."' $selected>".$l->project_name."</option>";
	           }
	           elseif ($folder_prop->folder_type != '1') {
	  	            $projects .= "\n <option value='".$l->project_id."' $selected>".$l->project_name."</option>"; 
	           }
	       }  
      }
              
      $projects .= "\n </select>";

      $where = "\n ";
		$my_users = 0;
		
		if ($protect->my_usertype != 3) { 
			$my_users = implode(',', $protect->my_userspace);
			$where = "\n AND h.id IN($my_users)";
		}
		
      // get all users
      $query = "SELECT h.id, h.user_type, j.name FROM #__hydra_users AS h, #__users AS j"
             . "\n WHERE j.id = h.jid"
             . $where
             . "\n ORDER BY j.name ASC";
             $database->setQuery($query);
             $list = $database->loadObjectList();
       
      $users = "\n <select name='user' size='1'>";
      
      for($i = 0; $i < count($list); $i++)
      {
	      $l = $list[$i];
	      $selected = '';
	
	      if ($l->name) {
	          if ($sel_user == $l->id) { 
	          	$selected = "selected='selected'"; 
	          }
	  
	          if ($folder_prop->folder_type == '2' AND ($l->id == $folder_prop->uid)) {	
	             $users .= "\n <option value='".$l->id."' ".$selected.">".$l->name." [ ".$hydra->formatUserType($l->user_type)." ]</option>";
	          } 
	      
	          if ($folder_prop->folder_type != '2') {
	  	          $users .= "\n <option value='".$l->id."' ".$selected.">".$l->name." [ ".$hydra->formatUserType($l->user_type)." ]</option>";
	          }
	      }  
      } 
      
      $users .= "\n </select>";
      
		switch ($protect->current_command)
		{
			case 'new_document':
				$heading   = HL_DOC_TYPE;
				$radio     = 'doc_type';
				$type1_lbl = HL_FOLDER_TYPE_0;
				$type2_lbl = HL_DOC_TYPE_1;
				$type3_lbl = HL_DOC_TYPE_2;
				break;
				
			case 'new_folder':
				$heading   = HL_FOLDER_TYPE;
				$radio     = 'folder_type';
				$type1_lbl = HL_FOLDER_TYPE_0;
				$type2_lbl = HL_FOLDER_TYPE_1;
				$type3_lbl = HL_FOLDER_TYPE_2;
				break;
				
			case 'create_files':
				$heading   = HL_FILE_TYPE;
				$radio     = 'file_type';
				$type1_lbl = HL_FOLDER_TYPE_0;
				$type2_lbl = HL_DOC_TYPE_1;
				$type3_lbl = HL_DOC_TYPE_2;
				break;		
		}
		
		$display_block = "style='display:block;'";
		$display_none  = "style='display:none;'";
		
		// list usertypes
		$usertypes = "<select name='access' size='1'>";
		
		$types = array('0' => HL_USER_TYPE_CLIENT,
		               '1' => HL_USER_TYPE_MEMBER,
		               '2' => HL_USER_TYPE_GROUPLEADER,
		               '3' => HL_USER_TYPE_ADMINISTRATOR 
		               );
		               
		foreach ($types AS $tid => $tname)
		{
			$selected = '';
			
			if ($access == $tid) { $selected = "selected='selected'"; }
			
			if ($tid <= $protect->my_usertype) {
			   $usertypes .= "<option value='$tid' $selected>$tname</option>";
			}   
		}
		
		$usertypes .= "</select>";
		
		?>
		<fieldset class='formFieldset'>
      <legend class='formLegend'><?php echo $heading;?></legend>
      <table class="formTable" width="100%">
      
        <?php if ($folder_prop->type == '0' OR (!isset($folder_prop->folder_type)) OR ($folder_prop->folder_type == 0)) { ?>
        
        <tr>
          <td width="10%"><input type="radio" name="<?php echo $radio;?>" value="0" checked="checked" id="ft_0" onclick="switchType();"<?php if ($type == '0') { echo "checked='checked'"; }?>/></td>
          <td width="20%" align="left" valign="top" nowrap><?php echo $type1_lbl;?></td>
          <td width="70%" align="left"></td>
       </tr>
       
       <?php } if ($folder_prop->folder_type != '2' AND (count($protect->my_projects) )) { ?> 
       
       <tr>
         <td width="10%"><input type="radio" name="<?php echo $radio;?>" value="1" id="ft_1" onclick="switchType();" <?php if ($type == '1'){echo "checked='checked'";} if ($folder_prop->folder_type == '1') {echo "checked='checked'";}?>/></td>
         <td width="20%" align="left" valign="top" nowrap><?php echo $type2_lbl;?></td>
         <td width="70%" align="left"valign="top">
           <div id="div_ft_1" <?php if ($type == 1 AND($id) ) { echo $display_block;} elseif (!$id AND ($folder_prop->folder_type == '1')) {echo $display_block;} else { echo $display_none;}?>>
           <?php echo $projects;?>
           </div>
         </td>
       </tr> 
       
      <?php } ?>
      
      <tr>
        <td width="10%"><input type="radio" name="<?php echo $radio;?>" value="2" id="ft_2" onclick="switchType();" <?php if ($type == '2'){echo "checked='checked'";}if ($folder_prop->folder_type == '2') { echo "checked='checked'";}?>/></td>
        <td width="20%" align="left" valign="top" nowrap><?php echo $type3_lbl;?></td>
        <td width="70%" align="left"valign="top">
          <div id="div_ft_2" <?php if ($type == '2'){echo $display_block;} elseif($folder_prop->folder_type == '2') {echo $display_block;} else { echo $display_none;}?>/>
          <?php echo $users;?>
          </div>
        </td>
      </tr>
  
      <?php if ($protect->my_usertype > 0) { ?>
      <tr>
       <td width="30%" colspan="2"><?php echo $hydra_template->drawLabel(HL_REQUIRED_TYPE);?></td>
       <td width="70%" align="left"><?php echo $usertypes."&nbsp;".HL_OR_HIGHER;?></td>
     </tr>
     <?php } ?>
     
      <tr>
       <td width="30%" colspan="2"><?php echo $hydra_template->drawLabel(HL_IS_ACTIVE);?></td>
       <td width="70%" align="left"><input type="checkbox" name="active" value="1" <?php if ($active OR (!$id)) { echo "checked='checked'"; }?>/></td>
     </tr>
  
      </table>
      </fieldset>
      <script type="text/javascript" language="javascript">
      
      function switchType()
      {
	       <?php if ($folder_prop->folder_type == '0' OR (empty($folder_prop->folder_type)) OR ($folder_prop->folder_type == 0)) {?>
	       var type0 = document.getElementById('ft_0');
	       <?php } ?>
	       
	       <?php if ($folder_prop->folder_type != '2') {?>
	       var type1 = document.getElementById('ft_1');
	       <?php } ?>
	       
	       var type2 = document.getElementById('ft_2');
	
	       <?php if ($folder_prop->folder_type != '2' AND (count($protect->my_projects))) {?>
	       var type1_div = document.getElementById('div_ft_1');
	       <?php } ?>
	       
	       var type2_div = document.getElementById('div_ft_2');
	
	       <?php if ($folder_prop->folder_type == '0' OR (empty($folder_prop->folder_type) OR ($folder_prop->folder_type == 0) )) {?>
	       if (type0.checked) {
	       	
	       	  <?php if (count($protect->my_projects)) { ?>
		        type1_div.style.display = 'none';
		        <?php } ?>
		        
		        type2_div.style.display = 'none';
	       }
	       <?php } ?>
	       <?php if ($folder_prop->folder_type != '2' AND (count($protect->my_projects))) {?>
	       if (type1.checked) {
		        type1_div.style.display = 'block';
		        type2_div.style.display = 'none';
	       }
	       <?php } ?>
	       if (type2.checked) {
		       <?php if ($folder_prop->folder_type != '2' AND (count($protect->my_projects)) ) {?>type1_div.style.display = 'none';<?php } ?>
		       type2_div.style.display = 'block';
	       }
      }
      
      </script>
		<?php
	}
	
}
?>