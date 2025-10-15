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
 * @subpackage DBQ_driver_pear DBQ driver for DBQ
 */
 
class DBQ_driver_pear extends DBQ_driver {
	
	function DBQ_driver_pear(&$parent) {
		$this->DBQ_driver($parent);
	}
	
	function connect(&$dbinfo) {
		$dsn = $dbinfo->type.'://'.$dbinfo->username.':'.$dbinfo->password.'@'.$dbinfo->hostname.'/'.$dbinfo->schemaname;
		$options = array ('persistent' => $dbinfo->persistent, 'debug' => $dbinfo->debug);
		$db = NULL;
		if (include_once ('DB.php')) {
			$db = & DB :: connect($dsn, $options);
		} else {
			$this->logApplicationError(_LANG_DB_CONNECT_FAILED." $dbinfo->name");
		}
		if (DB :: iserror($db)) {
			$this->logApplicationError(_LANG_DB_CONNECT_FAILED." $dbinfo->name: ".$db->getMessage());
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

		$db = & $this->db;
		$db->setFetchMode(DB_FETCHMODE_ASSOC);
		$res = NULL;

		// Execute the query
		//echo "query is $query<br/>";
		//echo "$expect_rows && $limit<br/>";
		if ($expect_rows && $limit) {
			$res = $db->limitQuery($query, $limitstart, $limit);
		} else {
			$res = $db->query($query);
		}

		// Check for an error
		if (DB :: iserror($res)) 
			return $this->logQueryError(_LANG_SQLPROBLEM_EXECUTE.$this->parent->name.': '.$res->getMessage());

		// Retrieve the results
		$count = NULL;
		if ($expect_rows) {
			while ($row = $res->fetchRow()) {
				$this->results[] = $row;
			}
			$count = count(@ $this->results);
		} else {
			$count = $db->affectedRows();
		}
		
		$this->count = $count;
		return $count;
	} // end execute

	function getErrorMessage() {
		return $this->db->ErrorMsg();
	}

 	function isSupportedLocally() {
 		return @(include 'DB.php' ) ? true : false;
 	}
}
?>