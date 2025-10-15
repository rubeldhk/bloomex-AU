<?php

/**
 * @package DBQ
 */

/**
 * Prevent Direct Access
 */
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
global $dbq_class_path;
require_once ($dbq_class_path.'common.class.php');

/**
 * A class to represent a database connection
 *
 * This class provides connection information used to connect 
 * to a database, whether local or remote.
 *
 * @subpackage DBQ_database DBQ Database Class
 */
class DBQ_database extends DBQ_common {

	/**#@+
	 * @access public
	 */
	var $id = NULL;
	var $name = NULL;
	var $type = NULL;
	var $driver = NULL;
	var $persistent = NULL;
	var $debug = NULL;
	var $hostname = NULL;
	var $schemaname = NULL;
	var $username = NULL;
	var $password = NULL;
	var $on_line = NULL;
	var $ordering = NULL;
	var $archived = NULL;
	var $description = NULL;
	var $comment = NULL;
	var $checked_out = NULL;
	var $checked_out_time = NULL;
	var $params = NULL;
	var $editor = NULL;
	/**#@-*/

	/**#@+
	 * @access private
	 */
	var $_params = NULL;
	var $_params_txt = NULL;
	/**#@-*/

	/**
	 * Constructor for the DBQ Database class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only the object framework should be constructed
	 */
	function DBQ_database($dummy = false) {
		// Call our parent
		$this->DBQ_common(_DBQ_DATABASES_TABLE, $dummy);
	}

}
?>

