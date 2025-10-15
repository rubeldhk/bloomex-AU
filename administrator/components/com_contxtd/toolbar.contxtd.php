<?php
/**
* ==================================================================
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
* @version $Id: toolbar.contxtd.php,v 1.2 2005/10/21 21:35:22 cubalibre Exp $
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

require_once( $mainframe->getPath( 'toolbar_html' ) );

switch ( $task ) {
	case 'new':
	case 'edit':
	case 'editA':
		/** @CubaLibre: changed class name */ 
		TOOLBAR_contxtd::_EDIT();
		break;

	default:
		/** @CubaLibre: changed class name */ 
		TOOLBAR_contxtd::_DEFAULT();
		break;
}
?>