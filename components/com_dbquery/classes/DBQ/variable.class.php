<?php

/**
 * @package DBQ
 */

//Prevent Direct Access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
//DBQ_Settings::includeClassFileForType('DBQ_common');
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/common.class.php');

/**
 * Represents variables used in a DBQ query
 *
 * @subpackage DBQ_variable
 */
class DBQ_variable extends DBQ_common {
	/**#@+
	 * @access public
	 */
	var $id = NULL;
	var $query_id = NULL;
	var $display_name = 'Unconfigured Variable';
	var $name = 'Unconfigured Variable';
	var $type = 'keyword';
	var $active = 0;
	var $ordering = NULL;
	var $debug = 0;
	var $description = NULL;
	var $parent = NULL;
	var $editor = NULL;
	var $checked_out = NULL;
	var $checked_out_time = NULL;
	var $params = NULL;
	var $stats = 0;


	/**#@+
	 * @access private
	 */
	var $_accepted_input = NULL;
	var $_ignored_input = array();
	var $_input = NULL;
	var $_invalid_input = array();
	var $_preregistered_input = array();
	var $_rejected_input = array();
	var $_replacement_value = NULL;
	var $_requires_user_attention = false;
	/**#@-*/
	
	/**
	 * Constructor class for the DBQ Variable class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_variable($dummy = false) {
		parent::DBQ_common(_DBQ_VARIABLES_TABLE, $dummy);
	}

	/**
	 * Determine if the variable will accept empty fields
	 * 
	 * @return Boolean True if the variable accepts blanks
	 * @access public
	 * @since 1.3
	 */
	function acceptsBlanks() {
		return @ ($this->required == 'BLANK');
	}
	
	/**
	 * Indicates if the variable contains invalid input
	 * 
	 * The function checks for messages about invalid input.  It does not
	 * mean that input has been placed on the array of rejected input.
	 * 
	 * @return Int Count of inputs which are invalid
	 * @access public
	 * @since 1.3
	 */
	function containsInvalidInput() {
		return @ count($this->_invalid_input);
	} // end containsInvalidInput

	/**
	 * Indicates if the variable contains rejected input
	 * 
	 * Input such as NULL or empty strings will not be found here
	 * 
	 * @return Int Count of inputs which have been rejected
	 * @access public
	 * @since 1.3
	 */
	function containsRejectedInput() {
		return @ count($this->_rejected_input);
	} // end containsRejectedInput

	/**
	 * Return the supplied value, escaped if the escape param has been set
	 *
	 * @param string $value Value to optionally escape
	 * @return string Value
	 * @access private
	 * @since 1.4
	 */
	function escaped($value) {
		return $this->getParamValue('ESCAPE_OVERRIDE') ? $value : addslashes($value) ;
	} // end escaped()
	
	/**
	 * Evaluate the provided string
	 *
	 * Evaluating code can lead to security vulnerabilities.
	 *
	 * @param string $replacement The string to evaluate upon
	 * @return boolean True if eval was successful
	 * @access private
	 * @since 1.4
	 */
	function evalCode(& $replacement) {
		
		// Do not proceed there is no code to evaluate
		if (! $this->code)
			return true;
			
		// Try to evaluate the code to dynamically determine a replacement value
		// Evaluated code should contain a statement such as "$replacment = $something;"

		global $dbq;
		$code = $this->code;
		//$search = '/$/';
		//$replace = '\\$';
		//preg_replace($search, $replace, $replacement);
		//echo "replacement is $replacement<br/>";
		if ( eval($code) === false)
			return $this->logApplicationError("Eval failed for variable '$this->name' ($this->id)");
			
		return true;		
	} // end evalCode()


	/**
	 * Get an array of input previously submitted by the user
	 *
	 * @return array Accepted Input for the variable
	 */
	function getAcceptedInput() {
		return @ $this->_accepted_input;
	} // end getAcceptedInput()
	
	/**
	 * Provide useful instructions about what the user must supply for the input
	 * 
	 * @return String Description for the Regex or Input type
	 * @access public
	 * @since 1.2
	 */
	function getCommentAboutInput() {

		// Check if we use alternative descriptions based on language and the like

		$comment = $this->getDescriptionByKey('INPUT_COMMENT_TEXT', 'INPUT_COMMENT_LANG_CODE');

		if ($comment)
			return $comment;
		
		// No, use DBQ defaults
		if ( $this->isList() ) {
			$comment = $this->getInputComment($this->input);
		} elseif ( $this->isKeyword() ) {
			$comment = $this->getEregComment($this->regex);
		}

		if ( $comment ) 
			return $comment;

		// No comment about input, log an error
		return $this->logApplicationError("Cannot find a comment for variable $this->name ($this->id) with type '$this->type'");
	} // end getCommentAboutInput()

	/**
	 * Determine the css classes required to validate the form on the client
	 *
	 * @return string CSS class definitions
	 * @access public
	 * @since 1.4
	 */
	function getCSSClass() {
		$classes = array();
		
		// A generic css definition for DBQ input fields
		$classes[] = 'DBQInput';

		// Indicate if this variable is required
		if ( $this->isRequiredWithinContext(false) )
			$classes[] = 'required';

		// Support an error field
		//$classes[] = 'errFld';

		// Translate the DBQ regex to the wform CSS class
		if ( @ $this->regex) {
			switch ($this->regex) {
			case 'EURODATE':
				$validation = 'date';
				break;
			case 'USDATE':
				$validation = 'date';
				break;
			default:
				$validation = strtolower($this->regex);
				break;
			}
			if ( $validation ) 
				$classes[]  = 'validate-'.$validation;
		}

		return implode(' ', $classes);
	} // end getCSSClass()
	
	/**
	 * Get the general description for the variable
	 *
	 * @return string General description of the variable
	 * @access public
	 * @since 1.4
	 */
	function getDescription() {
		return parent::getDescriptionByKey('GENERAL_DESCRIPTION', 'GENERAL_DESCRIPTION_LANG_CODE');
	} // end getDescription()
	

	/**
	 * Get the directory which contains templates for input types
	 * 
	 * @return String Absolute path where the templates reside
	 * @access public
	 * @since 1.4
	 */
	function getInputTemplateDirectory () {
		if ( isset($this->_parent)) {
			// We have a parent - just checking
			$dir = $this->_parent->getTemplateDirectory();
		} else {
			// Sorry Charlie, settle for the default
			$this->logApplicationError("Cannot find the parent query for variable '$this->name' ($this->id), so using the default template directory");
			$dir = $this->getTemplateBaseDir() . $this->getConfigValue('DEFAULT_TEMPLATE');
		}
		
		return $dir;
	}  // end getInputTemplateDirectory()
	
	/**
	 * Return a value from DBQ's Input configurations
	 * @access public
	 * @return string Value of the configuration
	 * @since 1.3
	 */
	function getInputType() {
		if ( ! isset($this->input) )
			return $this->logApplicationError("Variable '$this->name' ($this->id) does not have an input type");
		
		// Check if this is a standard type of input
		$inputs = $this->_settings->listSupportedInputs();
		if ( array_key_exists($this->input, $inputs))
			return @ $inputs[$this->input][0];
		
		// Check if this is a user defined type of input
		if ( array_key_exists($this->input, $this->_config['INPUT']))
			return @ $this->_config['INPUT'][$this->input][0];	
		
		// We have no clue what this is
		return $this->logUserError("Variable '$this->name' ($this->id) uses an unknown input type '$this->type'");
	} // end getInputType()
	
	/**
	 * Load an instance of a variable class
	 *
	 * @param Array $arg Array of data used to bind to the object
	 * @return Object Variable Object
	 * @access private
	 * @since 1.4
	 */
	function getInstance($arg) {
		
		// Default behavior - return a basic class
		if ( ! $arg )
			return new DBQ_variable();

		// Determine the variable type
		if ( ! is_array($arg) ) {
			// Need to get information from database
			$sql = 'SELECT * FROM '._DBQ_VARIABLES_TABLE.' t WHERE id = '.$arg.' LIMIT 1';

			global $database;
			$database->setQuery($sql);
			$arg = & $database->loadAssocList();
			if ( ! count($arg) )
				return $this->logApplicationError("Cannot retreive data for variable objected identified with id $arg");	
			$arg = $arg[0];
		}

		// Determine what and who we're talking about
		$type = $arg['type'];
		//$id = $arg['id'];
					
		// Create an object of the appropriate type
		$class = 'DBQ_variable_'.$type;
		//if ( $this->debug() )
		//	echo "variable class for $type should be $class";
			
		if (DBQ_Settings::includeClassFileForType($class))
			$var = new $class();
		else {
			$var = new DBQ_variable();
		}

		// Bind and initialize
		$var->bind($arg);
		$var->initialize();
		
		return $var;
	} // end getInstance()


	/**
	 * Returns an array of any invalid input
	 * 
	 * @return Array Invalid Input
	 * @access public
	 * @since 1.3
	 */
	function getInvalidInput() {
		return @ $this->_invalid_input;
	} // end getInvalidInput()

	/**
	 * Retrieve any previous input submitted by the user
	 * 
	 * @return String value of previous input, if any
	 * @access public
	 * @since 1.3
	 */
	function getPreviousInput() {
	  if ( empty($this->_accepted_input) && empty($this->_rejected_input) && empty($this->_ignored_input) && empty($this->_preregistered_input) )
			return NULL;

	  return array_merge((array)$this->_accepted_input, (array)$this->_rejected_input, (array) $this->_ignored_input, (array) $this->_preregistered_input);
	} // end getPreviousInput()

	/**
	 * Devise the return value for the variable.
	 * 
	 * This function requires that input has previously been registered
	 * 
	 * This function must be overridden in the derived class
	 * 
	 * @return String replacement value
	 * @access private
	 * @since 1.3
	 */
	function getReplacementValue() {
		return false;
	} // end getReplacementValue()

	/**
	 * Get the size of the variable
	 * 
	 * @access public
	 * @since 1.3
	 * @return Int Size attribute for the variable
	 **/
	function getSize() { 
		return @ $this->size; 
	} // end getSize()
	
	/**
	 * Determine if the variable is ready to interpolate
	 *
	 * @return Boolean True if the variable is ready in interpolate
	 * @access public
	 * @since 1.3
	 */
	function hasProblemsWithInput() {

		if (! $this->usesInputForm())
			return false; 
			
		if ($this->containsRejectedInput())
			return true;
		
		//if ($this->isRequired() && ! $this->inputSuccessfullyRegistered() )
		//   	return true;
			
		return false;	
	} // end hasProblemsWithInput()


	/**
	 * Indicates if the input for this variable has been successfully registered
	 * 
	 * @return Int Number of accepted input
	 * @access private
	 * @since 1.3
	 */
	function inputSuccessfullyRegistered() {
		return @ count($this->_accepted_input);
	} // end inputSuccessfullyRegistered()
	
	/**
	 * Determine if the variable a custom variable
	 * 
	 * @return boolean True if the variable is of a custom type
	 * @access public
	 * @since 1.1
	 */
	function isCustom() {
		return @ ( $this->type == 'custom' );
	} // end isCustom()
	


	/**
	 * Determine if this variable is a field variable
	 * 
	 * @return boolean True if the variable is a field variable
	 * @access public
	 * @since 1.4
	 */
	function isField() {
		return @ ( $this->type == 'field' );
	} // end isField()
	
	/**
	 * Determine if the variable is a hidden input field
	 * 
	 * @return boolean True if the variable is assigned as a hidden input field
	 * @access public
	 * @since 1.1
	 */
	function isHidden() {
		return @ ( $this->input == 'HIDDEN' );
	} // end isHidden()
	
		
	/**
	 * Determine if the variable is a keyword
	 * 
	 * @return boolean True if the variable is declared as a keyword
	 * @access public
	 * @since 1.2 
	 */
	function isKeyword() {
		return @ ( ($this->type == 'keyword') || ($this->type == 'upload') || ($this->type == 'code'));
	} // end isKeyword()


	/**
	 * Determine if the variable is a list
	 * 
	 * @return boolean True if the variable is declared as a list
	 * @access public
	 * @since 1.1 
	 */
	function isList() {
		return @ ( ($this->type == 'substitutions') || ($this->type == 'list') || ($this->type == 'results') || ($this->type == 'files') || ($this->type == 'customlist') );
	} // end isList()
	
	/**
	 * Determine if the variable is required
	 * 
	 * @return Boolean True if the variable is required
	 * @access public
	 * @since 1.2
	 */
	function isRequired() {
		return @ ($this->required == 'YES');
	} // end isRequired()

	/**
	 * Determines if the variable is effectively required, either by a required attribute or by the state of any host statements
	 *
	 * A variable contained in a non-required statement may be omitted in the the query (effectively not required)
	 * if no input has been provided for any variable in the statement.
	 *
	 * @param boolean $checkStatementUsage Check to see if the statement is being used
	 * @return boolean True if the variable is effectively required in the query
	 * @access public
	 * @since 1.4
	 */
	function isRequiredWithinContext($checkStatementUsage = true) {
		
		$parent =& $this->_parent;
		// Determine if there is a host statement and if it is either required or in use
		$hostStatement = $parent->getHostStatementNameByVarName($this->name);
		//echo "parent is $parent->name, hostStatement is $hostStatement";

		// There is no statement, so the normal regex will be used
		if ( ! $hostStatement )
			return $this->isRequired();

		// There is a statement that is active, so the variable will not be obmitted
		if ($parent->statementIsRequired($hostStatement) || ( $checkStatementUsage && $parent->statementIsInUse($hostStatement)) )
			return $this->isRequired();

		// There is a statement but it is not active, so the variable can be removed
		return false;
	} // end isRequriedWithinContext()

	
	/**
	 * Enter description here...
	 *
	 * @return boolean True if the variable is declared as code to be executed on the server
	 * @access public
	 * @since 1.3
	 */
	function isServerSideCode() {
		return @ ($this->type == 'code');
	} // end isServerSideCode()
	
	/**
	 * Determine if the variable is a statement
	 * 
	 * @return boolean True if the variable is declared as a statement
	 * @access public
	 * @since 1.1 
	 */	
	function isStatement() {
		return @ ($this->type == 'statement');
	} // end isStatement()

	/**
	 * Determine if the variable is a substitution variable
	 *
	 * @return boolean True if the variable is a substitution variable
	 */
	function isSubstitutions() {
		return @ ($this->type == 'substitutions');
	} // end isSubstituions()
	
	/**
	 * Determine if the supplied input is valid input for the given variable
	 * 
	 * This function should be overridden in derived classes
	 * 
	 * $param String $input Input to test, either a string for keywords or a sid for lists
	 * @return Boolean True if the input is valid
	 * @access public
	 * @since 1.2
	 */
	function isValidInput() {
		return $this->logApplicationError("Unknown variable type: '$this->type'");
	} // end isValidInput()

	function listClassAttributes1() {
		return array();
	} // end listClassAttributes1()
	
	function listClassAttributes2() {
		return array();
	} // end listClassAttributes

	/**
	 * List the input types
	 * 
	 * @return array List of supported inputs
	 * @access private
	 * @since 1.4
	 */
	function listSupportedInputs() {
		// Get a list of DBQ supported input types
		$allinputs =& $this->_settings->listSupportedInputs();
		
		// Get a list of valid input types for this variables
		$validinputs = $this->listValidInputs();
		
		// Subtract the difference and return the results
		$inputs = array_merge($allinputs, $validinputs);
		return $this->mashRows2($inputs);
	} // end listSupportedInputs()
	
	/**
	 * List the input types supported by this object
	 * 
	 * This class should be overloaded in any derived classes
	 *
	 * @return array List of supported inputs
	 * @access private
	 * @since 1.4
	 */	
	function listValidInputs() {
		return array();
	} // end listValidInputs
	
	/**
	 * Mark this variable as invalid because of the specified reason
	 *
	 * @param String $reason Reason why the input is invalid
	 * @return False
	 * @since 1.1
	 * @access protected
	 */
	function logInvalidInput($reason) {
		if (!isset ($this->_invalid_input)) 
			$this->_invalid_input = array();

		if ($this->debug())
			$this->debugWrite("Logging invalid input: $this->name, $reason");
			
		array_push($this->_invalid_input, $reason);

		return false;
	} // end logInvalidInput()

	/**
	 * Replace the variable's query regex with the current value
	 *
	 * @param string $query The query where the target will be replaced
	 * @param string $target The string in the query that will be replaced
	 * @param string $replacement Option string that will replace the target
	 * @return boolean True if replacement was successful
	 */
	function performReplacement(& $query, $target, $replacement = NULL) {
		
		// Determine the replacement value
		if ( is_null($replacement) )
			$replacement = $this->getReplacementValue();
		
		//echo "perform replacement('$target', '$replacement', '$query'<br/>";
		if ($this->debug())
			$this->debugWrite("Replace: '$this->name' ($this->id): '$target' to be replaced with '$replacement'");
				
		if ($tmp = str_replace($target, $replacement, $query)) {
			// Successfully found and replaced the variable 
			$query = $tmp;
			return true;
		} else {
			// We did not find the variable as expected, not sure what happened
			
			return $this->logApplicationError("Cannot perform replacement('$target', '$replacement', '$query'");
		}		
	} // end performReplacement
	
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
		
		// Get the name of the directory that contains template files
		$inputTemplateDir = $this->getInputTemplateDirectory();

		// If no input file was specified, determine the input from the variable
		if ( ! isset($input) )
			$input = $this->getInputType($this->input);
			
		$subdir = $this->getConfigValue('INPUTS_DIR');
		$i = $this->getConfigValue('VARIABLE_TEMPLATE_ID');
		$templateFile = $inputTemplateDir.'/'.$subdir.$input.'.'.$i.'.tpl';
		return $this->includeFile($templateFile);
	} // end printInputField()
		
	/**
	 * Register user input submitted by the user
	 *
	 * @param String $input
	 * @return Boolean True if the input is accepted
	 */	
	function registerInput($input) {
		
		// Store the input for later usage
		$this->_input = $input;
		// Write debug message
		if ($this->debug()) 
			$this->debugWrite("Testing input '$input' against variable '$this->name' ($this->id)");
		
		// Test for valid input	
		$isValid = $this->isValidInput();
		
		// Determine how to record the input
		if ($isValid) {
		  // Do not record the input if it is empty unless ...
		  if ( $input || $this->acceptsBlanks() || $input == '0' ) {
		    $this->_accepted_input[] = $input;
		    $action = 'Accepting valid';
		  } else {
		    $this->_ignored_input[] = $input;
		    $action = 'Ignoring valid';
		  }
		} else {
		  if ( $input || $this->acceptsBlanks() || $input == '0' ) {
		    $this->_rejected_input[] = $input;
		    $action = 'Rejecting invalid';
		  } else {
		    $this->_ignored_input[] = $input;
		    $action = 'Ignoring invalid';
		  }       
		}
		
		// Make a note about what we're doing
		if ($this->debug())
			$this->debugWrite("$action input '$input' for variable $this->name ($this->id)");

		return $isValid ? true : false;
	} // end registerInput()
	
	/**
	 * Register preloaded values into this variable
	 *
	 * @param string $input
	 * @access public
	 * @since 1.4
	 */
	function registerPreloadedInput($input) {

	  if ( $this->debug() )
			$this->debugWrite("preloading input '$input' on variable '$this->name' ($this->id)");
		$this->_preregistered_input[] = $input;
		return true;
	} // end registerPreloadedValue();

	/**
	 * Flag the variable as having a problem that requires user attention
	 *
	 * @param boolean $flag True or False
	 * @return boolean True if the variable needs attention by the user
	 * @access public
	 * @since 1.4
	 */
	function requiresUserAttention($flag=NULL) {
	  if ( ! is_null($flag)) 
	    $this->_requires_user_attention = $flag;
	  return $this->_requires_user_attention;
	} // end requiresUserAttention

	/**
	 * Determine if language codes are enabled
	 *
	 * @return boolean True if language codes have been enabled on the query
	 * @access private
	 * @since 1.4
	 */
	function shouldUseLanguageCodes() {
	  if ( isset($this->_parent) && is_object($this->_parent) )
	    return $this->_parent->getDescriptionParamKey('USE_LANGUAGE_CODES');
	  return $this->getDescriptionParamKey('USE_LANGUAGE_CODES');
	} // end shouldUseLanguageCodes()
	
	/**
	 * Determine if the variable uses code executed on the server
	 * 
	 * Code variables do not generate an input form for the user.
	 * 
	 * @return boolean True if the variable uses evaluated PHP code
	 * @depreciated
	 * @access public
	 * @since 1.3
	 */
	function usesCode() {
		return ($this->code);
	} // end usesCode()
	
	/**
	 * Indicates if the variable should create a form element
	 * 
	 * This method should be overridden if the variable does not need a form
	 *
	 * @return boolean True if this variable uses some type of input form
	 * @access protected
	 * @since 1.3
	 */
	function usesInputForm() {
		return true;
	} // end usesInputForm()
	
} // end of DBQ_variable class
?>

