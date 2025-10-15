<?php
/**
* @version $Id: contact.class.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Contact
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );


class mosDeliver extends mosDBTable {
	/** @var int Primary key */
	var $id 				= null;
	/** @var string */
	var $type 				= null;
	/** @var string */
	var $name 				= null;
	/** @var string */
	var $options	 		= null;
	/** @var string */
	var $description 		= null;
	/** @var int */
	var $published 			= null;
	/** @var int */
	var $ordering 			= null;

	/**
	* @param database A database connector object
	*/
	function __construct() {
		global $database;
        parent::__construct( 'tbl_options', 'id', $database );
	}
}

class mosDriverOption extends mosDBTable {
	/** @var int Primary key */
	var $id 				= null;
	/** @var string */
	var $service_name		= null;
	/** @var string */
	var $driver_option_type	= null;
	/** @var string */
	var $warehouse_id		= null;
	/** @var string */
	var $description		= null;
        var $email		= null;
	var $number		= null;
	var $driver_name		= null;
	var $login		= null;
	var $password		= null;
	var $created_by		= null;

	/**
	* @param database A database connector object
	*/
	function __construct() {
		global $database;
        parent::__construct( 'tbl_driver_option', 'id', $database );
	}
}

class mosShippingSurcharge extends mosDBTable {
	/** @var int Primary key */
	var $id 				= null;
	/** @var string */
	var $date 			= null;
	/** @var currency */
	var $amount 			= null;

	/**
	* @param database A database connector object
	*/
	function __construct() {
		global $database;
        parent::__construct( 'tbl_shipping_surcharge', 'id', $database );
	}
}

class mosFreeShipping extends mosDBTable {
	/** @var int Primary key */
	var $id 				= null;
	/** @var string */
	var $freedate 			= null;

	/**
	* @param database A database connector object
	*/
	function __construct() {
		global $database;
        parent::__construct( 'tbl_freeshipping', 'id', $database );
	}
}

class mosUnavailableDelivery extends mosDBTable {
	/** @var int Primary key */
	var $id 				= null;
        /** @var string */
	var $available_from_date 			= null;
	/** @var string */
	var $available_until_date 			= null;
	/** @var string */
	var $json_data 			= null;
        /** @var string */
	var $description 			= null;

	/**
	* @param database A database connector object
	*/
	function __construct() {
		global $database;
        parent::__construct( 'tbl_unavailable_delivery', 'id', $database );
	}
}


?>