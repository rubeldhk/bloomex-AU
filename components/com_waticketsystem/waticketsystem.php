<?php
/**
 * FileName: waticketsystem.php
 * Date: 03/09/2006
 * License: GNU General Public License
 * Script Version #: 2.0.4
 * JOS Version #: 1.0.x
 * Development James Kennard jg8949@aol.com (www.webamoeba.co.uk)
 */

// Don't allow direct linking
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
echo '<div class="wats">';

//add custom classes and functions
require('components/com_waticketsystem/waticketsystem.html.php');

// add database wrapper
// $watsDatabase = new watsDatabaseWrapperHTML( $database );
$watsDatabase = $database;

// get settings
$wats = new watsSettings( $watsDatabase );

// check for database debug
if ( $wats->get( 'debug' ) != 0 )
{
	$watsDatabase = new watsDatabaseWrapperHTML( $database );
	$watsDatabase->viewHeader();
}

//install lang file
require('components/com_waticketsystem/lang/'.$wats->get( 'lang' ));

// add css link if turned on
echo ($wats->get( 'css' ) == 'enable') ? "<link rel=\"stylesheet\" href=\"components/com_waticketsystem/wats.css\" type=\"text/css\" />" : "";

// create watsUser
// check id is set and watsUser exists

prevArray( $_GET );
prevLink( $_GET );

if ( $my->id == 0 OR ( $watsUser = new watsUserHTML( $watsDatabase ) AND $watsUser->loadWatsUser( $my->id  ) == false ))
{
	echo _WATS_ERROR_NOUSER;
}
else
{
	// check for agreement
	if ( $wats->get( 'agree' ) == 1 && $watsUser->agree == 0 && !isset($_POST['agree']) )
	{
		// needs to sign agreement
		echo '<p>'.$wats->get( 'agreelw' ).'</p>';
		echo '<p><a href="index.php?option=com_content&task=view&id='.$wats->get( 'agreei' ).'">'.$wats->get( 'agreen' ).'</a></p>';
		echo '<p>'.$wats->get( 'agreela' ).'</p>';
		echo '<form name="agree" method="post" action="'.$PHP_SELF.'?option=com_waticketsystem&Itemid='.$Itemid.'"><input type="submit" name="agree" value="'.$wats->get( 'agreeb' ).'"></form>';		
	}
	elseif ( isset($_POST['agree']) )
	{
		// user has agreed
		$watsUser->agree = 1;
		$watsUser->updateUser();
		// redirect
		watsredirect( "index.php?option=com_waticketsystem&Itemid=".$Itemid );
	}// end check for agreement
	else
	{
		$watsUser->view();
		
		//check user exists and has agreed to contract
		
		// parse GET action
		if ( isset( $_GET['act'] ) )
		{
			$act = trim( $_GET['act'] );
		}
		else
		{
			$act = null;
		} // end parse GET action
		// add javaScript
		echo "<script language=\"javascript\" type=\"text/javascript\" src=\"components/com_waticketsystem/wats.js\"></script>";
		// create category set
		$watsCategorySet = new watsCategorySetHTML( $watsDatabase, $watsUser );
		
		/*// create new navigation
		echo "<div id=\"watsNavigation\" class=\"watsNavigation\">
				<form name=\"watsTicketMake\" method=\"get\" action=\"index.php\">
				  <input name=\"option\" type=\"hidden\" value=\"com_waticketsystem\">
				  <input name=\"Itemid\" type=\"hidden\" value=\"".$Itemid."\">
				  <input name=\"act\" type=\"hidden\" value=\"ticket\">
				  <input name=\"task\" type=\"hidden\" value=\"make\">
				  <input type=\"submit\" name=\"watsTicketMake\" value=\""._WATS_TICKETS_SUBMIT."\" class=\"watsFormSubmit\">
				</form> ";
		$watsCategorySet->select();
		echo "</div>";
		
		// create find navigation
		echo "<div id=\"watsNavigation\" class=\"watsNavigation\">
				<form name=\"watsTicketMake\" method=\"get\" action=\"index.php\">
				  <input name=\"option\" type=\"hidden\" value=\"com_waticketsystem\">
				  <input name=\"Itemid\" type=\"hidden\" value=\"".$Itemid."\">
				  <input name=\"act\" type=\"hidden\" value=\"ticket\">
				  <input name=\"task\" type=\"hidden\" value=\"view\">
				  WATS-
				  <input name=\"ticketid\" type=\"text\" id=\"ticketid\" maxlength=\"255\" size=\"6\" />
				  <input type=\"submit\" name=\"watsTicketMake\" value=\""._WATS_MISC_GO."\" class=\"watsFormSubmit\">
				</form> ";
		echo "</div>";*/
		
		// create new navigation
		echo "<div id=\"watsNavigation\">
				<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
				  <tr>
				    <th>"._WATS_NAV_NEW."</th>
				    <th>"._WATS_NAV_CATEGORY."</th>
				    <th>"._WATS_NAV_TICKET."</th>
			      </tr>
				  <tr>
					<td width=\"33%\">
						<form name=\"watsTicketMake\" method=\"get\" action=\"index.php\">
						  <input name=\"option\" type=\"hidden\" value=\"com_waticketsystem\">
						  <input name=\"Itemid\" type=\"hidden\" value=\"".$Itemid."\">
						  <input name=\"act\" type=\"hidden\" value=\"ticket\">
						  <input name=\"task\" type=\"hidden\" value=\"make\">
						  <input type=\"submit\" name=\"watsTicketMake\" value=\""._WATS_TICKETS_SUBMIT."\" class=\"watsFormSubmit\">
						</form> 
					</td>
					<td>";
		// dispaly navigation drop down menu
		if ( $act == '' )
		{
			$watsCategorySet->select( -1 );
		}
		else if ( $act == 'category' )
		{
			// send viewing category ID
			$watsCategorySet->select( $_GET['catid'] );
		}
		else
		{
			// not viewing category
			$watsCategorySet->select( null );
		}
		echo       "</td>
					<td width=\"33%\">
						<form name=\"watsTicketMake\" method=\"get\" action=\"index.php\">
						  <input name=\"option\" type=\"hidden\" value=\"com_waticketsystem\">
						  <input name=\"Itemid\" type=\"hidden\" value=\"".$Itemid."\">
						  <input name=\"act\" type=\"hidden\" value=\"ticket\">
						  <input name=\"task\" type=\"hidden\" value=\"view\">
						  WATS-
						  <input name=\"ticketid\" type=\"text\" id=\"ticketid\" maxlength=\"255\" size=\"6\" />
						  <input type=\"submit\" name=\"watsTicketMake\" value=\""._WATS_MISC_GO."\" class=\"watsFormSubmit\">
						</form> 
					</td>
				  </tr>
				</table>";
		echo "</div>";
	
		
		// perform selected operation
		watsOption( $task, $act );
	}
	
	
	// check for database debug
	if ( $wats->get( 'debug' ) == 1 )
	{
		$watsDatabase->viewFooter();
	}

}
?>
<p class="watsCopyright"><?php echo $wats->get( 'copyright' )?></p>
</div>
<?php
function watsOption( $task, $act )
{
	global $watsUser, $watsDatabase, $Itemid, $wats, $watsCategorySet;

	switch ($act) {
		/**
		 * ticket
		 */	
		case 'ticket':
			switch ($task) {
				/**
				 * ticket view
				 */	
				case 'view':
					// create ticket object
					$ticket = watsObjectBuilder::ticket( $watsDatabase, intval( addslashes( $_GET[ 'ticketid' ] ) ) );
					// check there is a ticket
					if ( $ticket != null )
					{
						// check rites
						$rite =  $watsUser->checkPermission( $ticket->category, "v" );
						if ( ( $ticket->watsId == $watsUser->id AND $rite > 0 ) OR ( $rite == 2 ) )
						{
							// allow user to view ticket
							$ticket->loadMsgList();
							$ticket->view( $watsUser );
						}
						else
						{
							echo _WATS_ERROR_ACCESS;
						}
					}
					else
					{
						echo _WATS_ERROR_NOT_FOUND;
					} // end check rites
					break;
				/**
				 * ticket make
				 */	
				case 'make':
					watsTicketHTML::make( $watsCategorySet, $watsUser );
					break;
				/**
				 * ticket make complete
				 */	
				case 'makeComplete':
					// check rites
					$rite =  $watsUser->checkPermission( trim( $_POST[ 'catid' ] ), "m" );
					if ( $rite > 0 )
					{
						// allow user make ticket
						$createDatetime = date('YmdHis');
						$ticket = new watsTicketHTML( $watsDatabase, null, null, trim( $_POST[ 'ticketname' ] ), $watsUser->id, null, $createDatetime, 1, null, null, 1, trim( $_POST[ 'catid' ] ) );
						$ticket->_msgList[0] = new watsMsg( null, parseMsg( trim( $_POST[ 'msg' ] ) ), $watsUser->id, $createDatetime );
						$ticket->msgNumberOf ++;
						$ticket->save();
						// notify
						if ( $wats->get( 'notifyusers' ) == 1 )
						{
							watsMail( parseMsg( trim( $_POST[ 'msg' ] ) ), $watsUser->email );
							watsMail( parseMsg( trim( $_POST[ 'msg' ] ) ), $wats->get( 'notifyemail' ) );
						}
						// end notify
						// view new ticket
						watsredirect( "index.php?option=com_waticketsystem&Itemid=".$Itemid."&act=ticket&task=view&ticketid=".$ticket->ticketId );
					}
					else
					{
						// do not allow make ticket
						echo _WATS_ERROR_ACCESS;
					} // end check rites
					break;
				/**
				 * ticket deactivate
				 */	
				case 'delete':
					// find ticket to delete
					$ticket = watsObjectBuilder::ticket( $watsDatabase, trim( $_GET[ 'ticketid' ] ) );
					// check delete rite
					$rite =  $watsUser->checkPermission( $ticket->category, "d" );
					if ( (  $ticket->watsId == $watsUser->id AND $rite > 0 ) OR $rite == 2 )
					{
						$ticket->deactivate();
						// return to previous view
						$link = newLink( $_GET );
					        watsredirect( "index.php?".$link );
					}
					else
					{
						echo _WATS_ERROR_ACCESS;
					}
					break;
				/**
				 * ticket reply
				 */	
				case 'reply':
					// find ticket to reply to
					$ticket = watsObjectBuilder::ticket( $watsDatabase, trim( $_POST[ 'ticketid' ] ) );
					// check rite to view
					$rite =  $watsUser->checkPermission( $ticket->category, "v" );
					if ( ( $ticket->watsId == $watsUser->id AND $rite > 0 ) OR ( $rite == 2 ) )
					{
						// allow user to view ticket
						$ticket->loadMsgList();
						// check rites to reply
						$rite =  $watsUser->checkPermission( $ticket->category, "r" );
						if ( $rite == 2 OR ( $rite == 1 AND $ticket->watsId == $watsUser->id ) )
						{
							// allow user to reply
							$ticket->addMsg( parseMsg( $_POST[ 'msg' ] ), $watsUser->id, date('YmdHis') );
							// check for close
							if ( $_POST[ 'submit' ] == _WATS_TICKETS_REPLY_CLOSE )
							{
								// check rites to close
								$rite =  $watsUser->checkPermission( $ticket->category, "c" );
								if ( $rite == 2 OR ( $rite == 1 AND $ticket->watsId == $watsUser->id ) )
								{
									// close ticket
									$ticket->deactivate();
								}
								else
								{
									echo _WATS_ERROR_ACCESS;
								}// end check rites to close
							} // end check for close
							// notify
							if ( $wats->get( 'notifyusers' ) == 1 )
							{
								$emailmsg = _WATS_MAIL_REPLY.$watsUser->username."\n(".$ticket->name.")"."\n\n".parseMsg( $_POST[ 'msg' ] );
								// find addresses
								$sql = "SELECT  DISTINCT m.watsid, u.email FROM #__wats_msg AS m LEFT  JOIN #__users AS u ON m.watsid=u.id WHERE m.ticketid=".$ticket->ticketId;
								$watsDatabase->setQuery($sql);
								$notify = $watsDatabase->loadObjectList();
								// loop through email addresses
								$emails = count( $notify );
								$i = 0;
								while( $i < $emails )
								{
									if ( $wats->get( 'notifyemail' ) != $notify[$i]->email )
									{
										// email users with messages in the ticket
										watsMail($emailmsg, $notify[$i]->email);
									}
									$i ++;
								}
								watsMail( parseMsg( trim( $_POST[ 'msg' ] ) ), $wats->get( 'notifyemail' ) );
							}
							// end notify
							// return to ticket
							if ( function_exists( 'watsredirect' ) )
							{
							   watsredirect( "index.php?option=com_waticketsystem&Itemid=".$Itemid."&act=ticket&task=view&ticketid=".$ticket->ticketId );
							}
							else
							{
								$ticket->view( $watsUser );
							}
						} 
						else
						{
							echo _WATS_ERROR_ACCESS;
						} // end check rites to reply
					}
					else
					{
						echo _WATS_ERROR_ACCESS;
					} // end check rite to view
					break;
					
				/**
				 * ticket reopen
				 */	
				case 'reopen':
					// find ticket to reopen
					$ticket = watsObjectBuilder::ticket( $watsDatabase, $_GET[ 'ticketid' ] );
					// check for reopen rites
					$rite =  $watsUser->checkPermission( $ticket->category, "o" );
					if ( ( $ticket->watsId == $watsUser->id AND $rite > 0 ) OR ( $rite == 2 ) )
					{
						$ticket->reopen();
					}
					else
					{
						echo _WATS_ERROR_ACCESS;
					}// end check for reopen rites
					break;
					
				/**
				 * ticket completeReopen
				 */	
				case 'completeReopen':
					// find ticket to reopen
					$ticket = watsObjectBuilder::ticket( $watsDatabase, $_GET[ 'ticketid' ] );
					// check for reopen rites
					$rite =  $watsUser->checkPermission( $ticket->category, "o" );
					if ( ( $ticket->watsId == $watsUser->id AND $rite > 0 ) OR ( $rite == 2 ) )
					{
						// reactivate
						$ticket->reactivate();
						$ticket->addMsg( parseMsg( $_POST[ 'msg' ] ), $watsUser->id, date( 'YmdHis' ) );
						$ticket->loadMsgList();
						$ticket->view( $watsUser );
						// notify
						if ( $wats->get( 'notifyusers' ) == 1 )
						{
							$emailmsg = _WATS_MAIL_REPLY.$my->username."\n(".$ticket->ticketname.")"."\n\n".parseMsg( $_POST[ 'msg' ] );
							// find addresses
							$sql = "SELECT  DISTINCT m.watsid, u.email FROM #__wats_msg AS m LEFT  JOIN #__users AS u ON m.watsid=u.id WHERE m.ticketid=".$ticket->ticketId;
							$watsDatabase->setQuery($sql);
							$notify = $watsDatabase->loadObjectList();
							// loop through email addresses
							$emails = count( $notify );
							$i = 0;
							while( $i < $emails )
							{
								if ( $wats->get( 'notifyemail' ) != $notify[$i]->email )
								{
									// email users with messages in the ticket
									watsMail($emailmsg, $notify[$i]->email);
								}
								$i ++;
							}
							watsMail( parseMsg( trim( $_POST[ 'msg' ] ) ), $wats->get( 'notifyemail' ) );
						}
						// end notify
					}
					else
					{
						echo _WATS_ERROR_ACCESS;
					}// end check for reopen rites
					break;
				}
			break;
		/**
		 * category
		 */	
		case 'category':
			switch ($task) {
				/**
				 * purge dead tickets from category
				 */	
				case 'purge':
					// check for purge rite
					$rite =  $watsUser->checkPermission( $_GET['catid'], "p" );
					if ( $rite == 2 )
					{
						// create category object
						$catPurge = new watsCategoryHTML( $watsDatabase );
						// load details
						$catPurge->load( $_GET['catid'] );
						// load dead tickets
						$catPurge->loadTicketSet( 3, $watsUser->id, true );
						// purge dead tickets
						$catPurge->purge();
					}
					else
					{
						echo _WATS_ERROR_ACCESS;
					} // end check for purge rite
					//break;
				/**
				 * view category
				 */	
				default:
					//check rites
					$rite =  $watsUser->checkPermission( $_GET['catid'], "v" );
					if ( $rite > 0 )
					{
						// create category object
						$cat = new watsCategoryHTML( $watsDatabase );
						// load details
						$cat->load( $_GET['catid'] );
						// get lifecycle
						$lifecycle = 0;
						if ( $_GET['lifecycle'] > 0 )
						{
							$lifecycle = $_GET['lifecycle'];
						} // end get lifecycle
						// check for level of rites
						if ( $rite == 2 AND $_GET['lifecycle'] != 'p' )
						{
							// allow user to view category with ALL tickets
							$cat->loadTicketSet( $lifecycle, $watsUser->id, true );
						}
						//else if ( $rite == 1 OR ( $rite == 2 AND $_GET['lifecycle'] == 'p' )  )
						else if ( $rite > 0  )
						{
							// allow user to view category with OWN tickets
							$cat->loadTicketSet( $lifecycle, $watsUser->id, false );
						}
						// end check for level of rites
						// view tickets
						$start = ( $_GET[ 'page' ] - 1 ) *  $wats->get( 'ticketssub' );
						$finish = $start + $wats->get( 'ticketssub' );
						$cat->pageNav( $wats->get( 'ticketssub' ), $_GET[ 'page' ], 0, $watsUser );
						$cat->viewTicketSet( $finish, $start );
						$cat->pageNav( $wats->get( 'ticketssub' ), $_GET[ 'page' ], 0, $watsUser );
						// check purge rites
						if ( @$_GET['lifecycle'] == 3 AND $watsUser->checkPermission( $_GET['catid'], "p" ) == 2 )
						{
							$cat->viewPurge();
						} // end check purge rites
					}
					else
					{
						echo _WATS_ERROR_ACCESS;
					} // end check rites
					break;
				}
			break;
		/**
		 * assign
		 */	
		case 'assign':
			switch ($task) {
				/**
				 * view assigned tickets
				 */	
				case 'view';
					// create assigned object
					$assignedTickets = new watsAssignHTML( $watsDatabase );
					// load tickets
					$assignedTickets->loadAssignedTicketSet( $watsUser->id );
					// view tickets
					$start = ( $_GET[ 'page' ] - 1 ) *  $wats->get( 'ticketssub' );
					$finish = $start + $wats->get( 'ticketssub' );	
					$assignedTickets->viewTicketSet( $finish, $start );
					// display page navigation
					$assignedTickets->pageNav( $wats->get( 'ticketssub' ), $_GET[ 'page' ], $wats->get( 'ticketssub' ) );
					break;
				/**
				 * assign ticket to
				 */	
				case 'assignto':
					// create ticket object
					$ticket = watsObjectBuilder::ticket( $watsDatabase, $_POST[ 'ticketid' ] );
					// check for assign rites
					$riteA =  $watsUser->checkPermission( $ticket->category, "a" );
					if ( ( $ticket->assignId == $watsUser->id AND $riteA > 0 ) OR ( $riteA == 2 ) )
					{
						$ticket->setAssignId( $_POST[ 'assignee' ] );
					} // end chck for assign rites
					// check rites
					$rite =  $watsUser->checkPermission( $ticket->category, "v" );
					if ( ( $ticket->watsId == $watsUser->id AND $rite > 0 ) OR ( $rite == 2 ) )
					{
						// allow user to view ticket
						$ticket->loadMsgList();
						$ticket->view( $watsUser );
					}
					else
					{
						echo _WATS_ERROR_ACCESS;
					} // end check rites
					break;
				}
			break;
		/**
		 * user
		 */	
		case 'user':
			switch ($task) {
				/**
				 * user edit
				 */	
				case 'edit';
					echo "<span class=\"watsHeading1\">"._WATS_USER_EDIT."</span>";
					// check for view rites
					if ( $watsUser->checkUserPermission( 'v' ) )
					{
						$editUser = new watsUserHTML( $watsDatabase );
						$editUser->loadWatsUser( $_GET[ 'userid' ] );
						// check for edit rites
						if ( $watsUser->checkUserPermission( 'e' ) == 2 )
						{
							$editUser->viewEdit();
							// check for delete rites
							if ( $watsUser->checkUserPermission( 'd' ) == 2 )
							{
								echo "<span class=\"watsHeading1\">"._WATS_USER_DELETE."</span>";
								$editUser->viewDelete();
							}
						}
						else
						{
							$editUser->view();	
						} // end check for edit rites
					}
					// no rites
					else
					{
						echo _WATS_ERROR_ACCESS;
					}
					break;
				/**
				 * user complete edit
				 */	
				case 'editComplete':
					echo "<span class=\"watsHeading1\">"._WATS_USER_EDIT."</span>";
					// check for view rites
					if ( $watsUser->checkUserPermission( 'v' ) )
					{
						$editUser = new watsUserHTML( $watsDatabase );
						$editUser->loadWatsUser( $_POST[ 'userid' ] );
						// check for edit rites
						if ( $watsUser->checkUserPermission( 'e' ) == 2 )
						{
							// complete edit user
							$editUser->organisation = trim( $_POST[ 'organisation'] );
							$editUser->setGroup( trim( $_POST[ 'grpId' ] ) );
							$editUser->updateUser();
							$editUser->view();
							
						}
						else
						{
							$editUser->view();	
						} // end check for edit rites
					}
					// no rites
					else
					{
						echo _WATS_ERROR_ACCESS;
					}
					break;
				/**
				 * user delete
				 */	
				case 'delete':
					if ( isset( $_POST[ 'userid' ] ) AND isset( $_POST[ 'remove' ] ) )
					{
						// create user object
						$deleteUser = new watsUser( $watsDatabase );
						$deleteUser->load( intval( trim( $_POST[ 'userid' ] ) ) );
						// delete user
						$deleteUser->delete( $_POST[ 'remove' ] );
					}
					// return to home
					defaultAction( $watsCategorySet, $watsDatabase, $watsUser );
					break;
				/**
				 * user make
				 */	
				case 'make':
					// check for make user rites
					if ( $watsUser->checkUserPermission( 'm' ) == 2 )
					{
						echo "<span class=\"watsHeading1\">"._WATS_USER_ADD."</span>";
						watsUserHTML::makeForm( $watsDatabase );
					}
					else
					{
						echo _WATS_ERROR_ACCESS;
					}
					break;
				/**
				 * user make complete
				 */	
				case 'makeComplete':
					// check for make user rites
					if ( $watsUser->checkUserPermission( 'm' ) == 2 )
					{
						// check for input
						if ( isset( $_GET[ 'user' ], $_GET[ 'grpId' ], $_GET[ 'organisation' ] ) )
						{
							// make users
							echo "<span class=\"watsHeading1\">"._WATS_USER_ADD."</span>"._WATS_USER_ADD_LIST;
							$noOfNewUsers = count( $_GET['user'] );
							$i = 0;
							while ( $i < $noOfNewUsers )
							{
								// check for successful creation
								if ( watsUser::makeUser( $_GET[ 'user' ][ $i ], $_GET[ 'grpId' ], $_GET[ 'organisation' ], $watsDatabase  ) )
								{
									// give visual confirmation
									$newUser = new watsUserHTML( $watsDatabase );
									$newUser->loadWatsUser( $_GET[ 'user' ][ $i ] );

									$newUser->view();
								}
								else
								{
									echo "<p>".$_GET[ 'user' ][ $i ]." -> "._WATS_ERROR."</p>";
								} // check for successful creation
								$i ++;
							}
							// end make users
						}
						else
						{
							// display error
							echo "<span class=\"watsHeading1\">"._WATS_USER_ADD."</span>";
							echo _WATS_ERROR_NODATA;
							watsUserHTML::makeForm( $watsDatabase );
							// end display error
						}
						// end check for input
					}
					else
					{
						echo _WATS_ERROR_ACCESS;
					}
					break;
				/**
				 * user page view
				 */	
				default:
					// check for all user view rites
					if ( $watsUser->checkUserPermission( 'v' ) == 2 )
					{
						// determine number of users to show
						if ( isset( $_GET[ 'page' ] ) )
						{
							$start = ( $_GET[ 'page' ] - 1 ) * $wats->get( 'ticketssub' );
							$currentPage = $_GET[ 'page' ];
						}
						else
						{
							$start = 0;
							$currentPage = 1;
						}
						$finish = $start + $wats->get( 'ticketssub' );
						// make user set and load
						$watsUserSet = new watsUserSetHTML( $watsDatabase );
						$watsUserSet->load();
						// view user set
						$watsUserSet->view( $finish, $start );
						$watsUserSet->pageNav( $wats->get( 'ticketssub' ), $currentPage );
						// check for make user rites
						if ( $watsUser->checkUserPermission( 'm' ) == 2 )
						{
							watsUserHTML::makeButton();
						}
					}
					else
					{
					echo _WATS_ERROR_ACCESS;
					}
					break;
				}
			break;
		/**
		 * default
		 */	
		default:
			defaultAction( $watsCategorySet, $watsDatabase, $watsUser );
			break;
	}
}

function defaultAction( &$watsCategorySet, &$watsDatabase, &$watsUser )
{
	global $wats, $Itemid;
	// load tickets to categoryies
	$watsCategorySet->loadTicketSet( 0, $watsUser );
	// view tickets
	$watsCategorySet->viewWithTicketSet( $wats->get( 'ticketsfront' ), 0, $watsUser );
	// check for assigned tickets
	$assignedTickets = new watsAssignHTML( $watsDatabase );
	$assignedTickets->loadAssignedTicketSet( $watsUser->id );
	if ( count( $assignedTickets->ticketSet->_ticketList ) > 0 )
	{
		// view assigned tickets
		echo "<div id=\"watsAssignedTickets\">";
		$assignedTickets->viewTicketSet( $wats->get( 'ticketsfront' ), 0 );
		$assignedTickets->pageNav( $wats->get( 'ticketssub' ), 0, $wats->get( 'ticketsfront' ) );
		echo "</div>";
	}
	// check for all user view rites
	if ( $watsUser->checkUserPermission( 'v' ) == 2 )
	{
		// determine number of users to show
		$start = 0;
		$finish = $wats->get( 'ticketsfront' );
		// create user set and load
		$watsUserSet = new watsUserSetHTML( $watsDatabase );
		$watsUserSet->load();
		// view user set
		$watsUserSet->view( $finish, $start );
		$watsUserSet->pageNav( $wats->get( 'ticketssub' ), 0, $wats->get( 'ticketsfront' ) );
		// check for make user rites
		if ( $watsUser->checkUserPermission( 'm' ) == 2 )
		{
			watsUserHTML::makeButton();
		}
	}
}

/*
 * prev Array
 * generates an array of vales with added 'prev' in front of keys
 * @param array (normally $_GET)
 */
function prevArray( $oldArray )
{
	// create new array
	$newArray = array();
	// find keys
	$keys = array_keys( $oldArray );
	// loop through keys
	while ( $key = array_pop( $keys ) )
	{
		// add prev item to new array
		$newArray[ 'prev'.$key ] = $oldArray[ $key ];
	}
	return $newArray;
}

/*
 * prev Link
 * creates a get string based on getArray
 * @param array (usually prevArray)
 */
function prevLink( $getArray )
{
	// create get link
	$link = "prevLink=true";
	// find keys
	$keys = array_keys( $getArray );
	// loop through keys
	while ( $key = array_pop( $keys ) )
	{
		// check is previous
		if ( strncmp ( $key, 'prev', 4 ) === 0 )
		{
			//$newKey = substr( $key, 4 );
			$link = $link.'&'.$key.'='.mosGetParam( $getArray, $key );
			//$getArray[ $key ];
		}
	}
	return $link;
}

/*
 * new Link
 * decomposes a previous action to create it as a new link
 * @param array
 */
function newLink( $getArray )
{
	// create get link
	$link = "prevLink=true";
	// find keys
	$keys = array_keys( $getArray );
	// loop through keys
	while ( $key = array_pop( $keys ) )
	{
		// check is previous
		if ( strncmp ( $key, 'prev', 4 ) === 0 )
		{
			$newKey = substr( $key, 4 );
			$link = $link.'&'.$newKey.'='.mosGetParam( $getArray, $key );
			//$getArray[ $key ];
		}
	}
	return $link;
}

/*
 * prevInput
 * creates prev form elements ffrom array
 * @param array
 */
function prevInput( $getArray )
{
	// initialise input
	$input = '';
	// find keys
	$keys = array_keys( $getArray );
	// loop through keys
	while ( $key = array_pop( $keys ) )
	{
		$input .= "<input name=\"prev".$key."\" type=\"hidden\" value=\"".mosGetParam( $getArray, $key )."\">";
	}
	return $input;
}

/*
 * new Link
 * decomposes a previous action to create it as a new form input
 * @param array
 */
function newInput( $getArray )
{
	// initialise input
	$input = '';
	// find keys
	$keys = array_keys( $getArray );
	// loop through keys
	while ( $key = array_pop( $keys ) )
	{
		// check is previous
		if ( strncmp ( $key, 'prev', 4 ) === 0 )
		{
			$newKey = substr( $key, 4 );
			$input .= "<input name=\"prev".$newKey."\" type=\"hidden\" value=\"".mosGetParam( $getArray, $key )."\">";
		}
	}
	return $input;
}

function parseMsg( $msg )
{
	global $wats;
	if ( $wats->get( 'msgbox' ) == 'editor' )
	{
		$msg = nl2br( $msg );
	}
	else if ( $wats->get( 'msgbox' ) == 'bbcode' )
	{
		// include bbcode class
		include_once( 'components/com_waticketsystem/bbcode.inc.php' );
		// create bbcode instance
		$bbcode = new bbcode();
		// add tags
		$bbcode->add_tag(array('Name'=>'code','HtmlBegin'=>'<span style="font-family: Courier New, Courier, mono;">','HtmlEnd'=>'</span>'));
		$bbcode->add_tag(array('Name'=>'b','HtmlBegin'=>'<span style="font-weight: bold;">','HtmlEnd'=>'</span>'));
		$bbcode->add_tag(array('Name'=>'i','HtmlBegin'=>'<span style="font-style: italic;">','HtmlEnd'=>'</span>'));
		$bbcode->add_tag(array('Name'=>'u','HtmlBegin'=>'<span style="text-decoration: underline;">','HtmlEnd'=>'</span>'));
		$bbcode->add_tag(array('Name'=>'link','HasParam'=>true,'HtmlBegin'=>'<a href="%%P%%">','HtmlEnd'=>'</a>'));
		$bbcode->add_tag(array('Name'=>'color','HasParam'=>true,'ParamRegex'=>'[A-Za-z0-9#]+','HtmlBegin'=>'<span style="color: %%P%%;">','HtmlEnd'=>'</span>','ParamRegexReplace'=>array('/^[A-Fa-f0-9]{6}$/'=>'#$0')));
		$bbcode->add_tag(array('Name'=>'email','HasParam'=>true,'HtmlBegin'=>'<a href="mailto:%%P%%">','HtmlEnd'=>'</a>'));
		$bbcode->add_tag(array('Name'=>'size','HasParam'=>true,'HtmlBegin'=>'<span style="font-size: %%P%%pt;">','HtmlEnd'=>'</span>','ParamRegex'=>'[0-9]+'));
		$bbcode->add_alias('url','link');
		// parse message into bbcode
		$msg = strip_tags( $msg, '<code>');
		$msg = htmlspecialchars( $msg );
		$msg = $bbcode->parse_bbcode( $msg );
		$msg = nl2br( $msg );
	}
	else
	{
		$msg = strip_tags( $msg );
		$msg = htmlspecialchars( $msg );
		$msg = nl2br( $msg );
	}
	// return parsed message
	return $msg;
}

function watsredirect( $dest )
{
	global $wats;
	
	if ( $wats->get( 'debug' ) == 0 )
	{
		mosredirect( $dest );
	}
	else
	{
		echo "<a href=\"".$dest."\">".$wats->get( 'debugmessage' )."</a>";
	}
}
?>