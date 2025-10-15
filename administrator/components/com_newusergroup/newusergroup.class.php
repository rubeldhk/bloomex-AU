<?php
/**
* @version $Id: banners.class.php 1334 2005-12-07 05:32:52Z eddieajau $
* @package Joomla
* @subpackage Banners
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* @package Joomla
* @subpackage Banners
*/
class mosNewUserGroup extends mosDBTable {
	var	$id 				= null;
	var $departments_name	= '';
	var $area_name 			= '';

	function __construct( &$_db ) {
        parent::__construct( 'tbl_new_user_group', 'id', $_db );
	}
}

?>