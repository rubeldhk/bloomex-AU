<?php
/**
 * @version		$Id: toolbar.rokreporter.php 8 2007-04-13 04:07:05Z eddieajau $
 * @package		RokReporter
 * @copyright	(C) 2005 - 2007 New Life in IT Pty Ltd. All rights reserved.
 * @license		GNU General Public License
 */
// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
 * Toolbar class
 * @package		RokReporter
 */
class ReporterToolbar extends mosAbstractTasker
{
	/**
	 * About
	 */
	function about()
	{
		mosMenuBar::startTable();
		mosMenuBar::help( 'index', true );
		mosMenuBar::endTable();
	}
}

$controller = new ReporterToolbar( 'about' );
$controller->performTask( mosGetParam( $_REQUEST, 'task' ) );
