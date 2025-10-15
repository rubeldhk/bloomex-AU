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
* derived from the Mambo Contacts Component.
* ==================================================================
* $Id: install.contxtd.php, v1.0 2005/06/06
* @package Contacts XTD
* @copyright (C) 2005 Kurt Banfi
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* ==================================================================
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

function com_install() 
{

	/**
	* ContXTD Main Menu Extensions Reminder 
	*/
    echo "<div align='left'>";
    include ("../components/com_contxtd/installnotes.html");
    echo "</div>";
}

?>
