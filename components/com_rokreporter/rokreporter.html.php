<?php
/**
 * @version		$Id: rokreporter.html.php 9 2007-04-13 04:08:48Z eddieajau $
 * @package		RokReporter
 * @copyright	(C) 2005 - 2007 New Life in IT Pty Ltd. All rights reserved.
 * @license		GNU General Public License
 */

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct access not allowed' );

/**
 * @package		RokReporter
 */
class ReporterViews
{
	/**
	 * @param string The main template file to include for output
	 * @param array An array of other standard files to include
	 * @return patTemplate A template object
	 */
	function &createTemplate( $bodyHtml, $files=null)
	{
		global $mainframe;

		require_once( JPATH_SITE . '/includes/patTemplate/patTemplate.php' );
		$tmpl =& patFactory::createTemplate( $files );
		$tmpl->setRoot( dirname( __FILE__ ) . '/tmpl' );
		$tmpl->readTemplatesFromInput( 'common.html' );
		$tmpl->addGlobalVar( 'CAPTCHA', JR_CAPTCHA );

		if (preg_match( '//reports//i', $bodyHtml)) {
			$tmpl->setRoot( dirname( __FILE__ ) );
		}
		if ($bodyHtml) {
			$tmpl->setAttribute( 'body', 'src', $bodyHtml );
		}

		return $tmpl;
	}

	/**
	 * About page
	 */
	function about()
	{
		$tmpl =& ReporterViews::createTemplate( 'about.html' );
		$tmpl->displayParsedTemplate( 'body' );
	}

	/**
	 * Report list
	 * @param	array	Data rows
	 * @param	array	An array of data lists
	 * @param	object	Page navigation
	 */
	function reportList( &$rows, &$vars, &$lists, &$pageNav )
	{
		global $Itemid;
		$tmpl =& ReporterViews::createTemplate( 'list.html', array( 'adminlists.html', 'adminfilters.html' ) );

		$tmpl->addVars( 'body', $vars );
		$tmpl->addRows( 'list-items', $rows, 'item_' );

		// setup the page navigation footer
		$pageNav->setTemplateVars( $tmpl, 'index.php?option=com_rokreporter&Itemid=' . $Itemid, 'list-navigation' );

		$tmpl->displayParsedTemplate( 'form' );
	}

	/**
	 * View report
	 * @param	array	Data rows
	 * @param	array	An array of data lists
	 * @param	object	Page navigation
	 */
	function view( &$report, &$vars, &$lists, &$pageNav )
	{
		global $Itemid;
		$tmpl =& ReporterViews::createTemplate( 'view.html', array( 'adminlists.html', 'adminfilters.html' ) );

		$tmpl->addVars( 'body', $vars );
		$tmpl->addObject( 'list-items', $report->data(), 'item_' );

		// setup the page navigation footer
		$pageNav->setTemplateVars( $tmpl, 'index.php?option=com_rokreporter&Itemid=' . $Itemid, 'list-navigation' );

		$tmpl->displayParsedTemplate( 'form' );
	}
}