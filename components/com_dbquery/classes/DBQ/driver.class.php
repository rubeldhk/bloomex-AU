<?php

/**
 * @package DBQ
 */

// Prohibit direct access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
global $dbq_class_path;
require_once ($dbq_class_path.'common.class.php');

/**
 * A base class for DBQ Drivers
 *
 * @subpackage DBQ_driver Drivers used to connect to interfaces supported by DBQ
 */
 
class DBQ_driver extends DBQ_common {

	// Database object or resource
	var $count = NULL;
	var $db = NULL;
	var $debug = NULL;
	var $expect_rows = NULL;
	var $type = 'SQL';
	var $results = NULL;
	var $dbinfo = NULL;
	var $parent = NULL;
	var $id = NULL;
	var $name = NULL;

	/**
	 * Base constructor class for dbq
	 */
	function DBQ_driver(&$parent) {
		$this->parent =& $parent;
		$this->name = $parent->name;
		$this->id = $parent->id;
		return $this->DBQ_common(_DBQ_DRIVERS_TABLE, false);
	}
	
 	/**
 	 * Close the connection to the remote database
 	 */
	function close() {
		// Reset the Joomla connection
		global $mainframe;
		$host = $mainframe->getCfg('host');
		$user = $mainframe->getCfg('user');
		$password = $mainframe->getCfg('password');
		$database = $mainframe->getCfg('db');
		//echo "reconnect string is $host, $user, $password, $database<br/>";
		return mysql_connect($host, $user, $password) && mysql_select_db($database);
	}
	
	/*
 	function close() {
 		return true;
 	} // end close()
 	*/
 	
 	/**
  	 * Create a connection to the remove database
  	 */
 	function connect(&$dbinfo) {
 		$this->dbinfo =& $dbinfo;
 	} // end connect()
 	
 	/**
 	 * Count the total number of potential matches
 	 * 
 	 * @param string $query Original query string
 	 * @access private
 	 * @since 1.2
 	 */
 	function countTotal($query, $modification) {
 		
 		// Determine the count query	
		//if (!preg_match('/(.*)[[:space:]]FROM[[:space:]](.*)/i', $query, $results)) {
		if (!preg_match('/[[:space:]]FROM[[:space:]](.*)/i', $query, $results)) {
			$this->logApplicationError(_LANG_SQLPROBLEM_PARSE_FROM.$query);
			return false;
		}
		//$query2 = $modification.' '.$results[2];
		$query2 = $modification.' '.$results[1];
		
		// Call the query
		$this->execute($query2, 0, 0, true);
		$res =& $this->getResults();
		
		// Calculate and return the results
		$sum = count($res);
		//echo "sum is: $sum<br/>";
		//print_r($res);
		if ($sum == 1) {
			// single result, so return it
			return $res[0]['COUNT'];
		} else {
			// multiple results, possibly due to a GROUP BY clause
			return $sum;
		}
 	} // end countTotal()
 	
	/**
	 * Execute a query against the remote database
	 * 
	 * @param string $query the SQL to execute
	 * @param integer $limitstart Specifies the starting point for any result set
	 * @param integer $limit Specifies the number of requested results
	 * @param boolean $expectrows Inidcates whether or not to expect rows from the query
	 * @return integer The number of rows matched	 
	 * @access private
	 * @since 1.2
	 */
 	function execute($query, $limitstart = 0, $limit = 10, $expect_rows=true) {
 		$this->count = $count;
 	} // end execute

	/**
	 * Return the error message from the driver
	 * 
	 * @access private
	 * @since 1.2
	 */
	function getErrorMessage() {
		$this->logApplicationError('The function getErrorMessage() has not been overridden by the specific driver!');
	} // end getErrorMessage()

	/* Identifies the interface type
	 * 
	 * @return String Identifying string for the interface
	 * @access public
	 * @since 1.3
	 */
	function getInterfaceType() {
		return $this->type;
	} // end getInterfaceType
	
 	/**
 	 * Return the results from the a query's execute
 	 */
 	function getResults() {
 		return @ $this->results;
 	} // end getResults
 	
 	/**
 	 * Indicates whether the local machine supports the interface 
 	 * by having the neccessary libraries, files, etc...
 	 * 
 	 * This function must be overridden by the driver and should return true
 	 * if the the driver is supported
 	 * 
 	 * @return Boolean True if the driver is supported
 	 * @access public
 	 * @since 1.1
 	 */
 	function isSupportedLocally() {
 		return false;
 	}
 }
 
 ?>
