<?php

/**
 * @package DBQ
 */

//Prevent Direct Access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
DBQ_Settings::includeClassFileForType('DBQ_variable_list');
DBQ_Settings::includeClassFileForType('DBQ_query');

/**
 * Represents variables used in a DBQ query
 *
 * @subpackage DBQ_variable_list
 */
class DBQ_variable_results extends DBQ_variable_list {
	
	/**
	 * Constructor class for the DBQ Variable List class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_variable_results($dummy = false) {
		parent::DBQ_variable_list($dummy);
	} // end constructor()

	/**
	 * Get data from the database
	 *
	 * @return array Data from the database
	 * @access private
	 * @since 1.4
	 */
	function getData() {
		// Determine our data source
		$source = $this->getParamValue('DATA_SOURCE');
		switch ( $source ) {
		case 'QUERY':
			// Execute the query that provides the data
			$qid = $this->query_id;
			return $this->getDataFromQuery($qid);
		case 'SQL':
		default:
			// Get the sql and execute it against the Joomla DB
			$sql = $this->getParamValue('VARIABLE_SQL', true);
			return $this->getDataFromSQL($sql);
		}
	} // end getData()

	function listClassAttributes1() {
		return array('required', 'input', 'size', 'stats', 'page',);
	}

	function listClassAttributes2() {
		return array('query_id');
	}
	
	/**
	 * Load Substitution values for a Select list variable
	 * 
	 * This function does a lot of important work - querying a table and building a list of potential substitution results.
	 * As of DBQ 1.4, only data within the Joomla database can be queried.
	 *
	 * @return boolean True
	 * @access private
	 * @since 1.4
	 */	
	function initialize() {

		// Get the data to be processed
		$list =& $this->getData();

		// If no data has been returned, log an error and return
		if ( !count($list)) 
			return $this->logApplicationError("Could not initialize the variable '$this->name' ($this->id)");

		// Check if the results give us all the info that we need
		$row = $list[0];
		if ( ! is_array($row) or !( array_key_exists('label', $row) && array_key_exists('value', $row) ) )
			return $this->logUserError("The results variable '$this->name' ($this->id) does not have all the required information (label, value)to make the list");

		// If the results do not contain an 'id' field, then the value will be used as a key instead
		$useValueAsID = ! array_key_exists('id', $row);
		
		// Bind the results to the class object
		$results = array ();
		$listContainsKeys = false;
		DBQ_Settings::includeClassFileForType('DBQ_substitution');
		foreach ($list as $row) {

			// results that don't have labels or valuesare not valid values
			if ( ! $row['label'] || ! $row['value'] )
				continue;

			$obj = new DBQ_substitution(true);
			
			// We didn't select an ID field, so create one
			if ( $useValueAsID )
				$obj->id = $row['value'];
			
			// Bind and add to the list	
			$obj->bind($row);
			
			// Check if we are using onChange
			if ( $obj->key )
				$listContainsKeys = true;
				
			$results[$obj->id] = $obj;
		}
		
		
		// Work on the On Change feature
		$onChangeEnabled = $this->getParamValue('ON_CHANGE_ENABLED');
		$targetVariable = $this->GetParamValue('ON_CHANGE_TARGET_VARIABLE');
		
		// Indicate that this variable targets another variable's select list
		if ( $onChangeEnabled && $targetVariable )
			$this->onChangeIsEnabled(true);

		// Indicate that this variable can be a target of another variable's select list
		if ( $onChangeEnabled && $listContainsKeys ) 
			$this->usesKeysForChange(true);
			
		// Perform a sanity check
		if ( $onChangeEnabled && ! ( $targetVariable or $listContainsKeys ) )
			$this->logUserError("The variable '$this->name' ($this->id) says that 'On Change' is enabled, but there is no target variable and the list does not contain keys");
		
		//echo "$this->name: ". $this->onChangeIsEnabled(). ', ' . $this->usesKeysForChange(). "<br/>";	
		$this->_list =& $results;
		return true;
	} // end initialize()
	
}

?>
