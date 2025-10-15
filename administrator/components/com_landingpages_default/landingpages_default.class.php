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


class mosLandingPages extends mosDBTable {
	/** @var int Primary key */
	var $id 				= null;
	/** @var string */
	var $city 				= null;
	/** @var string */
    var $city_fr 				= null;
    /** @var string */
	var $province			= null;
	/** @var string */
	var $telephone	 		= null;
	/** @var string */
	var $lang 				= null;
	/** @var int */
	var $url 				= null;
	/** @var int */
	var $enable_location	= null;
	/** @var int */
	var $location_address	= null;
	/** @var int */
	var $location_country	= null;
	/** @var int */
	var $location_postcode	= null;
	/** @var int */
	var $location_telephone	= null;
        /** @var string */
	var $title	= null;
        /** @var string */
	var $description	= null;
        /** @var string */
	var $keywords	= null;
    var $title_fr	= null;
    /** @var string */
    var $description_fr	= null;
    /** @var string */
    var $keywords_fr	= null;

	/**
	* @param database A database connector object
	*/

	function __construct() {
		global $database;
		parent::__construct('tbl_landing_pages', 'id', $database );
	}
}

?>