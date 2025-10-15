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
 * Connect to a ADODB database.
 *
 * @subpackage DBQ_driver_adodb DBQ driver for DBQ
 */
 
class DBQ_driver_adodb extends DBQ_driver {
	
	function DBQ_driver_adodb(&$parent) {

		// Try to include the library
		if ( ! include_once ('adodb.inc.php')) {
			return false;
		}

		$this->DBQ_driver($parent);
		return true;
	} // end constructor DBQ_driver_adodb()
	
	function close() {
		global $mainframe;
		
		// Reset the ADODB connection if the database type is mysql
		if ( $this->dbinfo->type == 'mysql' || $this->dbinfo->type == 'mysqli') {
			parent::close();
		} else {
			return $this->db->Close();
		}
	}
	
	function connect(&$dbinfo) {
		$this->dbinfo =& $dbinfo;

		// Get connection information
		$type		= rawurlencode($this->dbinfo->type);
		$username	= rawurlencode($this->dbinfo->username);
		$password	= rawurlencode($this->dbinfo->password);
		$hostname	= rawurlencode($this->dbinfo->hostname);
		$schemaname	= rawurlencode($this->dbinfo->schemaname);

		// Make the DSN
		$dsn = $type.'://'.$username.':'.$password.'@'.$hostname.'/'.$schemaname;
		if (@$row->debug) $dsn .= '?debug';
		$db =& ADONewConnection($dsn);
		if (!$db) {
			$this->logApplicationError(_LANG_DB_CONNECT_FAILED." $dbinfo->name: ".'');
			return false;					
		}
		$this->db = $db;
		return true;
	}

	/**
	 * Executes a query using the MySQL Interface

	 */
	function execute($query, $limitstart = 0, $limit = 10, $expect_rows=true) {
		$rs = NULL;
		$this->results = array();
		$db = & $this->db;
		$db->SetFetchMode(ADODB_FETCH_ASSOC);
		$res = NULL;

		// Execute the query
		if ($expect_rows && $limit) {
			$res = $db->SelectLimit($query, $limit, $limitstart);
		} else {
			$res = $db->query($query);
		}

		// Check for an error
		if (!$res) {
			$this->logQueryError(_LANG_SQLPROBLEM_EXECUTE." $this->name: ".$db->ErrorMsg());
			return false;
		}

		// Retrieve the results
		$count = NULL;
		if ($expect_rows) {
			$this->results = $res->GetAll();
			$count = count(@ $this->results);
		} else {
			$count = $db->Affected_Rows();
		}
		
		// Close the result set and return the count
		
		$res->Close();
		$this->count = $count;
		return $count;
	} // end execute
	
	function getErrorMessage() {
		return $this->db->ErrorMsg();
	}
	
	 function isSupportedLocally() {
 		return @(include_once 'adodb.inc.php' ) ? true : false;
 	}
}
 ?>
