<?php

/**
 * @package DBQ
 */

/**
 * Prevent Direct Access
 */
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class

//DBQ_Settings::includeClassFileForType('DBQ_stats');
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/stats.class.php');
require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/common.class.php');


/**
 * A class to represent configuration information
 *
 * @subpackage DBQ_admin_config DBQ Administrative Configuration Class
 */
class DBQ_admin_stats extends DBQ_admin_common {



	/**
	 * Constructor class for the DBQ Stats class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_admin_stats($dummy = false) {
		$this->_identifier = $this->_identifiers['stats'];
		$this->_search_fields = array ('id', 'value', 'variable_id', 'query_id');
		$this->_parent_tbl = _DBQ_VARIABLES_TABLE;
		$this->_parent_tbl_idx = 'variable_id';

		parent :: DBQ_common(_DBQ_STATS_TABLE, $dummy);
	}

	function field($field, $i) {

		
		$obj =& $this->getObject();
		
		switch ($field) {
			case 'variable':
				$url = $this->getUrl('variable').'&task=show&clean=1&vid='.$obj->variable_id;
				parent :: fieldLink($url, $field);				
				break;
			case 'query':
				$url = $this->getUrl('query').'&task=show&clean=1&qid='.$obj->query_id;
				parent :: fieldLink($url, $field);				
				break;
			case 'percent' :
				echo "<td align=\"center\">$obj->percent%</td>";
				break;
			case 'analyze' :
				$url = $this->getUrl('stats').'&task=analyzestats&vid='.$obj->variable_id;
				parent :: fieldLink($url, NULL, $field);
				break;
			default :
				parent :: field($field, $i);
		}
	}
	
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
		$queryIdentifier = $this->getIdentifierForObjectType('query');
		$variableIdentifier = $this->getIdentifierForObjectType('variable');
		$timestartIdentifier = 'timestart';
		$timeendIdentifier = 'timeend';
		
		// Variables to use
		$timestart = $this->getUserStateFromRequest("$timestartIdentifier{$option}", $timestartIdentifier, '-1 year');
		$timeend = $this->getUserStateFromRequest("$timeendIdentifier{$option}", $timeendIdentifier, 'now');		
		if ( !isset($qid) ) $qid = $this->getUserStateFromRequest("$queryIdentifier{$option}", $queryIdentifier, 0);
		if ( !isset($vid) ) $vid = $this->getUserStateFromRequest("$variableIdentifier{$option}", $variableIdentifier, 0);
		$search = $mainframe->getUserStateFromRequest("search{$option}s", 'searchs', '');
		//$search = trim(strtolower($search));
		
		// Convert time specifications to timestamps
		if ($timestart) $timestampstart = strtotime($timestart);
		if ($timeend) $timestampend = strtotime($timeend);
		//echo "start time is $timestampstart and end time is $timestampend<br/>";
		if ( $timestampstart === -1 || $timestampend === -1 ) {
			echo _LANG_INVALID_DATE_RANGE . '<br/>';
		}

		// Get rows and calculate results
		$total = $this->getCountOfAllRecords($search, $vid, $qid, $timestampstart, $timestampend);
		$grandtotal = $this->getGrandTotal($search, $vid, $qid, $timestampstart, $timestampend);
		if ( $total <= $limitstart) $limitstart = 0;
		$rows = $this->listAllStats($grandtotal, $limit, $limitstart, $search, $vid, $qid, $timestampstart, $timestampend);
		require_once (_DBQ_ADMIN_PAGENAV_PATH);
		$pageNav = new mosPageNav($total, $globals->limitstart, $globals->limit);

		// Make list of all search fields
		$lists = array();
		
		// Search by time
		$lists[_LANG_TIME_START] =& $this->makeSearchField($timestartIdentifier,$timestart);
		$lists[_LANG_TIME_END] =& $this->makeSearchField($timeendIdentifier,$timeend);
		
		// Search by query
		$queries[] = mosHTML :: makeOption('', _LANG_SELECT._LANG_QUERY);
		$queries[] = mosHTML :: makeOption('', _LANG_ALL._LANG_QUERIES);
		foreach ($this->listAllQueriesShort() as $k => $v) {
			$queries[] = mosHTML :: makeOption($k, $v);
		}
		$lists[_LANG_QUERY] = mosHTML :: selectList($queries, $queryIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $qid);

		// Search by variable
		$variables[] = mosHTML :: makeOption('', _LANG_SELECT._LANG_VARIABLES);
		$variables[] = mosHTML :: makeOption('', _LANG_ALL._LANG_VARIABLES);
		foreach ($this->listAllVariablesShort($qid) as $k => $v) {
			$variables[] = mosHTML :: makeOption($k, $v);
		}
		$lists[_LANG_VARIABLE] = mosHTML :: selectList($variables, $variableIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $vid);

		// Display our rows
		$headers = array('value' => _LANG_VALUE, 'count' => _LANG_SUBMISSIONS, 'percent' => _LANG_PERCENT, 'query' => _LANG_QUERY, 'variable' => _LANG_VARIABLE);
		$screenName = _LANG_STATS.' '._LANG_INFORMATION;

		include_once ($dbq_xhtml_path.'summary.html.php');
		return true;
	}

/*
	function adminAnalyzeStats() {
		global $mainframe, $globals, $dbq_xhtml_path, $urls;

		// Unix timestamps 
		$timestampstart = NULL;
		$timestampend = NULL;
		
		// Define useful varialbes
		$option = $globals->option;
		$lists = array();

		$timestartIdentifier = 'timestart';
		$timeendIdentifier = 'timeend';	
		$queryIdentifier = $this->getIdentifierForObjectType('query');	
		$variableIdentifier = $this->getIdentifierForObjectType('variable');
		$vid = $this->getUserStateFromRequest("$variableIdentifier{$option}", $variableIdentifier, 0);		
		$qid = $this->getUserStateFromRequest("$queryIdentifier{$option}", $queryIdentifier, 0);
		$timestart = $this->getUserStateFromRequest("$timestartIdentifier{$option}", $timestartIdentifier, '-1 year');
		$timeend = $this->getUserStateFromRequest("$timeendIdentifier{$option}", $timeendIdentifier, 'now');

		// Convert time specifications to timestamps
		if ($timestart) $timestampstart = strtotime($timestart);
		if ($timeend) $timestampend = strtotime($timeend);
		//echo "start time is $timestampstart and end time is $timestampend<br/>";
		if ( $timestampstart === -1 || $timestampend === -1 ) {
			echo _LANG_INVALID_DATE_RANGE . '<br/>';
		}

		$rows = $this->analyzeStatsForVariable($vid, $timestampstart, $timestampend);	

		// Search by time
		$lists[_LANG_TIME_START] =& $this->adminInterfaceMakeSearchField($timestartIdentifier,$timestart);
		$lists[_LANG_TIME_END] =& $this->adminInterfaceMakeSearchField($timeendIdentifier,$timeend);

		// Search by query
		$queries[] = mosHTML :: makeOption('', _LANG_SELECT._LANG_QUERY);
		$queries[] = mosHTML :: makeOption('', _LANG_ALL._LANG_QUERIES);
		foreach ($this->listAllQueriesShort() as $k => $v) {
			$queries[] = mosHTML :: makeOption($k, $v);
		}
		$lists[_LANG_QUERY] = mosHTML :: selectList($queries, $queryIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $qid);
			
		// Search by variable
		$variables[] = mosHTML :: makeOption('', _LANG_SELECT._LANG_VARIABLES);
		$variables[] = mosHTML :: makeOption('', _LANG_ALL._LANG_VARIABLES);
		foreach ($this->listAllVariablesShort($qid) as $k => $v) {
			$variables[] = mosHTML :: makeOption($k, $v);
		}
		$lists[_LANG_VARIABLE] = mosHTML :: selectList($variables, $variableIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $vid);

		$screenName = _LANG_ANALYZE;
		$obj = & $this;
		include_once ($dbq_xhtml_path.'stats.html.php');
		return true;
	}
/*	
	/**
	 * Calculate simple statistcal information for a given variable
	 * 
	 * @param Integer $vid Variable that we are stat'ing
	 * @param Integer $start Time from which we analyse
	 * @param Integer $end Time until which we analyse
	 * @return Array Rows of input with some statistical information
	 * @access public
	 * @since 1.2
	 */
/*
	function analyzeStatsForVariable($vid, $start, $end) {

		if ( $start && $end ) {
			$start = $this->getEscaped($start);
			$end = $this->getEscaped($end);
			$where = " unix_timestamp(date_queried) between '$start' and '$end' ";
		}
				
		$sql = 'SELECT count(s.value) AS count, s.value  FROM '._DBQ_STATS_TABLE.' s '
				. "WHERE $where AND variable_id = $vid "
				.' GROUP BY s.value  ORDER BY count DESC';
		//echo "sql is $sql<br/>";
		if ($this->debug())
			$this->debugWrite('SQL is '.$sql);

		$this->_db->setQuery($sql);
		return $this->_db->loadRowList();
	}
*/	
	/**
	 * Returns of records matching the criteria
	 * Calls the base class function
	 * @param string   Value 
	 * @param integer  Variable ID
	 * @param integer Query ID
	 * @return integer  The number of rows
	 */
	function getCountOfAllRecords($search, $vid, $qid, $start=NULL, $end=NULL) {
		
		$sql = ' group by value';
		$where = array();
		if ( $start && $end ) {
			$start = $this->getEscaped($start);
			$end = $this->getEscaped($end);
			$where[] = " unix_timestamp(date_queried) between '$start' and '$end' ";
		}
		return parent :: getCountOfAllRecords($search, $vid, $qid, $sql, $where);
	}

	/**
	 * Returns of records matching the criteria, but without the GROUP BY
	 * 
	 * Calls the base class function
	 * 
	 * @param string   Value 
	 * @param integer  Variable ID
	 * @param integer Query ID
	 * @return integer  The number of rows
	 */
	function getGrandTotal($search, $vid, $qid, $start=NULL, $end=NULL) {
		$where = array();
		if ( $start && $end ) {
			$start = $this->getEscaped($start);
			$end = $this->getEscaped($end);
			$where[] = " unix_timestamp(date_queried) between '$start' and '$end' ";
		}
		return parent :: getCountOfAllRecords($search, $vid, $qid, '', $where);
	}	

	/**
	 * List previosly user submitted input
	 * 
	 * @param integer limit
	 * @param integer limitstart
	 * @param integer $id ID of object if we wish to list a specific object
	 * @param string $search1
	 * @param string $search2
	 * @param string $search3
	 * @param integer $start
	 * @param integer $end
	 * @return Array List of user submitted input
	 * @access public
	 * @since 1.1
	 */
	function listAllStats($total, $limit = 10, $limitstart = 0, $search1 = NULL, $search2 = NULL, $search3 = NULL, $start=NULL, $end=NULL) {

		$table = $this->_tbl;
		$tableidx = $this->_tbl_key;
		$join = array ();
		$where = array ();
		$select = array ();

		if ( $start && $end ) {
			$start = $this->getEscaped($start);
			$end = $this->getEscaped($end);
			$where[] = " unix_timestamp(date_queried) between '$start' and '$end' ";
		}
		
		// Handle search criteria
		if (@ $search1) {
			$where[] = 'LOWER(s.'.$this->_search_fields[1].") LIKE '%$search1%'";
		}
		if (@ $search2) {
			$where[] = 's.'.$this->_search_fields[2].' = '.$search2;
		}
		if (@ $search3) {
			$where[] = 's.'.$this->_search_fields[3].' = '.$search3;
		}

		//echo "Search is '$search', SearchField is '$searchField', count is " . count($search). "<BR>";
		$sql = 'SELECT count(s.value) AS count, (count(s.value) / '.$total.') * 100 AS percent, s.query_id, s.variable_id, s.value, q.name as query, v.name as variable FROM '._DBQ_STATS_TABLE.' s '.' LEFT JOIN '._DBQ_VARIABLES_TABLE.' v ON v.id = s.variable_id '.' LEFT JOIN '._DBQ_QUERIES_TABLE.' q ON q.id = s.query_id '. (count($where) ? ' WHERE '.implode(' AND ', $where) : '').' GROUP BY s.value '.' ORDER BY query_id, variable_id, count  DESC LIMIT '.$limitstart.','.$limit;
		//echo "sql is $sql<br/>";
		if ($this->debug())
			$this->debugWrite('SQL is '.$sql);

		return $this->listAllObjectsUsingSQL($sql);		
	}

	/**
	 * List variables id and name
	 *
	 * @param integer $qid Query ID to select for
	 * @return array Variable information
	 * @access private
	 * @since 1.0
	 */
	function listAllVariablesShort($id = NULL) {
		$sql = NULL;
		if ($id)
			$sql = ' h.variable_id = t.id AND h.query_id = '.$id.' ';
		return $this->listAllParentRecordsInUseShort(_DBQ_VARIABLES_TABLE, $sql, 'variable_id');
	} // end of listAllVariablesShort()

}
?>
