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
 * Represents queries stored in the database in the mos_dbquery table
 *
 * @subpackage DBQ_substitutions DBQ class representing queries
 */
class DBQ_substitution extends DBQ_common {

	/**#@+
	 * @access public
	 */
	var $id = NULL;
	var $variable_id = NULL;
	var $value = NULL;
	var $label = NULL;
	var $key = NULL;
	var $ordering = NULL;
	var $checked_out = NULL;
	var $checked_out_time = NULL;
	var $default = 0;
	var $params = NULL;
	/**#@-*/

	/**#@+
	* @access public
	*/
	//var $parent = NULL;
	var $editor = NULL;
	var $parent = NULL;
	/**#@-*/



	/**
	 * Constructor class for the DBQ Substitution class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only a simple skeleton class should be created
	 */
	function DBQ_substitution() {
		// Call our parent
		parent :: DBQ_common(_DBQ_SUBSTITUTIONS_TABLE);
	} // end of DBQ_substitution

	/**
	 * Indicates whether the item should be selected in a drop down menu
	 * 
	 * @param String $value The value to check
	 * @return Boolean True if the item should be selected
	 * @access public
	 * @deprecated 1.3 - Aug 9, 2005
	 * @since 1.2
	 */
	function shouldBeSelected($value) {
		return ( ($this->id == $value) || ($this->value == $value) ) ? true: false;
	} // end shouldBeSelected()
}
?>

