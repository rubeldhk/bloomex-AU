<?php


/**
 * Database Query Package
 *
 * @package DBQ
 * @author Toby Patterson tcp@gmitc.biz
 * @copyright Toby Patterson 2004-2005
 * @version 1.4
 **/

defined('_VALID_MOS') or die('Direct access is not permitted to this file');

/**
 * DBQ Definitions file
 */

/**
 * A common base class for all DBQ classes
 *
 * This base class provides common functionality that all derived classes
 * can use.
 *
 * @subpackage DBQ_common DB Query Common class
 */
class DBQ_common extends mosDBTable {
    /**#@+
     * @access private
     */

    var $_gid = 0;
    var $_config = array ();
    var $_settings;
    var $_error_log = NULL;
    var $_error_log_file = NULL;
    var $_error_msgs = array ();
    var $_config_types = array ();
    var $_parent = NULL;
    var $_params = NULL;
    // var $params = NULL; // will be used to store custom configurations
    /**#@-*/

    /**
     * Constructor for common base class
     *
     * @access public
     * @global string $mosConfig_absolute_path
     * @global object $database
     * @param string $table Name of table to use, defaulting to query's table
     * @param boolean $dummy Indicates if only the object framework should be constructed
     */
    function __construct($table = _DBQ_QUERIES_TABLE, $dummy = false) {
		global $database;

		// This shouldn't be a FULL varialbe, perhaps just a placeholder
		// return the object
		if ($dummy) {
			$this->_config =& $GLOBALS['DBQ_CONFIG'];
			$this->_db =& $database;
			$this->_error_log =& $GLOBALS['DBQ_ERROR_LOG'];
			return $this;
		}

        parent::__construct($table, 'id', $database);

		// Load DBQ Settings
		if ( ! array_key_exists('DBQ_Settings', $GLOBALS) ) {
			$settings = new DBQ_Settings();
			$GLOBALS['DBQ_Settings'] =& $settings;
		} 
		$this->_settings =& $GLOBALS['DBQ_Settings'];
		
		if ( isset($GLOBALS['DBQ_CONFIG']) ) {
			$this->_config = $GLOBALS['DBQ_CONFIG'];
		} else {
			// Retrieve config information from the database
			$database->SetQuery('SELECT t.type, t.name, t.key, t.value, t.description from '._DBQ_CONFIG_TABLE." t ORDER BY t.type, t.key");
			if (!$rows = $database->loadObjectList()) {
				$this->logCriticalError("Could not retrieve configuration: ".$database->getErrorMsg());
				return NULL;
			}

			// Add the data to our object
			foreach ($rows as $row) {
				// Stuff anything we find in the config DB into the object
				// Lets hope users don't add conflicting types ( ie, PARSE ) to the table

				// Register the config type in our object if it hasn't been registered already
				if (!array_key_exists($row->type, $this->_config_types) ) {
					$string = NULL;
					$tmp = "\$string = _LANG_$row->type;";
					eval ($tmp);
					$this->_config_types[$row->type] = $string ? $string : $row->type;
					//echo "String is $string, tmp is $tmp<BR>";
				}

				// Create a destination and place values
				//$destination = '_' . strtolower($row->type);
				$this->_config[$row->type][$row->key][0] = $row->value;
				$this->_config[$row->type][$row->key][1] = $row->description;
				$this->_config[$row->type][$row->key][2] = $row->name;
			} // End of foreach  db_configs
			$GLOBALS['DBQ_CONFIG'] = $this->_config;
		}

		// Finished initializing the object
		if ( array_key_exists('DBQ_ERROR_LOG', $GLOBALS) ) {
			$this->_error_log =& $GLOBALS['DBQ_ERROR_LOG'];
		} elseif ( $file = @ $this->getConfigValue('ERROR_LOG_FILE')) {
			if (!preg_match('/^//', $file)) {
				// Not an absolute filename
				global $mosConfig_absolute_path;
				$file = $mosConfig_absolute_path.'/'.$file;
			}
			//echo "error file is $file<br/>";

			if (!$this->_error_log = fopen($file, "a")) {
				echo "Could not open error log '$file'";
			} else {
				$GLOBALS['DBQ_ERROR_LOG'] =& $this->_error_log;
			}
		}

		// Append to include path if specified
		if ( $append = $this->getConfigValue('APPEND_INCLUDE_PATH') ) {
			$include_path = ini_get('include_path');
			ini_set('include_path', "$include_path:$append");
			$include_path = ini_get('include_path');
		}

    } // end constructor DBQ_common()

    /**
     * Check the objects variables to determine if they meet table requirements
     *
	 * Disabled in 1.4
	 * 
     * @return bool True if all requirements are satisfied
     * @access public
     * @since 1.0
     */
    function check() {
		return true;
		$columns = & $this->getTableInformation();
		foreach ($columns as $k => $v) {
			/* If the object is set with this key, then apply some
			 *  basic checks based on information in the database.
			 * Test the field if
			 * 1. there is something to test, ie, it tests true, or
			 * 2. it is required and not 'id'
			 * Because new data may not yet have an 'id', we only
			 *  test it when it is set
			 */
			$var = @ $this-> $k;
			$required = @ $v->Required;

			if ($var || ($required && $k !== 'id')) {
				$type = @ $v->Type;
				$size = @ $v->Size;
				$regex = $this->getEregCode(strtoupper($type));
				$strlen = strlen($var);

				// Test to see if it fits
				if ($strlen > $size) {
					$this->logInvalidInput($k, _LANG_TOO_LARGE."( $strlen > $size )");
					return false;
				}

				$final_regex = "/^$regex$/";

				//echo "Col input is $var. Testing '$k' as '$var' against reg '$final_regex' <BR>";
				if (!preg_match($final_regex, $var)) {
					// Failed a test
					$x = new stdClass();
					$x->comment = $this->getEregComment(strtoupper($type));
					$x->required = $required;
					$x->size = $size;
					$x->name = $k;
					$x->input = $var;
					//echo _LANG_VARIABLE . " $k \"$var\" " . _LANG_NO_PASS_REGEX ." \"$final_regex\"";
					$this->logUserError("element. $k \"$var\" "._LANG_NO_PASS_REGEX." \"$final_regex\"");
					$formatErrors[$k] = $x;
				}
			}
		}

		$this->_invalid_input = & $formatErrors;
		if (count($formatErrors) && $this->debug()) {
			$this->debugWrite('format error: ');
			$this->debugDump($formatErrors);

		}

		// return true only if we found no errors
		return count($formatErrors) ? false : true;
    } // end check()

	/**
	 * Remove escape characters that the bane of PHP configurations will put in data
	 *
	 * @param string $string The string to clean
	 * @return $string A clean string
	 * @since 1.4
	 */
	function cleanMagicQuotes($string) {
		if ( get_magic_quotes_gpc() ) {
			return stripslashes($string);
		}
		return $string;
	} // end cleanMagicQuotes

    /**
     * Determine if debugging is enabled
     *
     * @param boolean $toggle If not NULL, sets the debug attribute
     * @return boolean True if debug is enabled; otherwise False 
     * @access private
     * @since 1.0
     */
    function debug($toggle = NULL) {

		if ( isset($GLOBALS['DBQ_OUTPUT']) && ! $GLOBALS['DBQ_OUTPUT'] == 'XHTML' )
			return fasle;

		// Toggle debugging
		if (isset ($this) && !is_null($toggle)) {
			$this->debug = $toggle;
			return $toggle;
		}
		
		// Check the global debug setting
		if ( isset($GLOBALS['DBQ_Debug']) && $GLOBALS['DBQ_Debug'])
			return true;

		// Check if we are called by an object and the object's debug setting is enabled
		if (isset ($this) ) {
			// Check global debug and object debug
			if ( @ $this->getConfigValue('DEBUG') || @ $this->debug)
				return true;
			// Check parent's debug
			// TODO set debug for children when a parent record is saved on the admin end
			if ( $this->_parent && is_object($this->_parent) )
				return $this->_parent->debug();
		} 

		// Forget it
		return false;
    } // end debug()

    /**
     * Write the structure of an object to the javascript debug window
     *
     * @param object $obj Object to be dumped
     * @param string $mesg Message to annote the created dump
     * @access protected
     * @since 1.0
     */
    function debugDump($obj = NULL, $mesg = NULL) {

		if ( isset($GLOBALS['DBQ_OUTPUT']) && ! $GLOBALS['DBQ_OUTPUT'] == 'XHTML' )
			return fasle;

		ob_start();
		print_r($obj);
		$result = ob_get_contents();
		ob_end_clean();
		$captured = explode("\n", $result);

		// Create Lines of output
		$lines = array();
		$lines[] = addslashes($mesg);

		// Format everything in nice colors		
		foreach ($captured as $line) {
			$line = str_replace('[', '[<font color="red">', $line);
			$line = str_replace(']', '</font>]', $line);
			$line = str_replace("\r", '', $line);
			$line = str_replace('Array', '<font color="blue">Array</font>', $line);
			$line = str_replace('=>', '<font color="#556F55">=></font>', $line);
			$lines[] = $line;
		}
		$this->debugWrite(explode("\n", $lines) );
    } // end debugDump()

    /**
     * Initialize the debug system, IE, print the javascript
     *
     * @access private
     * @since 1.4
     */
    function debugInit() {

		if ( isset($GLOBALS['DBQ_OUTPUT']) && ! $GLOBALS['DBQ_OUTPUT'] == 'XHTML' )
			return fasle;

		// Load javascript function for writing to the window
		$JSFile = DBQ_settings::getPath('xhtml').'writeToDiv.js';
		/* Joomla 1.5 Code
		 global $mainframe;
		 $document =& $mainframe->getDocument();
		 $document->addScript(  );
		*/
		echo '<script language="JavaScript" type="text/javascript">';
		include $JSFile;
		echo '</script>';
    } // end debugInit()

    /**
     * Write to the javascript debug window
     * 
     * @param string $mesg Message to write
     * @access protected
     * @since 1.0
     */
    function debugWrite($mesg = NULL) {

		if ( isset($GLOBALS['DBQ_OUTPUT']) && ! $GLOBALS['DBQ_OUTPUT'] == 'XHTML' )
			return fasle;

		$mesg = DBQ_common::makeStringJavaScriptSafe($mesg);
		//$mesg = htmlspecialchars(str_replace("\n", $mesg), ENT_QUOTES);
		?>
			<script language="JavaScript" type="text/javascript">
				 writeToDiv('DBQDebug', '<?php echo $mesg ?>');
		</script>
			  <?php
			  } // end debugWrite()

    /**
     * Create a display name by replacing underscores with spaces and capitalizing the first letter
     *
     * @access public
     * @since 1.0
     * @param string $name Name to convert
     * @return string converted string
     */
    function displayName($name) {
		switch ($name) {
		case 'catid':
			return 'Category';
		case 'db_id':
			return 'Database';
		default:
			return ucwords(trim(str_replace('_', ' ', $name)));
		}
    } // end displayName()

	/**
	 * Get the base Joomla URL for the component
	 *
	 * @return string URL
	 * access public
	 * since 1.4
	 */
	function getBaseURL() {
		global $mosConfig_live_site;
		return $mosConfig_live_site . '/components/com_dbquery/';		
	} // end getBaseUrl()

    /**
     * Return a value from the object's paramters or DBQ's System configurations
     * 
     * If the key exists as a parameter for the object, the value of this key
     * will be returned.  If it does not exist, then the system default will be 
     * returned if it exists.
     * 
     * @param string $key Key to return
     * @return string Value of the configuration
     * @access public
     * @since 1.0
     */
    function getConfigValue($key) {

		$value = $this->getParamValue($key);
		
		// If we have a value, or a value has been set (eg "0"), then return the value
		if ($value || strlen($value)) 
			return $value;

		if ( array_key_exists($key, $this->_config['CONFIG']) && isset($this->_config['CONFIG'][$key][0]))
			return $this->_config['CONFIG'][$key][0];
		return NULL;
    } // end getConfigValue()

    /**
     * Retrieve the desired SQL code for counting rows
     * 
     * @param String $key Key that identifies the SQL code
     * @return String SQL code to use
     * @access protected
     * @since 1.0
     */
    function getCountSQL($key) {
		return @ $this->_config['COUNTSQL'][$key][0];
    } // end getCountSQL()


	/**
	 * Return an arrar of data from a DBQ query
	 *
	 * @param Integer $qid ID of the DBQ query
	 * @return Array Array of data
	 * @access private
	 * @since 1.4
	 **/
	function getDataFromQuery($qid) {

		// Create and load the query
		$query = new DBQ_query();
		if ( ! ( $query->load($qid) && $query->initialize() ) )
			return $this->logApplicationError("Object '$this->name' ($this->id) could not initialize the source query '$query->name' ($query->id)");

		// Register variables from input
		$input = $_POST;
		if ( count($input) )
			$query->registerInput($input);

		// Check if all input is provided
		if ( ! $query->allRequiredInputIsRegistered() )
			return $this->logApplicationError("Object '$this->name' ($this->id) did not register the required variables for the source query '$query->name' ($query->id)");
		
		// Execute the query
		$query->interpolateOnQuery();
		$query->queryInit();
		$query->queryExecute();

		// Fetch the results
		$list =& $query->queryGetResults();

		// Check for an empty list
		if ( ! count($list) )
			return $this->logApplicationError("Object '$this->name' ($this->id) loaded the source query '$qid' but no results were returned");

		$query->queryFinish();

		return $list;
	} // end getDataFromQuery()

	/**
	 * Return an array of data from a SQL query
	 *
	 * @param String $sql SQL query to be fired at the Joomla DB
	 * @return Array Array of data
	 * @access private
	 * @since 1.4
	 **/
	function getDataFromSQL($sql) {

		
		if ( ! $sql )
			return $this->logApplicationError("Object '$this->name' ($this->id) does not provide a query");

		if ( $this->debug() )
			$this->debugWrite("SQL used to initialize object $this->name ($this->id) is $sql");

		$db =& $this->_db;
		$db->setQuery($sql);
		$list =& $db->loadAssocList();
		// Check if we got anything
		if ( !count($list)) 
			return $this->logApplicationError("Cannot retrieve values for object '$this->name' ($this->id): " . $db->getErrorMsg() );
		return $list;
	} // end getDataFromSQL()

    /**
     * Get the general description from the query object's description parameters
     *
     *
     * @return string Description String
     * @access public
     * @since 1.4
     */
    function getDescription() {
		return $this->getDescriptionByKey('GENERAL_DESCRIPTION',NULL);
    } // end getDescription()

    /**
     * Try to obtain a language alternative for the current object
     *
     * This function looks in the object's description field and tries to determine a language string for the user in their language
     *
     * @param string $key Description key to look for
     * @param string $alt If the language is not the default, then try to evaluate the language code identified by alt
     * @return string Language string, or false if none could be found
     * @access private
     * @since 1.4
     */
    function getDescriptionByKey($key, $alt) {
		global $mosConfig_lang;
		$string = '';
	
		// Test for language, using the Joomla language as the default
		$language = $this->getDescriptionParamKey('DEFAULT_LANGUAGE');

		if ( ! $language || ($mosConfig_lang == $language) ) {
			$string = $this->getDescriptionParamKey($key);
			//echo "trying to get $key, found '$string'";
			if ( $string )
				return $string;
		} 

		// The descriptions are not in the default language, so try alternative language
		if ( ! $alt ) 
			$alt = '_LANG_TEMPLATE_'.$key;

		$langCode = $this->getDescriptionParamKey($alt);

		// Try to evaluate the language code
		//echo "Variable lang code is '$langCode' for key '$key', alt '$alt'<br/>";
		if ( defined( $langCode ) ) {
			$evalString = '$string = ' . $langCode . ';';
			@ eval($evalString);

			if ( $string )
				return $string;
		} 

		// Still nothing.  Don't log an error because strings such as input comments may not have been defined
		return $this->getDescriptionParamKey($key);
    } // end getDescriptionByKey()

    /**
     * Return a description from the object's description parameter
     * 
     * @access public
     * @param string $key Key to return
     * @return string Value of the description parameter
     * @since 1.4
     */
    function getDescriptionParamKey($key='') {

		// Check if this object uses parameters
		if ( ! @ $this->description )
			return NULL;

		// Check if a params object is already loaded
		if ( ! isset($this->_descriptions) ) {

			// Load parameters
			$xmlFile = $this->getXMLDescriptionParamFileName();

			// Test for a readable file
			if ( ! is_readable($xmlFile)) 
				return $this->logApplicationError("Cannot load parameters for '$this->name' ($this->id) based on file $xmlFile");

			// Test the object
			$descriptions = new mosParameters( $this->description, $xmlFile );
			if (  ! is_object($descriptions) ) 
				return $this->logApplicationError("Cannot make parameters for '$this->name' ($this->id) based on file $xmlFile");
				
			$this->_descriptions =& $descriptions;
		} // end is_object()


		// Check for value
		return is_object($this->_descriptions) ? $this->_descriptions->get($key) : NULL;
    } //end getDescriptionParamValue()
	
    /**
     * Determines the absolute path of important DBQ directories
     * 
     * This function will supercede the equivalent global variables
     *
     * @param String $type
     * @return String Absolute path of the requested directory
     * @access protected
     * @since 1.3
     */
    function getDirPath($type) {

		global $mosConfig_absolute_path;

		$dbq_user_path = $mosConfig_absolute_path.'/components/com_dbquery/';
		$dbq_class_path = $dbq_user_path.'classes/DBQ/';

		switch ($type) {
		case 'Classes':
			return $dbq_class_path;
		case 'VariableClasses':
			return $dbq_class_path.'variables/';
		case 'VaribleTemplates':
			return $dbq_user_path.'xhtml/';
		}
    } // end getDirPath()

	/**
	 * Returns the display name of a query 
	 * 
	 * @access public
	 * @since 1.3
	 * @return String Display name of the query
	 */
	function getDisplayName() {


		global $mosConfig_lang;
		$language = $this->getDescriptionParamKey('DEFAULT_LANGUAGE');

		// Check if this query is in the native language
		//echo "lang is ( $mosConfig_lang == $language )";
		if ( $mosConfig_lang == $language )
			return @ $this->display_name;

		// Different language - use the language string
		$langCode = $this->getDescriptionParamKey('DISPLAY_NAME_LANG_CODE');
		//echo "language code is $langCode<br/>";

		// Try to evaluate the language code
		if ( defined( $langCode ) ) {
			$evalString = '$string = ' . $langCode . ';';
			@ eval($evalString);
	
			if ( $string )
				return $string;
		}	

		// Return default string
		return @ $this->display_name;
	} // end getDisplayName()

    /**
     * Return a value from DBQ's Display Regex configurations
     * @access public
     * @param string $key Key to return
     * @return string Value of the configuration
     */
    function getDisplayRegex($key) {
		$regex = $this->_settings->getDisplayRegex($key);
		return $regex[0];
		return @ $this->_config['DISPLAY_REGEX'][$key][0];
    } // end getDisplayRexex()

    /**
     * Get a regular expression definition
     *
     * First custom definitions are checked, the system definitions
     *
     * @param string $key Regex identifier
     * @param int $i Regex attribute desired
     * @return mixed Either an array with the entire regex for the definition, the attribute if $i was specified, or False if the def does not exist
     * @access private
     * @since 1.4
     */
    function getEreg($key, $i = NULL) {
		$regex = NULL;
		
		// Check custom regexs
		if ( $key && array_key_exists($key, $this->_config['EREG']) ) {
			$regex = $this->_config['EREG'][$key];
		} else {
			// Check global defs
			$regex = $this->_settings->listRegularExpressions($key);
		}
		
		// Log an error if the regex does not exist
		if ( ! $regex && ! is_array($regex) )
			return $this->logApplicationError("Trying to obtain regular expression '$key' for object '$this->name' ($this->id) but the regex cannot be found");

		// Return a specific element if requested
		if ( ! is_null($i) && array_key_exists($i, $regex) )
			return $regex[$i];
			
		// Return normal
		return $regex;
    } // end getEreg()
	
    /**
     * Return a value from DBQ's Regular Expressions configurations
     * @access public
     * @param string $key Key to return
     * @return string Value of the configuration
     * @since 1.0
     */
    function getEregCode($key) {
		return $this->getEreg($key, 0);
    } // end getEregCode()

    /**
     * Return a comment from DBQ's Regular Expression configurations
     * @access public
     * @param string $key Key to return
     * @return string Value of the configuration
     * @since 1.0
     */
    function getEregComment($key) {
		return $this->getEreg($key, 1);
    } // end getEregComment()

    /**
     * Escape the provided string
     *
     * @access public
     * @param string $string String to escape
     * @return string Escaped string
     */
    function getEscaped($string) {
		return $this->_db->getEscaped(trim($string));
    } // end getEscaped()

    /**
     * Determine the name of the class used for this object on the frontend
     *
     * @return String Name of the class
     * @access private
     * @since 1.4
     */
    function getFrontEndClass() {
		$class = get_class($this);
		if ( $this->isAdmin() )
			$class = preg_replace('/_admin/', '', $class);
		return $class;
    } // getFrontEndClass()
	
    /**
     * Get the ID of the current object
     * @return integer ID of the current object
     * @since 1.0
     */
    function getID() {
		return $this->id;
    } // end getID()

    /**
     * Return a comment from DBQ's Input configurations
     * @access public
     * @param string $key Key to return
     * @return string Value of the configuration
     * @since 1.2
     */
    function getInputComment($key) {
		$inputs =& $this->_settings->listSupportedInputs();
		return @ $inputs[$key][1];
    } // getInputComment
	
    /**
     * Load an instance of an object class
     * 
     * Note, if called from an admin class, an object used in the frontend will be returned instead
     * 
     * @param Array $arg Array of values to bind to the new object
     * @return Object New object
     * @access private
     * @since 1.4
     */
    function getInstance($arg = null) {
		
		// Determine the class
		$class = $this->getFrontEndClass();
		$isvariable = true;

		// special handling for variables
		// PHP5 will give class as 'DBQ_variable, but PHP4 will give 'dbq_variable'
		//  stupid stupid stupid ...
		if (  $arg && ( ($class == 'DBQ_variable') || ($class == 'dbq_variable') )  ) {

			$type = NULL;
			$id = NULL;
			$isvariable = false;
			
			// Determine the variable type
			if ( ! is_array($arg) && preg_match('/^\d+$/', $arg) ) {
				$id = $arg;
			} elseif ( array_key_exists('type', $arg) ) {
				$type = $arg['type'];
			} elseif ( array_key_exists('id', $arg) ) {
				$id = $arg['id'];
			}
			
			// Need to get information from database
			if ( $id ) {
				$sql = 'SELECT * FROM '._DBQ_VARIABLES_TABLE.' t WHERE id = '.$id.' LIMIT 1';

				global $database;
				$database->setQuery($sql);
				$arg = & $database->loadAssocList();

				if ( ! count($arg) )
					return $this->logApplicationError("Cannot retreive data for variable objected identified with id $arg");	
				$arg = $arg[0];	
				$type = $arg['type'];			
			}

			if ( ! $type )
				$this->logApplicationError("Cannot determien object type for object '$this->name' ($this->id)");
					
			// Create an object of the appropriate type
			$class = 'DBQ_variable_'.$type;

		}

		//echo "DBQ_Common::getInstance says that class should be $class <br/>";
		
		// Require the library
		DBQ_Settings::includeClassFileForType($class);
			
		$obj = new $class;

		// Default behavior - return a basic class
		if ( $arg )
			$obj->bind($arg);
		
		$obj->initialize();

		//echo "common base: obj name is $obj->name";

		return $obj;
    } // end getInstance()
	
    /**
     * Return a comment about an error level
     *
     * @param Integer $code
     * @return String Comment field about this error
     */
    function getInternalErrorComment($key) {
		$codes = $this->_settings->listErrorCodes();
		return @ $codes[$key][1];
    } // getInternalErrorComment()

    /**
     * Return a string representing an error level
     *
     * @param Integer $code
     * @return String String that represents the numeric code
     */
    function getInternalErrorString($key) {
		$codes = $this->_settings->listErrorCodes();
		return @ $codes[$key][0];
    } // getInternationErrorString()

    /**
     * Get any invalid input submitted for the query
     *
     * @access public
     * @return array Array of all input errors
     * @since 1.0
     */
    function getInvalidInput($key = NULL) {
		if ( ! @ $this->_invalid_input ) return NULL;
		return isset($key) ? @ $this->_invalid_input[$key] : @ $this->_invalid_input;
    } // end getInvalidInput();

    /**
     * Get the last error message if it exists
     * @access public
     * @return string Error Message
     */
    function getLastErrorMsg() {
		$c = count($this->_error_msgs);
		if ($c == 0)
			return NULL;
		return $this->_error_msgs[$c -1];
    } // end getLastErrorMsg()

    /**
     * Get the last error message if it exists
     * @access public
     * @return string Error Message encoded for HTML
     */
    function getLastErrorMsgHTML() {
		return addcslashes(trim($this->getLastErrorMsg()), "'\0..\37");
    }

    /**
     * Get the Name of the current object
     * @return Formal name of the object
     * @since 1.3
     */
    function getName() {
		return $this->name;
    } // end getName()

    /**
     * Return a parameter from the object's parameter list
     * 
     * @access public
     * @param string $key Key to return
     * @return string Value of the configuration
     * @since 1.2
     */
    function getParamValue($key='', $unescape = false) {

		// Check if this object uses parameters
		if ( ! @ $this->params )
			return NULL;

		// Check if a params object is already loaded
		if ( ! is_object($this->_params) ) {

			// Load parameters
			$xmlFile = $this->getXMLParamFileName();
			if ( is_readable($xmlFile)) {

				$params = new mosParameters( $this->params, $xmlFile, 'dbquery' );
				if ( is_object($params) ) {
					$this->_params =& $params;
				} else {
					return $this->logApplicationError("Cannot load parameters based on file $xmlFile");
				}

			}
		} // end is_object()

		// Check for value
		$value = is_object($this->_params) ? $this->_params->get($key) : NULL;

		// Unescape the value, if requested
		return $unescape ? str_replace( '<br />', "\n", $value ) : $value;
    } //end getParamValue()


    /**
     * Get the table specification for the table in use
     * 
     * @access public
     * @return array Table Information
     * @access private
     * @since 1.0
     * @todo make a more db independent function
     */
    function & getTableInformation() {

		if ( isset($this->_tableinfo) and is_array($this->_tableinfo ))
			return $this->_tableinfo;
		
		// Table info has not been build yet		
		// Retrieve the structure from the database and store
		// Possibly hard code the vars later
		//$tableinfo = $this->retrieveTableInformation($this->_home_tbl);
		$tableinfo = $this->retrieveTableInformation($this->_tbl);
		
		$this->_tableinfo =& $tableinfo;
		return $tableinfo;
    } // end getTableInfo()

    /**
     * Get the base directory of templates
     * @access private
     * @global string $mosConfig_absolute_path
     * @global string $option
     * @return string Base directory
     *
     */
    function getTemplateBaseDir() {
		global $mosConfig_absolute_path;
		return $mosConfig_absolute_path."/components/com_dbquery/".$this->getConfigValue('TEMPLATE_DIR');
    } // end getTemplateBaseDir()

    /**
     * Return a value from DBQ's User configurations
     * 
     * @param string $key Key to return
     * @return string Value of the configuration
     * @access public
     * @since 1.0
     */
    function getUserConfig($key) {
		return @ $this->_config['USER_CONFIG'][$key][0];
    } // end getUserConfig()

    /*
     * Override the default Joomla function to allow for a 'clean' parameter
     *
     * The 'clean' tells us that we should clean all fields and use the new
     * request for all future input.
     * Gets the value of a user state variable
     * @param string The name of the user state variable
     * @param string The name of the variable passed in a request
     * @param string The default value for the variable if not found
     * @return string The desired parameter
     * @access public
     * @since 1.1
     */
    function getUserStateFromRequest($var_name, $req_name, $var_default=NULL) {
		global $mainframe;
		if ( mosGetParam($_REQUEST, 'clean') ) {
			//echo "clean setUserState($var_name, $var_default)<br/>";
			$mainframe->setUserState($var_name, $var_default);
		}
		return $mainframe->getUserStateFromRequest($var_name, $req_name, $var_default);
    } // end getUserStateFromRequest()

    /**
     * Return the name of the XML Parameter File for the given object type
     *
     * @access private
     * @since 1.3
     * @return String Filename
     **/
    function getXMLParamFileName($object = NULL) {

		// Allow for an option object to be passed in and checked
		if ( ! $object ) $object =& $this;

		// Determine the path to the file
		global $dbq_xml_path;
		$class = strtolower(get_class($object));
		$type = '';
		if ( ($class === 'dbq_frontend') or ($class === 'dbq_professional' ) ) {
			// DBQ Frontend actually uses the query.xml file
			$type = 'query';
		} else {
			// Default behavior - strip the prefix
			$type = preg_replace('/^.*_/','',$class);
		}
		return "${dbq_xml_path}params.$type.xml";
    } // end getXMLParamFileName


    /**
     * Return the name of the XML Parameter File for descriptions of the given object type
     *
     * @access private
     * @since 1.4
     * @return String Filename
     **/
    function getXMLDescriptionParamFileName($object = NULL) {

		// Allow for an option object to be passed in and checked
		if ( ! $object ) $object =& $this;

		// Determine the path to the file
		// In 1.4, there are only four objects that use descriptions, so just check for these
		global $dbq_xml_path;
		$class = strtolower(get_class($object));
		$type = '';
		if ( ($class === 'dbq_frontend') or ($class === 'dbq_professional' ) or ($class === 'dbq_query') ) {
			// DBQ Frontend actually uses the query.xml file
			$type = 'query';
		} elseif ( preg_match('/variable/', $class) )  {
			// Default behavior - strip the prefix
			$type = $object->Usesinputform() ? 'variable.input' : 'general';
		} else {
			$type = 'general';
		}
		return "${dbq_xml_path}descriptions.$type.xml";
    } // end getXMLDescriptionParamFileName
	
    /**
     * Determine if the current object has registered errors
     * 
     * @access public
     * @return int Count of error messages
     * @access public
     * @since 1.4
     */
    function hasErrors() {
		return count($this->_error_msgs);
    } // hasErrors()
	
    /**
     * Include a file
     * 
     * This function will test if the file exists and indeed a file.
     *
     * @param String $file
     * @return Boolean True if the file has been included
     * @todo Return status code of include
     */
    function includeFile($file) {
		// Test and include the file
		if ( ! is_file($file) )
			return $this->logCriticalError("Cannot read file '$file'");
			
		if ( ! include($file) )
			return $this->logCriticalError("Cannot include file '$file'");
			
		return true;
    } // end includeFile()
	
    /**
     * Abstract function for derived classes that may want to initialize an object after getInstance()
     * 
     * @return boolean True if initialization was successful
     * @access private
     * @since 1.4
     */
    function initialize() {
		return true;
    } // end initialize()
	
    /**
     * Indicates if the current object is using the admin interface
     * 
     * This function is overridden in the admin classes
     *
     * @return boolean False
     * @access private
     * @since 1.4
     */
    function isAdmin() {
		return false;
    } // end isAdmin()

    /**
     *	binds an array/hash to this object
     *	@param int $oid optional argument, if not specifed then the value of current key is used
     *	@return any result from the database operation
     */
    function load( $oid=null ) {
		$k = $this->_tbl_key;
		
		if ($oid !== null) {
			$this->$k = $oid;
		}
		
		$oid = $this->$k;
		
		if ($oid === null) {
			return false;
		}
		
		$this->reset();
		
		$query = "SELECT *"
			. "\n FROM $this->_tbl"
			. "\n WHERE $this->_tbl_key = '$oid'"
			;
		$this->_db->setQuery( $query );

		return $this->_db->loadObject( $this );
    } // end load()
	
    /**
     * Renamed in DBQ 1.3 RC3 in order to address bug in Joomla 1.0.8
     *
     * @param int $oid
     * @return boolean True if the load of data was successful
     * @todo rename function to load once bug is fixed in 1.0.8
     */
    function load_orig($oid=NULL) {
		$rs = parent::load($oid);

		// Load parameters by calling the get function without any arguments
		// abcde
		//$this->getParamValue();
		return $rs;
    } // end load()

    /**
     * Include a file
     * @access public
     * @param string $filename The file to be included
     * @return The return status of the include statement
     */
    function loadFile($filename = NULL) {
		if (!$filename || !is_file($filename)) {
			$this->logApplicationError(_LANG_COULD_NOT_LOAD_FILE.': '.$filename);
			return false;
		}
		return include $filename;
    }

    /**
     * Log an application error to DBQ's error log
     *
     * @access public
     * @param string $mesg Message to log
     * @return boolean false
     */
    function logApplicationError($mesg = '') {
		return $this->logError(3, $mesg);
    }

    /**
     * Log a critical error to DBQ's error log
     *
     * @param string $mesg Message to log
     * @return boolean false
     */
    function logCriticalError($mesg = '') {
		return $this->logError(4, $mesg);
    } // end logCriticalError()

    /**
     * Log an error to DBQ's error log
     *
     * @access private
     * @param string $source Source of the message
     * @param string $mesg Message to write
     * @param integer $priority Denotes the importance of the error (currently not used)
     * @return boolean Return status of logError()
     * @access protected
     * @since 1.0
     * @todo make config values to enable certain types of logging
     */
    function logError($code = 1, $mesg = '') {
		
		// Display a notice about the error
		if (  $this->debug() )
			$this->debugWrite($mesg);

		// Write the error message to the file
		if ( $this->getConfigValue('LOGTOERRORFILE') ) {
			$level = $this->getInternalErrorString($code);
			$error = @ "Object '$this->name' ($this->id) on ".date("F j, Y, g:i a")." : $level - $mesg\n";
			$this->_error_msgs[] = $error;
			fwrite($this->_error_log, $error."\n");
		}
		
		// Now record the error into the database		
		if ( $this->getConfigValue('LOGTOERRORTABLE') ) {
			global $my;
			
			// Determine the class and set errors related to the query to 'DBQ_query'
			$class = get_class($this);


			if ( ($class=='DBQ_frontend') or preg_match('/^DBQ_driver/', $class) )
				$class = 'DBQ_query';

			// Make sure that we don't break the SQL
			$oid = isset($this->id) ? $this->id : 0;
			$myid = isset($my->id) ? $my->id : 0;
			$name = isset($this->name) ? $this->_db->getEscaped($this->name) : '';
			$message = $this->_db->getEscaped($mesg);
			
			// Insert the error message into the database
			$sql = 'INSERT INTO '._DBQ_ERRORS_TABLE." VALUES ('$class', $oid, '$name', $myid, $code, now(), '$message')";
			$this->_db->setQuery($sql);
			$this->_db->query();
		}
		return false;
    } // end logError()

    /**
     * Log the submission of invalid input
     *
     * The function does not write a message to the error log.
     * Rather it writes a message to the object which can later be retrieved 
     * for the UI
     *
     * @param string $name Name of the variable
     * @param string $reason Reason for the input being invalid
     * @return Boolean False
     * @access private
     * @since 1.0
     */
    function logInvalidInput($name, $reason) {
		if (!isset ($this->_invalid_input[$name])) {
			if ($this->debug())
				$this->debugWrite("Logging invalid input: $name, $reason");
			$this->_invalid_input[$name] = $reason;
		}
		return false;
    } // end logInvalidInput()

    /**
     * Log a query error to DBQ's error log
     *
     * @param string $mesg Message to log
     * @return boolean false
     */
    function logQueryError($mesg = '') {
		return $this->logError(5, $mesg);
    } // end logQueryError()

    /**
     * Log an user error to DBQ's error log
     *
     * @param string $mesg Message to write
     * @return boolean Return status of logError()
     * @access private
     * @since 1.2
     */
    function logUserError($mesg = '') {
		return $this->logError(2, $mesg);
    } // end logUserError()

    /**
     * Create javascript for onMouseOver to display the provided text
     * 
     * @param String $text String to display
     * @return String Javascript code
     * @access private
     * @since 1.2 
     */
    function makeLibOver($text) {
		$text = addslashes($text);
		$text = htmlspecialchars($text);
		$text = preg_replace('/\n|\r|\r\n/', '<br/>', $text);
		return ' onMouseOver="return overlib(\'<table>'.$text.'</table>\', CAPTION, \''._LANG_DESCRIPTION.'\', BELOW, RIGHT);" onMouseOut="return nd();" ';
    } // end makeLibOver()
	
    /**
     * Make the provided string javascript safe
     *
     * @param string $text String to escape
     * @return string JavaScript safe string
     * @access public
     * @since 1.4
     */
    function makeStringJavaScriptSafe($text) {
        $text = preg_replace('/[[:cntrl:]]/',' ', $text);
        //$text = strip_tags( $text );
		return addslashes($text);
    } // end makeStringJavaScriptSafe()

    /**
     * Mash an array together
     *
     * The returned array will be of the structure of $array[$row[i][0]] = $row[i][1]
     *
     * @param array $rows Rows to mash
     * @return array Mashed rows
     * @access private
     * @since 1.0
     */
    function mashRows(& $rows) {
		$tmp = array ();
		foreach ($rows as $row) {
			$tmp[$row[0]] = $row[1];
		}
		return $tmp;
    } // end mashRows()

    /**
     * Mash an array together
     *
     * The returned array will be of the structure of $array[$row[key i]] = $row[i][1]
     *
     * @access private
     * @param array $rows Rows to mash
     * @return array Mashed rows
     */
    function mashRows1(& $rows) {
		$tmp = array ();
		foreach ($rows as $k => $row) {
			//echo "row is: " ; print_r($row); echo "<BR>";
			$tmp[$k] = $row[1];
		}
		return $tmp;
    } // end mashRows1()

    /**
     * Mash an array together
     *
     * The returned array will be of the structure of $array[$row[key i]] = $row[i][2]
     *
     * @access private
     * @param array $rows Rows to mash
     * @return array Mashed rows
     */
    function mashRows2(& $rows) {
		$tmp = array ();
		foreach ($rows as $k => $row) {
			//echo "row is: " ; print_r($row); echo "<BR>";
			$tmp[$k] = $row[2];
		}
		return $tmp;
    } // end mashRows2()

    /**
     * Open and read from a directory
     * 
     * @param string $dir Name of the directory to open
	 * @param string $dironly If true, list only directories
     * @param string $regex Regular Expression against which files are negatively matched
     * @return array $files Array of files, False if the directory could not be open
     * @access private
     * @since 1.1
     */
    function readFilesFromDirectory($dir, $dironly=0, $regex = '^[^\.]') {
		
		// Test for a directory
		if ( ! is_dir($dir)) 
			return $this->logUserError("The directory '$dir' is not a directory");

		// Test the opening of the directory
		$d = opendir($dir);
		if ( ! $d )
			return $this->logUserError("Cannot read files from the directory '$dir'");

		$files = array();
		$i = 0;

		// Define a default regex
		if ( ! $regex )
			$regex = '^[^\.]';

		// Read the files
		if ( $dironly ) {
			// Only read directories
			while ($file = readdir($d)) {
				if (is_dir($dir.'/'.$file) && ereg($regex, $file)) {
					$files[$i++] = $file;
				}
			}
		} else {
			// Only read files
			while ($file = readdir($d)) {
				if (is_file($dir.'/'.$file) && ereg($regex, $file)) {
					$files[$file] = $file;
				}
			}
		}

		// Close and finish
		closedir($d);
		return $files;

    } // end readFilesFromDirectory()

    /**
     * Retrieve information about the table in the database
     *
     * The information retrieved includes the 'Type' and 'Size'
     * This information is used to create forms in the administrative screens,
     *  and to validate input via the check() function.
     *
     * As of version 1.0, this function is specific to MySQL
     *
     * TODO This function will be coded to work with statically defined data
     *      in each class.  A configuration option will enable the calls to 
     *      the database.
     *
     * @access private
     * @since 1.0
     * @param string $table Table to query
     * @return array Table information
     * @todo use XML file to define fields
     */
    function retrieveTableInformation($table) {
		// code is currently specific to mysql....
		$sql = 'SHOW columns from '.$table;
		//print_r($this);
		$this->_db->setQuery($sql);
		$rows = $this->_db->loadObjectList();

		$results = array ();
		if (!count($rows)) return false;
		foreach ($rows as $row) {
			$tmp = array ();
			// Organize the rows by the field name
			// Not sure why the regex won't work w/ vars without ()
			// Will look into the problem later ...
			if ($row->Type == 'datetime') {
				$row->Type = 'datetime';
				$row->Size = 19; // Not really sure
			} elseif ($row->Type == 'text') {
				$row->Type = 'text';
				$row->Size = 2048;
			} else {
				preg_match('/(.*)\(([0-9]+)\)/', $row->Type, $tmp);
				//preg_match('/(.*?)(\(([0-9]*)\))/', $row->Type, $tmp);
				$row->Type = $tmp[1];
				$row->Size = $tmp[2];
			}
			$row->Required = @ ( $row->Null == '0' || $row->Null == 'NO') ? true : false;
			$results[$row->Field] = $row;
		}
		return $results;
    }

    /**
     * Set the parent of the current object
     *
     * @param object Parent object
     * @access private
     * @since 1.4
     */
    function setParent(& $parent ) {
		$this->_parent =& $parent;
    } // end setParent

    /**
     * Store an element in the database
     *
     * @access public
     * @since 1.0
     * @return boolean Return status of mosDBTable's store()
     */
    function store() {
		// Make some of the fields database safe, just in case

		//$class_vars = get_class_vars(get_class($this));
		if ( (int) $this->id == 0 ) $this->id = NULL;
		//if ( array_key_exists('ordering', $class_vars) and ! $this->ordering ) $this->ordering = 0;
		//if ( array_key_exists('size', $class_vars) and ! $this->size ) $this->size = 0;

		$ret = parent :: store();

		// Log errors and report errors to users in debug mode
		if ( ! $ret  ) {
			$error = 'Cannot save element to database: ' . $this->_db->getErrorMsg();
			if ($this->debug() )
				$this->debugWrite($error);
			return $this->logCriticalError($error);
		}
		return $ret;
    } // end store()

    /**
     * Write a message to the console
     *
     * @param string $message Message to write
     * @return boolean True
     * @access private
     * @since 1.4
     */
    function writeConsoleMessage($message) {
		$message = addslashes(nl2br($message));
		?>
			<script language="JavaScript" type="text/javascript">
				 writeToDiv('DBQMessages', '<?php echo $message ?>');
		</script>
			  <?php
			  return true;
    } // end writeConsoleMessage
} // End of common class
?>
