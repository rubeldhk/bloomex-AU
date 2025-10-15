<?php
/**
 * @version		$Id: admin.rokreporter.html.php 8 2007-04-13 04:07:05Z eddieajau $
 * @package		RokReporter
 * @copyright	(C) 2005 - 2007 New Life in IT Pty Ltd. All rights reserved.
 * @license		GNU General Public License
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
 * @package		RokReporter
 */
class ReporterViews
{
	/**
	 * About page
	 */
	function about()
	{
		global $mainframe;

		require_once( $mainframe->getCfg( 'absolute_path' ) . '/includes/patTemplate/patTemplate.php' );

		$tmpl =& patFactory::createTemplate();
		$tmpl->setRoot( JPATH_COMPONENT . '/tmpl' );
		$tmpl->setAttribute( 'body', 'src', 'about.html' );

		$tmpl->displayParsedTemplate( 'body' );
	}
}