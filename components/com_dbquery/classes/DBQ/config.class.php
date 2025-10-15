
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
 * A class to represent configuration information
 *
 * @subpackage DBQ_config DBQ Configuration Class
 */
class DBQ_config extends DBQ_common {

	/**#@+
	 * @access public
	 */
	var $id = NULL;
	var $name = NULL;
	var $type = NULL;
	var $key = NULL;
	var $value = NULL;
	var $ordering = NULL;
	var $description = NULL;
	var $comment = NULL;
	var $checked_out = NULL;
	var $checked_out_time = NULL;
	/**#@-*/

	/**#@+
	   * @access public
	   */
	var $editor = NULL;
	/**#@-*/

	/**
	 * Constructor for the DBQ Config class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only the object framework should be constructed
	 */
	function DBQ_config($dummy = false) {
		// Call our parent
		parent :: DBQ_common(_DBQ_CONFIG_TABLE, $dummy);
	}
}
?>

