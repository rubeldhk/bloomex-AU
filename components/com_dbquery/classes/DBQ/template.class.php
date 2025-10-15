<?php

/**
 * @package DBQ
 */

// Prohibit direct access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
global $dbq_class_path;
require_once ($dbq_class_path.'common.class.php');
/**
 * The Template class provides an overview of available templates for 
 * use with DBQ.
 *
 *
 * @subpackage DBQ_template Template Class
 */
class DBQ_template extends DBQ_common {
	/**#@+
	 * @access public
	 */
	var $id = NULL;
	var $directory = NULL;
	var $name = NULL;
	var $creationDate = NULL;
	var $author = NULL;
	var $copyright = NULL;
	var $authorEmail = NULL;
	var $authorUrl = NULL;
	var $version = NULL;
	var $description = NULL;	
	/**#@-*/

	/**#@+
	 * @access private
	 */
	/**#@-*/

	/**
	 * Constructor class for the DBQ Stats class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_template($dummy = false) {
		global $mosConfig_absolute_path;
		require_once( $mosConfig_absolute_path .'/includes/domit/xml_domit_lite_include.php' );
		//$this->_identifier = $this->_identifiers['template'];
		$this->_additional_select = array ();
		// Call our parent
		parent :: DBQ_common(_DBQ_TEMPLATE_TABLE, $dummy);
	}  // end constructor

	/**
	 * Get a description of the template
	 *
	 * @return string Description for the template
	 * @access public
	 * @since 1.4
	 */
	function getDescription() {
		return $this->description;
	} // end getDescription()

	/**
	 * Load a template object
	 *
	 * @param String $template
	 */
	function load($template) {

			$this->directory = $template;
			
			// Determine the directory
			$templateDir = $this->getTemplateBaseDir().$template.'/';
			if ( ! is_dir($templateDir) )
				return $this->logApplicationError("The template directory '$templateDir' does not exist");
			
			// Get the XML file
			$xmlFilesInDir = mosReadDirectory($templateDir,'.xml$');
			if (!isset($xmlFilesInDir[0]))
				return $this->logApplicationError("Cannot find xml file for template '$template' in directory '$templateDir'");
			$xmlfile = $xmlFilesInDir[0];

			// Load the XML File			
			$xmlDoc =new  DOMIT_Lite_Document();
			$xmlDoc->resolveErrors( true );
			if (!$xmlDoc->loadXML( $templateDir . $xmlfile, false, true )) 
				return $this->logApplicationError("Cannot read the xml file '$xmlfile' in the directory '$templateDir'");
			
			// grab the root and check the file type
			$element = &$xmlDoc->documentElement;
			if ( $element->getTagName() != 'mosinstall' || $element->getAttribute( 'type' ) != 'dbq_template') {
				return $this->logApplicationError("XML file for template '$template' is not for a DBQ template".$xmlDoc->getErrorString());
			}
				
			// Read the data and assign the data to the object
			$element = &$xmlDoc->getElementsByPath('name', 1 );
			$this->name = $element->getText();
			
			$element = &$xmlDoc->getElementsByPath('creationDate', 1);
			$this->creationDate = $element ? $element->getText() : _LANG_UNKNOWN;
			
			$element = &$xmlDoc->getElementsByPath('author', 1);
			$this->author = $element ? $element->getText() : _LANG_UNKNOWN;
			
			$element = &$xmlDoc->getElementsByPath('copyright', 1);
			$this->copyright = $element ? $element->getText() : '';
			
			$element = &$xmlDoc->getElementsByPath('authorEmail', 1);
			$this->authorEmail = $element ? $element->getText() : '';
			
			$element = &$xmlDoc->getElementsByPath('authorUrl', 1);
			$this->authorUrl = $element ? $element->getText() : '';
			
			$element = &$xmlDoc->getElementsByPath('version', 1);
			$this->version = $element ? $element->getText() : '';
			
			$element = &$xmlDoc->getElementsByPath('description', 1);
			$this->description = $element ? $element->getText() : '';
			
			return true;
	} // end load()

	/**
	 * Rename the directory within the parent directory
	 *
	 * @access public
	 * @since 1.3
	 * @param String $source Name of the directory to move
	 * @return Boolean True if the directory has been moved
	 *
	 **/
	function move($source) {

		// Determine who we're talking about
		$templateBaseDir = $this->getTemplateBaseDir();
		$destination = mosGetParam($_GET, 'userinput');
		if ( is_array($source) ) 
			$source = array_shift($source);

		// Make absolute and safe
		$source = $templateBaseDir . $source;
		$destination = preg_replace('/[^a-z0-9\._-]/', '', $destination);
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
	function store() {

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
	} // end store()

	/**
	 * Remove the selected template set
	 *
	 * @access public
	 * @since 1.3
	 * @param String $templateDir Name of the template directory to toast.
	 * @return Boolean True if the directory was successfully removed
	 **/
	function delete($templateDir) {

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
	} // end delete()
	

	

}

?>