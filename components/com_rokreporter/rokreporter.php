<?php
/**
 * @version		$Id: rokreporter.php 9 2007-04-13 04:08:48Z eddieajau $
 * @package		RokReporter
 * @copyright	(C) 2005 - 2007 New Life in IT Pty Ltd. All rights reserved.
 * @license		GNU General Public License
 */

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct access not allowed' );

define( 'JPATH_COMPONENT', dirname( __FILE__ ) );
require_once( JPATH_COMPONENT . '/defines.php' );
require_once( JPATH_COMPONENT . '/rokreporter.class.php' );

// set up security
$acl->_mos_add_acl( 'com_rokreporter', 'run', 'users', 'super administrator' );
$acl->_mos_add_acl( 'com_rokreporter', 'run', 'users', 'administrator' );
$acl->_mos_add_acl( 'com_rokreporter', 'run', 'users', 'manager' );

$acl->_mos_add_acl( 'com_rokreporter', 'run', 'users', '' );

// language support
//$lang =& JLanguage::getInstance( $mainframe->getCfg( 'lang' ) );
//$lang->load( 'com_rokreporter' );
//$lang->setDebug( 0 );

/**
 * @package		RokReporter
 */
class ReporterController extends mosAbstractTasker
{
	/**
	 * Constructor
	 */
	function ReporterController()
	{
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'reportList' );

		// set task level access control
		$this->setAccessControl( 'com_rokreporter', 'run' );

		// additional mappings
	}

	/**
	 * About
	 */
	function about()
	{
		require_once( JPATH_COMPONENT . '/rokreporter.html.php' );
		ReporterViews::about();
	}

	/**
	 * List the installed reports
	 */
	function reportList()
	{
		global $database, $mainframe;

		$list_limit = $mainframe->getCfg( 'list_limit' );

		// form data
		$limit 			= (int) mosSession::setFromRequest( 'global.listlimit', 'limit', $list_limit );
		$limitstart 	= (int) mosSession::setFromRequest( 'com_rokreporter.list.limitstart', 'limitstart', 0 );

		// search filter
		$search 		= mosSession::setFromRequest( 'com_rokreporter.list.search', 'search' );
		$search 		= strtolower( $search );

		// column ordering
		$orderCol	= mosSession::setFromRequest( 'com_rokreporter.list.orderCol', 'orderCol' );
		$orderDirn	= (int) mosSession::setFromRequest( 'com_rokreporter.list.orderDirn', 'orderDirn', 0 );
		if (empty( $orderCol )) {
			$orderCol = 'name';
		}

		// data lists
		$lists = array();
		$rows = array();

		$metaFiles = JFolder::folders( JPATH_COMPONENT . '/reports', '.' );

		foreach ($metaFiles as $file) {
			$row = ReportHelper::getMetaData( JPATH_COMPONENT . '/reports/' . $file . '/metadata.xml' );
			if ($search) {
				if (!eregi( $search, $row['name'] ) && !eregi( $search, $row['description'] )) {
					continue;
				}
			}
			$row['id'] = $file;
			$rows[] = $row;
		}

		define( 'ORDERDIRN', $orderDirn );
		define( 'ORDERCOL', $orderCol );

		function cmp_function( $a, $b ) {
			if (ORDERDIRN) {
				// descending
				return ($a[ORDERCOL] < $b[ORDERCOL]);
			} else {
				// ascending
				return ($a[ORDERCOL] > $b[ORDERCOL]);
			}
		}

		usort( $rows, 'cmp_function' );

		// prime template variables
		$vars = array(
			'orderCol' 	=> $orderCol,
			'orderDirn' => $orderDirn,
			'search'	=> $search,
		);

		require_once( JPATH_SITE . '/includes/pageNavigation.php' );
		$total = count( $metaFiles );
		$pageNav = new mosPageNav( $total, $limitstart, $limit );
		$rows = array_slice( $rows, $pageNav->limitstart, $pageNav->limit );

		require_once( JPATH_COMPONENT . '/rokreporter.html.php' );
		ReporterViews::reportList( $rows, $vars, $lists, $pageNav );
	}

	/**
	 * Run a report
	 */
	function run()
	{
		global $database;

		$report	= mosGetParam( $_REQUEST, 'report' );
		$report = preg_replace( '#\W#', '', $report );

		// form data
		$limit 		= mosSession::setFromRequest( 'global.listlimit', 'limit', $GLOBALS['mosConfig_list_limit'] );
		$limitstart = mosSession::setFromRequest( 'com_rokreporter.' . $report . '.limitstart', 'limitstart', 0 );

		// column ordering
		$orderCol	= mosSession::setFromRequest( 'com_rokreporter.' . $report . '.orderCol', 'orderCol', '' );
		$orderDirn	= mosSession::setFromRequest( 'com_rokreporter.' . $report . '.orderDirn', 'orderDirn', 0 );

		// search filter
		$search 	= mosSession::setFromRequest( 'com_rokreporter.' . $report . '.search', 'search' );
		$search 	= strtolower( $search );

		// prime template variables
		$vars = array(
			'limit' 		=> $limit,
			'limitstart'	=> $limitstart,
			'orderCol'		=> $orderCol,
			'orderDirn'		=> $orderDirn,
			'search'		=> $search,
			'report_id'		=> $report,
		);

		$metaData = ReportHelper::getMetaData( JPATH_COMPONENT . '/reports/' . $report . '/metadata.xml' );

		$reportClass = $report . '_Report';

		$report = new $reportClass( $database );
		$report->run( $vars );

		require_once( JPATH_ADMIN . '/includes/pageNavigation.php' );
		$total = $report->rowCount();
		$pageNav = new mosPageNav( $total, $limitstart, $limit );

		require_once( JPATH_COMPONENT . '/rokreporter.html.php' );
		$report->render( $vars, $metaData, $pageNav );
	}
}

// use php sessions to hold the state of lists
if (!session_id())
{
	session_name( md5( $mosConfig_live_site . 'rocketwerx' ) );
	session_start();
}

// check if we are running in report mode
$report	= mosGetParam( $_REQUEST, 'report' );
$report = preg_replace( '#\W#', '', $report );

if ($report)
{
	// check if report folder exists
	$reportInclude = JPATH_COMPONENT . '/reports/' . $report . '/report.php';

	if (file_exists( $reportInclude ))
	{
		require( $reportInclude );

		// check if report class exists
		$reportClass = $report . '_Report';

		if (class_exists( $reportClass ))
		{
			if ($task == 'run') {
				// use the main handler for the simple report list
				$tasker = new ReporterController();
				$tasker->performTask( $task );
			} else {
				// allows for custom functions on a selection from the report
				call_user_func( array( $reportClass, 'tasker' ), $task );
			}
		} else {
			echo JText::_( 'Report class not found' );
		}
	} else {
		echo JText::_( 'Not Auth' );
	}

}
else
{
	// Main tasker for listing available reports
	$tasker = new ReporterController();
	$tasker->performTask( mosGetParam( $_REQUEST, 'task' ) );
	$tasker->redirect();
}
