<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: version.php 1312 2008-03-12 18:39:16Z soeren_nb $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2008 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
if( class_exists( 'vmVersion' ) ) {
	$VMVERSION =new  vmVersion();
	
	$shortversion = $VMVERSION->PRODUCT . " " . $VMVERSION->RELEASE . " " . $VMVERSION->DEV_STATUS. " ";
		
	$myVersion = $shortversion . " [".$VMVERSION->CODENAME ."] <br />" . $VMVERSION->RELDATE . " "
	. $VMVERSION->RELTIME . " " . $VMVERSION->RELTZ;
	return;
}
if( !class_exists( 'vmVersion' ) ) {
/** Version information */
class vmVersion {
	/** @var string Product */
	var $PRODUCT = 'VirtueMart';
	/** @var int Release Number */
	var $RELEASE = '1.0.15';
	/** @var string Development Status */
	var $DEV_STATUS = 'stable';
	/** @var string Codename */
	// Type of Keelboat: http://en.wikipedia.org/wiki/Flying_Fifteen_%28keelboat%29
	var $CODENAME = 'Flying Fifteen';
	/** @var string Date */
	var $RELDATE = 'Mar, 12th 2008';
	/** @var string Time */
	var $RELTIME = '20:30';
	/** @var string Timezone */
	var $RELTZ = 'CET';
	/** @var string Revision */
	var $REVISION = '$Revision: 1312 $';
	/** @var string Copyright Text */
	var $COPYRIGHT = 'Copyright (C) 2005-2008 VirtueMart Development Team. All rights reserved.'; 
	/** @var string URL */
	var $URL = '<a href="http://virtuemart.net">VirtueMart</a> is a Free Component for Joomla!/Mambo released under the GNU/GPL License.';
}
$VMVERSION 		=new  vmVersion();
$shortversion 	= $VMVERSION->PRODUCT . " " . $VMVERSION->RELEASE . " " . $VMVERSION->DEV_STATUS. " ";	
$myVersion 		= $shortversion . " [".$VMVERSION->CODENAME ."] <br />" . $VMVERSION->RELDATE . " "	. $VMVERSION->RELTIME . " " . $VMVERSION->RELTZ;
	
}

?>