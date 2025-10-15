<?php


/**
 * @package DBQ
 */

// Prohibit direct access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
//DBQ_Settings::includeClassFileForType('DBQ_query');
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/query.class.php');

/**
 * Provides functions for the front-end of DBQ
 *
 * @subpackage DBQ_frontend DBQ FrontEnd Class
 */
class DBQ_frontend extends DBQ_query  {

	var $aop = NULL;
	var $source = NULL;
	//var $index = NULL;
	
	// Used for display of query results
	var $column = -1;
	var $column_max = NULL;
	var $header = -1;
	var $header_max = NULL;
	var $row = -1;
	var $row_max = NULL;
	var $headers = array();
	var $input_registered = false;


	var $_frontFirstHit;
	var $_frontLimit;
	var $_frontLimitStart;
	var $_frontDisableInput;
	
	function DBQ_frontend() {
		// Determine the assignment operator, either ',' or '=', depending if sef is enabled
		global $mosConfig_sef;
		$this->aop = $mosConfig_sef ? ',' : '=';
		//$this->index = $_SERVER['SCRIPT_NAME']; // TODO Who is overwritting this ?
		
		parent::DBQ_query();
	} // end DBQ_frontend() constructor

	/**
	 * Terminate processing because there is an unrecoverable error
	 *
	 * @param string $message Error message to log into the error file
	 * @return boolean True
	 * @access private
	 * @since 1.4
	 */
	function abortProccessingDueToError($message = NULL) {
		$this->includeTemplateFile('ERROR_FILE');
		$this->logUserError($message);		
		return true;
	} // abortProccessingDueToError()
	
	/**
	 * Create a url to dbquery pages 
	 * 
	 * This function is SEF aware and uses configuration options defined
	 *  in dbquery.def.php.
	 *
	 * @param array $url_info A key => value array that will generate arguments
	 * @return string The desired URL
	 * @since 1.0
	 * @access public
	 */
	function dbq_url(& $url_info) {

		global $mainframe, $task;

		// Indicate what the previous task was
		$PT = 'previousTask';
		if ( ! array_key_exists($PT, $url_info) ) 
			$url_info[$PT] = $task;

		// If we have been called using index2.php, provide this url instead
		if ($this->windowIsIndex2() && ! $mainframe->isAdmin() ) return  $this->dbq_url2($url_info);
		 
		global $mosConfig_sef;
		$aop = $this->aop;
		$source = $this->source;
		
		//echo "mainframe is " .$mainframe->isAdmin() ."<br/>";
		// Test for SEF, which affects the style of the url
		if ($mainframe->isAdmin()) {
			$host = _DBQ_URL_ADMIN;
			$delimiter = '&amp;';
			$joiner = '=';
			$tail = '&amp;act=preview';
		}
		elseif ($mosConfig_sef) {
			$host = _DBQ_URL_SEF;
			$delimiter = '/';
			$joiner = ',';
			$tail = $delimiter;
		} else {
			$host = _DBQ_URL;
			$delimiter = '&amp;';
			$joiner = '=';
			$tail = '';
		}

		// I can't believe PHP doesn't have a decent map() that works on KEYS and values
		$args = array ();
		foreach ($url_info as $k => $v) {
			$args[] = $k.$joiner.$v;
		}
		return $host.$delimiter.implode($delimiter, $args).$tail;

	} // end dbq_url()

	/**
	 * Check if we can print the next column
	 *
	 * This function is overridden in DBQ_Professional
	 * 
	 * @return boolean True if the next column should be displayed 
	 * @access private
	 * @since 1.4
	 */
	function canDisplayNextColumn() {
		return true;
	} // end canDisplayNextColumn()
	
	/**
	 * Check if we can print the next header
	 *
	 * This function is overridden in DBQ_Professional
	 * 
	 * @access private
	 * @since 1.4
	 */
	function canDisplayNextHeader() {
		return true;
	} // end canDisplayNextHeader()	
	
	/**
	 * Create a url to dbquery pages using Joomla's index2.php
	 * 
	 * This function is SEF aware and uses configuration options defined
	 *  in dbquery.def.php.
	 *
	 * Displays called using urls from this function will contain the mainframe only.
	 * 
	 * @param array $url_info A key => value array that will generate arguments
	 * @return string The desired URL
	 * @since 1.1
	 * @access public
	 */
	function dbq_url2(& $url_info) {

		global $task;
		// Indicate what the previous task was
		$PT = 'previousTask';
		if ( ! array_key_exists($PT, $url_info) )
			$url_info[$PT] = $task;

		$args = array ();
		foreach ($url_info as $k => $v) {
			$args[] = $k.'='.$v;
		}
		return _DBQ_URL2.'&amp;'.implode('&amp;', $args);
	} // end dbq_url2()

	/**
	 * Create a link to a dbq resource
	 * @param string text to display
	 * @param array url information
	 * @return string url to the resource
	 */
	function dbq_link($text = null, & $url_info, $alt = NULL) {
		if (@ !$alt)
			$alt = $text;
		
		$url = $this->windowIsIndex2() 
			? $this->dbq_url2($url_info) 
			: $this->dbq_url($url_info);
		//if ( $this->windowIsIndex2() ) echo "blah";
		return '<a href="'.$this->dbq_url($url_info).'" alt="'.$alt.'">'.$text.'</a>';
	} // end dbq_link()

	/**
	 * Execute a query by obtaining input submitted by user, registering the input,
	 * executing the query, and, if successful, loading the appropriate template
	 * 
	 * @since 1.0
	 * @access public
	 * @param String $qid Query Identifier
	 * @return Boolean True if query was executed
	 */
	function executeQuery($qid) {

		global $mosConfig_absolute_path;

		/*
		 * Process of this function
		 * 1) Retrieve and register input from user
		 * 2) Connect and query the database, load results 
		 * 3) Display to user in the chosen format
		 */

		// Obtain input and register it
		if ( $this->getInputFromRequest() )
			$this->registerInput();

		// Check if we have all the required input
		if (! $this->allRequiredInputIsRegistered() ) 
			return false;

		if ( ! $this->interpolateOnQuery() ) 
			return $this->abortProccessingDueToError("Failed to interoperate variables into query '$this->name' ($this->id)");


		global $subtask;
		$subtask = NULL;
		$success = NULL;
		$pageNav = NULL;
		$url = NULL;

		/*******
		 * Initialize the connection and execute the query
		 * Display an error, preferably from the template, 
		 *  if something went wrong
		 **/
		if ( !$this->queryInit()) 
			return $this->abortProccessingDueToError("Cannot initiate connection to database for query '$this->name' ($this->id)");


		/******
		 * Successful connection
		 * Prepare data for the templates
		 **/

		$limit = $this->getLimit();
		$limitstart = $this->getLimitStart();
		$limitstart = floor($limitstart / $limit) * $limit;
		$rowcount = $this->queryExecute($limitstart, $limit);
		//echo "rowcount is $rowcount<br/>";

		// A false return status indicates a database problem
		if ( $rowcount === false ) 
			return $this->abortProccessingDueToError("Query executed but a false return status was returned for query '$this->name' ($this->id)");
		
		// Do we expect rows ?
		if ($this->expect_rows) {

			if ($rowcount) {

			  $subtask = 'results';

				// Build page navigation objects
				if ($this->page_navigation) {
					require_once ($mosConfig_absolute_path.'/includes/pageNavigation.php');
					$total = $this->queryCountTotal();
					//echo "values are l: $limit, ls: $limitstart, t: $total<br/>";
					$pageNav = new mosPageNav($total, $limitstart, $limit);
					$this->_pageNav = $pageNav;
				}

			} else {
			  $subtask = 'noresults';
			}
		} else {
		  // We don't expect an results
		  $subtask = 'noresultsexpected';
		}

		// Close the connection
		$this->queryFinish();
		
		// Record a successful first access to the query
		if ($this->isFirstHit()) {
			$this->hit();
			if ( $this->notificationsShouldBeSent() )
				$this->sendNotifications();

		}
		
		if ( $this->debug() )
			$this->debugWrite('Interface type is '.$this->interfaceType() );
			
		// Display the results using the appropriate template			
		$ok = $this->includeTemplateFile('EXECUTE_FILE');

		// Optionally display the form again if we searched but no results were returned
		if ( ($subtask == 'noresults') && $this->getConfigValue('SHOW_FORM_ON_NO_RESULTS') )
			return $this->includeTemplateFile('PREPARE_FILE');

		return $ok;
	} // end of executeQuery()

	/**
	 * Display the current field
	 *
	 * @param string $field Optional name of file to get
	 * @return string String to display
	 * @access public
	 * @since 1.4
	 */
	function field($field=NULL) {

        // Get the name of the current field unless a specific field is requested
		if ( is_null($field) )
			$field = $this->getFieldName();

		//echo "field: r is $this->row, c is $this->column, field is $field<br/>";
		return $this->results[$this->row][$field];
	} // end printField()
	
	function getDescriptionSelectQuery()       { return $this->getDescription('GENERAL_DESCRIPTION'); }
	function getDescriptionFormAbove()         { return $this->getDescription('PREPARE_DESCRIPTION_ABOVE'); }
	function getDescriptionFormBelow()         { return $this->getDescription('PREPARE_DESCRIPTION_BELOW'); }
	function getDescriptionResultsAbove()      { return $this->getDescription('EXECUTE_DESCRIPTION_ABOVE'); }
	function getDescriptionResultsBelow()      { return $this->getDescription('EXECUTE_DESCRIPTION_BELOW'); }
	function getDescriptionNoResults()         { return $this->getDescription('EXECUTE_DESCRIPTION_NO_RESULTS'); }
	function getDescriptionWithoutResults()    { return $this->getDescription('EXECUTE_DESCRIPTION_WO_RESULTS'); }
	function getDescriptionError()             { return $this->getDescription('EXECUTE_DESCRIPTION_ERROR'); }

	
	/**
	 * Get the name of the current field
	 *
	 * @return String Name of the current field
	 * @access private
	 * @sinece 1.4
	 **/	
	function getFieldName() {
		return $this->headers[$this->column];
	} // end getFieldName()

	/**
	 * Get the name of the current header
	 *
	 * @return String Name of the current header
	 * @access private
	 * @sinece 1.4
	 **/	
	function getHeaderName() {
		return $this->headers[$this->header];
	} // end getHeaderName()
	
	// Generic get functions
	function getLimit() { return $this->_frontLimit; }
	function getLimitStart() { return $this->_frontLimitStart; }

	/**
	 * Load Input From Post Request or cookie
	 *
	 * @access private
	 * @since 1.3
	 * @return Int Count of the number of inputs received
	 **/
	function getInputFromRequest() {
		// Get the query info
		$inputcookie = _DBQ_COMPONENT_NAME.'-'.$this->id;
		$firsthit = false;

		// Get configuration information
		$defaultLimit		= $this->getDefaultLimit();
		$cookieDomain		= $this->getCookieDomain();
		$cookieExpiration	= $this->getCookieExpiration();
		//echo "dL is $defaultLimit, cD is $cookieDomain, cE is $cookieExpiration<br/>";

		// Get Input from PHP POST (preferred) and FILES variables
		// Accept input from the URL if enabled and if we are not coming from a previous query
		$everything	= $this->getParamValue('GET_INPUT_FROM_REQUEST');
		$previous	= mosGetParam($_GET, 'previousQuery');
		$source		= ( $everything && ! $previous ) ? $_REQUEST : $_POST;
		$input		= array();

		// Look at the input source for potential input
		foreach ($this->getListOfInputNames() as $key) {
			// If input has been sent the variable
			//echo "checking key '$key' against source '$source'<br/>'";
			if ( array_key_exists($key, $source) )
				$input[$key] = $source[$key];
		} // End of foreach($vars)
		
		// Try to find out input
		if (count($input)) {
			// We recieved input from the user
			//echo "got input from the user<br/>";
			$firsthit = true;
			setcookie($inputcookie, serialize($input), time() + $cookieExpiration, $cookieDomain,"",true);
			$limit = $defaultLimit;
			$limitstart = 0;
			if ($this->debug())
				$this->debugWrite('Obtaining input from ' . ($everything ? 'Request' : 'Post') );

		} elseif (@ $input = unserialize(stripslashes($_COOKIE[$inputcookie]))) {
			// input from a cookie set previously
			$limit = mosGetParam($_REQUEST, 'limit', $defaultLimit);
			$limitstart = mosGetParam($_REQUEST, 'limitstart', 0);
			if ($this->debug()) 
				$this->debugWrite("Input received from cookie '$inputcookie'");
			//echo "unserialized is " . unserialize(stripslashes($_COOKIE[$inputcookie])) . "<br/>";
		} else {
			//echo "got input from unknown source<br/>";
			// We recieved post information
			$firsthit = true;
			setcookie($inputcookie, serialize($input), time() + $cookieExpiration, $cookieDomain,"",true);
			$limit = $defaultLimit;
			$limitstart = 0;
			if ($this->debug()) 
				$this->debugWrite("Input received from unknown source");
		}
		
		// Store any file names as input as well
		if (isset($_FILES) && is_array($_FILES) && ! empty($_FILES) ) {
			foreach ($_FILES as $key => $value) $input[$key]=$value['name'];
		}
		
		$this->_frontFirstHit = $firsthit;
		$this->_frontLimit = $limit;
		$this->_frontLimitStart = $limitstart;
		$this->_frontInput =& $input;
		return count($input);
	} // end getInputFromRequest()

	/**
	 * Determines what output format has been requested by the user
	 *
	 * @return string Output Format
	 * @access public
	 * @since 1.4
	 **/
	function getOutputFormat() {
		if ( isset($GLOBALS['DBQ_OUTPUT'] ))
			return $GLOBALS['DBQ_OUTPUT'];
		return 'XHTML';
	} // end getOutputFormat()

	// Placeholder function
	function getConfigProValue() {
		return false;
	} // end getConfigProValue()
	
	/**
	 * Get the template file for a specified action
	 *
	 * @access public
	 * @param string $filetype Type of action that the template file handles
	 * @return string Filename for the requested template
	 */
	function getTemplateFile($filetype, $templatedir=NULL) {
		if (!$filetype)
			return false;
			
		// Use the specified template dir if specified
		if ( ! $templatedir) 
			$templatedir = $this->getTemplateDirectory();
		
		// Choose which file to load
		$file = $this->_settings->getTemplateFile($filetype);
		return $templatedir.'/'.$file;
	} // end getTemplateFile()


	/**
	 * Get the header for the current column
	 *
	 * @return string Header that should be displayed
	 * @access pubic
	 * @since 1.4
	 */
	function header() {

		// Field variables are enabled and a variable for this header exists
		$name = $this->headers[$this->header];

		// If no field object exists, then return the simple name
		if ( ! array_key_exists($name, $this->fields) )
			 return $name;

		// Build the code to make the header
		$field = $this->fields[$name];
		$displayName =& $field->getDisplayName();

		// Check if the query has enabled the display of field descriptions
		if ( $this->supportsXHTMLOutput() && $this->getConfigProValue('SHOW_FIELD_DESCRIPTION') ) {
			$description = $field->getDescription();
			$displayName = '<span '.$this->makeLibOver($description)." >$displayName</span>";
		}

		return $displayName;
	} // end printHeader()
	
	/**
	 * Record a hit and update variable stats
	 *
	 * @return ???
	 * @access private
	 * @since 1.3
	 **/
	function hit() {
		if ($this->stats) 
			$this->statInput();
			
		return parent::hit();
	} // end hit()

	/**
	 * Include the requested file
	 *
	 * @param string $type The type of template file to be used
	 * @param string $dir The template directory
	 * @return boolan True if the file was successfully included
	 */
	function includeTemplateFile($type, $dir=null) {
		$template = $this->getTemplateFile($type, $dir);	
		// Optionally write debug info
		if ($this->debug())
			$this->debugWrite("Template for $type is $template");
		return $this->includeFile($template);
	} // end includeTemplateFile
	
	/**
	 * Intialize various variables so that the results are displayed correctly
	 *
	 * @return boolean True if everything initializes
	 * @access public
	 * @since 1.4
	 */
	function initializeDisplay() {
		// ARRAYASSIGN
		//$this->results = $this->queryGetResults();
		$this->results = $this->queryGetResults();
		
		// Get field names
		if (! isset($this->fields )) 
			$this->fields =& $this->getFieldVariables();
			
		// Determine the max row and column values
		// TODO: duplicated ?
		if ( ! count($this->results) )
			return $this->logApplicationError("Cannot initialize display because there are no results for query '$this->name' ($this->id)");

		$this->row_max = count($this->results) -1;
		$this->column_max = count($this->results[0]) -1;
		$this->header_max = $this->column_max;

		// Do not use the ordering from template variables
		$i = 0;
		foreach ( array_keys($this->results[0]) as $header)
			$this->headers[$i++] = $header;

		
		// Useful debugging code	
		//echo "max rows is $this->row_max<br/>";
		//echo "max columns is $this->column_max<br/>";
		//echo '<pre>';
		//print_r($this->fields);
		//print_r($this->headers);
		//print_r($this->results);
		//echo '</pre>';
		
		return true;
	} // end initializeDisplay()

	/**
	 * Indicates if input has been registered by registerInput()
	 *
	 * @return boolean True if input has been registered by the frontend class
	 * @access public
	 * @since 1.4
	 */
	function inputHasBeenRegistered() {
		return $this->input_registered;
	} // end inputHasBeenRegistered()
		
	/**
	 * Indicates if this is the first time that the query has been run successfully
	 *
	 * @access public
	 * @since 1.3
	 * @return Boolean True if this is the first hit
	 **/
	function isFirstHit() {
		return $this->_frontFirstHit;
	} // end isFirstHit()
	

	/**
	 * Indicates that the running code is not DBQ Professional
	 * 
	 * The professional version contains different code
	 *
	 * @return boolean false
	 * @access public
	 * @since 1.3
	 */
	function isProfessionalVersion() {
		return false;
	} // end isProfessionalVersion

	/**
	 * Print a the CSS File for the template
	 * 
	 * return boolean True
	 * @access public
	 * @since 1.2
	 * @return boolean True if the file exists
	 */
	function loadCSSFile() {
		global $mainframe;
		$templateUrl = $this->getTemplateUrl();
		$file = 'template.css';
		$mainframe->addCustomHeadTag( '<link type="text/css" rel="stylesheet" href="'.$templateUrl.$file.'"/>' );
		return true;
	} // end loadCSSFile()
		
	/**
	 * Load the Custom Code file
	 *
	 * Using the custom code file depends on whether it is enabled by the query, and if the provided filename exists
	 * @return boolean True if the custom code file had been loaded
	 * @access public
	 * @since 1.2
	 */
	function loadCustomCodeFile() {
		if ( ! $this->getConfigValue('CUSTOM_CODE_ENABLED'))
			return false;
			
		// Determine file locations
		$templatedir = $this->getTemplateDirectory();
		$file = $this->getParamValue('CUSTOM_CODE_FILE');
		return $this->includeFile($templatedir . $file);
	} // end loadCustomCodeFile()
	
	/**
	 * Load the template's javascript file
	 *
	 * @return boolean True if the template file was loaded successfully
	 * @access public
	 * @since 1.4
	 */
	function loadTemplateJavaScript() {
		global $mainframe;
		$templateUrl = $this->getTemplateUrl();
		$mainframe->addCustomHeadTag( '<script type="text/javascript" src="'.$templateUrl.'/js/wforms.js" ></script>' );
		$mainframe->addCustomHeadTag( '<script type="text/javascript" src="'.$templateUrl.'/js/dbq.js" ></script>' );
		$mainframe->addCustomHeadTag( '<script type="text/javascript" src="'.$templateUrl.'/js/sorttable.js" ></script>' );
		return true;
	  	//return $this->includeTemplateFile('JAVASCRIPT_FILE');
	} // end loadTemplateJavaScript()
	/**
	 * Load the template language file
	 *
	 * The language file loaded depends on the query load.  If the template
	 *  attribute is set, then the language file for this template is loaded.  
	 *  Otherwise, the default template and its language file is used.
	 *
	 * The specific language is determined by the variable $mosConfig_lang.  
	 *  The default language is 'english'. 
	 *
	 * @global string $mosConfig_lang
	 * @global string $mosConfig_absolute_path
	 * @return boolean True if a language file was loaded
	 * @access public
	 * @since 1.2
	 */
	function loadTemplateLanguageFile() {

		// Determine file locations
		global $mosConfig_lang, $mosConfig_absolute_path;
		$templateDir = $this->getTemplateDirectory();
		$languagePath = $templateDir.'/language/';
		$i = $this->getConfigValue('TEMPLATE_LANGUAGE_FILE_ID');

		// Try to include the file
		$files[] = "${languagePath}$mosConfig_lang.$i.php";
		// Load the english file in case some strings are not translated
		if ( $mosConfig_lang !== 'english' ) 
		  $files[] = "${languagePath}english.1.php";

		foreach ($files as $file) {
			
			if ( $this->debug() )
				$this->debugWrite("Trying to load the language file '$file'");
			
			if ( ! $this->includeFile($file) )
				$this->logUserError("A language file cannot be loaded for '$this->name' ($this->id) when the language is '$mosConfig_lang'");
		}
		
		return true;
	} // end loadTemplateLanguageFile()


	/**
	 * Load the theme for a form
	 *
	 * @return boolean True if the file was loaded
	 * @access public
	 * @since 1.4
	 */
	function loadTemplateTheme() {
		global $mainframe, $mosConfig_live_site;
		$theme = $this->getConfigValue('DEFAULT_QUERY_THEME');

		$mainframe->addCustomHeadTag( '<link rel="stylesheet" type="text/css" media="all" href="'.$mosConfig_live_site.'/components/com_dbquery/themes/'.$theme.'" id="dbq-form-theme-style-sheet" />' );
		return true;
	} // end printThemeFile()

	/**
	 * Generate a url to the next screen to process a DBQ request
	 *
	 * @param object $query Optional DBQ query object
	 * @return String url to next task
	 * @access public
	 * @since 1.3
	 **/
	function makeLinkToNextTask($query = NULL) {
		// Default is to use the current object, but get info from the argument if provided
		if ( is_null($query) )
			$query =& $this;
		$vars = $query->usesInputForms();

		$args = array ('task' => $this->nextTask($vars), 'qid' => $query->id);
		return $this->dbq_url($args);
	} // end makeLinkToNextTask()

	/**
	 * Move to the next column
	 * 
	 * This function will reset all relevant counters when the end is reached
	 *
	 * @return boolean True if another column exists, False if there are no more columns
	 * @access public
	 * @since 1.4
	 */
	function nextColumn() {
		// Begin and continue
		if ( is_null($this->column) ) {
			$this->column = -1;

		}
		
		// Reset and stop
		if ( $this->column >= $this->column_max ) {
			$this->column = -1;
			return false;
		}
		
		// Increment and continue, but first check if we have permission to print the next column
		$this->column += 1;
		return $this->canDisplayNextColumn() ? true : $this->nextColumn();
	} // end nextColumn()
	
	/**
	 * Move to the next header
	 * 
	 * This function will reset all relevant counters when the end is reached
	 *
	 * @return boolean True if another header exists, False if there are no more headers
	 * @access public
	 * @since 1.4
	 */
	function nextHeader() {
		
		// Begin and continue
		if ( is_null($this->header) ) {
			$this->header = -1;

		}
		
		// Reset and stop
		if ( $this->header >= $this->header_max ) {
			$this->header = -1;
			return false;
		}
		
		// Increment and continue
		$this->header += 1;
		return $this->canDisplayNextHeader() ? true : $this->nextHeader();
	} // end nextRow()
	
	/**
	 * Move to the next row
	 * 
	 * This function will reset all relevant counters when the end is reached
	 *
	 * @return boolean True if another row exists, False if there are no more rows
	 * @access public
	 * @since 1.4
	 */
	function nextRow() {
		
		// Begin and continue
		if ( is_null($this->row) ) {
			$this->row = 0;
			return true;
		}
		
		// Reset and stop
		if ( $this->row >= $this->row_max ) {
			$this->row = -1;
			return false;
		}
		
		// Increment and continue
		$this->row += 1;
		return true;
	} // end nextRow()
	
	
	/**
	 * Determine what is the next task to be performed
	 *
	 * @param Boolean $users If the query uses variables
	 * @return String next task
	 * @access public
	 * @since 1.3
	 */
	function nextTask($uses = NULL) { 

		$uses_vars = (! is_null($uses)) ? $uses : $this->uses_vars;
		
		global $task;
		
		// Determine the next task
		$next = NULL;
		switch ($task) {
			default:
			case 'SelectQuery':
			    $next = $uses_vars ? 'PrepareQuery' : 'ExecuteQuery';
				break;
			case 'PrepareQuery':
				$next = 'ExecuteQuery';
				break;
			case 'ConfirmQuery':
				$next = 'ExecuteQuery';
				break;
		}
		return $next;
	} // end nextTask()

	/**
	 * Indicate whether or not notifications should be sent to the specified users
	 * 
	 * @return Boolean True if notifications should be sent
	 * @access protected
	 * @since 1.3
	 */
	function notificationsShouldBeSent() {
		false;
	} // end notificationsShouldBeSent()
			
	/**
	 * Create a form for users to enter data
	 * 
	 * @return Boolean True if the query was loaded and the form created
	 * @access public
	 * @since 1.0
	 */
	function prepareQuery() {

		// Optionally register previous input, eg, the user has used the return link ('previousTask' is set)
		if ( $this->shouldRegisterPreviousInput() )
				$this->getInputFromRequest() && $this->registerInput();
		
		// Optionally load data from the database if DBQ Pro has been installed
		if ( $this->isProfessionalVersion()) 
			$this->preloadFormData();
		
		// Load the template
		return $this->includeTemplateFile('PREPARE_FILE');
	} // end of prepareQuery()

	/**
	 * Register input recieved from the user
	 *
	 * @return Boolean True if input was registered
	 * @access protected
	 * @since 1.3
	 **/
	function registerInput() {
		
		// Do not proceed if there is no input to register
		if ( ! count($this->_frontInput) ) 
			return false;

		// Register the input into the query
		$input =& $this->_frontInput;
		$this->input_registered = parent::registerInput($input);
		
		return true;
	} // end registerInput()
	
	/**
	 * Determine if the display of fields should be rotated
	 *
	 * @return boolean True if rotation is requested
	 * @access public
	 * @since 1.4
	 */
	function resultsAreRotated() {
		return $this->rotate_results;
	} // end resultsAreRotated()
	
	/**
	 * Present to the user a list of queries, optionally restricted by the specified
	 *  category and using either the default or a specified template
	 * 
	 * @param String $category Name of a category of queries to list
	 * @param String $templatedir Name of the specific template directory to load from
	 * @return Boolean True If queries exist to load and the template could be loaded
	 * @access public
	 * @since 1.0
	 */
	function selectQuery($category=NULL, $templatedir=NULL) {
		
		// Retrieve rows, return false if no queries are available
		$queries =& $this->listAvailableQueries($category);
		if (!count($queries)) {
		  $this->writeConsoleMessage(_LANG_TEMPLATE_NO_QUERIES_AVAILABLE);
		  return false;
		} 
		$this->queries =& $queries;

		// include the template
		return $this->includeTemplateFile('SELECT_FILE', $templatedir);
	} // end selectQuery()

	/**
	 * Set the Append Thingy
	 * 
	 * @param string $aop String used to concatenate values in a url
	 * @return String The append string
	 * @since 1.1
	 * @access public
	 */
	function setAOP($aop) { 
		$this->aop = $aop;
		return $this->aop;
	} // end setAOP()

	/**
	 * Set the user's GID
	 *
	 * @param integer $gid
	 * @return True
	 * @access public
	 * @since 1.0
	 */
	function setGID($gid) {
		$this->_gid = $gid;
		return true;
	} // end setGID
	
	/**
	 * Set the source
	 * 
	 * @param string $source Program that is calling us
	 * @since 1.1
	 * @access public
	 */
	function setSource($source) { $this->source = $source; }

	
	/**
	 * Indicates if previous input should be registered into the query.
	 * 
	 * This function is useful for checking if we are returning to the form and need to
	 *  populate the form with data that the user has given already.
	 *
	 * Conditions are:
	 *  1) previousTask: There was a previous task which is now returning us to the form
	 *  2) input not already registered: In some cases, the $dbq object will have registered input already
	 *
	 * return boolean True if input should be registered
	 * @access private
	 * @since 1.4
	 */
	function shouldRegisterPreviousInput() {
		return  ( mosGetParam($_GET, 'previousTask') && ! $this->inputHasBeenRegistered() ) ;
		// && $this->getConfigValue('SHOW_RETURN_LINK')
	} // end shouldRegisterPreviousInput()

	/**
	 * Create a link that returns the user to the previous query
	 *
	 * @access public
	 * @since 1.4
	 */
	function showReturnLink() {
		if ($this->getConfigValue('SHOW_RETURN_LINK')) 
			$this->includeTemplateFile('RETURN_LINK_FILE');		
	} // showBackLink()
	
	/**
	 * Display the navigation bar
	 * 
	 * @access public
	 * @since 1.3
	 */
	function showPageNavigation() {
		if ($this->page_navigation) 
			$this->includeTemplateFile('PAGE_NAVIGATION_FILE');
	} // end showPageNavigation()
	
	/**
	 * Make the code for Professional features that apply specifically to popups
	 *
	 * @access public
	 * @since 1.4
	 */
	function showPopUpFeatures() {
		return true;
	} // end showPopUpFeatures()
	
	/**
	 * Display the print page
	 * 
	 * @access public
	 * @since 1.2
	 */
	function showPrintPage() {
		if ( $this->getConfigValue('SHOW_PRINT_PAGE') && ! $this->sourceIsModule())
			$this->includeTemplateFile('PRINT_PAGE_FILE');
	} // end showPrintPage()

	/**
	 * Load the template file for display results
	 * 
	 * The exact template file depends on the interface type.  Database results
	 * require different handling than the results of a xml query
	 * 
	 * @access public
	 * @since 1.3
	 */
	function showResults() {
			
		// Determine which file to load
		$interface = $this->interfaceType();
		$output = $this->getOutputFormat();
		$file = "DisplayResults.${interface}2${output}.html.php";
		$template = $this->getTemplateDirectory().$file;
		
		// Optionally write debug info
		if ($this->debug())
			$this->debugWrite("Template for displaying results is $template)");
		
		return $this->includeFile($template);
	} // end showResults()
	
	/**
	 * Determine if our source is the admin interface
	 * 
	 * @return boolean True if the source is the admin interface, otherwise false
	 * @since 1.1
	 * @access public
	 */
	function sourceIsAdmin() {
		if ( !isset($this->source)) return null;
		return $this->source == _DBQ_SOURCE_ADMIN;
	} // end sourceIsAdmin()

	/**
	 * Determine if our source is the user component interface
	 * 
	 * @return boolean True if the source is the user interface, otherwise false
	 * @since 1.1
	 * @access public
	 */
	function sourceIsComponent() {
		if ( !isset($this->source)) return null;
		return $this->source == _DBQ_SOURCE_COMPONENT;
	} // end sourceIsComponent()
	
	/**
	 * Determine if our source is the DBQ module
	 * 
	 * @return boolean True if the source is the DBQ Module, otherwise false
	 * @since 1.1
	 * @access public
	 */
	function sourceIsModule() {
		if ( !isset($this->source)) return null;
		return $this->source == _DBQ_SOURCE_MODULE;
	} // end sourceIsModule()
	
	/**
	 * Record user input into the database
	 *
	 * statInput() iterates over valid user input and 
	 *  records the input in the database for later statistical analysis
	 *
	 * @access private
	 * @since 1.0
	 * @return status of the database call
	 */
	function statInput() {
		
		// Cycle through all user input, ignoring empty queries
		$sqlarray = array ();
		foreach ($this->listVariablesByName() as $name => $variable) {

			
			// continue to the next input if we are not stating this variables
			if ( ! $variable->stats ) continue;
			if ( ! $variable->inputSuccessfullyRegistered() ) continue;
			//echo "submitting stats<br/>";

			$sqlarray[] .= $this->id.','.$variable->id.', \''.$variable->getReplacementValue().'\', now() ';

		}

		// Perform the insert if needed
		if (count($sqlarray) == 0)
			return true;
		$sql = 'INSERT INTO '._DBQ_STATS_TABLE.' VALUES (';
		$sql .= implode('), ( ', $sqlarray);
		$sql .= ')';

		$this->_db->setQuery($sql);
		return $this->_db->query();
	} // end statInput()

	/**
	 * Determines if we should print extra messages, including debugging information
	 *
	 * Added to support XML capabilities
	 *
	 * @return boolean True if we can print extra messages
	 * @access public
	 * @since 1.4
	 */
	function supportsXHTMLOutput() {
		return $this->supportsOutputOfType('XHTML');
	} // end supportsXHTMLOutput()

	function supportsXMLOutput() {
		return $this->supportsOutputOfType('XML');
	} // end supportsXMLOutput()

	function supportsOutputOfType($type) {
		if ( isset($GLOBALS['DBQ_OUTPUT']) && $GLOBALS['DBQ_OUTPUT'] != $type )
			return false;

		// default
		return true;
	} // end supportsOutputOfType()

	/**
	 * Indicate if this current script is a popup.
	 * 
	 * This check is performed by checking the name of the server script.
	 * If the name is index2.php, then the current window should be in a popup.
	 * 
	 * @since 1.1
	 * @return boolean True if the current page should be a popup
	 * @access public
	 */
	function windowIsIndex2() {
	  return (basename($_SERVER['SCRIPT_NAME']) == 'index2.php') ? true : false;
	}
}
?>
