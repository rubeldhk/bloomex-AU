<?php
/**
 * FileName: admin.waticketsystem.php
 * Date: 07/09/2006
 * License: GNU General Public License
 * Script Version #: 2.0.4
 * JOS Version #: 1.0.x
 * Development James Kennard jg8949@aol.com (www.webamoeba.co.uk)
 */

// Don't allow direct linking
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
echo "<script language=\"javascript\" type=\"text/javascript\" src=\"components/com_waticketsystem/admin.wats.js\"></script>";
echo '<div class="wats">';

//add custom classes and functions
require('components/com_waticketsystem/admin.waticketsystem.html.php');

// add database wrapper
$watsDatabase =& $database;

// get settings
$wats = new watsSettings( $watsDatabase );

// check for database debug
if ( $wats->get( 'debug' ) != 0 )
{
	$watsDatabase = new watsDatabaseWrapperHTML( $database );
	$watsDatabase->viewHeader();
}

// add javaScript
echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../components/com_waticketsystem/wats.js\"></script>";

// create watsUser
// check id is set and watsUser exists

	// parse GET action
	if ( isset( $_GET['act'] ) )
	{
		$act = trim( $_GET['act'] );
	}
	elseif ( isset( $_POST['act'] ) )
	{
		$act = trim( $_POST['act'] );
	}
	else
	{
		$act = null;
    } // end parse GET action
	
	// display navigation
?>
 <table border="0" cellspacing="0" cellpadding="0" class="adminform"> 
  <tr> 
     <td><a href="http://www.webamoeba.co.uk" target="_blank"><img src="components/com_waticketsystem/images/wats.gif" alt="webamoeba" border="0"/></a></td> 
     <td align="right"><table cellpadding="0" cellspacing="0" border="0" align="right"> 
         <tr valign="middle" align="center"> 
          <td align="center"><a href="index2.php?option=com_waticketsystem"><img src="../includes/js/ThemeOffice/home.png" alt="Webamoeba Ticket System" border="0"/></a></td> 
          <td>&nbsp;</td> 
          <td align="center"><a href="index2.php?option=com_waticketsystem&act=configure&hidemainmenu=1"><img src="../includes/js/ThemeOffice/config.png" alt="Configure" border="0"/></a></td> 
          <td>&nbsp;</td> 
          <td><a href="index2.php?option=com_waticketsystem&act=css"><img src="../includes/js/ThemeOffice/menus.png" alt="CSS" border="0"/></a></td> 
          <td>&nbsp;</td> 
          <td><a href="index2.php?option=com_waticketsystem&act=user"><img src="../includes/js/ThemeOffice/users.png" alt="Users" border="0"/></a></td> 
          <td>&nbsp;</td> 
          <td><a href="index2.php?option=com_waticketsystem&act=rites"><img src="../includes/js/ThemeOffice/globe3.png" alt="Rites Manager" border="0"/></a></td> 
          <td>&nbsp;</td> 
          <td><a href="index2.php?option=com_waticketsystem&act=category"><img src="../includes/js/ThemeOffice/add_section.png " alt="Categories" border="0"/></a></td> 
          <td>&nbsp;</td> 
          <td><a href="index2.php?option=com_waticketsystem&act=ticket"><img src="../includes/../components/com_waticketsystem/images/mdn_ticket1616.gif" alt="Tickets" border="0"/></a></td> 
          <td>&nbsp;</td> 
          <td><a href="index2.php?option=com_waticketsystem&act=database"><img src="../includes/js/ThemeOffice/sysinfo.png" alt="Database Maintenance" border="0"/></a></td> 
          <td>&nbsp;</td> 
          <td><a href="index2.php?option=com_waticketsystem&act=about"><img src="../includes/js/ThemeOffice/controlpanel.png" alt="About" border="0"/></a></td> 
        </tr> 
       </table></td> 
   </tr> 
</table> 
<br /> 
<?php
	// perform selected operation
	watsOption( $task, $act );
	
	// check for database debug
	if ( $wats->get( 'debug' ) == 1 )
	{
		$watsDatabase->viewFooter();
	}
	/*
		echo "<p style=\"font-family: Courier New, Courier, monospace; color: green;\">Debug mode is enabled.<br />----------------------<br />To disable debug mode, change your WATS debug configuration. The 'Query Debug Array' shows the queries in chronological order executed during the generation of this page.</p>";

		
		// return debug to site mode
		$watsDatabase->debug( $mosConfig_debug );
		
		$logItemNumber = 0;
		foreach ( $watsDatabase->_log as $logItem )
		{
			// check for semicolon
			if ( substr($logItem, -1, 1) != ';' )
			{
				$logItem .= ';';
			}
			// display query
			echo "\n<p style=\"font-family: Courier New, Courier, monospace; background-color: #CDFECF; margin-bottom: 0px;\"><u>Query $logItemNumber</u> <a href=\"javascript:watsToggleLayer('watsDebugQueryArrayItem$logItemNumber');\">[explain]</a><br />$logItem</p>";
			echo "<div style=\"display: none;\" id=\"watsDebugQueryArrayItem$logItemNumber\">";
			$watsDatabase->setQuery($logItem);
			// diaply explanation
			echo str_replace ( 'EXPLAIN '.$logItem, ' ', $watsDatabase->explain() );
			echo "</div>";
			$logItemNumber++;
		}
	}*/
?> 
<p class="smallgrey" style="clear: both ">WebAmoeba Ticket System for Mambo and Joomla</p> 
</div> 
<?php
function watsOption( &$task, &$act )
{
	global $watsDatabase, $wats, $option, $mainframe, $mosConfig_list_limit, $mosCommonHTML;

	switch ($act) {
		/**
		 * ticket
		 */	
		case 'ticket':
			echo "<table class=\"adminheading\"><tr><th class=\"ticket\">Ticket Viewer</th></tr></table>";
			echo "<form action=\"index2.php\" method=\"post\" name=\"adminForm\">";
			switch ($task) {
				/**
				 * view
				 */	
				case 'view':
					$ticket = watsObjectBuilder::ticket( $watsDatabase, intval( addslashes( $_GET[ 'ticketid' ] ) ) );
					$ticket->loadMsgList();
					$ticket->view( );
					break;
				default:
					$limit = $mainframe->getUserStateFromRequest( 'viewlistlimit', 'limit', $mosConfig_list_limit );
					$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
					$ticketSet = new watsTicketSetHTML( $watsDatabase );
					$ticketSet->loadTicketSet( -1 );
					$ticketSet->view( $limit, $limitstart );
					$ticketSet->pageNav( $option, $limitstart, $limit );
					// key
					echo "<p><img src=\"images/tick.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"Open\" /> = Open <img src=\"images/publish_x.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"Closed\" /> = Closed <img src=\"images/checked_out.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"Closed\" /> = Dead</p>";
					break;
			}
			echo "</form>";
			break;
		/**
		 * category
		 */	
		case 'category':
			echo "<table class=\"adminheading\"><tr><th class=\"categories\">Category Manager</th></tr></table>";
			echo "<form action=\"index2.php\" method=\"post\" name=\"adminForm\">";
			switch ($task) {
				/**
				 * view
				 */	
				case 'view':
					$category = new watsCategoryHTML( $watsDatabase );
					$category->load( $_GET['catid'] );
					echo "<table width=\"100%\">
							<tr>
							  <td width=\"60%\" valign=\"top\">";
					$category->viewEdit();
					echo "	  </td>
							  <td valign=\"top\">";
					$category->viewDelete();
					echo "	  </td>
							</tr>
						  </table>";
					break;
				/**
				 * view
				 */	
				case 'apply':
					// check input
					if ( isset( $_POST['catid'], $_POST['name'], $_POST['description'], $_POST['image'], $_POST['remove'] ) )
					{
						// check is numeric
						if ( is_numeric( $_POST['catid'] ) )
						{
							// create category
							$editCategory = new watsCategory( $watsDatabase );
							$editCategory->load( $_POST['catid'] );
							// check if deleting
							if ( $_POST['remove'] == 'removetickets' )
							{
								// delete category
								$editCategory->delete( );
								watsredirect( "index2.php?option=com_waticketsystem&act=category&mosmsg=Category Removed" );
							}
							else
							{
								// update name
								$editCategory->name = htmlspecialchars( addslashes( $_POST['name'] ) );
								// update description
								$editCategory->description = htmlspecialchars( addslashes( $_POST['description'] ) );
								// update image
								$editCategory->image = htmlspecialchars( addslashes( $_POST['image'] ) );
								// save changes
								$editCategory->updateCategory();
								// success
								watsredirect( "index2.php?option=com_waticketsystem&act=category&mosmsg=Category Updated" );
							}
							break;
						}
						// end check is numeric
					}
					// end check input
					// redirect input error
					watsredirect( "index2.php?option=com_waticketsystem&act=category&mosmsg=Error updating category" );
					break;
				/**
				 * new
				 */	
				case 'save':
					// save new category
					// check for input;
					if ( isset( $_POST['name'], $_POST['description'], $_POST['image'] ) )
					{
						// check input length
						if ( strlen( $_POST['name'] ) > 0 && strlen( $_POST['description'] ) > 0 )
						{
							// parse input
							$name = htmlspecialchars( $_POST['name'] );
							$description = htmlspecialchars( $_POST['description'] );
							$image = htmlspecialchars( $_POST['image'] );
							if ( watsCategory::newCategory( $name, $description, $image, $watsDatabase ) )
							{
								// success
								watsredirect( "index2.php?option=com_waticketsystem&act=category&mosmsg=Category Added" );
							}
							else
							{
								// already exists
								watsredirect( "index2.php?option=com_waticketsystem&act=category&task=new&mosmsg=The specified name already exists" );
							}
						}
					}
					else
					{
						watsredirect( "index2.php?option=com_waticketsystem&act=category&task=new&mosmsg=Please fill in the form correctly" );
					}
					break;
				/**
				 * new
				 */	
				case 'new':
					watsCategoryHTML::newForm();
					break;
				default:
					$limit 		= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
					$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
					$categorySet = new watsCategorySetHTML( $watsDatabase );
					$categorySet->view( $limit, $limitstart );
					$categorySet->pageNav( "com_waticketsysten", $limitstart, $limit );
					break;
			}
			echo "<input type=\"hidden\" name=\"task\" value=\"\" /><input type=\"hidden\" name=\"option\" value=\"com_waticketsystem\" /><input type=\"hidden\" name=\"act\" value=\"category\" /></form>";
			break;
		/**
		 * CSS
		 */	
		case 'css':
			echo "<table class=\"adminheading\"><tr><th class=\"menus\">CSS</th></tr></table>";
			echo "<form action=\"index2.php\" method=\"post\" name=\"adminForm\">";
			$watsCss = new watsCssHTML( $watsDatabase );
			$watsCss->open('../components/com_waticketsystem/wats.css');

			switch ($task) {
				/**
				 * apply
				 */	
				case 'apply':
					// check if is restoring
					if ( $_POST['restore'] == 'restore' )
					{
						// restore css
						if ( $watsCss->restore( '../components/com_waticketsystem/wats.restore.css' ) )
						{
							// redirect success
							watsredirect( "index2.php?option=com_waticketsystem&act=css&mosmsg=CSS Restored" );
						}
						else
						{
							// redirect failure
							watsredirect( "index2.php?option=com_waticketsystem&act=css&mosmsg=CSS Restore Failed" );
						}
					}
					else
					{
						// save changes
						$watsCss->processSettings();
						$watsCss->save();
						// redirect
						watsredirect( "index2.php?option=com_waticketsystem&act=css&mosmsg=Changes Saved" );
					}
					break;
				/**
				 * cancel
				 */	
				case 'cancel':
					watsredirect( "index2.php?option=com_waticketsystem" );
					break;
				/**
				 * backup
				 */	
				case 'backup':
					// open window
					echo "<script>popup = window.open ('../components/com_waticketsystem/wats.css','watsCSS','resizable=yes,scrollbars=1,width=500,height=500');</script>";
				/**
				 * default
				 */	
				default:
					// start Tab Pane
					{
						// load overlib
						mosCommonHTML::loadOverlib();
						// table
						echo "<table width=\"100%\">
								<tr>
								  <td width=\"60%\" valign=\"top\">";
						echo "<table class=\"adminform\">
									<tr>
										<th>
											Edit CSS
										</th>
									</tr>
									<tr>
										<td>";
										$watsCss->editSettings();
						if ( $watsCss->css == "enable" )
						{
							// prepare tabs
							$cssTabs = new mosTabs(1);
							$cssTabs->startPane('cssTabs');
							// fill tabs
							{
								// general
								$cssTabs->startTab( 'General', 'cssTabs' );
								$watsCss->editGeneral();
								$cssTabs->endTab();
								// navigation
								$cssTabs->startTab( 'Navigation', 'cssTabs' );
								$watsCss->editNavigation();
								$cssTabs->endTab();
								// categories
								$cssTabs->startTab( 'Categories', 'cssTabs' );
								$watsCss->editCategories();
								$cssTabs->endTab();
								// tickets
								$cssTabs->startTab( 'Tickets', 'cssTabs' );
								$watsCss->editTickets();
								$cssTabs->endTab();
								// assigned tickets
								$cssTabs->startTab( 'Assigned', 'cssTabs' );
								$watsCss->editAssignedTickets();
								$cssTabs->endTab();
								// users
								$cssTabs->startTab( 'Users', 'cssTabs' );
								$watsCss->editUsers();
								$cssTabs->endTab();
							}
							// end fill tabs
							$cssTabs->endPane();
						}
						echo "      	</td>
									</tr>
								</table>
						          </td>
								  <td valign=\"top\">";
						$watsCss->viewRestore();
						echo "	  </td>
								</tr>
						  </table>";
					}
					// end tab pane
					break;
			}
			echo "<input type=\"hidden\" name=\"option\" value=\"com_waticketsystem\" /><input type=\"hidden\" name=\"act\" value=\"css\" /><input type=\"hidden\" name=\"task\" value=\"\" /></form>";
			break;
		/**
		 * rites
		 */	
		case 'rites':
			echo "<table class=\"adminheading\"><tr><th class=\"impressions\">Rites Manager</th></tr></table>";
			echo "<form action=\"index2.php\" method=\"post\" name=\"adminForm\">";
			switch ($task) {
				/**
				 * new
				 */	
				case 'new':
					watsUserGroupHTML::newForm();
					break;
				/**
				 * save
				 */	
				case 'save':
					// save new group
					// check for input;
					if ( isset( $_POST[ 'name' ], $_POST[ 'image' ] ) )
					{
						// check input is valid
						if ( strlen( $_POST[ 'name' ] ) !== 0 )
						{
							// create new group
							$newCategory = watsUserGroup::makeGroup( htmlspecialchars( $_POST[ 'name' ] ), htmlspecialchars( $_POST[ 'image' ] ), $watsDatabase );
							// redirect
							watsredirect( "index2.php?option=com_waticketsystem&act=rites&task=view&groupid=".$newCategory->grpid );
						}
						else
						{
							watsredirect( "index2.php?option=com_waticketsystem&act=rites&task=new&mosmsg=Please fill in the form correctly" );
						}
					}
					else
					{
						// redirect to add
						watsredirect( "index2.php?option=com_waticketsystem&act=rites&task=new&mosmsg=Form Contents not recognised" );
						// end display error
					}
					// end check for input
					break;
				/**
				 * view
				 */	
				case 'view':
					echo "<input type=\"hidden\" name=\"groupid\" value=\"".$_GET['groupid']."\" />";
					$userGroup = new watsUserGroupHTML( $watsDatabase, $_GET['groupid'] );
					// load overlib
					mosCommonHTML::loadOverlib();
					echo "<table width=\"100%\">
							<tr>
							  <td width=\"60%\" valign=\"top\">";
					$userGroup->viewEdit();
					echo "	  </td>
							  <td valign=\"top\">";
					$userGroup->viewDelete();
					echo "	  </td>
							</tr>
						  </table>";
					break;
				/**
				 * apply
				 */	
				case 'apply':
					$userGroup = new watsUserGroupHTML( $watsDatabase, $_POST['groupid'] );
					
					// check if deleting
					if ( $_POST['remove'] == 'remove' || $_POST['remove'] == 'removetickets' || $_POST['remove'] == 'removeposts' )
					{
						// delete category
						$userGroup->delete( $_POST['remove'] );
						// watsredirect( "index2.php?option=com_waticketsystem&act=rites&mosmsg=Group Removed" );
					}
					else
					{
						// process form
						$userGroup->processForm();
						$userGroup->save();
						// redirect on completion
						watsredirect( "index2.php?option=com_waticketsystem&act=rites&mosmsg=Group Updated" );
					}
					break;
				default:
					$limit 		= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
					$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
					$userGroupSet = new watsUserGroupSetHTML( $watsDatabase );
					$userGroupSet->loadUserGroupSet();
					$userGroupSet->view( $limitstart, $limit );
					$userGroupSet->pageNav( "com_waticketsysten", $limitstart, $limit );
					break;
			}
			echo "<input type=\"hidden\" name=\"task\" value=\"\" /><input type=\"hidden\" name=\"option\" value=\"com_waticketsystem\" /><input type=\"hidden\" name=\"act\" value=\"rites\" /></form>";
			break;
		/**
		 * user
		 */	
		case 'user':
			echo "<table class=\"adminheading\"><tr><th class=\"user\">User Manager</th></tr></table>";
			echo "<form action=\"index2.php\" method=\"post\" name=\"adminForm\">";
			switch ($task) {
				/**
				 * edit
				 */	
				case 'edit':
					$editUser = new watsUserHTML( $watsDatabase );
					$editUser->loadWatsUser( $_GET[ 'userid' ] );
					echo "<table width=\"100%\">
							<tr>
							  <td width=\"60%\" valign=\"top\">";
					$editUser->viewEdit();
					echo "	  </td>
							  <td valign=\"top\">";
					$editUser->viewDelete();
					echo "	  </td>
							</tr>
						  </table>";
					break;
				/**
				 * new
				 */	
				case 'new':
					watsUserHTML::newForm( $watsDatabase );
					break;
				/**
				 * apply
				 */	
				case 'apply':
					// check input
					if ( isset( $_POST['userid'], $_POST['grpId'], $_POST['organisation'], $_POST['remove'] ) )
					{
						// check is numeric
						if ( is_numeric( $_POST['userid'] ) )
						{
							// create user
							$editUser = new watsUserHTML( $watsDatabase );
							$editUser->loadWatsUser( $_POST[ 'userid' ] );
							// check if deleting
							if ( $_POST['remove'] == 'removetickets' || $_POST['remove'] == 'removeposts' )
							{
								// delete user
								$editUser->delete( $_POST[ 'remove' ] );
								watsredirect( "index2.php?option=com_waticketsystem&act=user&mosmsg=User Removed" );
							}
							else
							{
								// check is numeric
								if ( is_numeric( $_POST['grpId'] ) )
								{
									$editUser->group = $_POST['grpId'];
								}
								// update organistation
								$editUser->organisation = htmlspecialchars( addslashes( $_POST['organisation'] ) );
								// save changes
								if ( $editUser->updateUser() )
								{
									// success
									watsredirect( "index2.php?option=com_waticketsystem&act=user&mosmsg=User Updated" );
								}
								else
								{
									// failure
									watsredirect( "index2.php?option=com_waticketsystem&act=user&mosmsg=Update failed, user not found" );
								}
							}
						}
						// end check is numeric
					}
					else
					{
						// redirect input error
						watsredirect( "index2.php?option=com_waticketsystem&act=user&mosmsg=Error updating user" );
					}// end check input
					break;
				/**
				 * save
				 */	
				case 'save':
					// save new users
					// check for input;
					if ( isset( $_POST[ 'user' ], $_POST[ 'grpId' ], $_POST[ 'organisation' ] ) )
					{
						// make users
						$noOfNewUsers = count( $_POST['user'] );
						$i = 0;
						while ( $i < $noOfNewUsers )
						{
							// check for successful creation
							if ( watsUser::makeUser( $_POST[ 'user' ][ $i ], $_POST[ 'grpId' ], $_POST[ 'organisation' ], $watsDatabase  ) )
							{
								// give visual confirmation
								$newUser = new watsUserHTML( $watsDatabase );
								$newUser->loadWatsUser( $_POST[ 'user' ][ $i ] );
								$newUser->view();
							}
							$i ++;
						}
						// end make users
						// redirect to list on completion
						watsredirect( "index2.php?option=com_waticketsystem&act=user&mosmsg=Users Added" );
					}
					else
					{
						// redirect to add
						watsredirect( "index2.php?option=com_waticketsystem&act=user&task=new&mosmsg=Please fill in the form correctly" );
						// end display error
					}
					// end check for input
					break;
				/**
				 * default
				 */	
				default:
					// get limits
					$limit 		= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
					$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
					$watsUserSet = new watsUserSetHTML( $watsDatabase );
					$watsUserSet->load();
					$watsUserSet->view( $limitstart, $limit );
					$watsUserSet->pageNav( $option, $limitstart, $limit );
					break;
			}
			echo "<input type=\"hidden\" name=\"act\" value=\"user\" /><input type=\"hidden\" name=\"option\" value=\"com_waticketsystem\" /><input type=\"hidden\" name=\"task\" value=\"\" /></form>";
			break;
		/**
		 * about
		 */	
		case 'about':
			echo "<table class=\"adminheading\"><tr><th class=\"cpanel\">About</th></tr></table>";
			$watsSettings = new watsSettingsHTML( $watsDatabase );
			$watsSettings->about();
			break;
		/**
		 * database
		 */	
		case 'database':
			echo "<table class=\"adminheading\"><tr><th class=\"info\">Database Maintenance</th></tr></table>";
			$watsDatabaseMaintenance = new watsDatabaseMaintenanceHTML( $watsDatabase );
			$watsDatabaseMaintenance->performMaintenance();
			break;
		/**
		 * default (configuration)
		 */	
		case 'configure':
			echo "<table class=\"adminheading\"><tr><th class=\"config\">Configuration</th></tr></table>";
			echo "<form action=\"index2.php\" method=\"post\" name=\"adminForm\">";
			switch ($task) {
				/**
				 * save
				 */	
				case 'apply':
					// create settings object
					$watsSettings = new watsSettingsHTML( $watsDatabase );
					// process form
					$watsSettings->processForm();
					// save
					$watsSettings->save();
					// redirect
					watsredirect( "index2.php?option=com_waticketsystem&act=configure&hidemainmenu=1" );
					break;
				/**
				 * cancel
				 */	
				case 'cancel':
					watsredirect( "index2.php?option=com_waticketsystem" );
					break;
				/**
				 * default
				 */	
				default:
					// load overlib
					mosCommonHTML::loadOverlib();
					$watsSettings = new watsSettingsHTML( $watsDatabase );
					// start Tab Pane
					{
						$settingsTabs = new mosTabs(1);
						$settingsTabs->startPane('settingsTabs');
						// fill tabs
						{
							// general
							$settingsTabs->startTab( 'General', 'settingsTabs' );
							$watsSettings->editGeneral();
							$settingsTabs->endTab();
							// Users
							$settingsTabs->startTab( 'Users', 'settingsTabs' );
							$watsSettings->editUser();
							$settingsTabs->endTab();
							// Agreement
							$settingsTabs->startTab( 'Agreement', 'settingsTabs' );
							$watsSettings->editAgreement();
							$settingsTabs->endTab();
							// Notification
							$settingsTabs->startTab( 'Notification', 'settingsTabs' );
							$watsSettings->editNotification();
							$settingsTabs->endTab();
							// Upgrade
							$settingsTabs->startTab( 'Upgrade', 'settingsTabs' );
							$watsSettings->editUpgrade();
							$settingsTabs->endTab();
							// Debug
							$settingsTabs->startTab( 'Debug', 'settingsTabs' );
							$watsSettings->editDebug();
							$settingsTabs->endTab();
						}
						// end fill tabs
						$settingsTabs->endPane();
					}
					// end tab pane
					break;
			}
			echo "<input type=\"hidden\" name=\"act\" value=\"configure\" /><input type=\"hidden\" name=\"option\" value=\"com_waticketsystem\" /><input type=\"hidden\" name=\"task\" value=\"\" /></form>";
			break;
		/**
		 * default (configuration)
		 */	
		default:
			// stats
			$watsDatabase->setQuery( "SELECT COUNT(*) as count FROM #__wats_ticket" );
			$set = $watsDatabase->loadObjectList();
			$watsStatTickets = $set[0]->count;
			$watsStatTicketsRaw = $watsStatTickets;
			if ( $watsStatTickets == 0 )
				$watsStatTickets = 1;
			$watsDatabase->setQuery( "SELECT COUNT(*) as count FROM #__wats_ticket WHERE lifeCycle=1" );
			$set = $watsDatabase->loadObjectList();
			$watsStatTicketsOpen = $set[0]->count;
			$watsDatabase->setQuery( "SELECT COUNT(*) as count FROM #__wats_ticket WHERE lifeCycle=2" );
			$set = $watsDatabase->loadObjectList();
			$watsStatTicketsClosed =  $set[0]->count;;
			$watsDatabase->setQuery( "SELECT COUNT(*) as count FROM #__wats_ticket WHERE lifeCycle=3" );
			$set = $watsDatabase->loadObjectList();
			$watsStatTicketsDead = $set[0]->count;
			$watsDatabase->setQuery( "SELECT COUNT(*) as count FROM #__wats_users" );
			$set = $watsDatabase->loadObjectList();
			$watsStatUsers = $set[0]->count;
			$watsDatabase->setQuery( "SELECT COUNT(*) as count FROM #__wats_category" );
			$set = $watsDatabase->loadObjectList();
			$watsStatCategories = $set[0]->count;
			// end stats
			?> 
<table class="adminheading" border="0"> 
  <tr> 
    <th class="frontpage"> Webamoeba Ticket System </th> 
  </tr> 
</table> 
<table class="adminform"> 
  <tr> 
    <td width="55%" valign="top"> <div id="cpanel"> 
        <div style="float:left;"> 
          <div class="icon"> <a href="index2.php?option=com_waticketsystem"> 
            <div class="iconimage"> <img src="images/frontpage.png" alt="Frontpage Manager" align="middle" name="image" border="0" /> </div> 
          Webamoeba Ticket System</a> </div> 
        </div> 
        <div style="float:left;"> 
          <div class="icon"> <a href="index2.php?option=com_waticketsystem&act=configure&hidemainmenu=1"> 
            <div class="iconimage"> <img src="images/config.png" alt="Configuration" align="middle" name="image" border="0" /> </div> 
          Configuration</a> </div> 
        </div> 
        <div style="float:left;"> 
          <div class="icon"> <a href="index2.php?option=com_waticketsystem&act=css"> 
            <div class="iconimage"> <img src="images/menu.png" alt="CSS" align="middle" name="image" border="0" /> </div> 
          CSS</a> </div> 
        </div> 
        <div style="float:left;"> 
          <div class="icon"> <a href="index2.php?option=com_waticketsystem&act=user"> 
            <div class="iconimage"> <img src="images/user.png" alt="User Manager" align="middle" name="image" border="0" /> </div> 
          User Manager</a> </div> 
        </div> 
        <div style="float:left;"> 
          <div class="icon"> <a href="index2.php?option=com_waticketsystem&act=rites"> 
            <div class="iconimage"> <img src="images/impressions.png" alt="Rites Manager" align="middle" name="image" border="0" /> </div> 
          Rites Manager</a> </div> 
        </div> 
        <div style="float:left;"> 
          <div class="icon"> <a href="index2.php?option=com_waticketsystem&act=category"> 
            <div class="iconimage"> <img src="images/categories.png" alt="Category Manager" align="middle" name="image" border="0" /> </div> 
          Category Manager</a> </div> 
        </div> 
        <div style="float:left;"> 
          <div class="icon"> <a href="index2.php?option=com_waticketsystem&act=ticket"> 
            <div class="iconimage"> <img src="images/addedit.png" alt="Ticket Viewer" align="middle" name="image" border="0" /> </div> 
            Ticket Viewer </a></div> 
        </div> 
        <div style="float:left;"> 
          <div class="icon"> <a href="index2.php?option=com_waticketsystem&act=database"> 
            <div class="iconimage"> <img src="images/systeminfo.png" alt="Database Maintenance" align="middle" name="image" border="0" /> </div> 
          Database Maintenance </a></div> 
        </div> 
        <div style="float:left;"> 
          <div class="icon"> <a href="index2.php?option=com_waticketsystem&act=about"> 
            <div class="iconimage"> <img src="images/cpanel.png" alt="About" align="middle" name="image" border="0" /> </div> 
          About </a></div> 
        </div> 
      </div></td> 
    <td width="45%" valign="top"> <div style="width=100%;"> 
        <table class="adminlist"> 
          <tr> 
            <th colspan="3"> Statistics </th> 
          </tr> 
          <tr> 
            <td width="80"> Tickets</td>  
            <td width="60"><?php echo $watsStatTicketsRaw?> / 100%</td> 
			<td><img src="components/com_waticketsystem/images/red.gif" style="height: 4px; width: 100%;"></td>
          </tr> 
          <tr> 
            <td> Open </td> 
            <td><?php echo $watsStatTicketsOpen?> / <?php echo intval((100/$watsStatTickets)*$watsStatTicketsOpen)?>%</td> 
			<td><img src="components/com_waticketsystem/images/red.gif" style="height: 4px; width: <?php echo (100/$watsStatTickets)*$watsStatTicketsOpen?>%;"></td>
          </tr>
          <tr>
            <td>Closed</td>
            <td><?php echo $watsStatTicketsClosed?> / <?php echo intval((100/$watsStatTickets)*$watsStatTicketsClosed)?>%</td>
            <td><img src="components/com_waticketsystem/images/red.gif" style="height: 4px; width: <?php echo (100/$watsStatTickets)*$watsStatTicketsClosed?>%;"></td>
          </tr>
          <tr>
            <td>Dead</td>
            <td><?php echo $watsStatTicketsDead?> / <?php echo intval((100/$watsStatTickets)*$watsStatTicketsDead)?>%</td>
            <td><img src="components/com_waticketsystem/images/red.gif" style="height: 4px; width: <?php echo (100/$watsStatTickets)*$watsStatTicketsDead?>%;"></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>Users</td>
            <td><?php echo $watsStatUsers?></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>Categories</td>
            <td><?php echo $watsStatCategories?></td>
			<td>&nbsp;</td>
          </tr> 
        </table> 
      </div></td> 
  </tr> 
</table> 
<?php
			break;
	}
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
