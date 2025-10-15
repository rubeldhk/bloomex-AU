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
 * @subpackage DBQ_variable_keyword
 */
class DBQ_variable_custom extends DBQ_variable_keyword {

	//var $code = NULL;
	//var $default_value = NULL;
	var $required = 'REQUIRED';
	var $input = 'TEXT';
	var $page = 1;
	var $regex = 'TEXT';
	var $size = 10;
	var $stats = 0;


	// Constructor	
	function DBQ_variable_custom($dummy = false) {
		parent::DBQ_variable( $dummy);
	}
	
	// Comment info
	function getCommentAboutInput() {
		return "A custom input variable";
	} // end getCommentAboutInput()


	// Do something about returning the replacement value
	function getReplacementValue() {
		if ( $this->containsInvalidInput() )
			return $this->logApplicationError("Trying to retrieve a replacement for variable $this->name which has invalid input");

		
		// Return a previously calculated replacement value
		if ( ! is_null($this->_replacement_value) )
			return $this->_replacement_value;
			
		$replacement = '';
		
		if ($this->inputSuccessfullyRegistered() || $this->acceptsBlanks() || ! $this->isRequired() ) {
			// Keyword that has been accepted
			//echo "using accepted input '$replacement' for replacement<br/>";
			$replacement = @ $this->_accepted_input[0];
			/*
			// Evaluate the code to determine the display string
			$this->evalCode($replacement);
			*/
			//} elseif ($this->usesDefaultValue()) {
			// No input for keyword variable but we can use the default value
			//$replacement = $this->getDefaultValue();
		} else {
			// Don't know what to do here
			$this->logApplicationError("Do not know how to generate replacement value for '$this->name'");
		}
		
		$this->_replacement_value = $this->escaped($replacement);
		return $this->_replacement_value;
	} // end getReplacementValue()


	
	// Load any values prior to use
	function initialize() {
		return true;
	} // end initialize()

	// Make a test to see if the input is accepted
	function isValidInput() {
		$input =& $this->_input;
		
		// Shortcut -- if required but empty, return false
		//echo "testing validity of input '$input' for variable $this->name<br/>";
		if ( $this->isRequired() && $input == '')
			return $this->logInvalidInput(_LANG_MISSING_INPUT);

		// Return three if there is no input but input is not required
		if ( !$this->isRequired() && $input == '') 
			return true;
			
		$ok = true;

		// Check length requirements			
		if (strlen($input) > $this->size) 
			$ok = $this->logInvalidInput(_LANG_INPUT_EXCEED_MAX_SIZE. $this->size);

		// Check if the input meets the regex requirements
		$regex = '/^'.$this->getEregCode($this->regex).'$/';
		if (! preg_match($regex, $input) )
			$ok = $this->logInvalidInput(_LANG_INPUT_DOESNT_MATCH_REGEX);
			
		return $ok;
	} // end isValidInput()

	// List attribs to display on the second details tab
	function listClassAttributes1() {
		return array('required', 'required', 'input', 'regex', 'size', 'stats', 'page');
	} // end listClassAttributes1()
	
	// List attribs to display on the third details tab
	function listClassAttributes2() {
		return array();
	} // end listClassAttributes

	/**
	 * List the input types supported by this object
	 *
	 * @return array List of supported inputs
	 * @access private
	 * @since 1.4
	 */
	function listValidInputs() {
		$validinputs = array('HIDDEN' => 'HIDDEN', 
							 'HTMLEDITOR' => 'HTMLEDITOR', 
							 'PASSWORD' => 'PASSWORD', 
							 'TEXT' => 'TEXT',
							 'TEXTAREA' => 'TEXTAREA'
						);
		return $validinputs;
	} // end listSupportedInputs()
	
	// Does the variable use a form input
	function usesInputForm() {
		return true;
	} // end usesInputForm()
} // end of DBQ_variable_custom class
?>