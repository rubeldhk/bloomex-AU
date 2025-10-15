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
 * Connect to the Joomla database.
 *
 * @subpackage DBQ_driver_joomla DBQ driver for DBQ
 */
 
class DBQ_driver_joomla extends DBQ_driver {
	
	function DBQ_driver_joomla(&$parent) {
		$this->DBQ_driver($parent);
	}
	
	function connect(&$dbinfo) {
		return true;
	}

	function execute($query, $limitstart = 0, $limit = 10, $expect_rows=true) {
		$rs = NULL;
		$db = $this->_db;
		
		if ($expect_rows && $limit) 
			$query = $query . " LIMIT $limitstart,$limit ";

		$db->setQuery($query);
		
		// Retrieve the results
		if ($expect_rows) {
			$this->results = $db->loadAssocList();
			$this->count = count($this->results);
		} else {
			$this->results = $db->query();
			$this->count = $db->getAffectedRows();
		}

		// Check for an error
		if ($db->getErrorNum()) {
			$this->logQueryError(_LANG_SQLPROBLEM_EXECUTE.': '.$db->getErrorMsg());
			return false;
		}
		
		$this->_state = 'X';
		return ($this->count) ? $this->count : 0;
	} // end execute
	
	 function isSupportedLocally() {
 		return true;
 	}
}
 ?>
