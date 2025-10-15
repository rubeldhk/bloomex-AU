<?php

/**
 * @package DBQ
 */

//Prevent Direct Access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
DBQ_Settings::includeClassFileForType('DBQ_variable_list');

/**
 * Represents variables used in a DBQ query
 *
 * @subpackage DBQ_variable_files
 */
class DBQ_variable_files extends DBQ_variable_list {
	
	/**
	 * Constructor class for the DBQ Variable List class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_variable_files($dummy = false) {
		parent::DBQ_variable_list($dummy);
	} // end constructor()


	function listClassAttributes1() {
		return array('required', 'input', 'size', 'stats', 'page',);
	}

	function listClassAttributes2() {
		return array();
	}

	
	/**
	 * Load Substitution values for a File List variable
	 * 
	 * @return boolean True if the directory can be read and a list of files is loaded
	 * @access private
	 * @since 1.4
	 */		
	function initialize() {
		
		// We're initializing, so read the directory and compare current values
		// with existing values in the db
		global $mosConfig_absolute_path;
		$list =& $this->_list;
		// Open the directory and return a list of all non-hidden files
		//echo "value for fap = ".$this->getConfigValue('FILES_ABSOLUTE_PATH');
		$dir = $this->getConfigValue('DEFAULT_FILE_DIRECTORY');
		if ( $this->getConfigValue('FILES_ABSOLUTE_PATH') ) 
			// Only list files under the website
			$dir = $mosConfig_absolute_path.$dir;

		$regex = $this->getConfigValue('FILE_LIST_REGEX');
		$files = $this->readFilesFromDirectory($dir, 0, $regex);
		
		// Quit if we have not read any files
		if ( ! $files || ! count($files) ) return NULL;

		$results = array ();
		
		// Test if the substitution value exists in the directory list
		if ( count($list) ) {
			foreach ( $list as $object ) {
				//echo "object $object->id with value of $object->value<br/>";
				if ( isset($files[$object->value])) {
					$results[$object->id] = $object;
					// Delete the file from the list
					unset($files[$object->value]);
				}
			}
		}
		
		//print_r($files);
		// Add any remaining files to the list
		$i=1;
		if ( count($files)) {
			foreach ($files as $file) {
				$obj = new DBQ_substitution(true);
				$obj->id = $i++;
				$obj->value = $file;
				$obj->label = $file;
				$results[$obj->id] = $obj;
			}
		}
		$this->_list =& $results;
		return true;
	} // end initialize()

	/**
	 * File upload variables do not preload anything because the data in the
	 *  database would not match the file structure on the user's system
	 *
	 * @param string $input
	 * @access public
	 * @since 1.4
	 */
	function registerPreloadedInput($input) {
		return;
	}
}

?>