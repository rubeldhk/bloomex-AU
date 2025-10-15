<?php

/**
 * @package DBQ
 */

/**
 * Prevent Direct Access
 */
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class

//DBQ_Settings::includeClassFileForType('DBQ_variable');
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/variable.class.php');
require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/common.class.php');

class DBQ_admin_variable extends DBQ_admin_common {

	var $_substitution_values = NULL;
	
	
	function DBQ_admin_variable() {
		$this->_identifier = $this->_identifiers['variable'];

		$this->_search_fields = array ('id', 'name', NULL, 'type');
		$this->_initializing = false;

		$this->_parent_tbl = _DBQ_QUERIES_TABLE;
		$this->_parent_tbl_idx = NULL;
		$this->_child_tbl = _DBQ_SUBSTITUTIONS_TABLE;
		parent::DBQ_admin_common(_DBQ_VARIABLES_TABLE);
	} // end constructor DBQ_Admin_Variable
	
	/**
	 * Copy a variable.
	 * 
	 * If the variable is a list, then copy it's list children as well
	 */
	function adminCopy($cid) {

		foreach ($cid as $id) {
			$obj =& $this->getInstance($id, false);
			if ( ! $obj->load($id)) return false;
			$obj->id = NULL;
			if ( $obj->name ) $obj->name = _LANG_COPY_OF.$obj->name;
			
			// Copy substitution values if variable is substituions
			if ( $obj->isSubstitutions() ) 
				$obj->shouldSaveSubstitutions(true);
				
			if ( $obj->store() && $this->updateOrder() ) echo 'Copied';
		}
		return true;
	} // end copy
	
	/**
	 * Convert a string containing new lines into an array
	 *
	 * @param string $input String to be parsed
	 * @param string $regex Option regex tokenizer, default is a newline
	 * @return array Array of strings
	 */
	function createSubstitutionElements($input, $regex = NULL) {
		if (!@ $input) 
			return $this->logApplicationError("Trying to create substitution elements for variable '$this->name' ($this->id), but no input is provided");
			
		if (!$regex)
			//        (and characters but | and \n) deliminator (any characters)
			//$regex = '/^([^\|\n]+)\|?([^\|\n]+)\|?([^\|\n]+)\|?(.*)$/m';
			$regex = '/^([^\|\n]+)\|?(.*)$/m';
		$results = NULL;
		
		// Get the current object
		$var =& $this->getObject();
		
		$ret = preg_match_all($regex, $input, $results);
		if ( ! is_array($results[0])) 
			// No matches - oh well
			return false;
			
		$n = count($results[0]);
		$array = array ();
		global $mosConfig_absolute_path;
		require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/substitution.class.php');
		for ($i = 0; $i < $n; $i++) {
			// Store this info in an object, ignoring empty results
			if ( $results[1][$i] == "\r") continue;
			//echo "  storing '{$results[1][$i]}' -> '{$results[2][$i]}'<br/>";
			//DBQ_Settings::includeClassFileForType('DBQ_substitution');
			$obj = new DBQ_substitution();
			$obj->value = trim($results[1][$i]);
			$obj->label = trim($results[2][$i]);
			// Create a default label if none exists
			if ( ! $obj->label )
				$obj->label = $obj->value;
			//$obj->ordering = trim($results[3][$i]);
			//$obj->default = trim($results[4][$i]);
			$obj->variable_id = $var->id;
			$array[] = $obj;
		}
		
		// Store the new list of substitution elements
		$var->_list =& $array;
		$var->shouldSaveSubstitutions(true);
		
		return true;
	} // end createSubstitutionElements()
		
	function adminEdit($id = NULL) {
		global $mainframe, $globals, $my, $dbq_xhtml_path;

		$task = $globals->task;
		$act = $globals->act;
		$option = $globals->option;

		// Make an object
		$obj =& $this->getInstance($id);
		
		// Load and checkout
		if ( $id and ! $this->loadAndCheckOut($id))
			return false;
			
		$displaytask = ($task == 'new' ? _LANG_ADD : _LANG_EDIT).' '._LANG_VARIABLE;
		$columnInfo = $obj->getTableInformation();
		
		// Get the list information
		// This is specific for substitution variables
		if (@ $obj->isSubstitutions()  ) {
			
			// If substituions exists, display them in a textarea box
			if ( isset($obj->_list)) {
				foreach ($obj->_list as  $v) 
					//$list[] = $v->value.'|'.$v->label.'|'.$v->ordering.'|'.$v->default;
					$list[] = $v->value.'|'.$v->label;
				$obj->_substitution_values = @ implode("\n",$list);
			}	
			
			$columnInfo['_substitution_values']->Disabled = false;
			$columnInfo['_substitution_values']->Required = false;
			$columnInfo['_substitution_values']->Size = 1024;
			$columnInfo['_substitution_values']->Null = false;
			
			// Create an form element that will tell us if the user wants to update the substitutions list
			$columnInfo['_update_substitutions']->Disabled = false;
			$columnInfo['_update_substitutions']->Required = false;
			$columnInfo['_update_substitutions']->Size = 1;
			$columnInfo['_update_substitutions']->Null = false;
		}

		$visibilityObjects = array ();
		//$colsToSkipInDisplay = array ('id', 'ordering', 'checked_out', 'checked_out_time');
		$colsToSkipInDisplay = array ('id', 'checked_out', 'checked_out_time');
		$colsToHideInDisplay = array ();

		// Build a list of attributes to display for this variable
		$colsToDisplay = array (_LANG_DETAILS_CONFIG => array ('name', 'display_name', 'type', 'ordering', 'active', 'debug') );
			
		$a1 = $obj->listClassAttributes1();
		$a2 = $obj->listClassAttributes2();
		
		if ( count($a1) )
			$colsToDisplay[_LANG_DETAILS_ATTRIBUTES] = $a1;
		
		if ( count($a2) )
			$colsToDisplay[_LANG_DETAILS_SPECIAL] = $a2;
		
		$colsToDisplay[_LANG_DETAILS_TEXT] = array ('description');
		//$colsToDisplay[_LANG_DETAILS_PARAMS] = array ('params');

		$inputs = array ('' => _LANG_SELECT._LANG_INPUT);
		$inputs += $this->listSupportedInputs();

		$regex = array ('' => _LANG_SELECT._LANG_REGEX);
		$regex += $this->listSupportedRegex();

		$types = array ('' => _LANG_SELECT._LANG_TYPE);
		$types += $this->listSupportedVariableTypes();

		$required = array ('' => _LANG_SELECT._LANG_REQUIRED);
		$required += $this->listSupportedRequirements();

		$queries = array ('' => _LANG_SELECT._LANG_QUERY);
		$queries += $this->listAllQueries();

		//$selectBoxes['ordering'] = &$orders;
		$selectBoxes['input'] = & $inputs;
		$selectBoxes['regex'] = & $regex;
		$selectBoxes['required'] = & $required;
		$selectBoxes['type'] = & $types;
		$selectBoxes['query_id'] = & $queries;

		include $dbq_xhtml_path.'details.html.php';
		return true;
	} // end edit()

	/**
	 * Print the contents of a field
	 *
	 * @param string $field Field name
	 * @param int $i ???
	 * @return boolean True
	 * @access private
	 * @since 1.4
	 */
	function field($field, $i) {

		$url = NULL;
		$obj =& $this->getObject();
		
		switch ($field) {

			case 'active':
				$status = $obj->active ? _LANG_YES : _LANG_NO;
				echo '<td align="center">'.$status.'</td>';
				break;
			case 'parent' :
			  // Get Parent Information
			  $parents = $this->getParentInformation($obj);
			  $count = count($parents);
			  if ( $count ) {
				$description = array();
				$ids = array();
				foreach ( $parents as $parent ) {
				  $descriptions[] = "$parent->name: '$parent->display_name'";
				  $ids[] = $parent->id;
				}
				$libover = $this->makeLibOver( implode('<br/>', $descriptions) );
				$url = $this->getUrl('query').'&task=show&clean=1&qid='.implode(',', $ids);
				$limittext = '&limitstart=0';
				echo "<td align=\"center\"><a href=\"$url$limittext\" $libover>$count</a></td>";
			  } else {
   				echo '<td align="center">'._LANG_NONE.'</td>';
			  }
			  break;
			case 'stats':
				if ( $obj->stats ) {
					$status = _LANG_YES;
					$url = $this->getUrl('stats').'&task=show&clean=1&vid='.$obj->id;
					parent :: fieldLink($url, NULL, $status);
				} else {
					echo '<td align="center">'._LANG_NO.'</td>';
				}
				break;
			case 'required':
				$required = $this->listSupportedRequirements() ;
				echo '<td align="center">'.@$required[$obj->required].'</td>';
				break;
			case 'type' :
				$text = $this->displayName($obj->type);
				if ($obj->type == 'substitutions' ) {

					$url = $this->getUrl('substitution').'&task=show&clean=1&vid='.$obj->id;
					parent :: fieldLink($url, NULL, $text ? $text : _LANG_LIST);
				} else {
					
					echo '<td align="center">'.$text.'</td>';
				}
				break;
			default :
				return parent :: field($field, $i);
		}
		
		return true;
	} // end field()
	
	/**
	 * Get an instance of the object
	 * 
	 * This method overrides the parent method and will initialize the variable before returning it
	 *
	 * @param Array $arg arguments that will be bound to the object
	 * @param boolean $reuse Set to false if you want a new object
	 * @return object DBQ_variable object
	 * @access private
	 * @since 1.4
	 */
	function xgetInstance($arg = NULL, $reuse = true) {
		
		// Reuse an existing object, if it exists
		if ( $reuse && isset($this->obj) && is_object($this->obj) )
			return $this->obj;

		// Get an instance
		$obj =& DBQ_variable::getInstance($arg, $reuse);
		if ( ! is_object($obj) )
			return $this->logApplicationError("Cannot get an instance");
			
		// Set this id to the same as the object's id
		//if ( property_exists($obj, 'id') and $obj->id )
		//	$this->id = $obj->id;
			
		$obj->initialize();
		$this->obj =& $obj;
		return $obj;
	} // end getInstance()

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

	  $select = array();
	  $join = array();

		// Empty array for where statement
		if ( @ ! $where )$where = array ();
		// Add additional select statements
		if ( is_array($this->_additional_select) && count($this->_additional_select) ) {
			foreach ($this->_additional_select as $sql) {
				$join[] = $sql[1];
			}
		}

		if (@ $id) {
			// Retrieve for just the specified ID
			$id = $this->getEscaped($id);
			$where[] = " t.$tableidx in ($id)";
		} else {
			if (isset($search1) && $search1 != '' ) {
				$search1 = $this->getEscaped($search1);
				$search1 = strtolower(trim($search1));
				$where[] = ' LOWER(t.'.$this->_search_fields[1].' ) LIKE '."'%$search1%' ";
			}
			if (isset($search2) && $search2 != '' ) {
				$search2 = $this->getEscaped($search2);
				$join[] = ' #__dbquery p ';
				//$where[] = " p.id = $search2 AND p.parse REGEXP CONCAT('[[:<:]]', t.name, '[[:>:]]') ";
				$where[] = " p.id = $search2 AND BINARY p.parse REGEXP CONCAT('[<{[]', t.name, '[>}:]') ";
			}
			if (isset($search3) && $search3 != '' ) {
				$search3 = $this->getEscaped($search3);
				if (!preg_match('/^[0-9]+$/', $search3))
				$search3 = " '$search3' ";
				$where[] = ' t.'.$this->_search_fields[3].' = '.$search3;
			}
		}

		// get the total number of records
		// Build the query
		//$sql = 'SELECT count(*) AS count FROM '.$this->_tbl.' t '. (count($where) ? " WHERE ".implode(' AND ', $where) : "");
		$sql = 'SELECT count(*) AS count '
		  . (count($select) ? ', '.implode(', ', $select) : '')
		  . " FROM $this->_tbl t ". (count($join) ? ','.implode(', ', $join) : '')
		  . (count($where) ? ' WHERE '.implode(' AND ', $where) : '')
		  . ($appendSQL ? $appendSQL : '');

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
	 * Get information about the current object's parents
	 *
	 * @param object $obj DBQ_variable object
	 * @return object Object with information about the parents
	 * @access private
	 * @since 1.4
	 */
	function getParentInformation(& $obj) {
	  $sql  = 'SELECT id, name, display_name FROM ' . $this->_parent_tbl . ' p WHERE ';
	  $sql .= " BINARY p.parse REGEXP CONCAT('[<{[]', '$obj->name', '[>}:]') ";
	  $this->_db->setQuery($sql);
	  return $this->_db->loadObjectList();
	} // end getParentInformation()

	/**
	 * List variables used in a query
	 *
	 * @return array Array of queries
	 * @access private
	 * @since 1.4
	 */
	function listAllParentRecordsInUseShort() {
	  return parent::listAllParentRecordsInUseShort(NULL, ' t.uses_vars = 1 ');
	} // end listAllParentRecordsInUseShort()

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
			$where[] = " t.$tableidx = '$id'";
		} else {
			// Handle search criteria
			if (isset($search1) && $search1 != '' ) {
				$search1 = $this->getEscaped($search1);
				$search1 = strtolower(trim($search1));
				$where[] = ' LOWER(t.'.$this->_search_fields[1].' ) LIKE '."'%$search1%' ";
			}
			if (isset($search2) && $search2 != '' ) {
				$search2 = $this->getEscaped($search2);
				$join[] = ', #__dbquery p ';
				//$where[] = " p.id = $search2 AND p.parse REGEXP CONCAT('[[:<:]]', t.name, '[[:>:]]') ";
				$where[] = " p.id = $search2 AND BINARY p.parse REGEXP CONCAT('[<{[]', t.name, '[>}:]') ";
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
		  . " ORDER BY ". (@ $altOrder ? "$altOrder, " : '')." t.ordering, t.type, t.name LIMIT $limitstart,$limit";
		if ($this->debug())
			$this->debugWrite('SQL is '.$sql);

		return $this->listAllObjectsUsingSQL($sql);
	} // end listAllRecords()
	
	/**
	 * List the id and name of Input Fields supported by DBQ
	 * @access public
	 * @return array Input Field Information
	 */
	function listSupportedInputs() {
		$obj =& $this->getInstance();
		$validInputs = array();

		// List all DBQ standard input types
		$standardInputs = $this->_settings->listSupportedInputs();

		// Filter the list by checking if the variable type supports each standard type
		foreach ($obj->listValidInputs() as $input) {
			if ( array_key_exists($input, $standardInputs) )
				 $validInputs[$input] = $standardInputs[$input];
		}

		// Return the list, plus any custom input types that the user may have defined
		$inputs = $this->mashRows2(array_merge($validInputs, $this->_config['INPUT'] ) );
		asort($inputs);
		return $inputs;

	} // end listSupportedInputs()

	/**
	 * List the id and name of Regular Expressions available via DBQ
	 *
	 * @access public
	 * @return array Regex Info
	 * @since 1.0
	 */
	function listSupportedRegex() {
		// Join everything together in one giant sloppy mess
		return $this->mashRows2(array_merge($this->_settings->listRegularExpressions(), $this->_config['EREG'] ) );

	} // end listSupportedRegex()

	/**
	 * Move the object 
	 *
	 * @return boolean True if the order was updated
	 * @access private
	 * @since 1.4
	 */
	function move($inc) {

		$obj =& $this->getObject();
		
		// Update the order
		return $obj->move($inc);
	} // function move()

	/**
	 * Update the variable, saving any substitution values if they exist
	 *
	 * @return boolean True if the variable has been saved
	 * @access private
	 * @since 1.3
	 */
	function adminSave() {
		$obj =& $this->getInstance($_POST);
		
		// Special handling for substitution variables
		if ( $obj->isSubstitutions() && array_key_exists('_substitution_values', $_POST ) && mosGetParam($_POST, '_update_substitutions', false )) 
			// Update the substitution list if the user requests this
			$this->createSubstitutionElements($_POST['_substitution_values']);

		return parent::adminSave();
	} // end save()

	
	/**
	 * Prepare the object and call the template to display a summary
	 * of existing objects in the database
	 * 
	 * This function is responsible for preparing the Admin Summary Screen.
	 * 
	 * In the absence of an $id to show, a list of all objects in the database will 
	 * be displayed using style Joomla's page navigation.  The list may be narrowed 
	 * by providing search criteria, which are
	 * 1) qid - Query ID
	 * 2) vtype - Variable Type
	 * 3) searchv - Search String for the variable's name
	 * 
	 * @global $mainframe, $globals, $dbq_xhtml_path
	 * @param integer $id Optional object id to display alone
	 * @return boolean True
	 * @access public
	 * @since 1.1
	 */
	function adminShow($id = NULL) {
		global $mainframe, $globals, $dbq_xhtml_path;

		// Define useful variables
		$option = $globals->option;

		$limit = $globals->limit;
		$limitstart = $globals->limitstart;
		$queryIdentifier = $this->getIdentifierForObjectType('query');
		$typeIdentifier = 'vtype';
		$qid = $this->getUserStateFromRequest("$queryIdentifier{$option}", $queryIdentifier, '');
		$type = $this->getUserStateFromRequest("$typeIdentifier{$option}", $typeIdentifier, '');
		$search = $this->getUserStateFromRequest("searchv{$option}", 'searchv', '');

		// Create a variable object
		$obj = new DBQ_variable();
		$this->setObject($obj);
		
		// Get rows and calculate results
		//$this->getSearchForVariableWhereClause();
		$where = NULL;

		$total = $id ? 1 : $this->getCountOfAllRecords($search, $qid, $type);
		if ( $total <= $limitstart) $limitstart = 0;
		$rows = $this->listAllRecords($limit, $limitstart, $id, $search, $qid, $type);

		require_once (_DBQ_ADMIN_PAGENAV_PATH);
		$pageNav = new mosPageNav($total, $limitstart, $limit);

		// Make lists of all search fields
		$lists = array ();

		// Search by name
		$lists[_LANG_NAME] = & $this->makeSearchField('searchv', $search);

		// Search by cateogry
		$queries[] = mosHTML :: makeOption('', _LANG_SELECT._LANG_QUERY);
		$queries[] = mosHTML :: makeOption('', _LANG_ALL._LANG_QUERIES);
		foreach ($this->listAllParentRecordsInUseShort() as $k => $v) {
			$queries[] = mosHTML :: makeOption($k, $v);
		}
		$lists[_LANG_QUERY] = & mosHTML :: selectList($queries, $queryIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $qid);

		$types[] = mosHTML::makeOption ('', _LANG_SELECT._LANG_TYPE);
		$types[] = mosHTML::makeOption ('', _LANG_ALL._LANG_TYPES);
		foreach ($this->listSupportedVariableTypes() as $k => $v) {
			$types[] = mosHTML::makeOption($k, $v);
		}
		$lists[_LANG_TYPE] = & mosHTML::selectList($types, $typeIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $type);
		
		// Determine which rows to display

		$headers = array (
			'name' => _LANG_NAME, 
			'display_name' => _LANG_DISPLAY_NAME, 
			'parent' => _LANG_PARENT.' '._LANG_QUERIES, 
			'type' => _LANG_TYPE,
			'input' => _LANG_INPUT,
			'required' => _LANG_REQUIRED, 
			'active' => _LANG_ACTIVE, 
			'stats' => _LANG_STATS, 
			'editor' => _LANG_CHECKED_OUT
		);
		
		$screenName = _LANG_VARIABLE.' '._LANG_CONFIGURATIONS;
		require ($dbq_xhtml_path.'summary.html.php');
		return true;
	} // end show()
	/**
	 * Update the ordering for the object class and object type
	 *
	 * @return boolean True if the order was updated
	 * @access private
	 * @since 1.4
	 */
	function updateOrder() {

		$obj =& $this->getObject();

		// Update the order
		return $obj->updateOrder();
	} // function updateOrder()
	
}
?>
