<?php

/**
 * @package DBQ
 */

//Prevent Direct Access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
DBQ_Settings::includeClassFileForType('DBQ_variable_list');
DBQ_Settings::includeClassFileForType('DBQ_substitution');

/**
 * Represents variables used in a DBQ query
 *
 * @subpackage DBQ_variable_list
 */
class DBQ_variable_substitutions extends DBQ_variable_list {
	
	var $_update_substitutions = false;
	
		
	/**
	 * Constructor class for the DBQ Variable List class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_variable_substitutions($dummy = false) {
		parent::DBQ_variable_list($dummy);
	} // end constructor()
	
	/**
	 * Delete a substitution variable and all substitution elements
	 *
	 * @return boolean True if everything was deleted
	 * @access public
	 * @since 1.4
	 */
	function delete() {
		return ( $this->deleteSubstitutions() && parent::delete() );
	} // end delete()

	/**
	 * Delete all substitution values from a list variable
	 * 
	 * @return boolean True if the delete query executed successfully
	 * @access private
	 * @since 1.4
	 */
	function deleteSubstitutions() {
		
		$sql = 'DELETE FROM '._DBQ_SUBSTITUTIONS_TABLE.' WHERE variable_id = '.$this->id;
		$this->_db->setQuery($sql);
		return $this->_db->query() ? true : $this->logApplicationError("Cannot delete substitution values of variable '$this->name' ($this->id)".$this->_db->getErrorMsg());	
	} // end deleteSubstitutions()
	
	/**
	 * Retrieve the default value 
	 * 
	 * @return String Default Value
	 * @access public
	 * @since 1.2
	 */
	function getDefaultValue() {
		$sql = 'SELECT value FROM '._DBQ_SUBSTITUTIONS_TABLE.
				' WHERE `default` = 1 AND variable_id = '.$this->id;
		$this->_db->setQuery($sql);
		$result = $this->_db->loadRow();

		return $result[0];
	} // end getDefaultValue
	
	/**
	 * Retrieve the list of values for a list variable
	 * 
	 * @return Array List of Substitution Objects to populate lists
	 * @access public
	 * @since 1.1
	 */
	function getList() {
		if ( ! $this->isList() )
			return NULL;
		return $this->_list;
	} // end getList()
		
	function listClassAttributes2() {
		return array('_update_substitutions', '_substitution_values');
	}
	
	/**
	 * Load Substitution values for a Select list variable
	 * 
	 * @access private
	 * @since 1.2
	 */	
	function initialize() {

		// Determine the method of ordering results
		$order = $this->getParamValue('ORDER_BY');
		if ( ! $order )
			$order = 'ordering';

		$sql = 'SELECT * FROM '._DBQ_SUBSTITUTIONS_TABLE." WHERE variable_id = $this->id order by $order";
				
		//echo "sql for loadList() is $sql<br/>";
		$this->_db->setQuery($sql);
		$list =  $this->_db->loadAssocList();

		// Check if we got anything
		if ( !count($list)) 
			return $this->logApplicationError("Cannot retrieve values for substitution '$this->name' ($this->id)");

		// Bind the results to the class object
		$results = array ();
		global $dbq_class_path;
		require_once ($dbq_class_path.'substitution.class.php');
		foreach ($list as $row) {
			$obj = new DBQ_substitution(true);
			$obj->bind($row);
			$results[$obj->id] = $obj;
		}
		$this->_list =& $results;
		return true;
	} // end initialize()

	/**
	 * Store the list object and substitution values to the DBQ database
	 *
	 * @return boolean True if everything stores OK
	 * @access public
	 * @since 1.4
	 */
	function store() {
		
		if ( ! parent::store() )
			return false;

		// Should we update the substitution list?
		if ( $this->shouldSaveSubstitutions() ) {

			// First, wipe out all existing substitution values
			$this->deleteSubstitutions();
				
			// Then, create new elements
			foreach ( $this->_list as $obj ) {
				
				// Test if the substitution element belongs to a different query
				if ( $obj->variable_id != $this->id ) {
					// Create a new substitution element
					$obj->id = NULL;
					$obj->variable_id = $this->id;
				}
				$obj->store() || $this->logApplicationError("Cannot store the substitution element '$obj->name' for variable '$this->name' ($this->id)");
			}
		}

		return true;
	} // end store
	
	
	/**
	 * Set the indicator that will copy substitution valus
	 *
	 * @param boolean $should indicates if substitution values should be copied
	 * @access private
	 * @since 1.4
	 */
	function shouldSaveSubstitutions($should = NULL) {
		if ( ! is_null($should))
			$this->_update_substitutions = $should;
		return $this->_update_substitutions;
	} // end shouldSaveSubstitutions()
}

?>