<?php
/**
 * @version		$Id: report.php 9 2007-04-13 04:08:48Z eddieajau $
 * @package		RokReporter
 * @copyright	(C) 2005 - 2007 New Life in IT Pty Ltd. All rights reserved.
 * @license		GNU General Public License
 */

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct access not allowed' );

/**
 * Reporting class
 * @package		RokReporter
 */
class content_edits_Report extends Report
{
	/** @var string The report id (folder name); */ 
	var $id = 'content_edits';

	/**
	 * Runs the report
	 * @param array An array of system variables
	 */
	function run( &$vars )
	{
		// set the default ordering
		if (empty( $vars['orderCol'] )) {
			$vars['orderCol'] = 'modified';
		}
		if ($vars['orderDirn'] === null) {
			$vars['orderDirn'] = 1;
		}

		$database = &$this->getDBO();

		$qb = new JQuery;
		$qb->select( 'a.id, a.title, u.name AS owner, a.modified, a.hits' );
		$qb->from( '#__content AS a' );
		$qb->join( 'LEFT', '#__users AS u ON u.id = a.created_by' );
		$qb->order( $this->orderBy( $vars['orderCol'], $vars['orderDirn'] ) );

		$this->query( $qb->toString(), $vars );
	}
}