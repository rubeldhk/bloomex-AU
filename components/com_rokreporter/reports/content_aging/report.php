<?php
/**
 * @version		$Id: report.php 9 2007-04-13 04:08:48Z eddieajau $
 * @package		RokReporter
 * @copyright	(C) 2005 - 2007 New Life in IT Pty Ltd. All rights reserved.
 * @license		GNU General Public License
 */

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct access not allowed' );

// include data handling class
require_once( JPATH_COMPONENT.'/framework/datetime.php' );

/**
 * Custom function that shows a 'days ago' representation of a date value
 */
function toAge( $value )
{
	static $todayDays;

	if ($todayDays == null) {
		$today = new JDate();
		$todayDays = $today->toDays();
	}
	if (intval( $value) == 0) {
		return 0;
	}
	$date = new JDate( $value );

	return $todayDays - $date->toDays();
}

/**
 * @param int The content state
 * @return string Text meaning of the state
 */
function stateToString( $value )
{
	if ($value > 0) {
		return 'Yes';
	} else if ($value == -1) {
		return 'Archived';
	} else if ($value == -2) {
		return 'Trash';
	} else {
		return 'No';
	}
}

/**
 * Reporting class
 * @package		RokReporter
 */
class content_aging_Report extends Report
{
	/** @var string The report id (folder name); */ 
	var $id = 'content_aging';

	/**
	 * Runs the report
	 * @param array An array of system variables
	 */
	function run( &$vars )
	{
		// set the default ordering
		if (empty( $vars['orderCol'] )) {
			$vars['orderCol'] = 'title';
		}

		$database = &$this->_db;

		$qb = new JQuery;
		$qb->select( 'a.id, a.title, a.state, a.created, a.modified, a.hits' );
		$qb->select( 'cs.name AS section_name, cc.name AS category_name, u.name AS owner' );
		$qb->from( '#__content AS a' );
		$qb->join( 'LEFT', '#__users AS u ON u.id = a.created_by' );
		$qb->join( 'LEFT', '#__sections AS cs ON cs.id = a.sectionid' );
		$qb->join( 'LEFT', '#__categories AS cc ON cs.id = a.catid' );
		$qb->order( $this->orderBy( $vars['orderCol'], $vars['orderDirn'] ) );
		$qb->group( 'a.id' );

		if ($vars['search']) {
			$qb->where( 'a.title LIKE ' . $database->Quote( '%' . $vars['search'] . '%' ) );
		}
		$this->query( $qb->toString(), $vars );
	}

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
	function reportTasks() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker();

		// set task level access control
		$this->setAccessControl( 'com_rokreporter', 'run' );

		// additional mappings
	}

	/**
	 * Mail a message to users on selected content items
	 */
	function mail()
	{
		global $database, $mainframe;

		$cid		= (array) mosGetParam( $_POST, 'cid', null );
		$captcha	= (int) mosGetParam( $_POST, JR_CAPTCHA, null );
		$report_id	=  mosGetParam( $_REQUEST, 'report_id' );
		$Itemid		= (int) mosGetParam( $_REQUEST, 'Itemid' );

		$subject	= mosGetParam( $_POST, 'mail_subject', null );
		$message	= mosGetParam( $_POST, 'mail_body', null );

		if ($captcha !== 1) {
			echo 'Error, not a valid post';
			return;
		}
		if (count( $cid ) == 0) {
			echo 'Error, no items selected';
			return;
		}
		if ($subject == '') {
			echo 'Error, you must provide a subject';
			return;
		}
		if ($message == '') {
			echo 'Error, you must provide a message';
			return;
		}

		mosArrayToInts( $cid, 0 );

		$cids = 'a.id=' . implode( ' OR a.id=', $cid );

		$query = 'SELECT a.title, u.name, u.email' .
				' FROM #__content AS a' .
				' INNER JOIN #__users AS u ON u.id = a.created_by' .
				' WHERE ' . $cids;
		$database->setQuery( $query );
		$users = $database->loadObjectList();

		// setup mail
		if ($mainframe->getCfg( 'mailfrom' ) != '' && $mainframe->getCfg( 'fromname' ) != '') {
			$from 	= $mainframe->getCfg( 'mailfrom' );
			$fromname = $mainframe->getCfg( 'fromname' );
		} else {
			$query = "SELECT name, email"
			. "\n FROM #__users"
			// administrator
			. "\n WHERE gid = 25"
			;
			$database->setQuery( $query );
			$admins = $database->loadObjectList();
			$admin 		= $admins[0];
			$from 	= $admin->name;
			$fromname = $admin->email;
		}

		// template replacements

		$nUsers = count( $users );
		for ($i = 0; $i < $nUsers; $i++) {
			$user = &$users[$i];

			// template replacements
			$body = str_replace( '[NAME]', $user->name, $message );
			$body = str_replace( '[TITLE]', $user->title, $body );

			// mail function
			mosMail( $from, $fromname, $user->email, $subject, $body );
		}
		$this->setRedirect( $_SERVER['PHP_SELF'] . '?option=com_rokreporter&amp;task=run&amp;report='.$report_id.'&amp;Itemid=' . $Itemid, 'Mails Sent' );
	}
}
