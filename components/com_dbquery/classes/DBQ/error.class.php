<?php

/**
 * @package DBQ
 */

// Prohibit direct access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
global $mosConfig_absolute_path;
require_once ($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/settings.class.php');
require_once 'common.class.php';


/**
 * The errors class provides information about errors generated during the usage of DBQ
 *
 *
 * @subpackage DBQ_Errors errors class
 */
class DBQ_Error extends DBQ_common {
	/**#@+
	 * @access private
	 */
	var $source = NULL;
	var $oid = NULL;
	var $oname = NULL;
	var $uid = NULL;
	var $priority = 0;
	var $date_reported = NULL;
	var $message = NULL;
	var $count = NULL;
	var $percent = NULL;
	/**#@-*/

	/**
	 * Constructor class for the DBQ errors class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_Error($dummy = false) {
		// Call our parent
		parent :: DBQ_common(_DBQ_ERRORS_TABLE, $dummy);
	}


	/**
	 * determine the source of an object listed in the error database
	 *
	 * @param String $source Optional string to work with
	 * @return String 
	 * @access private
	 * @since 1.3
	 */
	function determineObjectSource($source = NULL) {
		$source = ($source) ? $source : $this->source;
		
		// Unrecorded source ?
		if ( !isset($source) )
			return NULL;
		
		// Point to the query object if we're dealing w/ a query related class
		if ( preg_match('/^DBQ_driver/', $source))
			return 'query';
			
		if ( preg_match('/^DBQ_variable_/', $source))
			return 'variable';

		if ( $source == 'DBQ_frontend')
			return 'query';
			
		$results = array();
		if ( preg_match('/^DBQ_(.*)/', $source, $results))
			return $results[1];
		return NULL;
	} // end determineObject Source()

}
?>