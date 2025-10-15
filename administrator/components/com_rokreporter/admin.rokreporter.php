<?php
/**
 * @version		$Id: admin.rokreporter.php 8 2007-04-13 04:07:05Z eddieajau $
 * @package		RokReporter
 * @copyright	(C) 2005 - 2007 New Life in IT Pty Ltd. All rights reserved.
 * @license		GNU General Public License
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ensure user has access to this function
if (!($acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'all' )
		| $acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'com_rokreporter' ))) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}

define( 'JPATH_COMPONENT', dirname( __FILE__ ) );

require_once( $mainframe->getPath( 'admin_html' ) );

/**
 * @package		RokReporter
 */
class ReporterController extends mosAbstractTasker
{
	/**
	 * About
	 */
	function about()
	{
		ReporterViews::about();
	}
}
$tasker = new ReporterController( 'about' );
$tasker->performTask( mosGetParam( $_REQUEST, 'task' ) );
$tasker->redirect();
