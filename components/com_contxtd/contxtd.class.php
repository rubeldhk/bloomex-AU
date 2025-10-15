<?php
/**
* ==================================================================
* 
* Contacts XTD - Contacts Extended
* 
* Author: Kurt Banfi
* Email: mambo@clockbit.com
* Website: www.clockbit.com
* Version: 1.0.1
* 
* Contacts XTD is a component for Mambo 4.5.2 and 
* derived from the Mambo Contacts Component:
* ==================================================================
* @version $Id: contxtd.class.php,v 1.3 2005/10/21 21:35:22 cubalibre Exp $
* @package Mambo
* @subpackage Contact
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
* ==================================================================
* 
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
* 
* This program is distributed WITHOUT ANY WARRANTY; 
* without even the implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* 
* The "GNU General Public License" (GPL) is available at
* http://www.gnu.org/copyleft/gpl.html
* ==================================================================
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

include_once( $mosConfig_absolute_path .'/includes/vcard.class.php' );
	
/**
* @CubaLibre: changed class name
* @CubaLibre: added/modified variables 
*/

class mosContxtd extends mosDBTable {
	/** @var int Primary key */
	var $id=null;
	/** @var string */
	var $name=null;
	/** @var string */
	var $con_position=null;
	/** @CubaLibre: added var string */
	var $company=null;
	/** @var string */
	var $address=null;
	/** @var string */
	var $suburb=null;
	/** @var string */
	var $state=null;
	/** @var string */
	var $country=null;
	/** @var string */
	var $postcode=null;
	/** @CubaLibre: added var string */
	var $telephone1=null;
	/** @CubaLibre: added var string */
	var $telephone2=null;
	/** @var string */
	var $fax=null;
	/** @CubaLibre: added var string */
	var $mobile1=null;
	/** @CubaLibre: added var string */
	var $mobile2=null;
	/** @CubaLibre: added var string */
	var $website=null;
	/** @CubaLibre: modified var string */
	var $misc1=null;
	/** @var string, added by CubaLibre */
	var $misc2=null;
	/** @var string */
	var $image=null;
	/** @var string */
	var $imagepos=null;
	/** @var string */
	var $email_to=null;
	/** @var int */
	var $default_con=null;
	/** @var int */
	var $published=null;
	/** @var int */
	var $checked_out=null;
	/** @var datetime */
	var $checked_out_time=null;
	/** @var int */
	var $ordering=null;
	/** @var string */
	var $params=null;
	/** @var int A link to a registered user */
	var $user_id=null;
	/** @var int A link to a category */
	var $catid=null;
	/** @var int */
	var $access=null;

	/**
	* @param database A database connector object
	* @CubaLibre: changed funtion name
	* @CubaLibre: changed table name
	*/
	function __construct() {
	    global $database;
        parent::__construct( '#__contxtd_details', 'id', $database );
	}

	function check() {
		$this->default_con = intval( $this->default_con );
		return true;
	}
}

/**
* @package Mambo
* class needed to extend vcard class and to correct minor errors
*/
class MambovCard extends vCard {

	// needed to fix bug in vcard class
	function setName( $family='', $first='', $additional='', $prefix='', $suffix='' ) {
		$this->properties["N"] 	= "$family;$first;$additional;$prefix;$suffix";
		$this->setFormattedName( trim( "$prefix $first $additional $family $suffix" ) );
	}

	// needed to fix bug in vcard class
	function setAddress( $postoffice='', $extended='', $street='', $city='', $region='', $zip='', $country='', $type='HOME;POSTAL' ) {
		// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$key 	= 'ADR';
		if ( $type != '' ) {
			$key	.= ';'. $type;
		}
		$key.= ';ENCODING=QUOTED-PRINTABLE';
		$this->properties[$key] = encode( $postoffice) .';'. encode( $extended ) .';'. encode( $street ) .';'. encode( $city ) .';'. encode( $region) .';'. encode( $zip ) .';'. encode( $country );
	}

	// added ability to set filename
	function setFilename( $filename ) {
		$this->filename = $filename .'.vcf';
	}	

	// added ability to set position/title
	function setTitle( $title ) {
		$title 	= trim( $title );
		$this->properties['TITLE'] 	= $title;
	}

	// added ability to set organisation/company
	function setOrg( $org ) {
		$org 	= trim( $org );
		$this->properties['ORG'] 	= $org;
	}

	function getVCard( $sitename ) {
		$text 	= "BEGIN:VCARD\r\n";
		$text 	.= "VERSION:2.1\r\n";
		foreach( $this->properties as $key => $value ) {
			$text	.= "$key:$value\r\n";
		}
		$text	.= "REV:" .date("Y-m-d") ."T". date("H:i:s"). "Z\r\n";
		$text	.= "MAILER: Contacts XTD vCard for ". $sitename ."\r\n";
		$text	.= "END:VCARD\r\n";
		return $text;
	}
	
}

?>