<?php

// Include the file for the base class
//DBQ_Settings::includeClassFileForType('DBQ_common');
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/common.class.php');

class DBQ_admin_common extends DBQ_common {
	
	var $obj = NULL;
	var $id = NULL;

	/**#@+
	 * @access private
	 */
	var $_search_fields = array();
	var $_parent_tbl = NULL;
	var $_parent_tbl_idx = NULL;
	var $_child_tbl = NULL;
	var $_child_tbl_idx = NULL;
	var $_additional_select = array();
	var $_identifiers = array (
		'database' => 'dbid', 
		'query' => 'qid', 
		'variable' => 'vid', 
		'substitution' => 'sid', 
		'config' => 'cfgid', 
		'category' => 'categoryid', 
		'error' => 'errorid',
		'professional' => 'qid',
		'type' => 'typex', 
		'template' => 'tid', 
		'directory' => 'directoryname',
		'stats' => 'stats',
		'subdirectory' => 'subdirectory', 
		'file' => 'filename');
	/**#@-*/
	
	/**
	 * Constructor for the common admin interface object ojbect
	 *
	 * @param string $table Name of the table where data is stored for this category of objects
	 * @return DBQ_admin_common
	 * @access private
	 * @since 1.4
	 */
	function DBQ_admin_common($table) {

		parent::DBQ_common($table);
	} // end constructor DBQ_admin_common()
	
	/**
	 * Default copy function for the admin menu
	 * 
	 * @param Array of ids that we should copy
	 * @return boolean True if the copy was successful
	 * @access public
	 * @since 1.2
	 */
	function adminCopy($cid) {
		
		foreach ($cid as $id) {
			$obj =& $this->getInstance($id, false);
			if ( ! $obj->load($id)) return false;
			$obj->id = NULL;
			if ( @ $obj->name ) $obj->name = _LANG_COPY_OF.$obj->name;
			if ( $obj->store() && $this->updateOrder() ) echo 'stored';
		}
		return true;
	} // end copy

	/**
	 * Cancel the editing of an object
	 *
	 * @return boolean True if the object was checked-in 
	 * @access private
	 * @since 1.0
	 */
	function adminCancel() {
		
		$obj =& $this->getInstance();
		
		return $obj->bind($_POST) && $obj->checkin() ? true : false;
	} // end cancel()



	function field($field='', $i=NULL, $alt=NULL, $twist=NULL) {
		// Name always represents the field used to edit the element
		$obj =& $this->getObject();

		
		if ( $field === 'name') {
			$this->fieldIdentifier($twist);
		} elseif ( $field === 'ordering') {
			// Should be handled by summary screen
		} elseif (isset($alt)) {
			echo '<td align="center">'.$alt.'</td>';
		} else {
			//$text = property_exists($obj, $field ) ? $this->displayName($obj->$field) : '&nbsp;';
			$text =  ( isset($obj) && isset($obj->$field) ) ? $this->displayName($obj->$field) : '&nbsp;';
			echo '<td align="center">'.$text.'</td>';
		} 
	} // end field()

	function fieldIdentifier($twist=NULL, $idfield = 'id' ) {
		global $globals;
		$obj =& $this->getObject();
		
		// Use this field to link to the edit screen
		// Also create a onMouseOver event to show the description
		$identifier = $this->getIdentifierForObjectType();
		$url = _DBQ_URL_ADMIN.'&act='.$globals->act.'&task=edit&hidemainmenu=1&'.$identifier.'='.$obj->$idfield;
		echo '<td align="left">';
		//echo '<a href="#edit" onclick="return listItemTask(\'cb'.$i.'\',\'edit\')">';
		$libover = NULL;
		if ( @ $obj->description) {
			//$text = addslashes($this->description);
			$libover = $this->makeLibOver( $obj->getDescription() );
			//$libover = ' onMouseOver="return overlib(\'<table>'.$text.'</table>\', CAPTION, \''._LANG_DESCRIPTION.'\', BELOW, RIGHT);" onMouseOut="return nd();" ';
		}
		echo "<a href=\"$url\" title=\"\" $libover >";
		echo @ $twist ? $obj->$twist : $obj->name;
		echo '</a></td>';
	} // end fieldIdentifier()

	/**
	 * Build a link to another screen
	 *
	 * @param unknown_type $url
	 * @param unknown_type $field
	 * @param unknown_type $alt
	 * @param unknown_type $limit
	 * @param unknown_type $style
	 * @access private
	 * @since 1.4
	 */
	function fieldLink($url, $field=NULL, $alt=NULL, $limit=true, $style='') {
		$obj =& $this->getObject();
		$text = isset($alt) ? $alt : $obj->$field;
		$style = isset($style) ? "style=\"$style\"" : '';
		$limittext = $limit ? '&limitstart=0' : '';
		echo "<td align=\"center\"><a href=\"$url$limittext\" $style>$text</a></td>";
	} // end fieldLink

	/**
	 * Returns of records matching the criteria
	 * Calls the base class function
	 * @param string   Value 
	 * @param integer  Variable ID
	 * @param integer Query ID
	 * @param string  SQL to append to the query
	 * @return integer  The number of rows
	 * @access protected
	 * @since 1.0
	 */
	function getCountOfAllRecords($search1 = null, $search2 = null, $search3 = null, $appendSQL = NULL, $where=NULL) {


		// Empty array for where statement
		$join = array();
		if ( @ ! $where )$where = array ();
		// Add additional select statements
		if ( is_array($this->_additional_select) && count($this->_additional_select) ) {
			foreach ($this->_additional_select as $sql) {
				$join[] = $sql[1];
			}
		}

		if (isset($search1) && $search1 != '' ) {
			$search1 = $this->getEscaped($search1);
			$search1 = strtolower(trim($search1));
			$where[] = ' LOWER(t.'.$this->_search_fields[1].' ) LIKE '."'%$search1%' ";
		}
		if (isset($search2) && $search2 != '' ) {
			$search2 = $this->getEscaped($search2);
			if (!preg_match('/^[0-9]+$/', $search2))
			$search2 = " '$search2' ";
			$where[] = ' t.'.$this->_search_fields[2].' = '.$search2;
		}
		if (isset($search3) && $search3 != '' ) {
			$search3 = $this->getEscaped($search3);
			if (!preg_match('/^[0-9]+$/', $search3))
			$search3 = " '$search3' ";
			$where[] = ' t.'.$this->_search_fields[3].' = '.$search3;
		}

		// get the total number of records
		// Build the query
		//$sql = 'SELECT count(*) AS count FROM '.$this->_tbl.' t '. (count($where) ? " WHERE ".implode(' AND ', $where) : "");
		$sql = 'SELECT count(*) AS count '
		  . " FROM $this->_tbl t ". (count($join) ? ' '.implode(' ', $join) : '')
		  . (count($where) ? ' WHERE '.implode(' AND ', $where) : '')
		  . ($appendSQL ? $appendSQL : '');

		//echo "count SQL is $sql<br/>";

		$this->_db->setQuery($sql);
		$result = $this->_db->loadResultArray();

		//print_r($result);
		// Return the result, unless we have an array ( eg, from a GROUP BY statement )
		if (($c = count($result)) == 1) {
			return $result[0];
		} else {
			return $c;
		}
	} // end getCountOfAllRecords()
	
	/**
	 * Get the ID string that would identify the object ID to load, save, etc....
	 * 
	 * If no type is specified, then the identifer for the current object type is returned
	 * Internally, these values are stored in $this->_identifiers
	 * 
	 * @param string $type The object type for which we request the identifier
	 * @return string String 
	 * @since 1.1
	 */
	function getIdentifierForObjectType($type=NULL) {
		return @ $type ? $this->_identifiers[$type] : $this->_identifier;
	} // end getIdentifierForObjectType

	/**
	 * Overrides base class method so that an existing object will be returned
	 *
	 * This method overrides DBQ_common::getInstance so that an existing object will be returned
	 * 
	 * The default behavior is to return an existing object.
	 * 
	 * @param Array $arg Array of data that will be bound to the object
	 * @param boolean $reuse Indicates if an existing object should be reused
	 * @return object Object of the appropriate type
	 * @access private
	 * @since 1.4
	 */
	function & getInstance($arg = NULL, $reuse = true) {

		// Reuse an existing object, if it exists
		if ( $reuse && isset($this->obj) && is_object($this->obj) )
			return $this->obj;

		@ $obj =& parent::getInstance($arg);
		//echo "DBQ_admin_common says that instance is $obj->id, arg is $arg<br/>";
		if ( ! is_object($obj) )
			return $this->logApplicationError("Cannot get an instance");
		
		//echo "DBQ_admin_common again says that instance is $obj->id, arg is $arg<br/>";
		
		$this->setObject($obj);
		//echo "object name is $obj->name";
		
		return $this->obj;
	} // end getInstance()
	
	/**
	 * Get the current working object
	 *
	 * @return object The current working object
	 * @access private
	 * @since 1.4
	 */
	function & getObject() {
		return $this->obj;
	} // end getObject()
	
	/**
	 * Return a url for the administrative interface
	 * 
	 * @param string $type The type of URL requested
	 * @return string $url URL 
	 * @access private
	 * @since 1.4
	 */
	function getUrl($type) {
		if ( ! is_object($this->_settings) )
			return $this->logApplicationError("Requesting a URL for '$type' but settings are not initialized");
			
		$urls =& $this->_settings->adminurls;
		if ( ! array_key_exists($type, $urls) )
			return $this->logApplicationError("Requesting a URL for '$type' but type is not found");
			
		return $urls[$type];
	} // end getURL()
	
	/**
	 * Print a default header
	 *
	 * @param string $header String to print in the header box
	 * @access public
	 * @since 1.3
	 */
	function header($header) {
		$align = ( $header == 'Name' ) ? 'left' : 'center';
		echo '<th align="'.$align.'">'.$header.'</th>'."\n";
	} // end header()
	
	/**
	 * Print a special header checkbox that calles the checkAll() javascript function
	 *
	 * @param string $c 
	 * @access public
	 * @since 1.3
	 */
	function headerCheckbox($c) {
		echo '<th><input type="checkbox" name="toggle" value="" onclick="checkAll(';
		echo $c;
		echo ');" /></th>';
	} // end headerCheckbox
	
	/**
	 * Print an order dialog that is used to order objects
	 *
	 * @param int $c Numeric to be displayed in the box
	 * @access public
	 * @since 1.3
	 */
	function headerOrder($count=1) {
		// Test for new versions of Joomla
		global $mosConfig_absolute_path;
		include_once $mosConfig_absolute_path.'/administrator/includes/menubar.html.php';
		$html = new mosMenuBar();

		if ( is_object($html) && method_exists($html, 'apply') ) {
			$js = '<a href="javascript: saveorder( '. ($count -1).' )"><img src="images/filesave.png" border="0" width="16" height="16" alt="Save Order" /></a>';
			echo '<th>'._LANG_ORDER.'</th><th>'.$js.'</th><th colspan="2">'._LANG_REORDER.'</th>';
		} else {
			echo '<th colspan="2">'._LANG_ORDER.'</th><th colspan="2">'._LANG_REORDER.'</th>';
		}
	} // end headerOrder

	/**
	 * Define this object to be a part of the admin interface
	 *
	 * @return boolean true
	 * @access private
	 * @since 1.4
	 */
	function isAdmin() {
		return true;
	} // end isAdmin()
	
	/**
	 * A Utility function to generate a list of objects using a SQL query
	 *
	 * @param string $sql SQL to execute against the database
	 * @return array Array of objects
	 * @access private
	 * @since 1.4
	 */
	function & listAllObjectsUsingSQL($sql) {
		// Get rows of data
		$this->_db->setQuery($sql);
		$rows = & $this->_db->loadAssocList();
		$results = array ();
		
		// Determine the class that we need
		//$class = $this->getFrontEndClass();

		// Iterate through the results and make objects
		foreach ($rows as $row) {
			//$obj = new $class (true);
			//mosBindArrayToObject($row, $obj);
			//echo "object $obj of id $obj->id<br />";
			$obj =& $this->getInstance($row, false);
			$results[] = $obj;
		}
		return $results;
	} // end ListAllObjectsUsingSQL()

	/**
	 * List all queries registered with database query
	 *
	 * @return array Query information
	 * @access public
	 * @since 1.4
	 */
	function listAllQueries() {
		$sql = 'SELECT id, name FROM #__dbquery';
		//echo "sql is $sql<br/>";
		$this->_db->setQuery($sql);
		return $this->mashRows($this->_db->loadRowList());
	} // end listAllQueries

	/**
	 * Shortcut function to provide a list of queries by id and name
	 *
	 * @access public
	 * @since 1.0
	 * @return array Query information
	 */
	function listAllQueriesShort() {
		return $this->listAllParentRecordsInUseShort(_DBQ_QUERIES_TABLE,NULL,'query_id');
	} // end listAllQueriesShort()

	/**
	 * List all records in the table for the object type
	 *
	 * The returned results will be a list of objects matching the specified
	 * criteria.  The objects will be of the same class as the calling object.
	 *
	 * @access public
	 * @param integer limit
	 * @param integer limitstart
	 * @param integer $id ID of object if we wish to list a specific object
	 * @param string $search1
	 * @param string $search2
	 * @param string $search3
	 * @return array List of objects 
	 * @since 1.0
	 */
	function listAllRecords($limit = 10, $limitstart = 0, $id = NULL, $search1 = NULL, $search2 = NULL, $search3 = NULL, $where = NULL) {

		$table = $this->_tbl;
		$tableidx = $this->_tbl_key;
		$join = array ();
		$select = array ();

		// If no where criteria were passed in, initialize an empty where clause
		if ( ! ( isset($where) && is_array($where) ))
		$where = array ();

		$select[] = 'u.name as editor ';
		$join[] = 'LEFT JOIN '._DBQ_USERS_TABLE.' u ON u.id = t.checked_out ';

		// Escape all input -- Trust no one
		$limit = $this->getEscaped($limit);
		$limitstart = $this->getEscaped($limitstart);

		// Add additional select statements
		if ( is_array($this->_additional_select) && count($this->_additional_select) ) {
			foreach ($this->_additional_select as $sql) {
				$select[] = $sql[0];
				$join[] = $sql[1];
			}
		}

		// Get information about the parent
		if ($this->_parent_tbl && $idx = $this->_parent_tbl_idx) {
			$select[] = ' p.name as parent ';
			$join[] = ' LEFT JOIN '.$this->_parent_tbl." p ON p.id = t.$idx ";
		}

		if (@ $id) {
			// Retrieve for just the specified ID
			$id = $this->getEscaped($id);
			$where[] = " t.$tableidx in ($id)";
		} else {
			// Handle search criteria
			if (isset($search1) && $search1 != '' ) {
				$search1 = $this->getEscaped($search1);
				$search1 = strtolower(trim($search1));
				$where[] = ' LOWER(t.'.$this->_search_fields[1].' ) LIKE '."'%$search1%' ";
			}
			if (isset($search2) && $search2 != '' ) {
				$search2 = $this->getEscaped($search2);
				if (!preg_match('/^[0-9]+$/', $search2))
				$search2 = " '$search2' ";
				$where[] = ' t.'.$this->_search_fields[2].' = '.$search2;
			}
			if (isset($search3) && $search3 != '' ) {
				$search3 = $this->getEscaped($search3);
				if (!preg_match('/^[0-9]+$/', $search3))
				$search3 = " '$search3' ";
				$where[] = ' t.'.$this->_search_fields[3].' = '.$search3;
			}
		}

		// Use the new system of getting search critiera
		/*
		if ( $this->hasSearchCriteria() ) {
		  foreach ( $this->getSearchCriteria() as $search) 
		    $where[] = $search->getSearchWhereClause();
		}
		*/

		// Order by additional criteria
		$altOrder = @ $this->_search_fields[2];

		//echo "Search is '$search', SearchField is '$searchField', count is " . count($search). "<BR>";
		// Build the query
		$sql = 'SELECT t.* '
		  . (count($select) ? ', '.implode(', ', $select) : '')
		  . " FROM $table t ". (count($join) ? ''.implode(' ', $join) : '')
		  . (count($where) ? ' WHERE '.implode(' AND ', $where) : '')
		  . " ORDER BY ". (@ $altOrder ? "$altOrder, " : '')." t.ordering LIMIT $limitstart,$limit";
		//echo "SQL is $sql<br/>";
		if ($this->debug())
			$this->debugWrite('SQL is '.$sql);

		return $this->listAllObjectsUsingSQL($sql);
	} // end listAllRecords()

	/**
	 * List id and name of all parent records
	 *
	 * This function is typically used for generating select menus for
	 * the various Administrative screens
	 *
	 * @access public
	 * @param string $table Optional table name if different than actual parent
	 * @param string $where Optional WHERE clause to append to the SQL
	 * @return array List of parent records
	 */
	function listAllParentRecordsShort($table=NULL,$where=NULL) {
		$table = $table ? $table : $this->_parent_tbl;
		$sql   = 'SELECT DISTINCT t.id, t.name FROM '.$table.' t ';
		if (@ $where) {
			$sql .= $where;
		}
		$sql .= ' ORDER BY t.ordering';

		$this->_db->setQuery($sql);
		$tmp = $this->mashRows($this->_db->loadRowList());
		return $tmp;
	} // end listAllParentRecordsShort()

	
	/**
	 * List id and name of parent records currently in use
	 *
	 * This function is typically used for generating select menus for
	 * the various Administrative screens
	 *
	 * @access public
	 * @param string $table Optional table name if different than actual parent
	 * @param string $where Optional WHERE clause to append to the SQL
	 * @param string $index Optional column name that serves as a reference to the parent
	 * @return array List of parent records
	 */
	function listAllParentRecordsInUseShort($table=NULL,$where=NULL,$index=NULL) {
		$table = $table ? $table : $this->_parent_tbl;
		$index = $index ? $index : $this->_parent_tbl_idx;
		$sql   = 'SELECT DISTINCT t.id, t.name FROM '.$table.' t, ';
		//$sql  .= $this->_home_tbl.' h ';
		$sql  .= $this->_tbl.' h ';
		if (@ $where) {
		  $sql .= ' WHERE ' . $where;
			//$sql .= $where.' AND t.id = h.'.$index;
		} else {
			$sql .= ' WHERE t.id = h.'.$index;
		}
		$sql .= ' ORDER BY t.name';
		//echo "short search sql is $sql<br/>";
		$this->_db->setQuery($sql);
		$tmp = $this->mashRows($this->_db->loadRowList());
		return $tmp;
	} // end listAllParentRecrodsInUseShort()

	/**
	 * List the options for a variable's require attribute
	 *
	 * @return array Available requirement types
	 * @access public
	 * @since 1.4
	 */
	function listSupportedRequirements() {
		
		$obj =& $this->getObject();
		// Field variables don't use required statements
		if ( $obj->isField() )
			return array();
			
		// Get required options
		$tmp =& $this->_settings->listRequiredOptions();
		//$tmp = & $this->mashRows2($this->_config['REQUIRED']);
		
		// Statements don't support blanks
		if ( $obj->isStatement() ) {
			unset ($tmp['BLANK']);
			unset ($tmp['DEFAULT']);
		}
		
		return $tmp;
	} // end listSupportedRequirements()
		
	/**
	 * List the id and name supported variable types
	 * @access public
	 * @return array Variable type information
	 */
	function listSupportedVariableTypes() {
		return array (
			'code' => _LANG_VT_CODE,
			'custom' => _LANG_VT_CUSTOM,
			'field' => _LANG_VT_FIELD,
			'files' => _LANG_VT_FILES,
			'keyword' => _LANG_VT_KEYWORD, 
			'results' => _LANG_VT_RESULTS,
			'statement' => _LANG_VT_STATEMENT,
			'substitutions' => _LANG_VT_SUBSTITUTIONS,
			'upload' => _LANG_VT_FILE_UPLOAD,
			'user' => _LANG_VT_USER
		);
	} // end listSupportedVariableTypes()

	/**
	 * Load the child object and perform checkout for editing
	 * 
	 * @param int $id ID of object to edit
	 * @return True if the object is loaded and checked-out
	 * @access private
	 * @since 1.4
	 */
	function loadAndCheckOut($id) {
		global $my;

		if ( ! $id )
			return $this->logApplicationError("Cannot edit object without an 'id' argument");

		// Get the object
		if ( isset($this->obj) && is_object($this->obj) && ($this->obj->id == $id) ) {
			// We have the object already
			$obj =& $this->obj;
		} else {
			// Try to load the item
			$obj =& $this->getObject();

			if ( ! $obj->load($id) )
				// Cannot load ...
				return $this->logApplicationError("Cannot load the object with id '$id'");			
		}

		// Loaded.  Now do a checkout
		if ( $obj->checked_out && ($obj->checked_out <> $my->id) ) 
			mosRedirect($this->getUrl('config'), "The object '$obj->name' ($obj->id) is currently being edited by someone else.");

		return $obj->checkout($my->id);
	} // end loadAndCheckOut()

	
	function makeSearchField($name,$search) {
		return '<input type="text" name="'.$name.'" value="'.$search.'" class="text_area"onChange="document.adminForm.submit();" />';
	} // end makeSearchField()

	/**
	 * Move the object 
	 *
	 * @return boolean True if the order was updated
	 * @access private
	 * @since 1.4
	 */
	function move($inc) {

		$obj =& $this->getObject();
		
		$searchSQL = $sf = null;
		$sf = @ $this->_search_fields[2];

		// Make sure that we have a search field
		if ( ! $sf )
			return $this->logApplicationError("Cannot update order for '$obj->name' ($obj->id) because the search field has no value");
		
		// Make sure the the property exists
		//if ( ! property_exists($obj, $sf))
		//	return $this->logApplicationError("Cannot update order for '$obj->name' ($obj->id) because the object does not have the property '$sf'");
			
		// Determine what this group is
		$sv = $obj->$sf;
		
		if ( $this->debug() )
			echo "obj is $obj->id, inc is $inc, search value is $sv<br/>";

		// Update the order
		return $obj->move($inc, " $sf='$sv' ");
	} // function move()
	
	function adminOrderUp($id) {
		return $this->order($id, -1);
	} // end orderUp()

	function adminOrderDown($id) {
		return $this->order($id, 1);
	} // end orderDown()

	/**
	* Moves the order of a record
	* @param integer The increment to reorder by
	*/
	function order($id, $inc) {

		$obj =& $this->getInstance();
		$obj->load($id);

		if ($this->debug()) 
			$this->debugWrite("object is $obj, id is $id, inc is $inc");
			
		// Move the object
		$this->move($inc);

		// Now update the ordering
		$this->updateOrder();

		return true;
	} // end order()
	
	/**
	* Deletes one or more records
	* @param array An array of unique category id numbers
	*/
	function adminRemove($cid) {

		if (!is_array($cid) || count($cid) < 1) {
			echo _LANG_SELECT_ITEM;
			return false;
		}

		foreach ($cid as $id) {
			$obj = $this->getInstance($id, false);
			$obj->load($id);
			

			if (!$obj->canDelete() || !$obj->delete()) {
				echo _LANG_ITEM_NOT_DELETED;
				return false;
			}
		}

		return true;
	} // end remove()


	/**
	* Saves the record after being edited
	*
	* This function takes arguments from the POST request and binds the arguments
	* to object attributes, if they exist.
	*
	* @access public
	* @since 1.0
	* @return boolean
	*/
	function adminSave() {
		$obj =& $this->getInstance($_POST);
		
		// serialize the params array into a string
		$params = mosGetParam( $_POST, 'params', '' );
		if (is_array( $params )) {
			$txt = array();
			foreach ($params as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$obj->params = $obj->cleanMagicQuotes(mosParameters::textareaHandling($txt) );
		}
		
		// serialize the description array into a string
		// NOTE: not all objects use description params
		$descriptions = mosGetParam($_POST, 'descriptions', '');

		if ( is_array( $descriptions ) ) {
			$txt = array();
			foreach ($descriptions as $k=>$v) {
				// TODO Figure out what descriptions are escaped, but params are not
				//$txt[] = $k.'='.stripslashes($v);
				$txt[] ="$k=$v";
			}
			$obj->description = $obj->cleanMagicQuotes(mosParameters::textareaHandling($txt) );			
		}

		$this->setObject($obj);

		// Check, store, and checking
		if ( $obj->check() && $obj->store() && $obj->checkin() ) 
			return $this->writeConsoleMessage(_LANG_CHANGES_SAVED);
			
		// Something went wrong
		global $task;
		$task = 'edit';
			
		// Give a warning
		if ( $obj->debug() )
			$obj->debugWrite('Data check failed:'.$obj->getLastErrorMsg() );
		$this->writeConsoleMessage(_LANG_ERROR.': '.$obj->getLastErrorMsgHTML() );
		return false;
	} // end save()

	/**
	 * Save the order determined in the admin interface
	 * 
	 * @param array $cid list of ids to change
	 * @return boolean true if successful, false otherwise
	 * @access public
	 * @since 1.1
	 */
	function adminSaveOrder( &$cid) {
		$total = count($cid);
		$order = mosGetParam($_GET, 'order', array(0));
		$obj =& $this->getInstance();

		$error = NULL;
		$updateSort = array();

		for ( $i=0; $i < $total; $i++) {
			// load the object
			if ( ! $obj->load($cid[$i])) {
				$error = $obj->_db->getErrorMsg();
				$i = $total;
				continue;
			}
			//echo "cid is {$cid[$i]}<br/>";


			// skip over unchanged attribues
			if ( $obj->ordering == $order[$i] ) continue;

			//echo "object was {$obj->ordering}, now is {$order[$i]} <br/>";
			$obj->ordering = $order[$i];

			if ( ! $obj->store($cid[$i])) {
				$error = $obj->_db->getErrorMsg();
				$i = $total;
				continue;
			}
			$updateSort[$this->_search_fields[2]] = true;
		}

		if ( $error ) {
			$this->writeConsoleMessage(_LANG_ERROR.$error);
			return false;
		}

		// return status of updateOrder if anything has changed
		if ( count($updateSort) ) {
			foreach ( array_keys($updateSort) as $key) {
				//echo "key is $key";
				$this->updateOrder($key);
			}
		} else {
			return true;
		}
	} // end saveOrder()

	/**
	 * Set the current working object
	 *
	 * @param object $obj
	 * @return boolean true
	 * @access private
	 * @since 1.4
	 */
	function setObject(&$obj) {
		// Set this id to the same as the object's id
		//if ( property_exists($obj, 'id') and $obj->id )
		//	$this->id = $obj->id;
			
		// Set this id to the same as the object's id
		//if ( property_exists($obj, 'name') and $obj->name )
		//	$this->name = $obj->name;
			
		$this->obj =& $obj;
		return true;
	} // end setObject()
	
	/**
	 * Set the global task variable so that the admin menubar will display correctly
	 *
	 * @param string $newtask Task to show, defaulting to 'edit'
	 * @return boolean True
	 * @access private
	 * @since 1.4
	 */
	function setTask($newtask = 'edit') {
		global $task;
		$task = $newtask;
		return true;
	} // end setTask()
	
	/**
	 * Indicates whether this class should link to the error reporting screen
	 * 
	 * @return Boolean True if the class officially supports error reporting (which they mostly do)
	 * @access protected
	 * @since 1.3
	 * @todo Actually use this function
	 */
	function supportsErrorReportingInAdminConsole() {
	  $class = strtolower($this->getFrontEndClass());
		
	  return ! ( ($class == 'dbq_error') || ($class == 'dbq_template') || ($class == 'dbq_stats') );
	} // end supportsErrorReportingInAdminConsole()	
	
	/**
	 * Update the ordering for the object class and object type
	 *
	 * @return boolean True if the order was updated
	 * @access private
	 * @since 1.4
	 */
	function updateOrder() {

		$obj =& $this->getObject();
		
		$searchSQL = $sf = null;
		$sf = @ $this->_search_fields[2];

		// Make sure that we have a search field
		if ( ! $sf )
			return $this->logApplicationError("Cannot update order for '$obj->name' ($obj->id) because the search field has no value");
		
		// Make sure the the property exists
		//if ( ! property_exists($obj, $sf))
		//	return $this->logApplicationError("Cannot update order for '$obj->name' ($obj->id) because the object does not have the property '$sf'");
			
		// Determine what this group is
		$sv = $obj->$sf;
		
		if ( $this->debug() )
			$obj->debugWrite("obj is $obj->id, search value is $sv");

		// Update the order
		return $obj->updateOrder(" $sf='$sv' ");
	} // function updateOrder()
	

}
