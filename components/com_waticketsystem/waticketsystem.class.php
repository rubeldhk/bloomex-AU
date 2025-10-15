<?php
/**
 * FileName: waticketsystem.class.php
 * Date: 19/11/2006
 * License: GNU General Public License
 * Script Version #: 2.0.6
 * JOS Version #: 1.0.x
 * Development James Kennard jg8949@aol.com (www.webamoeba.co.uk)
 */

// Don't allow direct linking
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * watsUser
 * @version 1.0
 */
class watsUser extends mosUser
{
	var $groupName;
	var $agree;
	var $organisation;
	var $group;
	var $image;
	var $_userRites;

	/**
	 * @version 1.0
	 * @param watsId
	 */
	function watsUser( &$database )
	{
	    $this->mosUser( $database );
	}
	
	/**
	 *
	 * @param watsId
	 */
	function loadWatsUser( $uid )
	{
		global $wats;
		$returnValue = false;
		// load mosUser
		$this->load( $uid );
		// loadmosWatsUser
		$this->_db->setQuery( "SELECT  u.*, g.name, g.userrites, g.image, g.name AS groupname FROM #__wats_users AS u LEFT  JOIN #__wats_groups AS g ON g.grpid = u.grpid WHERE u.watsid=".$uid );
		$vars = $this->_db->loadObjectList();
		// set attributes
		if ( isset( $vars[0] ) )
		{
		    $this->groupName = $vars[0]->groupname ;
		    $this->agree = $vars[0]->agree;
		    $this->organisation = $vars[0]->organisation;
			$this->group = $vars[0]->grpid;
			$this->image = $vars[0]->image;
			$this->groupName = $vars[0]->name;
			$this->userRites = $vars[0]->userrites;
			$returnValue = true;
		}
		elseif ( $wats->get( 'users' ) == 1 )
		{
			// allow all user access enabled
			// get default group
			$this->_db->setQuery( "SELECT  g.grpid, g.name, g.userrites, g.image, g.name AS groupname FROM #__wats_groups AS g WHERE g.grpid=".$wats->get( 'userdefault' ) );
			$vars = $this->_db->loadObjectList();
			// setup user vars
		    $this->groupName = $vars[0]->groupname ;
		    $this->agree = "";
		    $this->organisation = $wats->get( 'dorganisation' );
			$this->group = $vars[0]->grpid;
			$this->image = $vars[0]->image;
			$this->groupName = $vars[0]->name;
			$this->userRites = $vars[0]->userrites;
			// check for import
			if ( $wats->get( 'usersimport' ) == 1 )
			{
				// import user to default group
				watsUser::makeUser( $this->id, $this->group, $this->organisation, $this->_db );
			}
			$returnValue = true;
		}
		return $returnValue;
	}
	
	/**
	 *
	 * @param catid
	 * @param rite
	 */
	function checkPermission( $catid, $rite )
	{
		// prepare for no rite
		$returnValue = 0;
		// run SQL to find permission
		$this->_db->setQuery( "SELECT type FROM #__wats_permissions WHERE catid=".$catid." AND grpid=".$this->group);
		$vars = $this->_db->loadObjectList();
		// check for result
		if ( isset( $vars[0] ) ) {
			// find rite in string
			// checks type as well because could return 0
			if ( strpos( $vars[0]->type, strtolower( $rite) ) !== false )
			{
				// check for OWN rite
				$returnValue = 1;
			}
			else if ( strpos( $vars[0]->type, strtoupper( $rite) ) !== false )
			{
				// check for ALL rite
				$returnValue = 2;
			} // end find rite in string
		} // end check for result
		return $returnValue;
	}

	/**
	 *
	 * @param watsId
	 */
	function checkUserPermission( $rite )
	{
		// prepare for no rite
		$returnValue = 0;
		// find rite in string
		// checks type as well because could return 0
		if ( strpos( $this->userRites, strtolower( $rite) ) !== false )
		{
			// check for OWN rite
			$returnValue = 1;
		}
		else if ( strpos( $this->userRites, strtoupper( $rite) ) !== false )
		{
			// check for ALL rite
			$returnValue = 2;
		} // end find rite in string
		return $returnValue;
	}
	
	/**
	 *
	 * @param watsId
	 */
	function makeUser( $watsId, $grpId, $organisation, &$database )
	{
		// check doesn't already exist
		$database->setQuery( "SELECT wu.watsid FROM #__wats_users AS wu WHERE watsid=".$watsId);
		$database->query();
		if ( $database->getNumRows() == 0 )
		{
			// create SQL
			$database->setQuery( "INSERT INTO #__wats_users ( watsid , organisation , agree , grpid ) VALUES ( '".$watsId."', '".$organisation."', '0000-00-00', '".$grpId."' );" );
			// execute
			$database->query();
			return true;
		}
		else
		{
			return false;
		} // end check doesn't already exist
	}
	
	/**
	 *
	 */
	function updateUser()
	{
		// check already exists
		$this->_db->setQuery( "SELECT wu.watsid FROM #__wats_users AS wu WHERE watsid=".$this->id);
		$this->_db->query();
		if ( $this->_db->getNumRows() != 0 )
		{
			// update SQL
			$this->_db->setQuery( "UPDATE #__wats_users SET organisation='".$this->organisation."', agree='".$this->agree."', grpid='".$this->group."' WHERE watsid='".$this->id."';" );
			// execute
			$this->_db->query();
			return true;
		}
		else
		{
			return false;
		} // end check doesn't already exist
	}
	
	/**
	 *
	 * @param groupId
	 */
	function setGroup( $groupId )
	{
		// check group exists and get name
		$this->_db->setQuery( "SELECT g.name, g.image FROM #__wats_groups AS g WHERE grpid=".$groupId);
		$groupDetails = $this->_db->loadObjectList();
		if ( count( $groupDetails ) != 0 )
		{
			// update object
			$this->group = $groupId;
			$this->groupName = $groupDetails[0]->name;
			$this->image = $groupDetails[0]->image;
			// update SQL
			$this->_db->setQuery( "UPDATE #__wats_users SET organisation='".$this->organisation."', agree='".$this->agree."', grpid='".$this->group."' WHERE watsid='".$this->id."';" );
			// execute
			$this->_db->query();
			return true;
		}
		else
		{
			return false;
		} // end check doesn't already exist
	}

	/**
	 *
	 * @param groupId
	 */
	function delete( $remove )
	{
		// find tickets
		$this->_db->setQuery( "SELECT ticketid FROM #__wats_ticket WHERE watsid=".$this->id);
		$tickets = $this->_db->loadObjectList();
		$noOfTickets = count( $tickets );
		$i = 0;
		while ( $i < $noOfTickets )
		{
			// remove ticket messages
			$this->_db->setQuery( "DELETE FROM #__wats_msg WHERE ticketid=".$tickets[$i]->ticketid );
			$this->_db->query();
			// remove highlights
			$this->_db->setQuery( "DELETE FROM #__wats_highlight WHERE ticketid=".$tickets[$i]->ticketid );
			$this->_db->query();
			$i ++;
		}
		// remove tickets
		$this->_db->setQuery( "DELETE FROM #__wats_ticket WHERE watsid=".$this->id);
		$this->_db->query();
		// remove all posts
		if ( $remove == "removeposts" )
		{
			$this->_db->setQuery( "DELETE FROM #__wats_msg WHERE watsid=".$this->id);
			$this->_db->query();
		} // end remove all posts
		// delete users highlights
		$this->_db->setQuery( "DELETE FROM #__wats_highlight WHERE watsid=".$this->id);
		$this->_db->query();
		// delete user
		$this->_db->setQuery( "DELETE FROM #__wats_users WHERE watsid=".$this->id);
		$this->_db->query();
	}
}

/**
 * @version 1.0
 * @created 09-Jan-2006 15:30
 */
class watsUserSet
{
	var $userSet;
	var $noOfUsers;
	var $_db;

	/**
	 * @param database
	 */
	function watsUserSet( &$database )
	{
	    $this->_db =& $database;
	}
	
	/**
	 * @param groupId
	 */
	function load( $groupId = null )
	{
		// load all users
	    if ( $groupId === null )
		{
			$this->_db->setQuery( "SELECT * FROM #__wats_users ORDER BY grpid" );
			$set = $this->_db->loadObjectList();
			$this->noOfUsers = count( $set );
			$i = 0;
			// create users
			while ( $i < $this->noOfUsers )
			{
				$this->userSet[$i] = new watsUserHTML( $this->_db );
				$this->userSet[$i]->loadWatsUser( $set[$i]->watsid  );
				$i ++;
			} // end create users
		} // end load all users
	}
}

/**
 * @version 1.0
 * @created 06-Dec-2005 21:42:51
 */
class watsObjectBuilder
{
	/**
	 *
	 * @param database
	 * @param ticketId
	 */
	 function ticket( &$database, $ticketId ) {
		// create query
		$query = "SELECT * FROM #__wats_ticket WHERE ticketid='".$ticketId."'";
		// execute query
		$database->setQuery( $query );
		$set = $database->loadObjectList();
		// check there are results
		if ( $set != null )
		{
			// create ticket object
			return new watsTicketHTML( $database, null, null, $set[0]->ticketname, $set[0]->watsid, null, null, $set[0]->lifecycle, $set[0]->ticketid, null, null, $set[0]->category, $set[0]->assign );
		} // end check there are results
		return null;
	 }
}

/**
 * Individual WATS Ticket Class
 * @version 1.0
 * @created 06-Dec-2005 21:42:32
 */
class watsTicket
{
	var $watsId;
	var $username;
	var $ticketId;
	var $name;
	var $category;
	var $lifeCycle;
	var $datetime;
	var $lastMsg;
	var $lastView;
	var $lastWatsId;
	var $assignId;
	var $msgNumberOf;
	var $_msgList;
	var $_db;

	/**
	 * 
	 * @param database
	 * @param username
	 * @param lastWatsId
	 * @param name
	 * @param watsId
	 * @param lastMsg
	 * @param datetime
	 * @param lifeCycle
	 * @param ticketId
	 * @param lastView
	 * @param create
	 */
	//function watsTicket( &$database, $username, $lastWatsId, $name, $watsId, $lastMsg, $datetime, $lifeCycle, $ticketId, $lastView, $msgNumberOf, $catId )
	function watsTicket( &$database, $username, $lastWatsId, $name, $watsId, $lastMsg, $datetime, $lifeCycle, $ticketId, $lastView, $msgNumberOf, $catId, $assignId = null )
	{
		$this->username = $username;
		$this->lastWatsId = $lastWatsId;
		$this->name = $name;
		$this->watsId = $watsId;
		$this->lastMsg = $lastMsg;
		$this->datetime = $datetime;
		$this->lifeCycle = $lifeCycle;
		$this->ticketId = $ticketId;
		$this->lastView = $lastView;
		$this->msgNumberOf = $msgNumberOf;
		$this->_msgList = array();
		$this->_db = &$database;
		$this->category = $catId;
		$this->assignId = $assignId;
	}

	/**
	 * returns username of assigned user.
	 */
	function getAssignedUsername()
	{
		// check for assignment
	    if ( $this->assignId != null )
		{
			// find username
			$this->_db->setQuery( "SELECT u.username FROM #__users AS u WHERE u.id=".$this->assignId );
			$user = $this->_db->loadObjectList();
			$returnValue = $user[0]->username;
		}
		else
		{
			// return no assigned user
			$returnValue = "not assigned XXX";
		}
		
		return $returnValue;
	}

	/**
	 * saves ticket to database
	 */
	function save()
	{
		// ticket
		$queryTicket = "INSERT INTO #__wats_ticket SET watsid=".$this->watsId.", ticketname='".$this->name."', lifecycle=".$this->lifeCycle.", datetime=".$this->datetime.", category=".$this->category;
		$this->_db->setQuery( $queryTicket );
		$this->_db->query();
		$this->ticketId = $this->_db->insertid();
		// message
		$queryMsg = "INSERT INTO #__wats_msg SET watsid=".$this->watsId.", ticketid='".$this->ticketId."',msg='".$this->_msgList[0]->msg."', datetime=".$this->datetime;
		$this->_db->setQuery( $queryMsg );
		$this->_db->query();
	}

	/**
	 * decreases view level
	 */
	function deactivate()
	{
		// check is not dead
		if ( $this->lifeCycle < 3 )
		{
			// update lifeCycle
			$this->lifeCycle ++;
			$queryDeactivateTicket = "UPDATE #__wats_ticket SET lifecycle=".$this->lifeCycle." WHERE ticketid=".$this->ticketId.";"; 
		}
		else
		{
			// remove ticket
			$queryDeactivateTicket = "DELETE FROM #__wats_ticket WHERE ticketid=".$this->ticketId.";";
			// remove all messages in ticket
			foreach ( $this->_msgList as $message )
			{
				$queryDeactivateMsg = "DELETE FROM #__wats_msg WHERE msgid=".$message->msgId.";";
				$this->_db->setQuery( $queryDeactivateMsg );
				$this->_db->query();
			} // end remove all messages in ticket
		}
		$this->_db->setQuery( $queryDeactivateTicket );
		$this->_db->query();
	}

	/**
	 * Updates database to reflect viewing of ticket
	 */
	function _highlightUpdate( $watsId )
	{
		// check for existing record
		$queryHighlight = "SELECT datetime FROM #__wats_highlight WHERE ticketid=".$this->ticketId." AND watsid =".$watsId.";";
		$this->_db->setQuery( $queryHighlight );
		$this->_db->query();
		if ( $this->_db->getNumRows() > 0 )
		{
			// update record
			$queryHighlight = "UPDATE #__wats_highlight SET datetime=".date('YmdHis')." WHERE ticketid=".$this->ticketId." AND watsid =".$watsId.";";
		}
		else
		{
			// insert record
			$queryHighlight = "INSERT INTO #__wats_highlight SET watsid=".$watsId.", ticketid=".$this->ticketId.", datetime=".date('YmdHis').";";
		}
		// perform query
		$this->_db->setQuery( $queryHighlight );
		$this->_db->query();

	}
	
	/**
	 * Reactivate ticket and updates database
	 */
	function reactivate()
	{
		$this->lifeCycle = 1;
		$queryDeactivateMsg = "UPDATE #__wats_ticket SET lifecycle=1 WHERE ticketid=".$this->ticketId.";";
		$this->_db->setQuery( $queryDeactivateMsg );
		$this->_db->query();
	}

	/**
	 * Populates _msgList with all related messages
	 */
	function loadMsgList()
	{
		// reset number of messages
		$this->msgNumberOf = 0;
		// load categories
		$this->_db->setQuery( "SELECT *, UNIX_TIMESTAMP(m.datetime) AS unixDatetime FROM #__wats_msg AS m WHERE ticketid=".$this->ticketId." ORDER BY datetime" );
		$messages = $this->_db->loadObjectList();
		// create message objects
		$i = 0;
		foreach( $messages as $message )
		{
			// create object
		    $this->_msgList[$i] = new watsMsg( $message->msgid, $message->msg, $message->watsid, $message->unixDatetime );
			// increment counter
			$i ++;
			$this->msgNumberOf ++;
		}
	}
	
	/**
	 * Add message to _msgList and database
	 */
	function addMsg( $msg, $watsId, $datetime )
	{
		// create SQL and execute
		$this->_db->setQuery( "INSERT INTO #__wats_msg ( ticketid, watsid, msg, datetime ) VALUES ( '".$this->ticketId."', '".$watsId."', '".$msg."', ".$datetime.");" );
		$this->_db->query();
		$this->_msgList[ count( $this->_msgList ) ] = new watsMsg( $this->ticketId, $msg, $watsId, $datetime );
		$this->msgNumberOf ++;
	}
	
	/**
	 * Add message to _msgList and database
	 */
	function setAssignId( $assignId )
	{
		$this->assignId = $assignId;
		// create SQL and execute
		$this->_db->setQuery( "UPDATE #__wats_ticket SET assign=".$this->assignId." WHERE ticketid=".$this->ticketId );
		$this->_db->query();
	}
}

/**
 * @version 1.0
 * @created 06-Dec-2005 21:43:47
 */
class watsTicketSet
{
	var $ticketNumberOf;
	var $_ticketList;
	var $_ticketListPointer;
	var $_db;

	/**
	 * 
	 * @param database
	 */
	function watsTicketSet( &$database )
	{
		$this->_db =& $database;
		$this->ticketNumberOf = 0;
		$this->_ticketListPointer = 0;
	}

	/**
	 * 
	 * @param lifeCycle (0 = open and closed, 1 = open, 2 = closed, 3 = dead)
	 * @param watsid
	 * @param category (id of category, -1 = all categories)
	 * @param riteAll (true = show all users tickets)
	 * @param assign ( true = assigned tickets only)
	 */
	 //$this->ticketSet->loadTicketSet( 0, $this->watsId, -1, true, true );
	function loadTicketSet( $lifecycle, $watsid, $category = null, $riteAll = false, $assign = false )
	{
		// create query
		$query = $sql = "SELECT COUNT(*) AS posts, t.ticketid, t.assign, t.watsid AS ownerid, t.ticketname, t.category, t.lifecycle, UNIX_TIMESTAMP(t.datetime) AS firstpost, UNIX_TIMESTAMP(h.datetime) AS lastview, SUBSTRING(MIN(CONCAT(DATE_FORMAT(m1.datetime, '%Y-%m-%d %H:%i:%s'), m1.msgid)), 20) as firstmsg, SUBSTRING(MAX(CONCAT(DATE_FORMAT(m1.datetime, '%Y-%m-%d %H:%i:%s'), m1.msgid)), 20) as lastpostid, SUBSTRING(MAX(CONCAT(DATE_FORMAT(m1.datetime, '%Y-%m-%d %H:%i:%s'), m1.watsid)), 20) as lastid, UNIX_TIMESTAMP(MAX(m1.datetime)) as lastdate, o.username AS username, SUBSTRING(MAX(CONCAT(DATE_FORMAT(m1.datetime, '%Y-%m-%d %H:%i:%s'), p.username)), 20) AS poster FROM #__wats_ticket AS t LEFT JOIN #__wats_highlight AS h ON t.ticketid = h.ticketid AND h.watsid=".$watsid." LEFT JOIN #__wats_msg AS m1 ON t.ticketid = m1.ticketid LEFT JOIN #__users AS o ON t.watsid = o.id LEFT JOIN #__users AS p ON m1.watsid = p.id ";
		// check lifeCycle
		if ( $lifecycle == 0 )
		{
			$query .= "WHERE ( t.lifecycle=1 OR t.lifecycle=2 )";
		}
		else
		{
			$query .= "WHERE t.lifecycle=".$lifecycle;
		}
		if ( $riteAll == false )
		{
			// set wats id
			$query .= " AND t.watsid=".$watsid;
		}
		if ( $category != null AND $category != -1 )
		{
			// set category
			$query .= " AND category=".$category;
		}
		if ( $assign )
		{
			// set assigned tickets only
			$query .= " AND t.assign=".$watsid;
		}
		// end create query
		$query .= " GROUP BY t.ticketid, t.watsid, t.ticketname, t.datetime ORDER BY lastdate desc;";
		$this->_db->setQuery( $query );
		$set = $this->_db->loadObjectList();
		// check there are results
		if ( $set != null )
		{
			// create ticket objects
			foreach( $set as $ticket )
			{
				// create object
				$this->_ticketList[$this->ticketNumberOf] = new watsTicketHTML( $this->_db, $ticket->username, $ticket->lastid, $ticket->ticketname, $ticket->ownerid, $ticket->lastdate, $ticket->firstpost, $ticket->lifecycle, $ticket->ticketid, $ticket->lastview, $ticket->posts, $ticket->category, $ticket->assign );
				// increment counter
				$this->ticketNumberOf ++;
			}// end create ticket objects
		} // end check there are results
	}
}

/**
 * @version 1.0
 * @created 06-Dec-2005 21:43:13
 */
class watsMsg
{
	var $msgId;
	var $msg;
	var $watsId;
	var $datetime;

	/**
	 * Populates msgId, msg, watsId and datetime with corresponding values
	 *
	 * @param msgId
	 */
	function watsMsg( $msgId, $msg = null, $watsId = null, $datetime = null )
	{
		$this->msgId=$msgId;
		$this->msg=$msg;
		$this->watsId=$watsId;
		$this->datetime=$datetime;
	}
	
	/**
	 * saves message to database
	 * 
	 * @param database
	 */
	function save( &$database )
	{
	}
}

/**
 * @version 1.0
 * @created 06-Dec-2005 21:44:11
 */
class watsCategory extends mosDBTable
{
    var $catid;
	var $name;
	var $ticketSet;
	var $description;
	var $image;

	/**
	 * 
	 * @param database
	 */
	function __construct( &$database )
	{
        parent::__construct( '#__wats_category', 'catid', $database );
	}

	/**
	 * Loads this->ticketSet
	 *
	 * @param database
	 * @param lifecycle
	 * @param watsid
	 * @param category
	 */
	function loadTicketSet( $lifecycle, $watsid, $riteAll = false )
	{
		// create new ticketset
		$this->ticketSet = new watsTicketSetHTML( $this->_db );
		// load tickets
		$this->ticketSet->loadTicketSet( $lifecycle, $watsid, $this->catid, $riteAll );
	}

	/**
	 * Purges loaded tickets
	 *
	 */
	function purge()
	{
		$ticketCount = count($this->ticketSet->_ticketList);
		$i = 0;
		while ( $i < $ticketCount )
		{
			$this->ticketSet->_ticketList[$i]->deactivate();
			$i ++;
		}
	}
	
	/**
	 * Returns an array of users who can have tickets assigned to.
	 *
	 */
	function getAssignee( $catid = null, $database = null )
	{
		if ( $catid == null )
		{
			$catid = $this->catid;
			$database = $this->_db;
		}
		$database->setQuery( "SELECT wu.watsid, u.username
								FROM #__wats_permissions AS p
								LEFT  JOIN #__wats_users AS wu ON wu.grpid = p.grpid
								LEFT  JOIN #__users AS u ON wu.watsid = u.id
								WHERE
								p.catid=".$catid." AND (
								p.type LIKE  \"%a%\" OR
								p.type LIKE  \"%A%\" )" );
		$assignees = $database->loadObjectList( );
		// check for reults
		if ( count( $assignees ) == 0 )
		{
			return null;
		}
		else
		{
			return $assignees;
		} // end check for reults
	}
}

/**
 * @version 1.0
 * @created 12-Dec-2005 13:32:13
 */
class watsAssign
{
	var $ticketSet;
	var $watsId;
	var $_db;
	
	/**
	 * 
	 * @param database
	 */	
	function watsAssign( &$database )
	{
		$this->_db =& $database;
	}

	/**
	 * Loads this->ticketSet
	 *
	 * @param watsid
	 */
	function loadAssignedTicketSet( $watsId )
	{
		// set watsId
		$this->watsId = $watsId;
		// create new ticketset
		$this->ticketSet = new watsTicketSetHTML( $this->_db );
		// load tickets
		$this->ticketSet->loadTicketSet( 0, $this->watsId, -1, true, true );
	}
}

/**
 * @version 1.0
 * @created 06-Dec-2005 21:43:13
 */
class watsCategorySet
{
    var $categorySet;
	var $_db;

	/**
	 * 
	 * @param database
	 */	
	function watsCategorySet( &$database, &$watsUser )
	{
		$this->_db =& $database;
		$this->categorySet = array();
		// load categories
		$this->_db->setQuery( "SELECT * FROM #__wats_category" );
		$vars = $this->_db->loadObjectList();
		// create category objects
		$i = 0;
		foreach( $vars as $var )
		{
			// check for viewing rite
			if ( $watsUser->checkPermission( $var->catid, "v" ) > 0 )
			{
				// create object
				$this->categorySet[$i] = new watsCategoryHTML( $this->_db );
				// load object
				$this->categorySet[$i]->load( $var->catid );
				// increment counter
				$i ++;
			} // end check for viewing rite
		} //end  create category object
	}

	/**
	 * 
	 * @param database
	 */	
	function loadTicketSet( $lifecycle, &$watsUser )
	{
		// itterate through categories
		$numberOfCategories = count($this->categorySet);
		$i = 0;
		while ( $i < $numberOfCategories )
		{
			// check view rites
			$rite =  $watsUser->checkPermission( $this->categorySet[$i]->catid, "v" );
			if ( $rite == 2 )
			{
				// allow user to load all tickets
				$this->categorySet[$i]->loadTicketSet( $lifecycle, $watsUser->id, true );
			}
			else if ( $rite = 1 )
			{
				// allow user to load own tickets only
				$this->categorySet[$i]->loadTicketSet( $lifecycle, $watsUser->id );
			}
			// increment counter
			$i ++;
		} // end itterate through categories
	}
}

/**
 * @version 1.0
 * @created 06-Dec-2005 21:44:11
 */
class watsSettings
{
    var $settings;
	var $_db;

	/**
	 * 
	 * @param database
	 */
	function watsSettings( &$database )
	{
		// set db
		$this->_db =& $database;
		$this->reload();
	}

	/**
	 * 
	 */
	function reload()
	{
		// reset settings array
		$this->settings = null;
		// load settings
		$this->_db->setQuery( "SELECT * FROM #__wats_settings" );
		$vars = $this->_db->loadObjectList();
		// create category objects
		foreach( $vars as $var )
		{
			// create index in array and give value
			$this->settings[$var->name] = $var->value;
		}
	}
	
	/**
	 * 
	 *  @param name of setting
	 */
	function get( $name )
	{
		if ( isset( $this->settings[$name] ) )
		{
			return $this->settings[$name];
		}
	}
}


/**
 * @version 1.0
 * @created 04-Sep-2006
 * In development
 */
class watsDatabaseWrapperItem
{
	var $_name;
	var $_sql;
	var $_errorNum;
	var $_errorMsg;
	var $_count;

	/** 
	 * @param name of Action
	 * @param sql executed
	 * @param error Number
	 * @param error Message
	 * @param count of results, null if inappropriate.
	 */
	function watsDatabaseWrapperItem( $name, $sql='', $errorNum=0, $errorMsg='', $count=null )
	{
		// set vars
		$this->_name     = $name;
		$this->_sql      = $sql;
		$this->_errorNum = $errorNum;
		$this->_errorMsg = $errorMsg;
		$this->_count    = $count;
	}

}

/**
 * @version 1.0
 * @created 26-Aug-2006
 * In development
 */
class watsDatabaseWrapper
{
    var $_log;
	var $_db;

	/** 
	 * @param database
	 */
	function watsDatabaseWrapper( &$database )
	{
		// set db
		$this->_db =& $database;
		$this->_log[] = new watsDatabaseWrapperItemHTML( 'WATS Database Wrapper Initiated for WATS Debug' );
	}
	
	/**
	* @param int
	*/
	function debug( $level )
	{
		$this->_log[] = new watsDatabaseWrapperItemHTML( 'Database debug level set to '.$level );
		$this->_db->debug( $level );
	}
	
	function query()
	{
		$tmp = $this->_db->query();
		$this->_log[] = new watsDatabaseWrapperItemHTML( 'Query Executed', $this->_db->getQuery(), $this->_db->getErrorNum(), $this->_db->getErrorMsg() );
		return $tmp;
	}

	function query_batch( $abort_on_error=true, $p_transaction_safe = false)
	{
		$tmp = $this->_db->query_batch( $abort_on_error, $p_transaction_safe );
		$this->_log[] = new watsDatabaseWrapperItemHTML( 'Query Batch Executed', $this->_db->getQuery(), $this->_db->getErrorNum(), $this->_db->getErrorMsg() );
		return $tmp;
	}

	function loadResult()
	{
		$tmp = $this->_db->loadResult();
		$this->_log[] = new watsDatabaseWrapperItemHTML( 'Query Executed: loadResult', $this->_db->getQuery(), $this->_db->getErrorNum(), $this->_db->getErrorMsg(), count( $tmp ) );
		return $tmp;
	}
	
	function loadResultArray( $numinarray = 0 )
	{
		$tmp = $this->_db->loadResultArray( $numinarray );
		$this->_log[] = new watsDatabaseWrapperItemHTML( 'Query Executed: loadResultArray', $this->_db->getQuery(), $this->_db->getErrorNum(), $this->_db->getErrorMsg(), count( $tmp ) );
		return $tmp;
	}
	
	function loadAssocList( $key='' )
	{
		$tmp = $this->_db->loadAssocList( $key );
		$this->_log[] = new watsDatabaseWrapperItemHTML( 'Query Executed: loadAssocList', $this->_db->getQuery(), $this->_db->getErrorNum(), $this->_db->getErrorMsg(), count( $tmp ) );
		return $tmp;
	}
	
	function loadObject( &$object )
	{
		$tmp = $this->_db->loadObject( $object );
		$this->_log[] = new watsDatabaseWrapperItemHTML( 'Query Executed: loadObject', $this->_db->getQuery(), $this->_db->getErrorNum(), $this->_db->getErrorMsg(), count( $tmp ) );
		return $tmp;
	}
	
	function loadObjectList( $key='' )
	{
		$tmp = $this->_db->loadObjectList( $key );
		$this->_log[] = new watsDatabaseWrapperItemHTML( 'Query Executed: loadObjectList', $this->_db->getQuery(), $this->_db->getErrorNum(), $this->_db->getErrorMsg(), count( $tmp ) );
		return $tmp;
	}

	function getErrorNum() { return $this->_db->getErrorNum(); }
	function getErrorMsg() { return $this->_db->getErrorMsg(); }
	function getEscaped( $text ) { return $this->_db->getEscaped( $text ); }
	function Quote( $text ) { return $this->_db->Quote( $text ); }
	function NameQuote( $s ) { return $this->_db->NameQuote( $s ); }
	function getPrefix() { return $this->_db->getPrefix(); }
	function getNullDate() { return $this->_db->getNullDate(); }
	function setQuery( $sql, $offset = 0, $limit = 0, $prefix='#__' ) { $this->_db->setQuery( $sql, $offset, $limit, $prefix ); }
	function replacePrefix( $sql, $prefix='#__' ) { return $this->_db->replacePrefix( $sql, $prefix ); }
	function getQuery() { return $this->_db->getQuery(); }
	function explain() { return $this->_db->explain(); }
	function getNumRows( $cur=null ) { return $this->_db->getNumRows( $cur ); }
	function loadRow() { return $this->_db->loadRow(); }
	function loadRowList( $key='' ) { return $this->_db->loadRowList( $key ); }
	function insertObject( $table, $object, $keyName = NULL, $verbose=false ) { return $this->_db->insertObject( $table, $object, $keyName, $verbose ); }
	function updateObject( $table, $object, $keyName, $updateNulls=true ) { return $this->_db->updateObject( $table, $object, $keyName, $updateNulls ); }
	function stderr( $showSQL = false ) { return $this->_db->stderr( $showSQL ); }
	function insertid() { return $this->_db->insertid(); }
	function getVersion() { return $this->_db->getVersion(); }
	function getTableList() { return $this->_db->getTableList(); }
	function getTableCreate( $tables ) { return $this->_db->getTableCreate( $tables ); }
	function getTableFields( $tables ) { return $this->_db->getTableFields( $tables ); }
	function GenID( $foo1=null, $foo2=null ) { return $this->_db->GenID( $foo1, $foo2 ); }
}

/**
 * @version 2.0
 * @created 21-May-2006 12:33:37
 */
function watsMail( $msg, $to, $subject = null )
{
    global $wats;
	$msg = strip_tags( $msg );
	if ( $subject == null )
	{
		$subject = $wats->get( 'newpostmsg' );
	}
	//echo "<p>sending message to ".$to."<br>".$msg."</p>";
	$msg =  $to."\n".
			$wats->get( 'newpostmsg1' )."\n".
			$wats->get( 'newpostmsg2' )."\n".
			$wats->get( 'newpostmsg3' )."\n\n".
			$msg.
			"\n\n\nPowered by WebAmoeba Ticket System";
	mosMail( $wats->get( 'sourceemail' ), $wats->get( 'name' ), $to, $subject, $msg );
}
?>