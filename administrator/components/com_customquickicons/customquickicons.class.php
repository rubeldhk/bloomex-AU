<?php

/**
* @version 1.0
* @package Custom QuickIcons
* @copyright (C) 2005 Halil K�kl� <halilkoklu at gmail dot com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or
  die( 'Direct Access to this location is not allowed.' );

/**
 * @package Custom QuickIcons
 */
class CustomQuickIcons extends mosDBTable {
	/** @var int Primary key */
	var $id					= null;
	/** @var string */
	var $title			= null;
	/** @var string */
	var $target			= null;
	/** @var string */
	var $icon				= null;
	/** @var int */
	var $ordering		= null;
	/** @var int */
	var $new_window	= null;
	/** @var string */
	var $prefix			= null;
	/** @var string */
	var $postfix		= null;
	/** @var int */
	var $published	= null;

  function __construct() {
  	global $database;
      parent::__construct( '#__custom_quickicons', 'id', $database );
  }
  
  function check() {
  	$returnVar = true;
  	
  	if (empty($this->icon)) {
			$this->_error = _QI_MSG_ICON;
  		$returnVar = false;
		}
  	if (empty($this->target)) {
			$this->_error = _QI_MSG_TARGET;
  		$returnVar = false;
		}
  	if (empty($this->title)) {
			$this->_error = _QI_MSG_TITLE;
  		$returnVar = false;
		}
  	
  	return $returnVar;
  }
}
?>