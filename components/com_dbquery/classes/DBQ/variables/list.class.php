<?php

/**
 * @package DBQ
 */

//Prevent Direct Access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
DBQ_Settings::includeClassFileForType('DBQ_variable');
DBQ_Settings::includeClassFileForType('DBQ_substitution');

/**
 * Represents variables used in a DBQ query
 *
 * @subpackage DBQ_variable_list
 */
class DBQ_variable_list extends DBQ_variable {
	
	var $required = 'REQUIRED';
	var $input = NULL;
	var $page = 1;
	var $size = 0;
	var $stats = 1;
	var $uses_custom_query = 0;
	
	var $_enable_on_change = false;
	var $_list = NULL; 
	var $_uses_keys_for_change = false;

		
	/**
	 * Constructor class for the DBQ Variable List class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_variable_list($dummy = false) {
		parent::DBQ_variable($dummy);
	} // end constructor()
	
	/**
	 * Retrieve the default value 
	 * 
	 * This class may be overleaded in the derived class
	 * 
	 * @return String Default Value
	 * @access public
	 * @since 1.2
	 */
	function getDefaultValue() {
		return array();
	} // end getDefaultValue
	
	/**
	 * Retrieve the list of values for a list variable
	 * 
	 * @return Array List of Substitution Objects to populate lists
	 * @access public
	 * @since 1.1
	 */
	function & getList() {
		return $this->_list;
	} // end getList()
	
	/**
	 * Get the name of the target variable used by the onChange event
	 *
	 * @return string Name of the target variable
	 * @access public
	 * @since 1.4
	 **/	
	function getOnChangeTargetVariable() {
		return $this->GetParamValue('ON_CHANGE_TARGET_VARIABLE');
	} // end getOnChangeTargetVariable()
	
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
					
		$replacement = '';
		
		// List variable, should be fetched already
		$list_joiner = $this->getConfigValue('MULTI_SELECT_JOINER');			
		$glue = $list_joiner ? $list_joiner : ',';
		$values =& $this->_fetched_list_values;
		if (is_array($values)) {
			//$replacement = implode($glue,$this->_fetched_list_values) ;
			$replacement = $this->escaped(array_shift($values));
			foreach ( $values as $value )
				$replacement .= $glue . $this->escaped($value);
		}
		//echo "replacement is $replacement<br/>";
		$this->_replacement_value = $replacement;
		return $this->_replacement_value;
	} // end getReplacementValue()

	/**
	 * Return a specific substitution element by it's ID 
	 *
	 * @param numeric $id Id of the substitution element
	 * @return Object Substitution object
	 * @access public
	 * @since 1.4
	 **/
	function & getSubstitution($id) {
		if ( is_array($this->_list) && array_key_exists($id, $this->_list) )
			return $this->_list[$id];
		return null;
	} // end getSubstitution()

	/**
	 * Determine if the variable is a checkbox varaible
	 * 
	 * @return boolean True if the variable is declared as a checkbox
	 * @access public
	 * @since 1.2 
	 */
	function isCheckbox() {
		return ($this->input == 'CHECKBOX');
	} // end isCheckbox()
	
	function isKeyword() {
		return false;
	} // end isKeyword()
	
	function isList() {
		return true;
	} // end isList();
	
	/**
	 * Determine if the variable is a multi-select variable
	 * 
	 * @return boolean True if the variable is declared as a multiselect
	 * @access public
	 * @since 1.2 
	 */
	function isMultiSelect() {
		return ($this->input == 'MULTISELECT');
	} // end isMultiSelect()

	/**
	 * Determine if the variable is a radio select varaible
	 * 
	 * @return boolean True if the variable is declared as a radio select
	 * @access public
	 * @since 1.2 
	 */
	function isRadio() {
		return ($this->input == 'RADIO');
	} // end isRadio()
	
	/**
	 * Determine if the variable is a select varaible
	 * 
	 * @return boolean True if the variable is declared as a select
	 * @access public
	 * @since 1.2 
	 */
	function isSelect() {
		return ($this->input == 'SELECT');
	} // end isSelect()

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
		
		// Shortcut -- if required but empty, return false
		//echo "testing validity of input '$input' for variable $this->name<br/>";
		if ( $this->isRequired() && $input == '')
			return $this->logInvalidInput(_LANG_MISSING_INPUT);

		// Return three if there is no input but input is not required
		if ( !$this->isRequired() && $input == '') 
			return true;
			
		if (array_key_exists($input, $this->_list)) {
			$obj = & $this->_list[$input];
			$this->_fetched_list_values[] = $obj->value;
			return true;
		}

		return $this->logInvalidInput(_LANG_INPUT_NOT_IN_LIST);			

	} // end isValidInput
	
	function listClassAttributes1() {
		return array('required', 'input', 'size', 'stats');
	} // end listClassAttributes1()

	/**
	 * List the input types supported by this object
	 *
	 * @return array List of supported inputs
	 * @access private
	 * @since 1.4
	 */
	function listValidInputs() {
		return array('SELECT', 'RADIO', 'CHECKBOX', 'MULTISELECT');
	} // end listValidInputs()
	
	/**
	 * Load list values for a list variable
	 * 
	 * This class should be overloaded in derived classes
	 * 
	 * @access private
	 * @since 1.4
	 */	
	function initialize() {
		return true;
	} // end initialize()

	/**
	 * Register preloaded values into this variable
	 *
	 * @param string $input
	 * @access public
	 * @since 1.4
	 */
	function registerPreloadedInput($input) {

		if ( $this->supportsMultipleSelection() ) {
			// The input should contain multiple values 
			$joiner			= $this->getConfigValue('MULTI_SELECT_JOINER');
			$input_values	= explode($joiner, $input);
			if (in_array($this->_list[$id]->value, $input_values)) {
					$this->_preregistered_input[] = $id;
			}
		} else {
			// The input should be a single value
			foreach (array_keys($this->_list) as $id) {
				if ( $this->_list[$id]->value == $input) {
					$this->_preregistered_input[] = $id;	      
					break;
				}
			}
		}

		if ( $this->debug() )
			$this->debugWrite("preloading input '$input' on variable '$this->name' ($this->id)");

		return true;
	} // end registerPreloadedValue();

	/**
	 * Print the input field for this variable type
	 * 
	 * By default, this function will try to determine the template directory of the parent query.
	 * Otherwise, it uses the system default.
	 * 
	 * @return Boolean True if the template file for the input was included
	 * @access public
	 * @since 1.3
	 *
	 */
	function printInputField($input = NULL) {
		
		// Test for onChange parameters
		if ( $this->usesKeysForChange() ) 
			// This is the target, so print out the javascript function
			// Get the name of the directory that contains template files
			parent::printInputField('onChangeKeys');

		
		return parent::printInputField($input);
	} // end printInputField()

	/**
	 * Enable onChange events on this variable
	 *
	 * Using this feature requires that substitution elements have a 'key' attribute
	 *
	 * @param boolean $value Optional new value
	 * @return boolean True if onChange is enabled
	 * @access public
	 * @since 1.4
	 **/
	function onChangeIsEnabled($value = NULL) {
		if ( ! is_null($value) )
			$this->_enable_on_change = $value;
			
		return $this->_enable_on_change;
	} // end onChangeIsEnabled
	
	/**
	 * Determines if the variable supports multiple selections in lists
	 * 
	 * @return Boolean True if the variable is a list variable and supports multiple substitution values
	 * @access public
	 * @since 1.3
	 */
	function supportsMultipleSelection() {
		return ( $this->isMultiSelect() || $this->isCheckbox() );
	} // end supportsMultipleSelection()
	
	/**
	 * Determines if the variable supports substitution values, such as in a dropdown list
	 * 
	 * @return Boolean True if the variable is a list variable and supports substitutions values
	 * @access public
	 * @since 1.3
	 */
	function supportsSubstitutions() {
		return ( $this->isSelect() || $this->isMultiSelect() || $this->isRadio() || $this->isCheckbox() || $this->isFileSelect());
	} // end supportsSubstitutions()
	
	/**
	 * Indicates if the substitution elements contain keys that can used to select subsets of elements
	 *
	 * @param boolean $value Optional new value
	 * @return boolean True if the variable's list does use keys
	 * @access private
	 * @since 1.4
	 */
	function usesKeysForChange($value = NULL) {
		if ( ! is_null($value) )
			$this->_uses_keys_for_change = $value;
			
		return $this->_uses_keys_for_change;
	} // end usesKeysForChange()
}

?>