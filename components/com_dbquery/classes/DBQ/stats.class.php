<?php

/**
 * @package DBQ
 */

/**
 * Prevent Direct Access
 */
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
global $mosConfig_absolute_path;
require_once ($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/settings.class.php');
require_once DBQ_Settings::getPath('class') . 'common.class.php'; 


/**
 * A class to represent configuration information
 *
 * @subpackage DBQ_admin_config DBQ Administrative Configuration Class
 */
class DBQ_stats extends DBQ_common {
	/**#@+
	 * @access private
	 */
	var $query_id = NULL;
	var $variable_id = NULL;
	var $value = NULL;
	var $date_queried = NULL;
	var $variable = NULL;
	var $query = NULL;
	var $count = NULL;
	var $percent = NULL;
	/**#@-*/

	/**
	 * Constructor class for the DBQ Stats class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_stats($dummy = false) {
		// Call our parent
		parent :: DBQ_common(_DBQ_STATS_TABLE, $dummy);
	}

}
?>