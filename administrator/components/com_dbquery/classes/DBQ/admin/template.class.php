<?php

/**
 * @package DBQ
 */

/**
 * Prevent Direct Access
 */
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class

//DBQ_Settings::includeClassFileForType('DBQ_template');
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/template.class.php');
require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/common.class.php');

/**
 * The Template class provides an overview of available templates for 
 * use with DBQ.
 *
 *
 * @subpackage DBQ_template Template Class
 */
class DBQ_admin_template extends DBQ_admin_common {



	/**
	 * Constructor class for the DBQ Stats class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_admin_template($dummy = false) {
		global $mosConfig_absolute_path;
		require_once( $mosConfig_absolute_path .'/includes/domit/xml_domit_lite_include.php' );
		$this->_identifier = $this->_identifiers['template'];
		// Call our parent
		parent :: DBQ_admin_common(_DBQ_TEMPLATE_TABLE, $dummy);
	}  // end constructor

	/**
	 * Duplicate the directory 
	 *
	 * @access public
	 * @since 1.3
	 * @param String $source Name of template directory to be copied
	 * @return Boolean True if the directory was successfully copied
	 **/
	function adminCopy($source) {
		global $dbq_lib_path;

		// Determine who we're talking about
		$templateBaseDir = $this->getTemplateBaseDir();
		$destination = mosGetParam($_GET, 'userinput');
		if ( is_array($source) ) 
			$source = array_shift($source);

		// Make absolute and safe
		$source = $templateBaseDir . $source;
		$destination = preg_replace('/[^A-Za-z0-9\._-]/', '', $destination);
		$destination = $templateBaseDir . $destination;
		//echo "copy $source to $destination<br/>";
				
		// Basic sanity checking
		if ( ! is_dir($source) ) {
			echo $source . ': ' . _LANG_DIRECTORY_NOT_FOUND; 
			return false;
		} elseif ( ! is_readable($source) ) {
			echo $source . ': ' . _LANG_DIRECTORY_NOT_READABLE;
			return false;
		} elseif ( file_exists($destination) ) {
			echo $destination . ': ' . _LANG_FILE_EXISTS;
			return false;
		}
		
		// Perform the actual copy
		require_once($dbq_lib_path.'copyr.php');
		if ( copyr($source, $destination) ) {
			echo _LANG_DIRECTORY_COPIED;
			return true;
		} else {
			echo _LANG_DIRECTORY_NOT_COPIED;
			return false;
		}

	} // end adminCopy()

	/**
	 * Edit a template file
	 *
	 * @access public
	 * @since 1.3
	 * @return Boolean True if successful
	 **/
	function adminEdit($templateDir) {
		global $mainframe, $mosConfig_absolute_path, $dbq_xhtml_path, $option;

		// Get the name of the file which is edited by default, if the user does not select one
		$templateIdentifier = $this->getIdentifierForObjectType('template');
		$fileIdentifier = $this->getIdentifierForObjectType('file');
		$directoryIdentifier = $this->getIdentifierForObjectType('directory');
		$subdirectoryIdentifier = $this->getIdentifierForObjectType('subdirectory');

		// Variables to use, escape for security
		$templateFile = $this->getUserStateFromRequest("$fileIdentifier{$option}", $fileIdentifier, '');
		$templateDirectory = $this->getUserStateFromRequest("$templateIdentifier{$option}", $templateIdentifier, '');
		$templateSubDirectory = $this->getUserStateFromRequest("$subdirectoryIdentifier{$option}", $subdirectoryIdentifier, '/');
		//echo "hoping templatefile $templateFile is set, and templateDir is $templateDir<br/>";
		
		if ( ! $templateDir ) 
			$templateDir = $this->getUserStateFromRequest("$templateIdentifier{$option}", $templateIdentifier, 'default');

		// Start making paths and file names
		$templateBaseDir = $this->getTemplateBaseDir();	
		$templateFile = basename(trim($templateFile));
		$templateDir = basename(trim($templateDirectory));
		if ( $templateSubDirectory )
			$templateSubDir = '/' . basename(trim($templateSubDirectory));
		//$templateSubDir = '';
		$directoryToUse = "$templateBaseDir$templateDir$templateSubDir";
		$fileToEdit = "$directoryToUse/$templateFile";
		
		// Debug Message
		if ( $this->debug() )
			echo "templateFile is $templateFile, templateDir is $templateDir, fileToEdit is $fileToEdit<br/>";		
		
		// List files and directories for this template
		$templateDirectories = $this->readFilesFromDirectory("$templateBaseDir$templateDir", true);
		$templateFiles = $this->readFilesFromDirectory($directoryToUse);
		
		// Try to open the file in the editor window
		if ( ! is_file($fileToEdit ) ) {
			// Where is it again?
			echo $fileToEdit, ': ', _LANG_TEMPLATE_NOT_FOUND;
			$keys = array_keys($templateFiles);
			$templateFile = $templateFiles[$keys[0]];
			$fileToEdit = "$directoryToUse/$templateFile";
		} 
		
		// Open the file and create the screen
		if ( ! is_readable($fileToEdit ) ) {
			// Cannot read it
			echo $fileToEdit, ': ', _LANG_TEMPLATE_NOT_READABLE;
		} elseif ( $fp = fopen( $fileToEdit, 'r' ) ) {
			// OK, opened
			$content = fread( $fp, filesize( $fileToEdit ) );
		} else {
			$content = '';
		}
		
		// Place to put all the list options
		$lists = array();
		
		// List the directories that we can edit
		
		$templates = array();
		foreach ($templateFiles as $k => $v) {
			$templates[] = mosHTML :: makeOption($k, $v);
		}
		$lists[_LANG_FILES] = mosHTML :: selectList($templates, $fileIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $templateFile);
					
		// List the files that we can edit
		$directories = array();
		
		$directories[] = mosHTML :: makeOption('/', '/');
		foreach ($templateDirectories as $k => $v) {
			// Use the values only, as the key is numeric
			$directories[] = mosHTML :: makeOption($v, $v);
		}
		$lists[_LANG_DIRECTORY] = mosHTML :: selectList($directories, $subdirectoryIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $templateSubDirectory);

		include_once($dbq_xhtml_path . 'edit.html.php');
		return true;
	} // end adminEdit()

	/**
	 * Display fields for a given template
	 *
	 * @param String $field Name of the field to display
	 * @param Integer $i Optional count to be used for generating javascript admin controls
	 **/
	function field($field, $i) {

		
		$obj =& $this->getObject();

		switch ($field) {
			case 'authorUrl':
				parent :: fieldLink($obj->authorUrl, $field,NULL,false);				
				break;
			case 'name':
				$this->fieldIdentifier(NULL, 'directory' );
				break;
			default :
				parent :: field($field, $i);
		}

	} // end field()

	/**
	 * Rename the directory within the parent directory
	 *
	 * @access public
	 * @since 1.3
	 * @param String $source Name of the directory to move
	 * @return Boolean True if the directory has been moved
	 *
	 **/
	function adminMove($source) {

		// Determine who we're talking about
		$templateBaseDir = $this->getTemplateBaseDir();
		$destination = mosGetParam($_GET, 'userinput');
		if ( is_array($source) ) 
			$source = array_shift($source);

		// Make absolute and safe
		$source = $templateBaseDir . $source;
		$destination = preg_replace('/[^[:alphanum:]]/', '', $destination);
		$destination = $templateBaseDir . $destination;
		//echo "move $source to $destination<br/>";
		
		// Basic sanity checking
		if ( ! is_dir($source) ) {
			echo $source . ': ' . _LANG_DIRECTORY_NOT_FOUND; 
			return false;
		} elseif ( ! is_readable($source) ) {
			echo $source . ': ' . _LANG_DIRECTORY_NOT_READABLE;
			return false;
		} elseif ( file_exists($destination) ) {
			echo $desination . ': ' . _LANG_FILE_EXISTS;
			return false;
		}
		
		// Shake and Move
		if ( rename($source, $destination ) ) {
			echo _LANG_DIRECTORY_COPIED;
			return true;
		} else {
			echo _LANG_DIRECTORY_NOT_COPIED;
			return false;
		}

	} // end adminMove()

	/**
	 * Save data info a file
	 *
	 * The original file will be copied for backup before writting if the
	 * BACKUP_FILE_BEFORE_WRITING attributes is set to a positive value.
	 * Previous backup files will be removed.
	 *
	 * Default mode is 'w', which will open the file for writing, destroying 
	 * the file's previous contents.
	 * 
	 * @access public
	 * @since 1.3
	 * @return Boolean True If everything is successful
	 *
	 **/
	function adminSave() {

		global $mainframe, $mosConfig_absolute_path, $dbq_xhtml_path, $option;

		// Mode used to open the file
		$mode = 'w';
		
		// Get the name of the file which is edited by default, if the user does not select one
		$templateIdentifier = $this->getIdentifierForObjectType('template');
		$fileIdentifier = $this->getIdentifierForObjectType('file');
		$directoryIdentifier = $this->getIdentifierForObjectType('directory');
		$subdirectoryIdentifier = $this->getIdentifierForObjectType('subdirectory');

		// Variables to use, escape for security
		$templateFile = $this->getUserStateFromRequest("$fileIdentifier{$option}", $fileIdentifier, '');
		$templateDirectory = $this->getUserStateFromRequest("$templateIdentifier{$option}", $templateIdentifier, '');
		$templateSubDirectory = $this->getUserStateFromRequest("$subdirectoryIdentifier{$option}", $subdirectoryIdentifier, '/');
		$templateDir = $this->getUserStateFromRequest("$templateIdentifier{$option}", $templateIdentifier, 'default');

		// Start making paths and file names
		$templateBaseDir = $this->getTemplateBaseDir();	
		$templateFile = basename(trim($templateFile));
		$templateDir = basename(trim($templateDirectory));
		if ( $templateSubDirectory )
			$templateSubDir = '/' . basename(trim($templateSubDirectory));
		//$templateSubDir = '';
		$directoryToUse = "$templateBaseDir$templateDir$templateSubDir";
		$fileToEdit = "$directoryToUse/$templateFile";
		//echo "templateFile is $templateFile, templateDir is $templateDir, fileToEdit is $fileToEdit<br/>";	
		
		// Basic Sanity Checks - always a good thing
		if ( ! is_file($fileToEdit) ) 
			return $this->logApplicationError("Cannot save file '$fileToEdit' because it does not exist");
		if ( ! is_writable($fileToEdit) ) 
			return $this->logApplicationError("Cannot save file '$fileToEdit' because it is not writable");
						
		// Make a backup if configured to do so
		if ( $this->getConfigValue('BACKUP_FILE_BEFORE_WRITING') ) {
			$newfilename = $fileToEdit . '.bak';
			
			// Remove previous backup
			if ( is_file($newfilename) && ! unlink($newfilename) ) 
				return $this->logApplicationError("Cannot remove previous backup file '$newfilename'");
			
			// Copy existing file to the newfile
			if (!copy($fileToEdit, $newfilename)) 
				return $this->logApplicationError("Cannot backup file '$fileToEdit' to new file '$newfilename'");

		}

		// Open the file
		if (!$handle = fopen($fileToEdit, $mode)) {

			return $this->logApplicationError("Cannot open file '$fileToEdit' with mode '$mode'");
		}
		
		// Do the writting
		//$data = mosGetParam($_POST, 'filecontent', NULL, _MOS_ALLOWRAW);
		$data = $_REQUEST['filecontent'];
		// Bad PHP, Bad!
		//$badPHP = get_magic_quotes_gpc();
		if ( get_magic_quotes_gpc() ) $data = stripslashes($data);
		if (fwrite($handle, $data) === FALSE) {
			return $this->logApplicationError("Cannot write to file '$fileToEdit' with mode '$mode'");
		}
		
		// Finish things up
		fclose($handle);
		
		if ( $this->debug() )
			$this->debugWrite("Successfully wrote data to $fileToEdit");
				
		return true;
	} // end adminSaveFile()

	/**
	 * Remove the selected template set
	 *
	 * @access public
	 * @since 1.3
	 * @param String $templateDir Name of the template directory to toast.
	 * @return Boolean True if the directory was successfully removed
	 **/
	function adminRemove($templateDir) {

		if ( is_array($templateDir) ) 
			$templateDir = array_shift($templateDir);
			
		// Construct the absolute path of the directory
		$path = $this->getTemplateBaseDir().$templateDir;
		if ( get_magic_quotes_gpc() ) $path = stripslashes($path);
		//echo "path is $path<br/>";
		
		if (is_dir( $path )) {
			return deldir( mosPathName( $path ) );
		} else {
			echo _LANG_DIRECTORY_NOT_FOUND;
			return false;
		}
	} // end adminRemove()
	
	/**
	 * Show a list of available templates
	 *
	 * @access public
	 * @since 1.2
	 * @param String $id Optional numberic ID of a specific template
	 * @return Boolean True if successful
	 **/
	function adminShow($id = NULL) {
		global $mainframe, $globals, $dbq_xhtml_path;
		
		// Define useful varialbes
		$option = $globals->option;
		$limit = $globals->limit;
		$limitstart = $globals->limitstart;
		
		// Make an object
		$obj =& $this->getInstance();
		
		// Identifiers for variables
		$queryIdentifier = $this->getIdentifierForObjectType('template');

		
		// Variables to use
		$qid = $this->getUserStateFromRequest("$queryIdentifier{$option}", $queryIdentifier);		
		$search = $mainframe->getUserStateFromRequest("search{$option}t", 'searcht');
		$search = trim(strtolower($search));
		
		// Get rows and calculate results
		$total = $this->getCountOfAllRecords($search, $id, $qid);
		if ( $total <= $limitstart) $limitstart = 0;
		$rows = $this->listAllRecords($limit, $limitstart, $search, $id, $qid);
		require_once (_DBQ_ADMIN_PAGENAV_PATH);
		$pageNav = new mosPageNav($total, $globals->limitstart, $globals->limit);

		// Make list of all search fields
		//$lists = array();

		// Display our rows
		$headers = array('name' => _LANG_NAME, 'directory' => _LANG_DIRECTORY, 'author' => _LANG_AUTHOR,
						'version' => _LANG_VERSION, 'creationDate' => _LANG_DATE, 'authorUrl' => _LANG_AUTHOR_URL );
		$screenName = _LANG_TEMPLATE.' '._LANG_MANAGER;

		// No filtering options
		$lists = array();

		include_once ($dbq_xhtml_path.'summary.html.php');
		return true;
	} // end adminShow()
	
	/**
	 * Returns of records matching the criteria
	 * Calls the base class function
	 * @param string   Value 
	 * @param integer  Variable ID
	 * @param integer Query ID
	 * @return integer  The number of rows
	 */
	function getCountOfAllRecords($search, $vid, $qid, $start=NULL, $end=NULL) {
		$templateBaseDir = $this->getTemplateBaseDir();
		
		$i = 0;
		$files = mosReadDirectory($templateBaseDir, NULL, false, true);
		foreach ($files as $file ) {
			if ( is_dir($file) ) $i++;
		}
		return $i;
	} // end getCountOfAllRecords()

	/**
	 * 
	 *
	 */
	function listAllRecords($limit = 10, $limitstart = 0, $search1 = NULL, $search2 = NULL, $search3 = NULL) {

		$templateBaseDir = $this->getTemplateBaseDir();
		//$templateDirs = mosReadDirectory($templateBaseDir);
		$templateDirs = $this->readFilesFromDirectory($templateBaseDir, true);
		$rows = array();
		
		// Iterate through the listing of templates
		$rowid = 0;	
		for ($i=$limitstart; $i < $limit; $i++) {

			// break out of loop if there are no more elements to parse
			if (!isset($templateDirs[$i])) {
				$i = $limit;
				continue;
			}
			$templateDir = $templateDirs[$i];

			// Create a simple new template object
			$row =new  DBQ_template();

			// Load the object
			$row->id = $rowid;
			$row->load($templateDir);		
			$rows[$rowid] =& $row;
			$rowid++;
		}

		return $rows;
	
	} // end listAllRecords()

}

	/**
	 * Recursively delete a directory
	 *
	 * @author Joomla Installation
	 * @access public
	 * @since 1.3
	 * @param String $dir Directory to delete
	 * @return Depends ...
	 **/
	function deldir( $dir ) {
		$current_dir = opendir( $dir );
		$old_umask = umask(0);
		while ($entryname = readdir( $current_dir )) {
			if ($entryname != '.' and $entryname != '..') {
				if (is_dir( $dir . $entryname )) {
					deldir( mosPathName( $dir . $entryname ) );
				} else {
					@chmod($dir . $entryname, 0777);
					unlink( $dir . $entryname );
				}
			}
		}
		umask($old_umask);
		closedir( $current_dir );
		return rmdir( $dir );
	} // end deldir()
?>