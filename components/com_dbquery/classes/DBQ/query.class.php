<?php


/**
 * @package DBQ
 * @subpackage Query
 */

// Prohibit direct access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
//DBQ_Settings::includeClassFileForType('DBQ_common');
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/common.class.php');
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/variable.class.php');

/**
 * Represents queries stored in the database in the mos_dbquery table
 *
 * @subpackage DBQ_query DBQ Query Class
 */
class DBQ_query extends DBQ_common {

	/**#@+
	 * @access public
	 */
	var $id = NULL;
	var $name = NULL;
	var $display_name = NULL;
	var $db_id = NULL;
	var $sql = NULL;
	var $catid = NULL;
	var $template = NULL;
	var $expect_rows = NULL;
	var $category = NULL;
	var $page_navigation = NULL;
	var $rotate_results = NULL;
	var $display_regex;
	var $stats = NULL;
	var $count_sql = NULL;
	var $access = NULL;
	var $published = NULL;
	var $ordering = NULL;
	var $archived = NULL;
	var $debug = NULL;
	var $uses_vars = NULL;
	var $hits = NULL;
	var $description = NULL;
	var $comment = NULL;
	var $checked_out = NULL;
	var $checked_out_time = NULL;
	var $parse = NULL;
	var $contacts_id = NULL;
	var $params = NULL;
	var $use_confirmation = 0;
	var $use_notification = 0;
	var $notification_emails = NULL;
	var $notification_subject = NULL;
	var $notification_message = NULL;
	// var $notifications = NULL; // some stuff about how to notify ppl, e-mail for example 
	// var $display_options = NULL; // pop-up, inline, etc
	// var $enable_print = NULL; // optional popup for printing
	/**#@-*/

	/**#@+
	 * @access private
	 *
	 * Needed for admin display
	 */
	var $editor = NULL;
	var $category_access = NULL;
	var $parent = NULL;
	var $groupname = NULL;
	/**#@-*/

	/**#@+
	 * @access private
	 */
	var $_parse = array ();
	var $_params = NULL;
	var $_params_txt = NULL;
	var $_query_variables_by_order = NULL;
	var $_query_variables_by_id = NULL;
	var $_query_variables_by_name = NULL;
	// Input rejected during registerVarInput()
	// Results from a query execution
	var $_results = array();
	// SQL based on $sql, but with variables substituted
	var $_sql = NULL;
	var $_state = 'E';
	var $_states = array (
		'E' => 0, // Empty: sql is Empty
		'P' => 1, // Parsed: sql is parsed into PARSE
		'L' => 2, // Loaded: variables are Loaded
		'R' => 3, // Registered: input is in INPUT
		'I' => 4, // Interpolated: _query is created
		'C' => 5, // Connected to remote database
		'X' => 10 // Query has been exectued already
	);


	/**#@-*/

	/**
	 * Constructor class for the DBQ Query class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_query($dummy = false) {
		return $this->DBQ_common(_DBQ_QUERIES_TABLE, $dummy);
	} // end DBQ_query()

	/**
	 * Determine if access to a query if permitted given a query id
	 *
	 * @param integer $qid The query id which will be examined
	 * @param string $source Option text indicating the source of this request
	 * @return Boolean True if access should be permitted
	 * @access public
	 * @since 1.0
	 */
	function accessToQueryPermitted($qid = 0, $source = NULL) {
		$gid = $this->_gid;
		if (!isset ($gid) || !$qid)
			return false; // We need an ID to check
		$sql = 'SELECT COUNT(*) AS COUNT FROM '._DBQ_QUERIES_TABLE.' q '.' LEFT JOIN '._DBQ_CATEGORIES_TABLE.' c ON c.id = q.catid '.' LEFT JOIN '._DBQ_DATABASES_TABLE.' d ON d.id = q.db_id '.' WHERE q.id = '.$qid.' AND q.access <= '.$gid.' AND c.access <= '.$gid;
		// Apply some rules only to sources other than the Admin screens
		if ($source != _DBQ_SOURCE_ADMIN)
			$sql .= ' AND d.on_line = 1 AND q.published = 1 AND q.archived = 0';
		$this->_db->setQuery($sql);
		$row = $this->_db->loadObjectList();
		if ($row[0]->COUNT >= 1) {
			return true;
		} else {
			return false;
		}
	} // end accessToQueryPermitted()

	/**
	 * Check if all required input is registered
	 *
	 * @access public
	 * @since 1.0
	 * @return boolean True if all required input has been registered; otherwise, false
	 */
	function allRequiredInputIsRegistered() {

		if (!$this->checkState('P'))
			return $this->logApplicationError("Unready State in allRequiredInputIsRegistered()");

		// Return true if we don't have any variables
		if ( ! $this->uses_vars ) {
			$this->_state = 'R'; 
			return true;
		}

		// Get a list of variables for this query
		// ARRAYASSIGN
		$variables = $this->getVariablesForQuery();
		$variableNames = $this->getListofInputNames();


		if ( ! count($variables) )
			return $this->logUserError("No variables defined for query '$this->name' ($this->id)");

		// Iterate through the variable list and test for bad input
		$problems = array();
		foreach ($variableNames as $name) {

			// Check if the variable exists
			if ( ! array_key_exists($name, $variables) ) 
				return $this->logApplicationError("Query '$this->name' ($this->id) uses a variable named '$name' but the variable does not exist or is not published");
			// Note problems if var is required but input is not successfully registered
			// or if var contains invalid input
			$variable =& $variables[$name];
			//if ($variable->hasProblemsWithInput() || ( $this->variableIsEffectivelyRequired($variable) && ! $variable->inputSuccessfullyRegistered()) ) {
			if ($variable->hasProblemsWithInput() || ( $variable->isRequiredWithinContext() && ! $variable->inputSuccessfullyRegistered()) ) {
			  $problems[] = $name;
			  $variable->requiresUserAttention(true);
			}
		}

		// Return true if all required vars have input
		if ( empty($problems) ) {
			$this->_state = 'R';
			return true;
		}
		return false;
	} // end allRequiredInputIsRegistered()


	/**
	 * Build a list of variables used in a query.
	 *
	 * The variables are sorted in a way such that all keyword and list
	 *  which are located in a statement follow that statement.  Otherwise,
	 *  the order specified in the 'ordering' is abided by.
	 *
	 * @param Boolean $hard Build list of variables for initialization
	 * @access public
	 * @since 1.0
	 * @return array All variables used by a query
	 */
	function buildDisplayOfVariables($hard=false) {

		// Retrieve existing elements
		$variableNames = & $this->getListOfAllVariableNames();
		$inputNames = & $this->getListOfInputNames();
		$statementNames = & $this->getListOfStatementNames();
		$fieldNames = & $this->getListOfFieldNames();
		$varInStmtNames = & $this->getListOfVarsInStatements();
		$display = array ();

		if ($this->debug()) {
			$this->debugDump("Vars: ");
			$this->debugDump($variableNames);
			$this->debugDump("Input Variables: ");
			$this->debugDump($inputNames);
			$this->debugDump("Statement Variables: ");
			$this->debugDump($statementNames);
			$this->debugDump("Field Variables: ");
			$this->debugDump($fieldNames);
			$this->debugDump("Variables in Statements: ");
			$this->debugDump($varInStmtNames);
		} // end debug

		// If there are no variables, then just return an empty array
		if ( ! count($variableNames) ) 
			return $display;

		/**
		 * Build an array of variables that will constitute the display
		 * 1) Retrieve existing variables from the db
		 * 2) Create a lookup index
		 * 3) Add a dbvars $rows if the variable name has been found in the query via $varMatches
		 * 4) Add a row for any vars found in the query but not yet in the db
		 */

		// Get Variables
		// ARRAYASSIGN
		$existingVariables = $this->getVariablesForQueryByID(true);

		// Create Index
		$index = array ();
		if ( count($existingVariables) ) {
			//foreach ($dbrows as $key => $dbrow) {
			foreach ( array_keys($existingVariables) as $key) {
				//echo "key is $key<br/>";
				
				// If a variable exists in the database but not in the current query, ignore the variable
				if ( ! in_array($key, $variableNames) )
					continue;

				$rows[$key] =& $existingVariables[$key];
				$index[$existingVariables[$key]->id] = $key;
			} // end foreach
		}

		// Create empty rows for new variables
		$fakeid = '';
		foreach ($variableNames as $name) {
			// If the variable exists already, we do not need to create an object
			if (in_array($name, $index))
				continue;
				
			// Create a name and fake id for the object.
			if ( $this->debug() )
				echo "making a new variable for $name<br/>";
				
			$newvar = new DBQ_variable(true);
			$newvar->name = $name;
			$newvar->display_name = $this->displayName($name);
			$newvar->id = ($fakeid .= '0');
			
			// Give this variable a type based on the syntax found in the query
			if (in_array($name, $inputNames)) {
				// default type
				$newvar->type = 'keyword';
			} elseif ( in_array($name, $statementNames))  {
				$newvar->type = 'statement';
			} elseif ( in_array($name, $fieldNames))  {
				$newvar->type = 'field';
			} else {
				// A rouge variable ?  How did he get here
				$this->logApplicationError("While building a display list of variables for query '$this->name' ($this->id), the variable name '$name' was found but it's type cannot be identified");
			}
			
			// Add the variable to the list
			$rows[$name] = $newvar;
			$index["$fakeid"] = $name; // prevent duplicates
		}
		
		// Get previous input from POST, if it exists
		$cid = mosGetParam($_POST, 'cid');
		if ( $cid && is_array($cid)) {
			foreach (array_keys($cid) as $id) {
				// Check if this input is a POST
				if ( ! is_array($cid[$id]) )
					continue;
				//echo "array_key_exists($id, $index) && (cid[$id]['name'] == index[$id] <br/>";
				if (array_key_exists($id, $index) && ($cid[$id]['name'] == $index[$id]) ) {
					$obj =& $rows[$index[$id]];
					$obj->bind($cid[$id]);
				}
			}
		}
		
		// Do some processing so that we can show where things come from
		foreach ($variableNames as $name) {
			//echo "ROW: $row->name<BR>";
			$idx = array_search($name, $variableNames);
			$rows[$name]->_origregex = $this->_parse['ALL']['REGEX'][$idx];
			if (is_array($varInStmtNames) && array_key_exists($name, $varInStmtNames)) 
				$rows[$name]->_instatement = $varInStmtNames[$name];
		}
		
		if (false && $this->debug()) {
			foreach (array_keys($variableNames) as $name)
				echo "Name for '$name'<br/>";			
			foreach (array_keys($rows) as $name)
				echo "Variable for '$name'<br/>";
		}
		// Return the results
		return $rows;
/*
		while ($row = array_shift($normalForms)) {
			//if ($row->type == _DBQ_STMT_CODE) {
			//echo "Row is $row->name, $row->input, $row->id<br/>";			

			if ($row->isStatement()) {
				//echo "Statement row is $row->name<br/>";
				// Search for variables contained in the statement
				if (in_array($row->name, $varInStmtMatches)) {
					foreach (array_reverse($subForms) as $sf) {
						//echo "SF $sf->name == $row->name<br/>";
						if ($varInStmtMatches[$sf->name] == $row->name) {
							//if ( @ $row->required == _CODE_YES ) $sf->_parent_stmt_required = true;
							array_unshift($normalForms, $sf);
						}
					}
				} // end in_array()
				$display[] = $row;
				
			} elseif ( $row->isKeyword() || $row->isList() || $row->isCustom() ) {
				// Check if the variable is supported
				// TODO check valid input type
				$display[] = $row;
			} else {
				$this->logApplicationError("Type '$row->type' of variable '$row->name' ($row->id) is not supported.");
			}
		}
		return $display;
*/
	} // end buildDisplayOfVariables()

	/**
	 * Check the state of the query object
	 *
	 * @param string $state State to check against
	 * @return boolean True if a the object is currently in the given state or greater
	 * @since 1.0
	 */
	function checkState($state) {
		//echo "$state to $this->_state : " . $this->_states[$state]. "<=". $this->_states[$this->_state] . "<BR>";
		return $this->_states[$state] <= $this->_states[$this->_state];
	} // end checkState



	/**
	 * Idicates if Regular Expressions should always be displayed in the Prepare Query User Screen
	 *
	 * @return boolean True if Regular Expressions should always be displayed
	 * @access public
	 * @since 1.0
	 */
	function displayRegexNormally() {
		return ($this->getDisplayRegex($this->display_regex) > 0) ? true : false;
	} // end displayRegexNormally()

	/**
	 * Idicates if Regular Expressions should be displayed in the Prepare Query User Screen on errors

	 * @return boolean True if Regular Expressions should be displayed on errors
	 * @access public
	 * @since 1.0
	 */
	function displayRegexOnError() {
		return ($this->getDisplayRegex($this->display_regex) < 0) ? true : false;
	} // end displayRegexOnError()

	/**
	 * Returns the cookie domain to use for the query
	 * 
	 * @return Integer Domain for the cookie
	 * @access public
	 * @since 1.2
	 */
	function getCookieDomain() {
		$value = $this->getConfigValue('COOKIE_DOMAIN');
		return $value ? $value : '/';
	} // end getCookieDomain()
	
	/**
	 * Returns the cookie expiration time to use for the query
	 * 
	 * @return Integer Number of seconds the a cookie should live
	 * @access public
	 * @since 1.2
	 */
	function getCookieExpiration() {
		$value = $this->getConfigValue('COOKIE_EXPIRATION');
		return $value ? $value : 300;
	} // end getCookieExpiration()
	
	/**
	 * Returns the default limit to use for the query
	 * 
	 * @return Integer Default limit
	 * @access public
	 * @since 1.2
	 */
	function getDefaultLimit() {
		$value = $this->getConfigValue('DEFAULT_LIMIT');
		return $value ? $value : 10;
	} // end getDefaultLimit()

	/**
	 * Get a description for the query
	 *
	 * The default description is the General Description
	 *
	 *
	 */
	function getDescription($key = NULL) {

		// Make sure that we have a key

		if ( is_null($key) )
			$key = 'GENERAL_DESCRIPTION';  
			
		$alt = $key . '_LANG_CODE';
		return $this->getDescriptionByKey($key, $alt);

		/*
		if ( $string ) 
		// Hmmm, no string at all !  Log an error and use the general description, which is hopefully defined
		  $this->logUserError("In query '$this->name' ($this->id), trying to load a description for key '$key' but no text is found.");
		return parent::getDescription('GENERAL_DESCRIPTION');
		*/
	} // end getDescription()
	
	/**
	 * Get a list of all field variables
	 *
	 * @return Array List of field variables
	 * @access public
	 * @since 1.4
	 */
	function getFieldVariables() {
		$fields = array();
		
		// Get the names of field variables
		$fieldnames = $this->getListOfFieldNames();
		if ( ! count($fieldnames) )
			return $fields;
			
		// Extract field variables
		// ARRAYASSIGN
		$variables = $this->getVariablesForQuery();
		foreach ($fieldnames as $name) {
			// Is it a field variable
			if ( array_key_exists($name, $variables) && $variables[$name]->isField() )
				$fields[$name] =& $variables[$name];
		}
		return $fields;
	} // end getFieldVariables()
	
	/**
	 * Get the GID of the user if previously set
	 * @return integer GID
	 */
	function getGID() {
		return $this->_gid;
	} // end getGID()

	/**
	 * Get the SQL code for the query
	 * @access public
	 * @deprecated
	 * @return string SQL for the query
	 */
	function getQuery() {
		return @ $this->sql;
	} // end getQuery()

	/**
	 * Return the name of a statement which hosts a variable, if any
	 * 
	 * @param string Variable Name
	 * @return string Name of Host Statement, if any
	 * @since 1.1
	 */
	function getHostStatementNameByVarName($varname) {
		return @$this->_parse['MIXED']['MAP'][$varname];
	} // end getHostStatementNameByVarName()

	/**
	 * Get a name list of all variables for the current query
	 *
	 * @return unknown
	 * @access private
	 * @since 1.0
	 */
	function getListOfAllVariableNames() {
		Return @ $this->_parse['ALL']['NAMES'];
	} // end getListOfAllVariableNames()

	/**
	 * Get a name list of all statement variables for the current query
	 *
	 * @return array Array of statement names
	 * @access private
	 * @since 1.0
	 */
	function getListOfStatementNames() {
		return @ $this->_parse['STMTS']['NAMES'];
	} // end getListOfStatementNames
	
	/**
	 * Get a name list of all template variables for the current query
	 *
	 * @return array Array of Template variables
	 * @access private
	 * @since 1.4
	 * @todo Change name to getListofTemplateNames
	 */
	function getListOfFieldNames() {
		return @ $this->_parse['TMPL']['NAMES'];
	} // end getListOfFieldNames()

	/**
	 * Get a name list of all normal variables for the current query
	 *
	 * @return unknown
	 * @access private
	 * @since 1.0
	 */
	function getListOfInputNames() {
		return @ $this->_parse['VARS']['NAMES'];
	} // end getListOfInputNames()

	/**
	 * Get a name list of all variables inside statements for the current query
	 *
	 * @return unknown
	 * @access private
	 * @since 1.0
	 */
	function getListOfVarsInStatements() {
		return @ $this->_parse['MIXED']['MAP'];
	} // end getListOfVarsInStatements()

	/**
	 * Get a Query's ID given its name
	 *
	 * @access public
	 * @param string $name Name of the query to search for
	 * @return integer Query ID, if found
	 */
	function getQIDByQueryName($name) {
		if (!$name)
			return false;
		$sql = 'SELECT id FROM '._DBQ_QUERIES_TABLE." WHERE name = '$name'";
		$this->_db->setQuery($sql);
		$r = $this->_db->loadObjectList();
		return ($r ? $r[0]->id : NULL);
	} // end getQIDByQueryName()


	/**
	 * Determine where the query's template directory is
	 * 
	 * @return string Absolute path to the query's template directory
	 * @access private
	 * @since 1.4
	 */
	function getTemplateDirectory() {
		$template = $this->template ? $this->template : $this->getConfigValue('DEFAULT_TEMPLATE');
		return $this->getTemplateBaseDir() . $template . '/';
	} // end getTemplateDirectory

	/**
	 * Determine what is the template's url
	 * 
	 * @return string Url to the query's template directory
	 * @access private
	 * @since 1.4
	 */
	function getTemplateURL() {
		
		$template = $this->template ? $this->template : $this->getConfigValue('DEFAULT_TEMPLATE');
		$templatedir = $this->getConfigValue('TEMPLATE_DIR');
		return $this->getBaseURL() . $templatedir . $template . '/';
	} // end getTemplateURL()
	
	/**
	 * Return a specific variable for the query
	 *
	 * @param string $name Name of the variable to return
	 * @return object DBQ_variable if the variable exists, otherwise false
	 * @access public
	 * @since 1.4
	 */
	function getVariable($name) {
		if ( array_key_exists($name, $this->_query_variables_by_name) )
			return $this->_query_variables_by_name[$name];
			
		return null;
	} // end getVariable()

	/**
	 * Get a list of variables for the current query
	 *
	 * This function returns an array of DBQ_Variable objects that 
	 * represents all the active variables for this query.
	 *
	 * @return array Variables sorted by order
	 * @access public
	 * @since 1.0
	 */
	function getVariablesForQuery() {
		return $this->checkState('L')  ? $this->_query_variables_by_order : $this->logApplicationError("Unready State in getVariablesForQuery()");
	} // end getVariablesForQuery

	/**
	 * Get a list of variables for a specified query
	 *
	 * This function returns data stored in the database
	 *
	 * @access public
	 * @param integer $id ID of the query for which variables are selected
	 * @return array Variables sorted by order
	 * @since 1.0
	 */
	function getVariablesForQueryByID($activeOnly = true) {
		

		if (!isset ($this->id))
			return $this->logApplicationError("Trying to load variables for query on uninitialized object");

		// If our variables are loaded, just return the array
		if ($this->checkState('L'))
			return $this->_query_variables_by_order;
		
		// Make the SQL	
		// ARRAYASSIGN
		$variableNames = $this->getListOfAllVariableNames();
		//$sql = 'SELECT * FROM '._DBQ_VARIABLES_TABLE." t WHERE query_id = $this->id ";
		$sql = 'SELECT * FROM '._DBQ_VARIABLES_TABLE.' t WHERE BINARY name IN (\'' . implode('\', \'', $variableNames) . '\') ';
		if ($activeOnly)
			$sql .= " AND active = 1 ";
		$sql .= " ORDER BY t.ordering";
		//echo "sql for getVariablesForQueryByID($activeOnly) is $sql<br/>";
		
		// Fire off the query, check for results
		$this->_db->setQuery($sql);
		// ARRAYASSIGN
		$list = $this->_db->loadAssocList();
		if ( empty($list) ) return NULL;
		
		// Use the results to create an array of variables	
		$results = array ();
		foreach ($list as $row) {
			$obj = DBQ_variable::getInstance($row);
			
			// Load list of substitution elements, if needed
			//if ( $obj->isList() )
			//	$obj->loadList();
			
			$obj->setParent($this);
			$results[$obj->name] = $obj;
		}
		return $results;
	} // end getVariablesForQueryByID()

	/**
	 * Return an array of variables that will be used in the query
	 * 
	 * To be considered usable, the variables must be active, use form input and must be present in the parse structure
	 *
	 */
	function getVariablesUsedInForm() {
		
		// Return if the query doesn't use vars
		if ( ! $this->uses_vars )
			return array();

		// ARRAYASSIGN
		$variables = $this->getVariablesForQuery();
		$names = $this->getListOfInputNames();
		$vars = array();

		//echo "calling getVariablesUsedInForm";
		//echo implode(', ', array_keys($variables));

		// Return if the parse is empty or there are not variables
		if ( ! count($names) || ! count($variables) )
			return array();
			//return $this->logApplicationError("Query '$this->name' ($this->id) claims to use variables but none are found");
		
		// Check all of the names - the order depends on the query settings
		$names = $this->getConfigValue('ORDER_AS_FOUND_IN_QUERY') ? $this->getListOfInputNames() : array_keys($variables);
		foreach ( $names as $name ) {

			if ( ! array_key_exists($name, $variables) )
				continue;

			$variable =& $variables[$name];

			// Skip if the variable doesn't use an input field, except hidden variables
			if (  ! $variable->usesInputForm() && ! $variable->isHidden())
				continue;
	
			$vars[$name] =& $variable;
		}

		return $vars;
	} // end getVariablesUsedInForm()
	
	/**
	 * Identifies the interface type
	 * 
	 * @return String Identifier for interface type
	 * @access public
	 * @since 1.3
	 */
	function interfaceType() {
		return is_object(@$this->_driver) ? $this->_driver->getInterfaceType() : NULL;
	} // end interfaceType()
	
	/**
	 * Indicates if the interface for the query is a SQL interface
	 * 
	 * @return Boolean True if the interface is a SQL interface
	 * @access public
	 * @since 1.3
	 */
	function interfaceTypeIsSQL() {
		return is_object(@$this->_driver) ? ($this->_driver->getInterfaceType() === 'SQL') : NULL;
	} // end interfaceTypeIsSQL()

	/**
	 * Indicates if the interface for the query is a DOMIT interface
	 * 
	 * @return Boolean True if the interface is a DOMIT interface
	 * @access public
	 * @since 1.3
	 */	
	function interfaceTypeIsDOMIT() {
		return is_object(@$this->_driver) ? ($this->_driver->getInterfaceType() === 'DOMIT') : NULL;
	} // end interfaceTypeIsDOMIT()
	
	/**
	 * Interpolate the previously provided input on the query
	 *
	 * @return boolean True if interpolation was successful
	 */
	function interpolateOnQuery() {
		global $dbq_class_path;
		if (!$this->checkState('R'))
			return $this->logApplicationError("Unready State in interpolateOnQuery()");

		/**
		 * Updated for DBQ 1.4
		 * 
		 * The basic idea here is to process the three basic variable types 
		 * - Statements, Inputs, and Fields - in turn.
		 * 
		 * For each, we iterate through the variables of the a certain type
		 * and perform the replacement.
		 * 
		 * Although the actual replacement is performed in the variable object,
		 * we need to pass in target and (sometimes) replacement information
		 * because variables do not know anything about the query and variable 
		 * syntax in the query.
		 */

		// Things that we will use
		// ARRAYASSIGN
		$variables = $this->getVariablesForQuery();
		$unusedStatements = array ();
		$query = $this->sql;

		// Iterate through all statements
		$stmtMatches = $this->_parse['STMTS'];
		if ( count($stmtMatches['NAMES']) ) {
			foreach ( $stmtMatches['NAMES'] as $i => $name ) {
				$replacement = '';
				if ( $this->statementIsInUse($name) ) {
					// Statement is in use
					$replacement = $stmtMatches['STUBS'][$i];
				} else {
					// Statement is not in use
					$unusedStatements[$name] = true;
				}
				$target =& $stmtMatches['REGEX'][$i];
				$variable =& $variables[$name];
				if ( ! is_object($variable) )
					return $this->logUserError("Trying to interpolate on query '$this->name' ($this->id) but the variable '$name' cannot be found");
					
				$variable->performReplacement($query, $target, $replacement);
			}
		} // end statement variables

		
		// Interate through all query variables and perform replacement
		$mixed =& $this->_parse['MIXED'];
		$varMatches =& $this->_parse['VARS'];
		if ( count($varMatches) ) {
			foreach ($varMatches['NAMES'] as $i => $name ) {

				//echo "variable is $i, $name<br/>";
				// This doesn't apply if a var is also used outside of a statement
				//if (array_key_exists(@ $mixed['MAP'][$name], $unusedStatements))
				//	continue;

				$target =&  $varMatches['REGEX'][$i];
				$variable =& $variables[$name];
				if ( ! is_object($variable) )
					return $this->logUserError("Trying to interpolate on query '$this->name' ($this->id) but the variable '$name' cannot be found");
				
				$variable->performReplacement($query, $target);
			} 
		} // end normal variables
		
		// Interate through all template variables and perform replacement
		$tmplMatches =& $this->_parse['TMPL'];
		if ( count($tmplMatches)) {
			foreach ($tmplMatches['NAMES'] as $i => $name) {
				$target =&  $tmplMatches['REGEX'][$i];
				$variable =& $variables[$name];
				if ( ! is_object($variable) )
					return $this->logUserError("Trying to interpolate on query '$this->name' ($this->id) but the variable '$name' cannot be found");
				
				$variable->performReplacement($query, $target);
			} 
		}// end template variables
	
		$this->_state = 'I';
		$this->_sql = $query;
		return true;
	} // end interpolateOnQuery()

	/**
	 * Initialize the query for use
	 * 
	 * This function will build the internal database structures used by DBQ for query processing,
	 *  notably, the parse and variable structures.
	 *
	 * @param boolean $hard Determines if the query should be parsed anew
	 * @return boolean True if the query has been initialized
	 * @access public
	 * @since 1.4
	 */
	function initialize($hard = false) {
	  return ($this->parseQuery($hard) && $this->loadVariables());
	} // end initialize()

	/**
	 * List the queries available to the user
	 *
	 * Modified in 1.3 to return a list of objects
	 * 
	 * @param string $category The category, either the name or id, by which the results are restricted
	 * @return array List of DBQ_Query objects
	 * @access public
	 * @since 1.0
	 **/
	function listAvailableQueries($category = NULL) {

		$categorySQL1 = NULL;
		$categorySQL2 = NULL;

		if ($category) {
			if (preg_match('/^[0-9]+$/', $category)) {
				// $category is an ID
				$categorySQL2 = " AND q.catid = '$category'";
			} else {
				// $category is name
				$categorySQL2 = " AND c.name = '$category'";
			}
		}

		$gid = $this->_gid;
		
		// Get a list of queries that will be displayed
		$sql = 'SELECT q.id AS id, c.description AS category_description FROM '._DBQ_QUERIES_TABLE.' q, '._DBQ_CATEGORIES_TABLE.' c, '._DBQ_DATABASES_TABLE.' d '
            .' WHERE q.catid = c.id AND q.db_id = d.id  AND q.access <= '.$gid.' AND c.access <= '.$gid
            .' AND d.on_line = 1 AND q.published = 1 AND c.published = 1'
            . ($categorySQL2 ? $categorySQL2 : '')
            .' ORDER BY q.catid,q.ordering';

		if ( $this->debug() )
			$this->debugWrite("SQL used to select available categories is '$sql'");

		$this->_db->setQuery($sql);
		$results =& $this->_db->loadAssocList();

		/*
		// Uncomment if you are having problems listing a list of queries
		echo 'showing the results now<br/><pre>';
		print_r($results);
		echo '</pre>';
		*/
		
		// Return an empty array if not results are found
		if ( ! count($results) ) {
			$this->logUserError("Trying to list queries to display using Select Query, but no queries are available using category '$category'");
			return array();
		}
		
		// Make a list of objects that we can work with
		$queries = array();
		foreach ($results as $r ) {
			$id = $r['id'];
			$obj = new DBQ_frontend();

			// Load the query
			if ( $obj->load($r['id']) && ( ! $obj->getParamValue('HIDE_ON_SELECT_SCREEN') ) && $obj->initialize() ) {
				$obj->category_description = $r['category_description'];
				$queries[] = $obj;
			}
		}
		
		return $queries;
	} // end listAvailableQueries()

	/**
	 * Obtain the list of variables for the given query, keyed by name
	 * 
	 * @return Array List of Variable Objects, keyed by name
	 * @access private
	 * @since 1.2
	 */
	function listVariablesByName() {
		return $this->_query_variables_by_name;
	} // end listVariablesByName()

	/**
	 * Load Variables from the database for a query if the query uses variables
	 *
	 * @return boolean True
	 * @access public
	 * @since 1.0
	 */
	function loadVariables() {
		// Load the variables in the query
		if ( !$this->uses_vars || isset($this->_query_variables_by_order)) {
		  // Loaded already
		  $this->_state = 'L';
		  return true;
		}
			
		// Get the variables
		// ARRAYASSIGN
		$this->_query_variables_by_order = $this->getVariablesForQueryByID();
		
		// Test if we have variables when the object says we use them
		if ( empty($this->_query_variables_by_order) ) {
			// TODO Address issue of different sources
			// Probably is an error, unless this is a new query, in which case there should be no parse data
			if ( $this->parse )
				$this->logApplicationError("Cannot load variables for query '$this->name' ($this->id");
		} else {
			// For lookup purposes, build an index based on id and name
			while (list ($key, $value) = each($this->_query_variables_by_order)) {
				//echo "key is $key, value = $value->name";
				$this->_query_variables_by_id[$value->id] = & $this->_query_variables_by_order[$key];
				$this->_query_variables_by_name[$value->name] = & $this->_query_variables_by_order[$key];
			}
		}

		// Finally, load any substiution values
		$this->_state = 'L';
		if ( $this->debug() )
		  $this->debugWrite('Loaded the following variables: '. implode(', ', array_keys($this->_query_variables_by_name)) );
		return true;
	} // end loadVariables()

	/**
	 * Return the next access level in the sequence
	 * 
	 * @access public
	 * @param int $group The integer identifying the access group
	 * @return 
	 * @since 1.1
	 */
	function nextAccessLevel($group) {
		$x = $this->_accessLevels[$group +1];
		return $x ? $x : $this->_accessLevels[0];
	}

	/**
	 * Parse the query for variables
	 * 
	 * The resulting data structure is stored in the property _parse .
	 * 
	 * Given no argument and if the property 'parse' exists, the function
	 * will unserialze the preparsed string 'parse' instead of parsing 
	 * the property 'sql'.
	 * 
	 * Two important configurations used by this function are 
	 * 'VAR_SUBSTITUTIONS' and 'STMT_SUBSTITUTIONS' which contain
	 * the regular expressions used to find variables and statements.
	 * 
	 * @param string $hard Force a parsing of the 'sql' property
	 * @return boolean true if successful 
	 * @since 1.0
	 * @access public
	 */
	function parseQuery($hard = false) {

		if (@ $this->parse && !$hard) {
			$this->_parse = unserialize($this->parse);
			$this->_state = 'P';
			//echo "Using parsed input from db<br/>";
			return true;
		}

		// First, parse Variables in the Query, store them
		//$varRegex = $this->getConfigValue('VAR_SUBSTITUTION');
		$varRegex = '/\\$\\{(.*?)\\}/';
		$varMatches = array ();
		preg_match_all($varRegex, $this->sql, $varMatches);
		$this->_parse['VARS']['REGEX'] = & $varMatches[0];
		$this->_parse['VARS']['NAMES'] = & $varMatches[1];

		// Next, parse Statements in the Query, store them
		//$stmtRegex = $this->getConfigValue('STMT_SUBSTITUTION');
		$stmtRegex = '/\\$\\[(.*?):(.*?)\\]/s';
		$stmtMatches = array ();
		preg_match_all($stmtRegex, $this->sql, $stmtMatches);
		/* Future implementation
		if ( ! empty($stmtMatches[1] )) {
			foreach ( $stmtMatches[1] as $i => $stmtname ) {
				$this->_parse['STMTS'][$stmtname] = $stmtMatches[0][$i];
				$this->_parse['STUBS'][$stmtname] = $stmtMatches[2][$i];
			}
		}
		*/

		$this->_parse['STMTS']['REGEX'] = & $stmtMatches[0];
		$this->_parse['STMTS']['NAMES'] = & $stmtMatches[1];
		$this->_parse['STMTS']['STUBS'] = & $stmtMatches[2];

		// Next, parse for template variables
		//$tmplRegex = $this->getConfigValue('TMPL_SUBSTITUTION');
		$tmplRegex = '/\\$<(.*?)>/';
		$tmplMatches = array ();
		preg_match_all($tmplRegex, $this->sql, $tmplMatches);
		$this->_parse['TMPL']['REGEX'] = & $tmplMatches[0];
		$this->_parse['TMPL']['NAMES'] = & $tmplMatches[1];
		
		// Make a master list of 
		//$this->_parse['ALL']['REGEX'] = array_merge($stmtMatches[0], array_merge($varMatches[0], $tmplMatches[0]) );
		$this->_parse['ALL']['REGEX'] = array_merge($stmtMatches[0], $varMatches[0], $tmplMatches[0] );
		//$this->_parse['ALL']['NAMES'] = array_merge($stmtMatches[1], array_merge($varMatches[1], $tmplMatches[1]) );
		$this->_parse['ALL']['NAMES'] = array_merge($stmtMatches[1], $varMatches[1], $tmplMatches[1] );

		// Finally, union for Variables located in Statements, store them
		// We do this for times when we need to specially treat Vars in Statements
		$varsInStmtMatches = array ();
		$c = count($stmtMatches[1]);
		for ($i = 0; $i < $c; $i ++) {
			$v = array ();
			preg_match_all($varRegex, $stmtMatches[2][$i], $v);
			//$match = $v[1][0]; // Contains the matched varRegex string
			$varsInStmtMatches[$stmtMatches[1][$i]] = $v;
		}
		//print_r($varsInStmtMatches); echo "<br/>";
		$this->_parse['MIXED']['REGEX'] = array ();
		$this->_parse['MIXED']['NAMES'] = array ();
		$this->_parse['MIXED']['MAP'] = array ();
		while (list ($key, $value) = each($varsInStmtMatches)) {
			/* Structure will look like 
			 *   MIXED -> REGEX -> NAME OF STMT -> Array of REGEX OF VARIABLE
			 *   MIXED -> NAMES -> NAME OF STMT -> Array of TEXT NAME OF VARIALBE
			 *   MIXED -> MAP   -> TEXT NAME OF VARIABLE -> NAME OF STMT
			 * Just looks complicated, but its not.
			 */
			//echo "k $key v "; print_r($value); echo "<BR>";
			$this->_parse['MIXED']['REGEX'][$key] = $value[0];
			$this->_parse['MIXED']['NAMES'][$key] = $value[1];
			foreach ($value[1] as $v) {
				$this->_parse['MIXED']['MAP'][$v] = $key;
			}
		}
		$this->_state = 'P';
		return true;
	} // end parseQuery()

	/**
	 * Serialize a parced query.
	 * 
	 * Serialization of a query's parse tree is one of the key optimizations 
	 *  that DBQ employs to speed up client side screens.
	 *
	 * @access private
	 * @return Boolean True if the parse structure has been serialized
	 * @since 1.0
	 */
	function parseSerialize() {
		if (@ $this->_parse) {
			$this->parse = serialize($this->_parse);
			return true;
		} else {
			return false;
		}
	}  // end parseSerialize

	/**
	 * Utility function to help register input for a query based on an input array
	 **/
	function registerInput($input) {

		$vars = $this->getVariablesUsedInForm();

		if (is_array($vars) and count($vars) ) {
			$problem = false;

			// Cycle through the variables and attempt to find input
			foreach (array_keys($vars) as $key) {
				$var =& $vars[$key];
				// If input has been submitted and qualifies for input, the register the input
				if ( array_key_exists($key, $input) && ($var->usesInputForm() || $var->isHidden()) ) {	
					if ( is_array($input[$var->name]) ) {
						// Severals input for variable may have been submitted
						foreach ( $input[$key] as $minput)
							$var->registerInput($this->cleanMagicQuotes($minput));
					} else {
						// Simple
						$var->registerInput($this->cleanMagicQuotes($input[$key]));
					}
				}
			} // End of foreach($vars)
		}

		return true;

	} // end registerInput()

	/**
	 * Set the query ID for loading
	 *
	 * Set the object's state to 'Empty'
	 *
	 * @access public
	 * @since 1.0
	 * @param integer $qid 
	 * @return boolean True
	 */
	function setQueryID($qid) {
		$this->id = $qid;
		$this->_state = 'E';
		return true;
	}

	/**
	 * Checks if a statement variable by a given name contains variables
	 *   which have been identified as Invalid Input
	 *
	 * @access public
	 * @since 1.0
	 * @param string $stmtname The statement name
	 * @return boolean True if the statement contains variables with Invalid Input
	 */
	function statementContainsInvalidInput($stmtname) {
		if (!$stmtname) 
		  return $this->logApplicationError(_LANG_PARAMETER_MISSING.':'.'$stmtname');

		// Lookup the statement's variables, then test for invalid input
		$varsInStatement = & $this->_parse['MIXED']['NAMES'][$stmtname];
		foreach ($varsInStatement as $varname) {
			if ($this->_query_variables_by_name[$varname]->containsInvalidInput())
				return true;
		}
		return false;
	} // end statementContainsInvalidInput()

	/**
	 * Check if the statment is being used by any variables contained in the statement
	 *
	 * @param string $stmtname Name of the statement to check
	 * @return boolean True if the statement is being used
	 * @access private
	 * @since 1.4
	 */
	function statementIsInUse($stmtname) {
		
		// ARRAYASSIGN
		$statement = $this->getVariable($stmtname);
		
		// Check if we have the variable
		if ( ! $statement  )
			return $this->logApplicationError("Cannot obtain the variable named '$stmtname' for query '$this->name' ($this->id)");
		
		// Required statements are always used	
		if ( $statement->isRequired() )
			return true;

		// Check if there are any variables using this statement
		// ARRAYASSIGN
		$mixed = $this->_parse['MIXED']['NAMES'][$stmtname];
		if (count($mixed) == 0)
			return false;
		
		// Check to see what variables are using this statement	
		// ARRAYASSIGN	       
		$variables = $this->getVariablesForQuery();
		
		foreach ( $mixed as $varname ) {
			$variable = $variables[$varname];

			// Make sure that the variable is set before calling methods
			if ( ! (isset($variable) && is_object($variable)) ) {
			    $this->logUserError("While checking if a statement is being used '$this->name' ($this->id), the variable $varname was requested but is missing");
			    continue;
			}
			if ( $variable->inputSuccessfullyRegistered() || $variable->containsRejectedInput() || $variable->acceptsBlanks() ) 
				return true;
		}
		
		// No one is using the statement
		return false;	
	} // end statementIsInUse()

	function XstatementIsInUse($statementName) {
		
		$mixed = & $this->_parse['MIXED'];
		//echo "checking if the statement $statementName is in use.";
		foreach ( $mixed['NAMES'][$statementName] as $variableName ) {
		  // ARRAYASSIGN
		  $variable = $this->getVariable($variableName);
		  if ( ! (isset($variable) && is_object($variable)) )
		    return $this->logUserError("The statement '$statementName' should contain the variable $variableName, but it cannot be found");

		  if ( $variable->inputSuccessfullyRegistered() || $variable->containsRejectedInput() || $variable->acceptsBlanks() ) {
		    //echo "variable $variable->name is used in statement $statementName<br/>";
		    return true;
		  }
		}
		//echo "the statement is not in use<br/>";
		return false;
		  
	} // end statementIsInUse()
	
	/**
	 * Checks if a statement of a given name is required or not
	 *
	 * @access public
	 * @since 1.0
	 * @param string The name of a statement
	 * @return boolean True if the statement is required
	 */
	function statementIsRequired($stmtname) {
		//echo $this->_query_variables_by_name[$stmtname]->required;
		//return ($this->_query_variables_by_name[$stmtname]->required == _CODE_YES) ? true : false;
		return ($this->_query_variables_by_name[$stmtname]->isRequired() ) ? true : false;
	} // end statementIsRequired()

	/**
	 * Obtain a count of potential results
	 *
	 * @access public
	 * @since 1.0
	 * @return integer Count of potential results, False if a problem occured
	 */
	function queryCountTotal() {
		
		// Return a precalculated total count of matches
		if ( isset($this->_countTotal) ) return $this->_countTotal;
		
		if (!$this->checkState('C'))
			return $this->logApplicationError("Unready State in queryCountTotal()");

		$sql = $this->_sql;

		// Call the query to determine the total number of possible matches
		$this->_countTotal = $this->_driver->countTotal($sql, $this->getCountSQL($this->count_sql));
		return $this->_countTotal;
	} // end queryCountTotal()

	/**
	 * Execute a query that has been parsed and prepared by DBQ
	 *
	 * In order to successfully execute this query, the query object must be in
	 * a 'Connected', or 'C', state.
	 *
	 * Note that MySQL will indicate 0 rows are affected if a query successfully
	 *  executes but now rows where changed, for example, an UPDATE statement that 
	 *  set a field to an existing value.
	 *
	 * @access public
	 * @since 1.0
	 * @param integer $limitstart Specifies the starting point for any result set
	 * @param integer $limit Specifies the number of requested results
	 * @return integer The number of rows matched
	 */
	function queryExecute($limitstart = 0, $limit = 10, $sql = NULL) {

		if (!$this->checkState('C'))
			return $this->logApplicationError("Unready State in queryExecute()");

		if (@ !$sql)
			$sql = & $this->_sql;

                // disregard limit if we are not using page navigation
		if ( ! $this->page_navigation )
			$limit = 0;

		if ($this->debug()) {
			$this->debugWrite("Executing query '$this->name' ($this->id): '$sql'");
			$this->debugWrite("limitstart is $limitstart, limit is $limit");
		}

		// Execute the query against the driver
		$rowcount = $this->_driver->execute($sql, $limitstart, $limit, $this->expect_rows);
		if ($rowcount !== false)
			$this->_state = 'X';
		
		// Move the results to this object for later use
		$results =& $this->_driver->getResults();

		if( $result !== false ) array_push($this->_results,$results);
		
		// Return a rowcount
		return $rowcount;
	} // end queryExecute()


	/**
	 * Initialize the connection to a database
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $override Indicates that the state check should not be used
	 * @return boolean True if the connection succeeded
	 */
	function queryInit($override = false) {
		global $dbq_class_path;
		
		if (!$override && !$this->checkState('I'))
			return $this->logApplicationError("Unready State in queryInit()");

		// Retrieve DB information from the database
		$rows = NULL;
		$sql = 'SELECT d.type, c.value as driver, d.hostname, d.username, d.persistent, d.debug, d.name, d.schemaname, d.password FROM '._DBQ_DATABASES_TABLE." d, "._DBQ_QUERIES_TABLE." q, "._DBQ_CONFIG_TABLE." c ".' WHERE q.db_id = d.id AND q.id = '.$this->id.' AND c.type = \'DBDRIVER\' AND c.key = d.driver';
		//echo "SQL to get DB info is $sql<br/>";
		$this->_db->setQuery($sql);
		if (!$rows = $this->_db->LoadObjectList()) {
			$this->logApplicationError(_LANG_NO_DATABASE_CONFIG);
			return false;
		}
		$row = & $rows[0];
		$drivername = strtolower($row->driver);

		if (@ !$row || !$drivername) 
			return $this->logApplicationError(_LANG_NO_DATABASE_CONFIG.': '.$this->db_id);

		// Enable debugging on this query if requested
		if ($row->debug)
			$this->debug = $row->debug;

		if (!include_once ($dbq_class_path."drivers/$drivername.class.php")) 
			return $this->logCriticalError(_LANG_UNSUPPORTED_DRIVER.": $row->driver");


		// Get the driver
		$driverclass = "DBQ_driver_$drivername";
		$driver = new $driverclass ($this);
		if ( ! $driver->isSupportedLocally() )
			return $this->logCriticalError(_LANG_UNSUPPORTED_DRIVER.": $row->driver");

		if (!$driver || ! $driver->connect($row) ) {
			$this->logApplicationError(_LANG_UNSUPPORTED_DRIVER.": $row->driver");
			return false;
		}

		// Store the driver and the new state
		$this->_driver = $driver;
		$this->_state = 'C';

		return true;
	} // end queryInit

	/**
	 * Reconnect to the Joomla database
	 * 
	 * Because the MySQL interface sucks, we sometimes have to reconnect 
	 * to the Joomla database after talking to other databases.
	 * 
	 * @return boolean Status of reconnect
	 * @since 1.1
	 * @access public
	 */
	function queryFinish() {
		return @ $this->_driver->close();
	} // end queryFinish()

	/**
	 * Return the result set from a previously executed query
	 *
	 * @access public
	 * @since 1.0
	 * @return array Result Set
	 */
	function queryGetResults($i=NULL) {
		if ( ! $i ) $i = count($this->_results) - 1;
		return $this->_results[$i];
	} // end queryGetResults()


	function queryGetNextResult() {
		return next($this->_results);
	} // end queryGetNextResult()


	function queryGetCurrentResult() {
		return current($this->_results);
	} // end queryGetCurrentResult()

	function queryResetResults() {
		return reset($this->_results);
	} // end queryResetResults()

	/**
	 * Store the query object to the database
	 *
	 * @access public
	 * @since 1.0
	 * @return boolean Return status of parent's store()
	 */
	function store() {
		// Test if the sql has variables in it
		//$varRegex = $this->getConfigValue('VAR_SUBSTITUTION');
		$varRegex = '/\\$\\{(.*?)\\}/';
		//$stmtRegex = $this->getConfigValue('STMT_SUBSTITUTION');
		$stmtRegex = '/\\$\\[(.*?):(.*?)\\]/';
		//$tmplRegex = $this->getConfigValue('TMPL_SUBSTITUTION');
		$tmplRegex = '/\\$<(.*?)>/';
		if (preg_match($varRegex, $this->sql) || preg_match($tmplRegex, $this->sql) || preg_match($stmtRegex, $this->sql)) {
			$this->uses_vars = true;
		} else {
			$this->uses_vars = false;
		}

		return ($this->initialize(true) && $this->parseSerialize() && parent::store() );
	} // end store()

	function usesInputForms() {
		// The query doesn't use any vars whatsoever
		if ( ! $this->uses_vars )
			return false;
		
		// ARRAYASSIGN
		$vars = $this->getVariablesForQuery();
			
		// The query uses vars, but perhaps not all use inputs
		if ( ! count($vars) )
			return false;
		
		// The query uses vars, but perhaps not all use inputs
		foreach ($vars as $var) {
			if ( $var->usesInputForm() )
				// found one !
				return true;
		}
		
		// OK, there are no variables that require an input form
		return false;
	} // end usesInputForms()

	/**
	 * Utility function that determines if a line is required by its
	 *  required attribute, or by the state of any host statements.
	 * 
	 * @param Object $variable DBQ Variable Object
	 * @return Boolean True if the variable will be required
	 * @access public
	 * @since 1.2
	 */
	function XvariableIsEffectivelyRequired(&$variable) {
		
		/**
		 * Create a number of variables to use later for this line
		 *  *) $hostStatementRequired - Indicates if a statement hosting a variable is required
		 *  *) $notice - Information about what input is required
		 */
		$hostStatement = NULL;
		$willUseHostStatement = true;
		
		// Determine if there is a host statement and if it is either required or in use
		if (@ $hostStatement = $this->getHostStatementNameByVarName($variable->name)) 
			$willUseHostStatement = ($this->statementIsRequired($hostStatement) || $this->statementIsInUse($hostStatement) );
		
		// The variable is effectively required if the variable is required and the statement will be used
		if ( $variable->isRequired() && $willUseHostStatement )
			return true;

		// We're Safe
		return false;
	} // end variableIsEffectivelyRequired()

}
?>
