<?php

/**
 * @package DBQ
 */

//Prevent Direct Access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
DBQ_Settings::includeClassFileForType('DBQ_variable_code');

/**
 * Represents variables used in a DBQ query
 *
 * @subpackage DBQ_variable_files
 */
class DBQ_variable_user extends DBQ_variable_code {
	
	
	/**
	 * Constructor class for the DBQ Variable List class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_variable_user($dummy = false) {
		parent::DBQ_variable_code($dummy);
	} // end constructor()

	/**
	 * Return the requested information from the joomla user object
	 *
	 * @return string
	 * @access public
	 * @since 1.4.1
	 */
	function getReplacementValue() {
		
		// Return a previously calculated replacement value
		if ( ! is_null($this->_replacement_value) )
			return $this->_replacement_value;
			
		$replacement = NULL;
		
		global $my;
		$attribute = $this->getParamValue('USER_ATTRIBUTE');
		if ( ! $attribute )
			$attribute = 'id';
		$replacement = $my->$attribute;
		
		// Evaluate the code to determine the display string
		$this->evalCode($replacement);
			
		$this->_replacement_value = $this->escaped($replacement);
		return $this->_replacement_value;
	} // end getReplacementValue()

}

?>