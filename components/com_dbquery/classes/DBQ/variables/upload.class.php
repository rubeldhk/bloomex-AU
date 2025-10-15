<?php

/**
 * @package DBQ
 */

//Prevent Direct Access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
DBQ_Settings::includeClassFileForType('DBQ_variable_keyword');

/**
 * Represents variables used in a DBQ query
 *
 * @subpackage DBQ_variable_upload
 */
class DBQ_variable_upload extends DBQ_variable_keyword {

	var $_tempfile = NULL;
	var $_destfile = NULL;
	
	/**
	 * Constructor class for the DBQ Variable upload class
	 *
	 * @access public
	 * @since 1.4
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_variable_upload($dummy = false) {
		parent::DBQ_variable_keyword($dummy);
	} // end constructor()
	
	/**
	 * Devise the return value for the variable.
	 * 
	 * This function requires that input has previously been registered
	 * 
	 * This function overrides the base method to provide file specific functionality
	 * 
	 * @return String replacement value
	 * @access private
	 * @since 1.4
	 */
	function getReplacementValue() {
		
		// Return a previously calculated replacement value
		if ( ! is_null($this->_replacement_value) )
			return $this->_replacement_value;
					
		// Perform Basic checks
		$replacement = parent::getReplacementValue();
		$tmpFile = $this->_tempfile;
		$newFile = $this->_destfile;

		// Apparently we are not used
		if ( ! $replacement ) 
			return '';
		
		// Make sure that the destination file is the same as replacement
		if ( basename($newFile) !== $replacement ) 
			$newFile = dirname($newFile).'/'.$replacement;

		// Move the file if we have a replacement and all required info
		if (!( isset($replacement) && isset($tmpFile) && isset($newFile) ))
			return $this->logApplicationError("Cannot process uploaded file for variable '$this->name' ($this->id)");

		// Determine how to resolve conflicts
		// If UNIQUE, then the function isValidInput() should have tested this condition already
		$existing = $this->getConfigValue('EXISTING_FILE');
		switch ($existing) {
		case 'REMOVE':
			// Remove the existing file so that it can be replaced
			if ( is_file($newFile) ) {
				if ( $this->debug() )
					$this->debugWrite("Trying to remove file '$newFile' for variable '$this->name' ($this->id)");
				if ( ! unlink($newFile) )
					$this->logApplicationError("Cannot remove the previous existing file '$newFile' for variable $this->name ($this->id)");
			}
			// Remove the old file
			break;
		case 'RENAME':
			// Rename the old file
			// Not yet implemented
			break;
		case 'UNIQUE':
		default:
			// Just try to move the new file
			break;
		}
			
		if ( ! move_uploaded_file($tmpFile, $newFile) )
			return $this->logApplicationError("Cannot move the file '$tempFile' to '$newFile'");

		// Return normal replacement value
		$this->_replacement_value = $this->escaped($replacement);
		return $this->_replacement_value;
	} // end getReplacementValue()
	
	/**
	 * Determine if the variable is a file upload 
	 * 
	 * @return boolean True if the variable is a browse input box for file upload
	 * @access public
	 * @since 1.2
	 */
	function isFileUpload() {
		return true;
	} // end isFileUpload()
	
	/**
	 * Determine if the supplied input is valid input for the given variable
	 * 
	 * $param String $input Input to test, either a string for uploads or a sid for lists
	 * @return Boolean True if the input is valid
	 * @access public
	 * @since 1.2
	 */
	function isValidInput() {
		
		// Perform basic checks
		if ( !parent::isValidInput() )
			return false;
		
		// Now apply special checks for files
		global $mosConfig_absolute_path;
		$input =& $this->_input;

		// Do not continue if the user is not using this variable
		if ( ! $input )
			return true;

		// Fail if there is no file
		if ( ! array_key_exists($this->name, $_FILES ) ) 
			return false;

		// Prepare for tests
		$dir = $this->getConfigValue('DEFAULT_UPLOAD_DIRECTORY').'/';

		if ( $this->getConfigValue('FILES_ABSOLUTE_PATH') ) 
			// Only list files under the website
			$dir = $mosConfig_absolute_path.$dir;

		$file = $dir.$input;
		$tempfile = $_FILES[$this->name]['tmp_name'];
		$tempsize = $_FILES[$this->name]['size'];
		$maxsize = $this->getConfigValue('MAXIMUM_FILE_SIZE');
		$regex = $this->getConfigValue('FILE_UPLOAD_REGEX');

		if ( ! $maxsize ) 
			$maxsize = 1000000;

		// Write debug message
		if ( $this->debug() ) 
			$this->debugWrite("Uploaded file for var $this->name is '$file', stored locally as '$tempfile', size $tempsize/$maxsize, using regex '$regex'");

		// Assume that everything is okay, until it isn't
		$ok = true;

		// Check if the file conforms to any regex defs

		$final_regex = "/^$regex\$/";
		if ( $regex && ! preg_match($final_regex, $input) )
			$ok = $this->logInvalidInput(_LANG_INPUT_DOESNT_MATCH_REGEX);
				
		// Check if we can physically store the file
		if ( ! is_writable($dir) ) 
			$ok = $this->logInvalidInput(_LANG_DIRECTORY_NOT_WRITTABLE);

		// Check what to do with existing files
		$existing = $this->getConfigValue('EXISTING_FILE');
		if ( ($existing == 'UNIQUE') && file_exists($file) ) 
			$ok = $this->logInvalidInput(_LANG_FILE_EXISTS);

		// Check if the file exists, is too large, is has not been uploaded
		if ( ! file_exists($tempfile) ) 
			$ok = $this->logInvalidInput(_LANG_TEMP_FILE_MISSING);
		if ( $_FILES[$this->name]['size'] > $maxsize ) 
			$ok = $this->logInvalidInput(_LANG_FILE_TOO_LARGE."($maxsize)");
		if ( ! is_uploaded_file($tempfile) ) 
			$ok = $this->logInvalidInput(_LANG_FILE_WACKO);

		if ( ! $ok ) 
			return false;

		// Everything looks ok
		$this->_destfile = $file;
		$this->_tempfile = $tempfile;
		return true;
	} // end isValidInput

	function listClassAttributes2() {
		return array('code');
	}

	
	/**
	 * List the input types supported by this object
	 *
	 * @return array List of supported inputs
	 * @access private
	 * @since 1.4
	 */
	function listValidInputs() {
		$validinputs = array('FILEUPLOAD' => 'FILEUPLOAD'); 
		return $validinputs;
	} // end listSupportedInputs()	
	
	/**
	 * Override base class to specify a input type
	 *
	 * @return boolean True if the input field can be printed
	 * @access public
	 * @since 1.4
	 */
	function printInputFieldX() {
		return parent::printInputField('file');
	} // end printInputField
}

?>
