<?php
/**
* ==================================================================
* Contacts XTD - Contacts Extended Searchbot
* 
* Author: Kurt Banfi
* Email: mambo@clockbit.com
* Website: www.clockbit.com
* Version: 1.0.1
* 
* Contacts XTD is a component for Mambo 4.5.2 and 
* derived from the Mambo Contacts Component:
* ==================================================================
* @version $Id: contxtd.searchbot.php,v 1.2 2005/10/21 21:34:23 cubalibre Exp $
* @package Mambo
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
* http://www.gnu.org/copyleft/gpl.htmls
* ==================================================================
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onSearch', 'botSearchContactsXTD' );

/**
* Contacts XTD Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
* @param string Target search string
* @param string mathcing option, exact|any|all
* @param string ordering option, newest|oldest|popular|alpha|category
*/

/** @CubaLibre: changed function name */
function botSearchContactsXTD( $text, $phrase='', $ordering='' ) {
	global $database, $my;

     $text = trim( $text );
	if ($text == '') {
		return array();
	}

	$section = _CONTACT_TITLE;

	switch ( $ordering ) {
		case 'alpha':
			$order = 'a.name ASC';
			break;
		case 'category':
			$order = 'b.title ASC, a.name ASC';
			break;
		case 'popular':
		case 'newest':
		case 'oldest':
		default:
			$order = 'a.name DESC';
	}

	/** @CubaLibre
	 * changed component name
	 * changed table name
	 * added additional values
	 */
	$query = "SELECT a.name AS title,"
	. "\n CONCAT_WS( ', ', a.name, a.con_position, a. company, a.misc1 ) AS text,"
	. "\n '' AS created,"
	. "\n CONCAT_WS( ' / ', '$section', b.title ) AS section,"
	. "\n '2' AS browsernav,"
	. "\n CONCAT( 'index.php?option=com_contxtd&task=view&&contact_id=', a.id ) AS href"
	. "\n FROM #__contxtd_details AS a"
	. "\n INNER JOIN #__categories AS b ON b.id = a.catid AND b.access <= '$my->gid'"
	. "\n WHERE ( a.name LIKE '%$text%'"
	. "\n OR a.misc1 LIKE '%$text%'"
	. "\n OR a.misc2 LIKE '%$text%'"
	. "\n OR a.con_position LIKE '%$text%'"
	. "\n OR a.company LIKE '%$text%'"
	. "\n OR a.website LIKE '%$text%'"
	. "\n OR a.address LIKE '%$text%'"
	. "\n OR a.suburb LIKE '%$text%'"
	. "\n OR a.state LIKE '%$text%'"
	. "\n OR a.country LIKE '%$text%'"
	. "\n OR a.postcode LIKE '%$text%'"
	. "\n OR a.telephone1 LIKE '%$text%'"
	. "\n OR a.telephone2 LIKE '%$text%'"
	. "\n OR a.fax LIKE '%$text%' )"
	. "\n OR a.mobile1 LIKE '%$text%'"
	. "\n OR a.mobile2 LIKE '%$text%'"
	. "\n AND a.published = '1'"
	. "\n ORDER BY $order"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	return $rows;
}
?>