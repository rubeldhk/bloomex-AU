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
class ua_platforms_Report extends Report
{
	/** @var string The report id (folder name); */ 
	var $id = 'ua_platforms';

	/**
	 * Runs the report
	 * @param array An array of system variables
	 */
	function run( &$vars )
	{
		// set the default ordering
		if (empty( $vars['orderCol'] )) {
			$vars['orderCol'] = 'agent';
		}

		$database = &$this->getDBO();

		$qb = new JQuery;
		$qb->select( '*' );
		$qb->from( '#__stats_agents AS a' );
		$qb->where( 'a.type = 1' );
		$qb->order( $this->orderBy( $vars['orderCol'], $vars['orderDirn'] ) );

		// loads the main data
		$this->query( $qb->toString(), $vars );

		// select total hits
		$qb = new JQuery;
		$qb->select( 'SUM( hits )' );
		$qb->from( '#__stats_agents AS a' );
		$qb->where( 'a.type = 1' );

		$database->setQuery( $qb->toString() );
		$totalhits = $database->loadResult();

		// preprocess rows to calculated percentages
		$rows = &$this->data();

		$nRows = count( $rows );
		for ($i = 0; $i < $nRows; $i++) {
			$row = &$rows[$i];
			$percent = $row->hits > 0 ? $row->hits / $totalhits * 100.0 : 0.0;
			$row->percent = sprintf( '%.1f', $percent );
		}
	}
}
