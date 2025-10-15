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
 * @subpackage DBQ_variable_keyword
 */
class DBQ_variable_keyword extends DBQ_variable {


	var $required = 'REQUIRED';
	var $input = 'TEXT';
	var $page = 1;
	var $regex = 'TEXT';
	var $size = 10;
	var $stats = 0;

	var $code = NULL;
	var $default_value = NULL;
	
	/**
	 * Constructor class for the DBQ Variable Keyword class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_variable_keyword($dummy = false) {
		parent::DBQ_variable($dummy);
	} // end constructor()

	/**
	 * Retrieve the default value 
	 * 
	 * @return String Default Value
	 * @access public
	 * @since 1.2
	 */
	function getDefaultValue() {
		return $this->default_value;
	} // end getDefaultValue
		
	/**
	 * Return the value of a variable for the input field
	 *
	 * @access public
	 * @return String Value to use in the input field
	 * @since 1.3
	 */
	function getPreviousInput() {
		$value = parent::getPreviousInput();
		if ( is_array($value) )
			$value = $value[0];
			
		if ( ( $value === '' ) and $this->usesDefaultValue() )
			$value = $this->getDefaultValue();
		
		return $value;
	} // end getPreviousInput()
	
	/**
	 * Devise the return value for the variable.
	 * 
	 * This function requires that input has previously been registered
	 * 
	 * @return String replacement value
	 * @access private
	 * @since 1.3
	 */
	function getReplacementValue() {
		if ( $this->containsInvalidInput() )
			return $this->logApplicationError("Trying to retrieve a replacement for variable $this->name which has invalid input");
		
		// Return a previously calculated replacement value
		if ( ! is_null($this->_replacement_value) )
			return $this->_replacement_value;

		// Determine a replacement value
		$replacement = NULL;
		if ( count($this->_accepted_input ) ) {
			// Input was accepted by the user
			$replacement = $this->_accepted_input[0];
			//} elseif ( $this->usesDefaultValue() ) {
			// Use a default value
			//$replacement = $this->getDefaultValue();
		}

		// Log an error if we don't have anything yet
		if ( !( ($replacement !== NULL) || $this->acceptsBlanks() || ! $this->isRequired() )) 
			return $this->logApplicationError("Do not know how to generate replacement value for '$this->name' ($this->id)");

		// Evaluate the replacement value if code is enabled
		$this->evalCode($replacement);		

		$this->_replacement_value = $this->escaped($replacement);
		return $this->_replacement_value;
	} // end getReplacementValue()
	
	/**
	 * Retrieve the height of a text area input box
	 * 
	 * @return Int value for height
	 * @access public
	 * @since 1.2
	 */
	function getTextAreaHeight() {
		$value = $this->getConfigValue('TEXT_AREA_HEIGHT');
		return $value ? $value : 10;
	} // end getTextAreaHeight()

	/**
	 * Retrieve the width of a text area input box
	 * 
	 * @return Int value for width
	 * @access public
	 * @since 1.2
	 */	
	function getTextAreaWidth() {
		$value = $this->getConfigValue('TEXT_AREA_WIDTH');
		return $value ? $value : 60;
	} // end getTextAreaWidth()

	/**
	 * Retrieve the width of a text box
	 * 
	 * @return Int value for size of input box
	 * @access public
	 * @since 1.2
	 */
	function getTextInputWidth() {
		$value = $this->getConfigValue('TEXT_INPUT_WIDTH');
		return $value ? $value : 20;
	} // end getTextAreaHeight()

	/**
	 * Determine if the variable is a file upload 
	 * 
	 * @return boolean True if the variable is a browse input box for file upload
	 * @access public
	 * @since 1.2
	 */
	function isFileUpload() {
		return ( $this->type == 'file' );
	} // end isFileUpload()
	
	/**
	 * Determine if the variable is a hidden input field
	 * 
	 * @return boolean True if the variable is assigned as a hidden input field
	 * @access public
	 * @since 1.1
	 */
	function isHidden() {
		return ( $this->input == 'HIDDEN' );
	} // end isHidden()

	function isKeyword() {
		return true;
	} // end isKeyword()
	
	/**
	 * Determine if the variable is a text area field with a html editor
	 * 
	 * @return boolean True if the variable is declared as a text area field
	 * @access public
	 * @since 1.2 
	 */
	function isHtmlEditor() {
		return ( $this->input == 'HTMLEDITOR' );
	} // end isHtmlEditor()

	function isList() {
		return false;
	} // end isList()
	
	/**
	 * Determine if the variable is a text field
	 * 
	 * @return boolean True if the variable is declared as a text field
	 * @access public
	 * @since 1.2 
	 */
	function isText() {
		return ($this->input == 'TEXT');
	} // end isText()

	/**
	 * Determine if the variable is a text area field
	 * 
	 * @return boolean True if the variable is declared as a text area field
	 * @access public
	 * @since 1.2 
	 */
	function isTextArea() {
		return ($this->input == 'TEXTAREA');
	} // end isTextArea

	/**
	 * Determine if the supplied input is valid input for the given variable
	 * 
	 * $param String $input Input to test, either a string for keywords or a sid for lists
	 * @return Boolean True if the input is valid
	 * @access public
	 * @since 1.2
	 */
	function isValidInput() {
		
		$input =& $this->_input;
		
		//echo "testing validity of input '$input' for variable $this->name<br/>";
		
		// Tests about what to do when no input is provided
		if ( $input == '' ) {
			// Quick check when there is no input and the variable is not required
			if (!$this->isRequired() || !$this->isRequiredWithinContext())
				return true;
			
			// The variable is required, either by it's attribute or by the statement
			return $this->logInvalidInput(_LANG_MISSING_INPUT);
		}
		
		$ok = true;

		// Check length requirements			
		if (strlen($input) > $this->size) 
			$ok = $this->logInvalidInput(_LANG_INPUT_EXCEED_MAX_SIZE. $this->size);

		// Check if the input meets the regex requirements
		$regex = '/^'.$this->getEregCode($this->regex).'$/s';
		if (! preg_match($regex, $input) )
			$ok = $this->logInvalidInput(_LANG_INPUT_DOESNT_MATCH_REGEX);
			
		return $ok;

	} // end isValidInput
	
	function listClassAttributes1() {
		return array('required', 'input', 'regex', 'size', 'stats', 'page');
	}
	
	function listClassAttributes2() {
		return array('default_value', 'code');
	}
	
	/**
	 * List the input types supported by this object
	 *
	 * @return array List of supported inputs
	 * @access private
	 * @since 1.4
	 */
	function listValidInputs() {
		$validinputs = array(
							 'HIDDEN' => 'HIDDEN', 
							 'HTMLEDITOR' => 'HTMLEDITOR', 
							 'PASSWORD' => 'PASSWORD', 
							 'TEXT' => 'TEXT',
							 'TEXTAREA' => 'TEXTAREA'
						);
		return $validinputs;
	} // end listValidInputs()

	
	/**
	 * Determine if the default value should be used if non is provided
	 * 
	 * @return Boolean True if the default value should be used
	 * @access private
	 * @since 1.2
	 */
	function usesDefaultValue() {
		return ($this->required == 'DEFAULT');
	} // end usesDefaultValue()
	
	function usesInputForm() {
		return true;
	} // end usesInputForm()
}

?>