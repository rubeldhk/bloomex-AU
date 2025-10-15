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
class user_visits_Report extends Report
{
	/** @var string The report id (folder name); */ 
	var $id = 'user_visits';

	/**
	 * Runs the report
	 * @param array An array of system variables
	 */
	function run( &$vars )
	{
		// set the default ordering
		if (empty( $vars['orderCol'] )) {
			$vars['orderCol'] = 'name';
		}

		$database = &$this->getDBO();

		$qb = new JQuery;
		$qb->select( 'u.*, COUNT(c.id) AS content_items' );
		$qb->from( '#__users AS u' );
            $qb->join( 'LEFT', '#__content AS c ON c.created_by = u.id' );
            $qb->group( 'u.id' );
		$qb->order( $this->orderBy( $vars['orderCol'], $vars['orderDirn'] ) );

		$this->query( $qb->toString(), $vars );
	}
}