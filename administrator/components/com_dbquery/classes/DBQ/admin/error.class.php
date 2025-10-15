<?php

/**
 * @package DBQ
 */

/**
 * Prevent Direct Access
 */
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class

//DBQ_Settings::includeClassFileForType('DBQ_Error');
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/error.class.php');
require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/common.class.php');


/**
 * The errors class provides information about errors generated during the usage of DBQ
 *
 *
 * @subpackage DBQ_Errors errors class
 */
class DBQ_admin_error extends DBQ_admin_common {

	/**
	 * Constructor class for the DBQ errors class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_admin_error($dummy = false) {
		$this->_search_fields = array ('uid', 'message', 'priority', 'oid');
		// Call our parent
		parent :: DBQ_admin_common(_DBQ_ERRORS_TABLE, $dummy);
		$this->_tbl_key = 'priority';
	}

	/**
	 * Helper function to print the admin interface
	 *
	 * @param String $field
	 * @param Iterator $i
	 */
	function field($field, $i) {

		
		$obj =& $this->getObject();
		
		switch ($field) {
			case 'source' :
				$source = $obj->determineObjectSource();
				if ( $source ) {
					$url = $this->getUrl($source).'&task=show&clean=1';
					parent :: fieldLink($url, $field);				
				} else {
					parent :: field($field, $i);
				}
				break;
			case 'oname' :
				$source = $obj->determineObjectSource();
				if ( $source ) {
					$id = $this->getIdentifierForObjectType($source);
					$url = $this->getUrl($source)."&task=show&clean=1&$id=$obj->oid";
					parent :: fieldLink($url, $field);
				} else {
					parent :: field($field, $i);
				}
				break;
			case 'message':
				$libover = $this->makeLibOver($obj->message);
				$short = substr($obj->message, 0, 100);
				echo "<td><span $libover>$short</span></td>";				
				break;
			case 'priority':
				$libover = $this->makeLibOver($this->getInternalErrorComment($obj->priority));
				echo "<td align=\"center\"><span $libover>".$this->getInternalErrorString($obj->priority).'</span></td>';				
				break;
			default :
				parent::field($field, $i);
		}
	} // end field()

	
	/**
	 * List the id and name of Error Codes available via DBQ
	 * 
	 * @access public
	 * @return array Error Codes
	 * @since 1.3
	 */
	function listSupportedErrorCodes() {
		return $this->mashRows2($this->_settings->listErrorCodes());
	} // end listSupportedErrorCodes()
		
	function adminShow($vid = NULL,$qid = NULL) {
		global $mainframe, $globals, $dbq_xhtml_path;

		// Make an object
		$obj =& $this->getInstance();
		
		// Unix timestamps 
		$timestampstart = NULL;
		$timestampend = NULL;
		
		// Define useful varialbes
		$option = $globals->option;
		$limit = $globals->limit;
		$limitstart = $globals->limitstart;
		
		// Identifiers for variables
		$messageIdentifier = 'DBQErrorClassMessage';
		$priorityIdentifier = 'DBQErrorClassPriority';
		$objectIdentifier = 'oid';
		$sourceIdentifier = 'source';
		$useridIdentifier = 'DBQUserID';
				
		$timestartIdentifier = 'timestart';
		$timeendIdentifier = 'timeend';
		
		// Variables to use
		$timestart = $this->getUserStateFromRequest("$timestartIdentifier{$option}", $timestartIdentifier, '-1 week');
		$timeend = $this->getUserStateFromRequest("$timeendIdentifier{$option}", $timeendIdentifier, 'now');		

		$message = $this->getUserStateFromRequest("$messageIdentifier{$option}", $messageIdentifier, '');	
		$priority = $this->getUserStateFromRequest("$priorityIdentifier{$option}", $priorityIdentifier, '');	
		$objectid = $this->getUserStateFromRequest("$objectIdentifier{$option}", $objectIdentifier, '');	
		$source = $this->getUserStateFromRequest("$sourceIdentifier{$option}", $sourceIdentifier, '');	
		//$ = $this->getUserStateFromRequest("$useridIdentifier{$option}", $useridIdentifier, '');	
		
		// Convert time specifications to timestamps
		if ($timestart) $timestampstart = strtotime($timestart);
		if ($timeend) $timestampend = strtotime($timeend);
		//echo "start time is $timestampstart and end time is $timestampend<br/>";
		if ( $timestampstart === -1 || $timestampend === -1 ) {
			echo _LANG_INVALID_DATE_RANGE . '<br/>';
		}

		// Get rows and calculate results
		$total = $this->getCountOfAllRecords($source, $message, $priority, $objectid, $timestampstart, $timestampend);
		if ( $total <= $limitstart) $limitstart = 0;

		$rows = $this->listAllRecords($limit, $limitstart, $source, $message, $priority, $objectid, $timestampstart, $timestampend);
		require_once (_DBQ_ADMIN_PAGENAV_PATH);
		$pageNav = new mosPageNav($total, $globals->limitstart, $globals->limit);

		// Make list of all search fields
		$lists = array();

		// Search by time
		$lists[_LANG_TIME_START] =& $this->makeSearchField($timestartIdentifier,$timestart);
		$lists[_LANG_TIME_END] =& $this->makeSearchField($timeendIdentifier,$timeend);
		$lists[_LANG_MESSAGE] =& $this->makeSearchField($messageIdentifier,$message);
		//$lists[_LANG_PRIORITY] =& $this->adminInterfaceMakeSearchField($priorityIdentifier,$priority);
		//$lists[_LANG_SOURCE] =& $this->adminInterfaceMakeSearchField($sourceIdentifier,$source);

		// Search by source
		$sources[] = mosHTML :: makeOption('', _LANG_SELECT._LANG_SOURCE);
		$sources[] = mosHTML :: makeOption('', _LANG_ALL._LANG_SOURCE);
		foreach ($this->listAllSources() as $k => $v) {
			$sources[] = mosHTML :: makeOption($k, $v);
		}
		$lists[_LANG_SOURCE] = mosHTML :: selectList($sources, $sourceIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $source);

		// Search by objectid
		$objects[] = mosHTML :: makeOption('', _LANG_SELECT._LANG_OBJECT);
		$objects[] = mosHTML :: makeOption('', _LANG_ALL._LANG_OBJECT);
		foreach ($this->listAllObjects($source) as $k => $v) {
			$objects[] = mosHTML :: makeOption($k, $v);
		}
		$lists[_LANG_OBJECT] = mosHTML :: selectList($objects, $objectIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $objectid ? $objectid : 9999999);

		// Search by priority
		$errors[] = mosHTML :: makeOption('', _LANG_SELECT._LANG_PRIORITY);
		$errors[] = mosHTML :: makeOption('', _LANG_ALL._LANG_PRIORITY);
		foreach ($this->listSupportedErrorCodes() as $k => $v) {
			$errors[] = mosHTML :: makeOption($k, $v);
		}
		$lists[_LANG_PRIORITY] = mosHTML :: selectList($errors, $priorityIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $priority);


		// Display our rows
		$headers = array('source' => _LANG_SOURCE, 'oname' => _LANG_OBJECT, 'priority' => _LANG_PRIORITY, 'message' => _LANG_MESSAGE, 'date_reported' => _LANG_DATE_REPORTED);
		$screenName = _LANG_ERRORS.' '._LANG_INFORMATION;

		include_once ($dbq_xhtml_path.'summary.html.php');
		return true;
	} // end adminShow()

	
	/**
	 * Returns of records matching the criteria
	 * 
	 * Calls the base class function
	 * @return integer The number of rows matched
	 */
	function getCountOfAllRecords($source, $message, $priority, $oid,  $start=NULL, $end=NULL) {
		
		$where = array();
		
		if ( $start && $end ) {
			$start = $this->getEscaped($start);
			$end = $this->getEscaped($end);
			$where[] = " unix_timestamp(date_reported) between '$start' and '$end' ";
		}
		if ( $source )
			$where[] = " source = '$source' ";
		
		//return parent :: getCountOfAllRecords($search, $vid, $qid, $sql, $where);
		return parent :: getCountOfAllRecords($message, $priority, $oid, null, $where);
	} // end getCountOfAllRecords

	/**
	 * List all objects with errors
	 *
	 * @return Array
	 * @access protected
	 * @since 1.3
	 */
	function listAllObjects($source) {
		$sql = 'SELECT DISTINCT oid, oname FROM '._DBQ_ERRORS_TABLE;
		if ( $source )
			$sql .= " WHERE source = '$source'";
		$this->_db->setQuery($sql);
		$rows =& $this->_db->loadAssocList();
		$results = array();
		foreach ($rows as $row) {
			$results[$row['oid']] = $row['oname'];
		}
		return $results;
	} // end listAllSources();
	
	/**
	 * List previosly user submitted input
	 * 
	 * @param integer limit
	 * @param integer limitstart
	 * @param string $source Identifier for the class of errors to list
	 * @param string $search1
	 * @param string $search2
	 * @param string $search3
	 * @param integer $start
	 * @param integer $end
	 * @return Array List of user submitted input
	 * @access protected
	 * @since 1.3
	 */
	function listAllRecords($limit = 10, $limitstart = 0, $source, $search1 = NULL, $search2 = NULL, $search3 = NULL, $start=NULL, $end=NULL) {

		$where = array ();

		if ( $start && $end ) {
			$start = $this->getEscaped($start);
			$end = $this->getEscaped($end);
			$where[] = " unix_timestamp(date_reported) between '$start' and '$end' ";
		}
		if ( $source )
			$where[] = " source = '$source' ";
		
		$table = $this->_tbl;
		$tableidx = $this->_tbl_key;
		$join = array ();
		$select = array ();
				
		//$select[] = 'u.name as editor ';
		//$join[] = 'LEFT JOIN '._DBQ_USERS_TABLE.' u ON u.id = t.checked_out ';

		// Escape all input -- Trust no one
		$limit = $this->getEscaped($limit);
		$limitstart = $this->getEscaped($limitstart);
/*		
		foreach ($this->_additional_select as $sql) {
			$select[] = $sql[0];
			$join[] = $sql[1];
		}

		if ($this->_parent_tbl && $idx = $this->_parent_tbl_idx) {
			$select[] = ' p.name as parent ';
			$join[] = ' LEFT JOIN '.$this->_parent_tbl." p ON p.id = t.$idx ";
		}
*/
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


		// Order by additional criteria
		$altOrder = @ $this->_search_fields[2];

		//echo "Search is '$search', SearchField is '$searchField', count is " . count($search). "<BR>";
		$sql = 'SELECT t.* '. (count($select) ? ', '.implode(', ', $select) : '')." FROM $table t ". (count($join) ? implode(' ', $join) : ''). (count($where) ? ' WHERE '.implode(' AND ', $where) : '')." ORDER BY ". (@ $altOrder ? "$altOrder DESC, " : '')." date_reported DESC LIMIT $limitstart,$limit";
		
		//echo "SQL is $sql<br/>";
		if ($this->debug())
			$this->debugWrite('SQL for list errors is '.$sql);

		return $this->listAllObjectsUsingSQL($sql);

	} // end listAllRecords

	/**
	 * List all sources of errors
	 *
	 * @return Array
	 * @access protected
	 * @since 1.3
	 */
	function listAllSources() {
		$sql = 'SELECT DISTINCT source FROM '._DBQ_ERRORS_TABLE;
		$this->_db->setQuery($sql);
		$rows =& $this->_db->loadResultArray();
		$results = array();
		foreach ($rows as $row) {
			$results[$row] = $row;
		}
		return $results;
	} // end listAllSources();
}
?>