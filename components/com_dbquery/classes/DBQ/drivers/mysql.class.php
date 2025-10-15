<?php

/**
 * @package DBQ
 */
 
// Prohibit direct access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
global $dbq_class_path;
require_once ($dbq_class_path.'/driver.class.php');

/**
 * Connect to the MySQL database.
 *
 * @subpackage DBQ_driver_mysql DBQ driver for DBQ
 */
 
class DBQ_driver_mysql extends DBQ_driver {
	
	function DBQ_driver_mysql(&$parent) {
		$this->DBQ_driver($parent);
	}
	

	
	function connect(&$dbinfo) {

		$db =& mysql_connect($dbinfo->hostname, $dbinfo->username, $dbinfo->password);
		if (!$db || !mysql_select_db($dbinfo->schemaname)) {
			$this->logApplicationError(_LANG_DB_CONNECT_FAILED." $dbinfo->name: ".mysql_error());
			return false;					
		}
		
		$this->db =& $db;
		return true;
	}

	/**
	 * Executes a query using the MySQL Interface

	 */
	function execute($query, $limitstart = 0, $limit = 10, $expect_rows=true) {
		$rs = NULL;
		$this->results = array();
		if ($expect_rows && $limit) {
			$rs = mysql_query($query. " LIMIT $limitstart,$limit ");
		} else {
			$rs = mysql_query($query);
		}
		
		// Check for an error
		if (!$rs) {
			$this->logQueryError(_LANG_SQLPROBLEM_EXECUTE.mysql_error($this->db));
			return false;
		}
		
		// Retrieve the results
		$count = NULL;
		if ($expect_rows) {
			while ($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
				$this->results[] =  $row;
			}
			//$this->_query_results =  
			$this->_state = 'X';
			$count = count(@$this->results);
		} else {
			$count = mysql_affected_rows();
		}			
		//print_r($rs); echo "<br/>";
		//print_r(@$this->results);

		// Store the count information
		$this->count = $count;
		return $count;
	} // end execute
	
 	function isSupportedLocally() {
 		return function_exists('mysql_connect') ? true : false;
 	}
}
 ?>