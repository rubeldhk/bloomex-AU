<?php

/**
 * @package DBQ
 */

//Prevent Direct Access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
DBQ_Settings::includeClassFileForType('DBQ_variable');

/**
 * Represents variables used in a DBQ query
 *
 * @subpackage DBQ_variable_statement
 */
class DBQ_variable_statement extends DBQ_variable {

	var $required = 'REQUIRED';
	
	/**
	 * Constructor class for the DBQ Variable Statement class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_variable_statement($dummy = false) {
		parent::DBQ_variable($dummy);
	} // end constructor()
	
	function isStatement() {
		return true;
	} // end isStatement()
	
	function listClassAttributes1() {
		return array('required');
	}
	
	function usesInputForm() {
		return false;
	} // end usesInputForm()
}

?>