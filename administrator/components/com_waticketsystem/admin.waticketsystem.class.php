<?php
/**
 * FileName: waticketsystem.class.php
 * Date: 08/10/2006
 * License: GNU General Public License
 * Script Version #: 2.0.5
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
		$returnValue = false;
		// load mosUser
		$this->load( $uid );
		// loadmosWatsUser
		$this->_db->setQuery( "SELECT  u.*, g.name, g.userrites, g.image, g.name AS groupname FROM #__wats_users AS u LEFT  JOIN #__wats_groups AS g ON g.grpid = u.grpid WHERE u.watsid=".$uid );
		$vars = $this->_db->loadObjectList();
		// set attributes
		if ( isset( $vars[0] ) ) {
		    $this->groupName = $vars[0]->groupname ;
		    $this->agree = $vars[0]->agree;
		    $this->organisation = $vars[0]->organisation;
			$this->group = $vars[0]->grpid;
			$this->image = $vars[0]->image;
			$this->groupName = $vars[0]->name;
			$this->userRites = $vars[0]->userrites;
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
		$this->_db->setQuery( "SELECT type FROM #__wats_permissions WHERE catid=".$catid ." AND grpid=".$this->group);
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
		switch ( $remove )
		{
			case 'removeposts':
				// remove all posts
				$this->_db->setQuery( "DELETE FROM #__wats_msg WHERE watsid=".$this->id);
				$this->_db->query();
			case 'removetickets':
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
				break;
		}
		// delete users highlights
		// $this->_db->setQuery( "DELETE FROM #__wats_highlight WHERE watsid=".$this->id);
		// $this->_db->query();
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
	    $this->_db = &$database;
	}
	
	/**
	 * @param groupId
	 */
	function load( $groupId = null )
	{
		// load all users
	    if ( $groupId === null )
		{
			//$this->_db->setQuery( "SELECT * FROM #__wats_users ORDER BY grpid" );
			$this->_db->setQuery( "SELECT w.watsid, w.organisation, w.agree, w.grpid FROM #__wats_users AS w LEFT JOIN #__users AS u ON w.watsid = u.id ORDER BY grpid, username" );
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
		$query = "SELECT * FROM #__wats_ticket WHERE ticketid=".$ticketId;
		// execute query
		$database->setQuery( $query );
		$set = &$database->loadObjectList();
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
	function watsTicket( &$database, $username, $lastWatsId, $name, $watsId, $lastMsg, $datetime, $lifeCycle, $ticketId, $msgNumberOf, $catId, $assignId = null )
	{
		$this->username = $username;
		$this->lastWatsId = $lastWatsId;
		$this->name = $name;
		$this->watsId = $watsId;
		$this->lastMsg = $lastMsg;
		$this->datetime = $datetime;
		$this->lifeCycle = $lifeCycle;
		$this->ticketId = $ticketId;
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
 * Individual WATS User Group Category Permission Class
 * @version 1.0
 * @created 01-May-2006 17:42:08
 */
class watsUserGroupCategoryPermissionSet
{
	var $grpid;
	var $catid;
	var $groupname;
	var $categoryname;
	var $rites;
	var $_new;
	var $_db;

	/**
	 * 
	 */
	function watsUserGroupCategoryPermissionSet( &$database, $grpid, $catid )
	{
		$this->grpid = $grpid;
		$this->catid = $catid;
		$this->_db = &$database;
		$this->categoryRites = array();
		// load group details
		$this->_db->setQuery( "SELECT p.type, g.name as groupname, c.name as categoryname FROM #__wats_permissions AS p LEFT JOIN #__wats_groups AS g ON p.grpid = g.grpid LEFT JOIN #__wats_category AS c ON p.catid = c.catid WHERE p.grpid=".$this->grpid." AND p.catid=".$this->catid );
		$group = $this->_db->loadObjectList();
		// check group exists
		if ( count($group) == 1 )
		{
			$this->groupname = $group[0]->groupname;
			$this->categoryname = $group[0]->categoryname;
			$this->rites = $group[0]->type;
			$this->_new = false;
		}
		else
		{
			$this->groupname = 'unknown group permission set';
			$this->categoryname = 'unknown group permission set';
			$this->_new = true;
		}
	}

	/**
	 *
	 */
	function checkPermission( $rite )
	{
		// prepare for no rite
		$returnValue = 0;
		// find rite in string
		// checks type as well because could return 0
		if ( strpos( $this->rites, strtolower( $rite) ) !== false )
		{
			// check for OWN rite
			$returnValue = 1;
		}
		else if ( strpos( $this->rites, strtoupper( $rite) ) !== false )
		{
			// check for ALL rite
			$returnValue = 2;
		} // end find rite in string
		return $returnValue;
	}
	
	/**
	 *
	 */
	function setPermission( $rite, $level )
	{
		$rites = array( 'V', 'M', 'R', 'C', 'D', 'P', 'A', 'O' );
		// check is valid rite
		$position = array_search( strtoupper( $rite ), $rites );
		if ( $position === false && strlen( $rite ) != 1 )
			return false;
		// check level
		if ( $level > 2 || $level < 0 )
			return false;
		// determine level
		if ( $level == 0 )
		{
			$level = '-';
		}
		elseif ( $level == 1 )
		{
			$level = strtolower( $rite );
		}
		elseif ( $level == 2 )
		{
			$level = strtoupper( $rite );
		}
		// check position
		$checkRite = substr( $this->rites, $position, 1 );
		if ( $checkRite == '-' || $checkRite == strtolower( $rite ) || $checkRite == strtoupper( $rite )  )
		{
			// change rite
			$tempRites = substr( $this->rites, 0, $position );
			$tempRites .= $level;
			$tempRites .= substr( $this->rites, $position + 1, strlen( $this->rites ) - ($position + 1)  );
			$this->rites = $tempRites;
		}
		else
		{
			// rites messed up, append to end (run db maintenance to resolve)
			$this->rites = $level;
		}
		return true;
	}
	
	/**
	 * 
	 */
	function save()
	{
		$this->_db->setQuery( "UPDATE #__wats_permissions SET type=\"".$this->rites."\" WHERE catid=".$this->catid." AND grpid=".$this->grpid.";" );
		$this->_db->query();
	}
	
	/**
	 * static
	 */
	function newPermissionSet( $grpId, $catId, &$database )
	{
		// check doesn't already exist
		$database->setQuery( "SELECT type FROM #__wats_permissions WHERE catid=".$catId." AND grpid=".$grpId);
		$database->query();
		if ( $database->getNumRows() == 0 )
		{
			// create SQL
			$database->setQuery( "INSERT INTO #__wats_permissions ( catid, grpid, type ) VALUES ( '".$catId."', '".$grpId."', '--------' );" );
			// execute
			$database->query();
			return true;
		}
		else
		{
			// category with that name already exists
			return false;
		} // end check doesn't already exist
	}
}

/**
 * @version 1.0
 * @created 09-Jan-2006 15:30
 */
class watsUserGroupCategoryPermissionSetSet
{
	var $watsUserGroupCategoryPermissionSet;
	var $noOfSets;
	var $groupId;
	var $_db;

	/**
	 * @param database
	 */
	function watsUserGroupCategoryPermissionSetSet( &$database )
	{
	    $this->_db = &$database;
	}
	
	/**
	 * @param groupId
	 */
	function load( $groupId )
	{
		$this->groupId = $groupId;
		// load all sets
		$this->_db->setQuery( "SELECT catid FROM #__wats_category ORDER BY catid" );
		$set = $this->_db->loadObjectList();
		$this->noOfSets = count( $set );
		$i = 0;
		// create sets
		while ( $i < $this->noOfSets )
		{
			$this->watsUserGroupCategoryPermissionSet[$i] = new watsUserGroupCategoryPermissionSet( $this->_db, $groupId, $set[$i]->catid );
			//$this->userSet[$i]->loadWatsUser( $set[$i]->watsid  );
			$i ++;
		} // end create sets
		// end load all sets
	}
}

/**
 * Individual WATS User Group Class
 * @version 1.0
 * @created 01-May-2006 15:59:42
 */
class watsUserGroup
{
	var $grpid;
	var $name;
	var $image;
	var $userRites;
	var $categoryRites;
	var $_users;
	var $_new;
	var $_db;

	/**
	 * 
	 */
	function watsUserGroup( &$database, $grpid = -1 )
	{
		$this->grpid = $grpid;
		$this->_db = &$database;
		$this->categoryRites = array();
		$this->_users = array();
		// load group details
		$this->_db->setQuery( "SELECT * FROM #__wats_groups WHERE grpid=".$this->grpid );
		$group = $this->_db->loadObjectList();
		// check group exists
		if ( count($group) == 1 )
		{
			$this->name = $group[0]->name;
			$this->image = $group[0]->image;
			$this->userRites = $group[0]->userrites;
			$this->_new = false;
			$this->categoryRites = new watsUserGroupCategoryPermissionSetSetHTML($database);
			$this->categoryRites->load( $grpid );
		}
	}
	
	/**
	 * 
	 */
	function newPermissionSet( $catId )
	{
		return watsUserGroupCategoryPermissionSet::newPermissionSet( $this->grpid , $catId, $this->_db );
	}

	/**
	 * Load group rites to categories
	 */
	function loadCategoryRites()
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
	 * V = view users
	 * M = make users
	 * E = edit users
	 * D = delete users
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
	 * V = view users
	 * M = make users
	 * E = edit users
	 * D = delete users
	 */
	function setUserPermission( $rite, $level )
	{
		$rites = array( 'V', 'M', 'E', 'D' );
		$rite = strtoupper( $rite );
		// check is valid rite
		$position = array_search( $rite, $rites );
		if ( $position === false && strlen( $rite ) != 1 )
			return false;
		// check level
		if ( ! is_bool( $level ) )
			return false;
		// check position
		$checkRite = substr( $this->userRites, $position, 1 );
		if ( $checkRite == '-' || $checkRite == $rite )
		{
			// change rite
			$tempRites = substr( $this->userRites, 0, $position );
			if ( $level )
			{
				$tempRites .= $rite;
			}
			else
			{
				$tempRites .= '-';
			}
			$tempRites .= substr( $this->userRites, $position + 1, strlen( $this->userRites ) - ($position + 1)  );
			$this->userRites = $tempRites;
		}
		else
		{
			// rites messed up check if is in rites
			$position = strstr( $rite, $this->userRites );
			if ( $position === false )
			{
				// append to end (run db maintenance to resolve)
				if ( $level )
				{
					$tempRites .= $rite;
				}
				else
				{
					$tempRites .= '-';
				}
			}
			else
			{
				// bung in alternate position
				$tempRites = substr( $this->rites, 0, $position );
				if ( $level )
				{
					$tempRites .= $rite;
				}
				else
				{
					$tempRites .= '-';
				}
				$tempRites .= substr( $this->rites, $position + 1, strlen( $this->rites ) - ($position + 1)  );
				$this->userRites = $tempRites;
			}
		}
		return true;
	}
	
	/**
	 * 
	 */
	function save()
	{
		$this->_db->setQuery( "UPDATE #__wats_groups SET name=\"".$this->name."\", image=\"".$this->image."\", userrites=\"".$this->userRites."\" WHERE grpid=".$this->grpid.";" );
		$this->_db->query();
	}
	
	/**
	 * 
	 */
	function loadUsers()
	{
		$this->_users = null;
		$this->_users = array();
		$this->_db->setQuery( "SELECT watsid FROM #__wats_users WHERE grpid=".$this->grpid.";" );
		$users = $this->_db->loadObjectList();
		foreach ( $users as $user )
		{
			echo 'a';
			$tempUser = new watsUser( $this->_db );
			echo 'b';
			$tempUser->loadWatsUser( $user->watsid );
			echo 'c';
			$this->_users[] = $tempUser;
			echo 'd';
		}
	}
	
	/**
	 * 
	 */
	function delete( $option )
	{
		$this->loadUsers();
		foreach ( $this->_users as $editUser )
		{
			$editUser->delete( $option );
		}
		// remove permission sets
		$this->_db->setQuery( "DELETE FROM #__wats_permissions WHERE grpid=".$this->grpid.";" );
		$this->_db->query();
		// remove group
		$this->_db->setQuery( "DELETE FROM #__wats_groups WHERE grpid=".$this->grpid.";" );
		$this->_db->query();
	}
	
	/**
	 * static
	 */
	function makeGroup( $name, $image, &$database )
	{
		// create new category
		$database->setQuery( "INSERT INTO #__wats_groups ( name, image, userrites ) VALUES ( '".$name."', '".$image."', '----' );" );
		$database->query();
		// create object
		$newGroup = new watsUserGroup( $database, $database->insertid() );
		// create permission sets
		$database->setQuery( "SELECT c.catid FROM #__wats_category AS c;" );		
		$categories = &$database->loadObjectList();
		foreach ( $categories as $category )
		{
			$newGroup->newPermissionSet( $category->catid );
		}
		// return new group
		return $newGroup;
	}
}

/**
 * @version 1.0
 * @created 06-Dec-2005 21:43:47
 */
class watsUserGroupSet
{
	var $noOfGroups;
	var $_userGroupList;
	var $_db;

	/**
	 * 
	 * @param database
	 */
	function watsUserGroupSet( &$database )
	{
		$this->_db = &$database;
		$this->noOfGroups = 0;
		$this->_userGroupList = array();
	}

	/**
	 * 
	 */
	function loadUserGroupSet()
	{
		// create query
		$query = $sql = "SELECT grpid FROM #__wats_groups ORDER BY name";
		// end create query
		$this->_db->setQuery( $query );
		$set = $this->_db->loadObjectList();
		// check there are results
		if ( $set != null )
		{
			// create user group objects
			foreach( $set as $group )
			{
				// create object
				$this->_userGroupList[$this->noOfGroups] = new watsUserGroupHTML( $this->_db, $group->grpid );
				// increment counter
				$this->noOfGroups ++;
			}// end create user group objects
		} // end check there are results
	}
	
	/**
	 * 
	 */
	function getNamesAndIds()
	{
		$array = array();
		foreach( $this->_userGroupList as $group )
		{
			$array[$group->grpid] = $group->name;
		}
		asort( $array );
		return $array;
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
		$this->_db = &$database;
		$this->ticketNumberOf = 0;
		$this->_ticketListPointer = 0;
	}

	/**
	 * 
	 * @param lifeCycle (-1 = all, 0 = open and closed, 1 = open, 2 = closed, 3 = dead)
	 * @param watsid
	 * @param category (id of category, -1 = all categories)
	 * @param riteAll (true = show all users tickets)
	 * @param assign ( true = assigned tickets only)
	 */
	 //$this->ticketSet->loadTicketSet( 0, $this->watsId, -1, true, true );
	function loadTicketSet( $lifecycle, $category = null )
	{
		// create query
		$query = $sql = "SELECT COUNT(*) AS posts, t.ticketid, t.assign, t.watsid AS ownerid, t.ticketname, t.category, t.lifecycle, UNIX_TIMESTAMP(t.datetime) AS firstpost, SUBSTRING(MIN(CONCAT(DATE_FORMAT(m1.datetime, '%Y-%m-%d %H:%i:%s'), m1.msgid)), 20) as firstmsg, SUBSTRING(MAX(CONCAT(DATE_FORMAT(m1.datetime, '%Y-%m-%d %H:%i:%s'), m1.msgid)), 20) as lastpostid, SUBSTRING(MAX(CONCAT(DATE_FORMAT(m1.datetime, '%Y-%m-%d %H:%i:%s'), m1.watsid)), 20) as lastid, UNIX_TIMESTAMP(MAX(m1.datetime)) as lastdate, o.username AS username, SUBSTRING(MAX(CONCAT(DATE_FORMAT(m1.datetime, '%Y-%m-%d %H:%i:%s'), p.username)), 20) AS poster FROM #__wats_ticket AS t LEFT JOIN #__wats_msg AS m1 ON t.ticketid = m1.ticketid LEFT JOIN #__users AS o ON t.watsid = o.id LEFT JOIN #__users AS p ON m1.watsid = p.id ";
		// check lifeCycle
		if( $lifecycle == -1 )
		{
			// do nothing select all
		}
		elseif ( $lifecycle == 0 )
		{
			$query .= "WHERE ( t.lifecycle=1 OR t.lifecycle=2 )";
		}
		else
		{
			$query .= "WHERE t.lifecycle=".$lifecycle;
		}
		if ( $category != null AND $category != -1 )
		{
			// set category
			$query .= " AND category=".$category;
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
				$this->_ticketList[$this->ticketNumberOf] = new watsTicketHTML( $this->_db, $ticket->username, $ticket->lastid, $ticket->ticketname, $ticket->ownerid, $ticket->lastdate, $ticket->firstpost, $ticket->lifecycle, $ticket->ticketid, $ticket->posts, $ticket->category, $ticket->assign );
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
		$assignees = &$database->loadObjectList( );
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
	
	/**
	 * static
	 */
	function newCategory( $name, $description, $image, &$database )
	{
		// check doesn't already exist
		$database->setQuery( "SELECT name FROM #__wats_category WHERE name='".$name."';");
		$database->query();
		if ( $database->getNumRows() == 0 )
		{
			// create SQL
			$database->setQuery( "INSERT INTO #__wats_category ( name, description, image ) VALUES ( '".$name."', '".$description."', '".$image."' );" );
			// execute
			$database->query();
			$newCategoryId = &$database->insertid();
			// iterate through user groups and create rites entries
			$watsUserGroupSet =  new watsUserGroupSet( $database );
			$watsUserGroupSet->loadUserGroupSet();
			//print_r( $watsUserGroupSet );
			foreach ( $watsUserGroupSet->_userGroupList as $watsUserGroup )
			{
				//print_r($watsUserGroup);
				$watsUserGroup->newPermissionSet( $newCategoryId );
			}
			return true;
		}
		else
		{
			// category with that name already exists
			return false;
		} // end check doesn't already exist
	}
	
	/**
	 *
	 */
	function updateCategory()
	{
		// check already exists
		$this->_db->setQuery( "SELECT catid FROM #__wats_category WHERE catid=".$this->catid);
		$this->_db->query();
		if ( $this->_db->getNumRows() != 0 )
		{
			// update SQL
			$this->_db->setQuery( "UPDATE #__wats_category SET name='".$this->name."', description='".$this->description."', image='".$this->image."' WHERE catid='".$this->catid."';" );
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
	 */
	function delete()
	{
		// remove tickets
		$this->_db->setQuery( "DELETE FROM #__wats_ticket WHERE category=".$this->catid.";" );
		$this->_db->query();
		// remove rites matrixes
		$this->_db->setQuery( "DELETE FROM #__wats_permissions WHERE catid=".$this->catid.";" );
		$this->_db->query();
		// remove category
		$this->_db->setQuery( "DELETE FROM #__wats_category WHERE catid=".$this->catid.";" );
		$this->_db->query();
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
	function __construct( &$database )
	{
		$this->_db = &$database;
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
	function __construct( &$database )
	{
		$this->_db = &$database;
		// load categories
		$this->_db->setQuery( "SELECT * FROM #__wats_category" );
		$vars = $this->_db->loadObjectList();
		// create category objects
		$i = 0;
		foreach( $vars as $var )
		{
			// create object
			$this->categorySet[$i] = new watsCategoryHTML( $this->_db );
			// load object
			$this->categorySet[$i]->load( $var->catid );
			// increment counter
			$i ++;
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
 * @created 11-Feb-2006 13:23:36
 */
class watsCss
{
	var $path;
	var $cssStyles;
	var $css;

	/**
	 * 
	 */
	function __construct( &$database )
	{
		$this->cssStyles = array();
		$database->setQuery( "SELECT value FROM #__wats_settings WHERE name=\"css\"" );
		$this->css = &$database->loadObjectList();
		$this->css = $this->css[0]->value;
	}

	/**
	 * opens and parses file
	 */
	function open($pathIn)
	{
		// check path exists
		if ( file_exists ( $pathIn ) )
		{
			// set path
			$this->path = $pathIn;
			// open file
			$cssFile = fopen( $this->path, "r" );
			// read file
			$cssFileContent = fread( $cssFile, filesize( $this->path ) );
			// close file
			fclose( $cssFile );
			// parse file
			{
				// replace unnecessary white spaces with one 
				$cssFileContent = preg_replace( "/[\s]+/", ' ', $cssFileContent );
				// divide into styles
				$cssFileStyles = explode("}", $cssFileContent);
				// loop through styles
				foreach ($cssFileStyles as $cssStyle)
				{
					// get selector
					$cssSelector = trim ( substr( $cssStyle, 0,  strpos( $cssStyle, '{' ) )) ;
					// check is valid selector before continuing
					if ( strlen( $cssSelector ) > 0 )
					{
						// get properties
						$cssProperties = trim ( substr( $cssStyle, strpos( $cssStyle, '{' ) + 1, strlen( $cssStyle ) ) ) ;
						$cssProperties = str_replace("; ", ";\n", $cssProperties);
						// add to styles
						$this->cssStyles[ $cssSelector ] = $cssProperties;
					}
				}
				// end loop through styles
			}
			//end parse file
		}
		// end check path exists
	}
	
	/**
	 * 
	 */
	function save()
	{
		// check can write to file
		if ( is_writable( $this->path ) )
		{
			// write to file
			if ( $cssFile = fopen( $this->path, "wb" ) )
			{
				// prepare file content
				$cssFileContent = '';
				$keys = array_keys( $this->cssStyles );
				// iterate through styles
				foreach( $keys as $key )
				{
					// add style to content
					$cssFileContent .= $key."\r\n{\r\n".$this->cssStyles[$key]."\r\n}\r\n\r\n";
				}
				// end iterate through styles
				// end prepare file content
				if ( fwrite($cssFile, $cssFileContent) === false )
				{
					echo "<p>An error occured when attempting to open the css file for writing.</p>";
				}
				// close file
				fclose( $cssFile );
			}
			else
			{
				echo "<p>An error occured when attempting to open the css file for writing.</p>";
			}
			// end write to file
		}
		else
		{
			echo "<p>Unable to write to css file. Plase change the file rites.</p>";
		}
		// end check can write to file
	}
	
	/**
	 * returns style if exists, else returns false.
	 * @param selector of selector
	 */
	function getStyle( $selector )
	{
		// check for style
		if ( isset( $this->cssStyles[ $selector ] ) )
		{
			// return style
			return $this->cssStyles[ $selector ];
		}
		else
		{
			// return no style
			return false;
		}
	}
	
	/**
	 * sets style properties, adds style if does not exist.
	 * @param selector of style
	 * @param properties of style
	 */
	function setStyle( $selector, $properties )
	{
		// check for style
		if ( isset( $this->cssStyles[ $selector ] ) )
		{
			$this->cssStyles[ $selector ] = $properties;
		}
	}
	
	/**
	 * returns array of styles.
	 */
	function getAllStyles()
	{
		return $this->cssStyles;
	}
	
	/**
	 * restores installation default css.
	 * @param path to restore from.
	 */
	function restore( $restorePath )
	{
		// check retoreFile exists
		if ( is_file( $restorePath ) == false )
			return false;
		// check can read restore file
		if ( is_readable( $restorePath ) == false )
			return false;
		// check can write to file
		if ( is_writable( $this->path ) == false )
			return false;
		// start restore
		{
			{
				// open to read
				$restoreFile = fopen( $restorePath, "r" );
				// read file
				$restoreFileContent = fread( $restoreFile, filesize( $restorePath ) );
				// close file
				fclose( $restoreFile );
			}
			if ( $cssFile = fopen( $this->path, "wb" ) )
			{
				// write
				if ( fwrite($cssFile, $restoreFileContent) === false )
				{
					return false;
				}
				// close file
				fclose( $cssFile );
				// end wite
			}
			else
			{
				return false;
			}
		}
		// end restore
		return true;
	}
	
}

/**
 * @version 1.0
 * @created 06-Dec-2005 21:44:11
 */
class watsSettings
{
    var $_settings;
	var $_db;

	/**
	 * 
	 * @param database
	 */
	function __construct( &$database )
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
		$this->_settings = null;
		// load settings
		$this->_db->setQuery( "SELECT * FROM #__wats_settings" );
		$vars = $this->_db->loadObjectList();
		// create category objects
		foreach( $vars as $var )
		{
			// create index in array and give value
			$this->_settings[$var->name] = $var->value;
		}
	}
	
	/**
	 * 
	 *  @param name of setting
	 */
	function get( $name )
	{
		if ( isset( $this->_settings[$name] ) )
		{
			return $this->_settings[$name];
		}
	}
	
	/**
	 * 
	 *  @param name of setting
	 *  @param value of setting
	 */
	function set( $name, $value )
	{
		if ( isset( $this->_settings[$name] ) )
		{
			$this->_settings[$name] = $value;
		}
	}
	
	/**
	 * 
	 */
	function save()
	{
		$keys = array_keys( $this->_settings );
		foreach( $keys as $key )
		{
			// changed for MySQL prior to 4.2, does not allow AS in UPDATE statement.
			// $this->_db->setQuery( "UPDATE #__wats_settings AS s SET s.value=\"".$this->_settings[$key]."\" WHERE s.name=\"".$key."\";" );
			$this->_db->setQuery( "UPDATE #__wats_settings SET value=\"".$this->_settings[$key]."\" WHERE name=\"".$key."\";");
			$this->_db->query();
		}
	}
}

/**
 * @version 1.0
 * @created 07-May-2006 15:44:11
 */
class watsDatabaseMaintenance
{
	var $_db;

	/**
	 * 
	 * @param database
	 */
	function __construct( &$database )
	{
		// set db
		$this->_db = &$database;
	}
	
	/**
	 * 
	 */
	function performOrphanUsers()
	{
		// find errors
		$this->_db->setQuery( "SELECT w.watsid, u.id AS id FROM #__wats_users AS w LEFT JOIN #__users AS u ON u.id = w.watsid WHERE u.id is null;" );
		$errors = $this->_db->loadObjectList();
		// find errors
		// resolve errors
		foreach( $errors as $error )
		{
			// remove orphan users
			$orphanUser = new watsUserHTML( $this->_db );
			$orphanUser->loadWatsUser( $error->watsid );
			$orphanUser->delete( 'removeposts' );
		}
		// end resolve errors
		return count( $errors );
	}
	
	/**
	 * 
	 */
	function performUserPermissionsFormat()
	{
		$this->_db->setQuery( "SELECT grpid, userrites FROM #__wats_groups;" );
		$rows = $this->_db->loadObjectList();
		$errors = array();
		$rites = array( 'V', 'M', 'E', 'D' );
		// find errors
		foreach( $rows as $row )
		{
			// check length
			if ( strlen( $row->userrites ) != 4 )
			{
				$errors[] = $row;
			}
			else
			{
				// prepare rites
				$ritesArray = $this->_stringToCharArray( strtoupper( $row->userrites ) );
				// check for unknown occurences
				for ( $i = 0; $i < 4 ; $i ++ )
				{
					if ( $ritesArray[$i] != $rites[$i] && $ritesArray[$i] != '-' )
					{
						// add error
						$errors[] = $row;
						// stop itearor
						$i = 4;
					}
				}
			}
		}
		// end find errors
		
		return count( $errors );
	}
	
	/**
	 * 
	 */
	function performPermissionSetsFormat()
	{
		$this->_db->setQuery( "SELECT grpid, catid, type FROM #__wats_permissions;" );
		$rows = $this->_db->loadObjectList();
		$errors = array();
		$rites = array( 'V', 'M', 'R', 'C', 'D', 'P', 'A', 'O' );
		// find errors
		foreach( $rows as $row )
		{
			// check length
			if ( strlen( $row->type ) != 8 )
			{
				$errors[] = $row;
			}
			else
			{
				// prepare rites
				$ritesArray = $this->_stringToCharArray( strtoupper( $row->type ) );
				// check for unknown occurences
				for ( $i = 0; $i < 8 ; $i ++ )
				{
					if ( $ritesArray[$i] != $rites[$i] && $ritesArray[$i] != '-' )
					{
						// add error
						$errors[] = $row;
						// stop itearor
						$i = 8;
					}
				}
			}
		}
		// end find errors
		// resolve errors
		foreach( $errors as $error )
		{
			// rebuild rites
			$newRites = "";
			foreach( $rites as $rite )
			{
				if ( strstr( $error->type, strtoupper( $rite ) ) !== FALSE )
				{
					// All rites
					$newRites .= strtoupper( $rite );
				}
				else if ( strstr(  $error->type, strtolower( $rite ) ) !== FALSE )
				{
					// Own rites
					$newRites .= strtolower( $rite );
				}
				else
				{
					// No rites
					$newRites .= '-';
				}
			}
			// apply new rites string
			$this->_db->setQuery( "UPDATE #__wats_permissions SET p.type=\"".$newRites."\" WHERE p.grpid=".$error->grpid." AND p.catid=".$error->catid.";" );
			$this->_db->query();
		}
		// end resolve errors
		return count( $errors );
	}
	
	/**
	 *
	 */
	function _stringToCharArray( $str )
	{
		$length = strlen( $str );
		$output = array();
		for( $i = 0; $i < $length; $i++ )
		{
			$output[$i] = $temp_output = substr( $str, $i, 1 );
		}
		return $output;
	}
	
	/**
	 * 
	 */
	function performOrphanPermissionSets()
	{
		// get group missing
		$this->_db->setQuery( "SELECT p.grpid, p.catid FROM #__wats_permissions AS p LEFT JOIN #__wats_groups AS g ON p.grpid = g.grpid WHERE g.grpid IS NULL;" );
		$groupErrors = array();
		$groupErrors = $this->_db->loadObjectList();
		// end group missing
		// get category missing
		$this->_db->setQuery( "SELECT p.grpid, p.catid FROM #__wats_permissions AS p LEFT JOIN #__wats_category AS c ON p.catid = c.catid WHERE c.catid IS NULL;" );
		$categoryErrors = array();
		$categoryErrors = $this->_db->loadObjectList();
		// end category missing
		
		// merge arrays
		$errors = $categoryErrors;
		foreach ( $groupErrors as $groupError )
		{
			$found = false;
			foreach ( $categoryErrors as $categoryError )
			{
				if ( $groupError->grpid == $categoryError->grpid && $groupError->catid == $categoryError->catid )
				{
					$found = true;
				}
			}
			if ( $found == false )
			{
				$errors[] = $groupError;
			}
		}
		// end merge arrays
	
		// resolve errors
		foreach( $errors as $error )
		{
			// apply new rites string
			$this->_db->setQuery( "DELETE FROM #__wats_permissions WHERE grpid=".$error->grpid." AND catid=".$error->catid.";" );
			$this->_db->query();
		}
		// end resolve errors*/
		return count( $errors );
	}
	
	/**
	 * 
	 */
	function performOrphanTickets()
	{
		// get user missing
		$this->_db->setQuery( "SELECT t.ticketid, u.id FROM #__wats_ticket AS t LEFT JOIN #__users AS u ON t.watsid = u.id WHERE u.id IS NULL;" );
		$userErrors = array();
		$userErrors = $this->_db->loadObjectList();
		// end user missing
		// get category missing
		$this->_db->setQuery( "SELECT t.ticketid, t.category, c.catid FROM #__wats_ticket AS t LEFT JOIN #__wats_category AS c ON t.category = c.catid WHERE c.catid IS NULL;" );
		$categoryErrors = array();
		$categoryErrors = $this->_db->loadObjectList();
		// end category missing
		
		// merge arrays
		$errors = $categoryErrors;
		foreach ( $userErrors as $userError )
		{
			$found = false;
			foreach ( $categoryErrors as $categoryError )
			{
				if ( $userError->ticketid == $categoryError->ticketid )
				{
					$found = true;
				}
			}
			if ( $found == false )
			{
				$errors[] = $userError;
			}
		}
		// end merge arrays
	
		// resolve errors
		foreach( $errors as $error )
		{
			// remove messages
			$this->_db->setQuery( "DELETE FROM #__wats_msg WHERE ticketid=".$error->ticketid.";" );
			$this->_db->query();
			// remove ticket
			$this->_db->setQuery( "DELETE FROM #__wats_ticket WHERE ticketid=".$error->ticketid.";" );
			$this->_db->query();
		}
		// end resolve errors
		return count( $errors );
	}
	
	/**
	 * 
	 */
	function performOrphanMessages()
	{
		// get user missing
		$this->_db->setQuery( "SELECT m.msgid FROM #__wats_msg AS m LEFT JOIN #__users AS u ON m.watsid = u.id WHERE u.id IS NULL;" );
		$userErrors = array();
		$userErrors = $this->_db->loadObjectList();
		// end user missing
		// get ticket missing
		$this->_db->setQuery( "SELECT m.msgid FROM #__wats_msg AS m LEFT JOIN #__wats_ticket AS t ON m.ticketid = t.ticketid WHERE t.ticketid IS NULL;" );
		$ticketErrors = array();
		$ticketErrors = $this->_db->loadObjectList();
		// end ticket missing
		
		// merge arrays
		$errors = $ticketErrors;
		foreach ( $userErrors as $userError )
		{
			$found = false;
			foreach ( $ticketErrors as $ticketError )
			{
				if ( $userError->msgid == $ticketError->msgid )
				{
					$found = true;
				}
			}
			if ( $found == false )
			{
				$errors[] = $userError;
			}
		}
		// end merge arrays
	
		// resolve errors
		foreach( $errors as $error )
		{
			// remove messages
			$this->_db->setQuery( "DELETE FROM #__wats_msg WHERE msgid=".$error->msgid.";" );
			$this->_db->query();
		}
		// end resolve errors
		return count( $errors );
	}
	
	/**
	 * 
	 */
	function performMissingPermissionSets()
	{
		// get number of groups
		$this->_db->setQuery( "SELECT COUNT(*) AS size FROM #__wats_groups" );
		$groupCounter = $this->_db->loadObjectList();
		// get number of categories
		$this->_db->setQuery( "SELECT COUNT(*) AS size FROM #__wats_category" );
		$categoryCounter = $this->_db->loadObjectList();
		// get number of sets
		$this->_db->setQuery( "SELECT COUNT(*) AS size FROM #__wats_permissions" );
		$setCounter = $this->_db->loadObjectList();
		// number of sets that should exist
		$sets = $groupCounter[0]->size * $categoryCounter[0]->size;
		// number of sets missing
		$totalMissingSets = $sets - $setCounter[0]->size;
		
		if ( $totalMissingSets == 0 )
		{
			// no inconsistencies
			return 0;
		}
		else
		{
			// determine where inconsistencies are and resolve them
			// get groups
			$this->_db->setQuery( "SELECT grpid FROM #__wats_groups" );
			$groups = array();
			$groups = $this->_db->loadObjectList();
			// get categories
			$this->_db->setQuery( "SELECT catid FROM #__wats_category" );
			$categories = array();
			$categories = $this->_db->loadObjectList();
			// itterate through groups
			foreach ( $groups as $group )
			{
				// itterate through categories
				foreach ( $categories as $category )
				{
					// check set exists
					$this->_db->setQuery( "SELECT COUNT(*) AS size FROM #__wats_permissions WHERE catid=".$category->catid." AND grpid=".$group->grpid.";" );
					$result = $this->_db->loadObjectList();
					// check exists
					if ( $result[0]->size != 1 )
					{
						// inconsistency found -> create missing set
						$watsUserGroup = new watsUserGroup( $this->_db, $group->grpid );
						$watsUserGroup->newPermissionSet( $category->catid );
					}
				}
			}
		}
		return $totalMissingSets;
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
	function __construct( $name, $sql='', $errorNum=0, $errorMsg='', $count=null )
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
	function __construct( &$database )
	{
		// set db
		$this->_db = $database;
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
	function insertObject( $table, &$object, $keyName = NULL, $verbose=false ) { return $this->_db->insertObject( $table, $object, $keyName, $verbose ); }
	function updateObject( $table, &$object, $keyName, $updateNulls=true ) { return $this->_db->updateObject( $table, $object, $keyName, $updateNulls ); }
	function stderr( $showSQL = false ) { return $this->_db->stderr( $showSQL ); }
	function insertid() { return $this->_db->insertid(); }
	function getVersion() { return $this->_db->getVersion(); }
	function getTableList() { return $this->_db->getTableList(); }
	function getTableCreate( $tables ) { return $this->_db->getTableCreate( $tables ); }
	function getTableFields( $tables ) { return $this->_db->getTableFields( $tables ); }
	function GenID( $foo1=null, $foo2=null ) { return $this->_db->GenID( $foo1, $foo2 ); }
}

?>