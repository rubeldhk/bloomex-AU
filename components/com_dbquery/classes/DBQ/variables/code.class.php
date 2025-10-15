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
 * @subpackage DBQ_variable_files
 */
class DBQ_variable_code extends DBQ_variable_keyword {
	
	
	/**
	 * Constructor class for the DBQ Variable List class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_variable_code($dummy = false) {
		parent::DBQ_variable_keyword($dummy);
	} // end constructor()

	function getReplacementValue() {
		
		// Return a previously calculated replacement value
		if ( ! is_null($this->_replacement_value) )
			return $this->_replacement_value;
			
		$replacement = NULL;
		// Evaluate the code to determine the display string
		$this->evalCode($replacement);
			
		$this->_replacement_value = $this->escaped($replacement);
		return $this->_replacement_value;
	} // end getReplacementValue()

	/**
	 * Enter description here...
	 *
	 * @return boolean True if the variable is declared as code to be executed on the server
	 * @access public
	 * @since 1.3
	 */
	function isServerSideCode() {
		return true;
	} // end isServerSideCode()	

	/**
	 * Since server code does not take input, this function returns true
	 *
	 * @return boolean True
	 * @access public
	 * @since 1.4
	 */
	function isValidInput() {
		return true;
	} // end isValidInput()
	
	function listClassAttributes1() {
		return array('stats', 'code');
	}

	function listClassAttributes2() {
		return array();
	}

	/**
	 * List the input types supported by this object
	 *
	 * @return array List of supported inputs
	 * @access private
	 * @since 1.4
	 */
	function listValidInputs() {
		$validinputs = array();
		return $validinputs;
	} // end listValidInputs()
	
	function usesInputForm() {
		return false;
	} // end usesInputForm()
}

?>