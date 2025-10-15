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
class export_csv_Report extends Report
{
	/** @var string The report id (folder name); */ 
	var $id = 'export_csv';

	/**
	 * Custom report tasks
	 * @param string The task
	 */
	function tasker( $task = '' )
	{
		$tasker = new reportTasks();
		$tasker->performTask( $task );
		$tasker->redirect();
	}

	/**
	 * Runs the report
	 * @param array An array of system variables
	 */
	function run( &$vars )
	{
		// set the default ordering
		if (empty( $vars['orderCol'] )) {
			$vars['orderCol'] = 'search_term';
		}

		$database = &$this->getDBO();
		$tables = $database->getTableList();

		// convert to object list first
		$nTables = count( $tables );
		for ($i = 0; $i < $nTables; $i++) {
			$o = new stdClass;
			$o->name = $tables[$i];
			$tables[$i] = $o;
		}

		$this->data( $tables );
	}
}

/**
 * Custom report tasks
 * @package		RokReporter
 */
class reportTasks extends mosAbstractTasker
{
	/**
	 * Constructor
	 */
	function reportTasks()
	{
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker();

		// set task level access control
		$this->setAccessControl( 'com_rokreporter', 'run' );

		// additional mappings
	}

	/**
	 * Mail a message to users on selected content items
	 */
	function download()
	{
		global $database;

		$table = mosGetParam( $_POST, 'table' );

		if (!$table) {
			echo 'Error, no table specified';
			return false;
		}
		$table = $database->getEscaped( $table );
		$rows = tableToArray( $table );
		$buffer = trim( arrayToCSV( $rows ) );

		header( "Content-type: application/octet-stream" );
 		header( "Content-Length: ".strlen( $buffer ) );
		header( "Content-disposition: attachment; filename=$table-".date("Y-m-d").".csv" );
		header( "Pragma: no-cache" );
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header( "Expires: 0" );
		echo $buffer;
		exit( 0 );
	}
}
