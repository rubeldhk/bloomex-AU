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
 * @subpackage DBQ_variable_field
 */
class DBQ_variable_field extends DBQ_variable {

	var $code = NULL;
	
	// Constructor	
	function DBQ_variable_field($dummy = false) {
		parent::DBQ_variable( $dummy);
	}
	

	function getCommentAboutInput() {
		return '';
	} // end getCommentAboutInput()


	function getReplacementValue() {
		return $this->name;
	} // end getReplacementValue()


	function initialize() {
		return true;
	} // end initialize()

	// Make a test to see if the input is accepted
	function isValidInput() {
		return true;
	} // end isValidInput()

	/**
	 * Determine if this field should be sent to the next query
	 *
	 * @return boolean True if the field should be sent to the next query
	 * @access public
	 * @since 1.4
	 */
	function sendToNextQuery() {
		return $this->getParamValue('NEXT_QUERY_TRANSMIT');
	} // end sendToNextQuery
	
	// List attribs to display on the second details tab
	function XlistClassAttributes1() {
		return array();
	} // end listClassAttributes1()
	
	// List attribs to display on the third details tab
	function listClassAttributes2() {
		return array('code');
	} // end listClassAttributes

	
	// register input for this variable
	function registerInput($input) {
		return true;
	} // end registerInput()
	
	// Does the variable use a form input
	function usesInputForm() {
		return false;
	} // end usesInputForm()
} // end of DBQ_variable_field class
?>