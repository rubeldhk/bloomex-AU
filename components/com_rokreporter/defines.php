<?php
/**
 * @version		$Id: defines.php 9 2007-04-13 04:08:48Z eddieajau $
 * @package		RokReporter
 * @copyright	(C) 2005 - 2007 New Life in IT Pty Ltd. All rights reserved.
 * @license		GNU General Public License
 */

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct access not allowed' );

if (!defined( 'JPATH_BASE' )) {
	define( 'JPATH_BASE', $GLOBALS['mosConfig_absolute_path'] );
}
if (!defined( 'JPATH_SITE' )) {
	define( 'JPATH_SITE', $GLOBALS['mosConfig_absolute_path'] );
}
if (!defined( 'JPATH_ADMIN' )) {
	define( 'JPATH_ADMIN', JPATH_SITE.DIRECTORY_SEPARATOR.'administrator' );
}

require_once( JPATH_COMPONENT.'/framework/database/query.php' );
require_once( JPATH_COMPONENT.'/framework/filesystem/folder.php' );
require_once( JPATH_COMPONENT.'/framework/filesystem/path.php' );
