<?php
/**
* FileName: install.waticketsystem.php
* Date: 18/11/2006
* License: GNU General Public License
* Script Version #: 2.0.6
* JOS Version #: 1.0.x
* Development James Kennard james@webamoeba.co.uk (www.webamoeba.co.uk)
**/

// Don't allow direct linking
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

function changeIcon( $name, $icon ) {
	global $database;
	$database->setQuery( "UPDATE #__components SET admin_menu_img=\"".$icon."\" WHERE `name`=\"".$name."\" AND `option`=\"com_waticketsystem\";");
	$database->query();
	}

function com_install()
{
	global $database;

	// new install
	$version = "0.0.0";
	// determine upgrade status
	$database->setQuery( "DESCRIBE #__wats_settings" );
	$vars = $database->loadObjectList();
	// if ( count($vars) != 0 )
	if ( $database->getErrorNum() == 0 )
	{
		// upgrade
		$database->setQuery( "SELECT value FROM #__wats_settings WHERE name=\"upgrade\"" );
		$vars = $database->loadObjectList();
		if ( count( $vars ) == 1 )
		{
			if ( $vars[0]->value == 1 )
			{
				$upgrade = true;
				$database->setQuery( "SELECT value FROM #__wats_settings WHERE name=\"versionmajor\"" );
				$vars = $database->loadObjectList();
				$version = $vars[0]->value;
				$database->setQuery( "SELECT value FROM #__wats_settings WHERE name=\"versionminor\"" );
				$vars = $database->loadObjectList();
				$version .= '.'.$vars[0]->value;
				$database->setQuery( "SELECT value FROM #__wats_settings WHERE name=\"versionpatch\"" );
				$vars = $database->loadObjectList();
				$version .= '.'.$vars[0]->value;
			}
		}
	}
	// end determine upgrade status
	
	switch ( $version ) {
		/**
		 * new install
		 */	
		case '0.0.0':
			$database->setQuery( "DROP TABLE IF EXISTS `#__wats_category`;");
			$database->query();
			$database->setQuery( "CREATE TABLE  `#__wats_category` (
					  `catid` int(11) NOT NULL auto_increment,
					  `name` varchar(50) NOT NULL default '',
					  `description` varchar(255) default NULL,
					  `image` varchar(255) default NULL,
					  PRIMARY KEY  (`catid`),
					  UNIQUE KEY `name` (`name`)
					);");
			$database->query();
			$database->setQuery( "DROP TABLE IF EXISTS `#__wats_category`;");
			$database->query();
			$database->setQuery( "CREATE TABLE  `#__wats_category` (
					  `catid` int(11) NOT NULL auto_increment,
					  `name` varchar(50) NOT NULL default '',
					  `description` varchar(255) default NULL,
					  `image` varchar(255) default NULL,
					  PRIMARY KEY  (`catid`),
					  UNIQUE KEY `name` (`name`)
					);");
			$database->query();
			$database->setQuery( "DROP TABLE IF EXISTS `#__wats_groups`;");
			$database->query();
			$database->setQuery( "CREATE TABLE  `#__wats_groups` (
					  `grpid` int(10) unsigned NOT NULL auto_increment,
					  `name` varchar(50) NOT NULL default '',
					  `image` varchar(255) default NULL,
					  `userrites` varchar(4) NOT NULL default '----',
					  PRIMARY KEY  (`grpid`)
					);");
			$database->query();
			$database->setQuery( "DROP TABLE IF EXISTS `#__wats_highlight`;");
			$database->query();
			$database->setQuery( "CREATE TABLE  `#__wats_highlight` (
                      `watsid` int(11) NOT NULL default '0',
                      `ticketid` int(11) NOT NULL default '0',
                      `datetime` timestamp,
                      PRIMARY KEY  (`watsid`,`ticketid`)
                    );");
			$database->query();
			$database->setQuery( "DROP TABLE IF EXISTS `#__wats_msg`;");
			$database->query();
			$database->setQuery( "CREATE TABLE `#__wats_msg` (
					  `msgid` int(11) NOT NULL auto_increment,
					  `ticketid` int(11) NOT NULL default '0',
					  `watsid` int(11) NOT NULL default '0',
					  `msg` text NOT NULL,
					  `datetime` timestamp,
					  PRIMARY KEY  (`msgid`)
					);");
			$database->query();
			$database->setQuery( "DROP TABLE IF EXISTS `#__wats_permissions`;");
			$database->query();
			$database->setQuery( "CREATE TABLE  `#__wats_permissions` (
					  `grpid` int(11) NOT NULL default '0',
					  `catid` int(11) default '0',
					  `type` varchar(8) NOT NULL default '',
					  KEY `grpid` (`grpid`,`catid`)
					);");
			$database->query();
			$database->setQuery( "DROP TABLE IF EXISTS `#__wats_settings`;");
			$database->query();
			$database->setQuery( "CREATE TABLE  `#__wats_settings` (
					  `name` varchar(255) NOT NULL default '',
					  `value` varchar(255) default NULL,
					  PRIMARY KEY  (`name`)
					);");
			$database->query();
			$database->setQuery( "DROP TABLE IF EXISTS `#__wats_ticket`;");
			// Altered due to version probs with MySQL.
			/*$database->setQuery( "CREATE TABLE  `#__wats_ticket` (
					  `watsid` int(11) NOT NULL default '0',
					  `ticketid` int(11) NOT NULL auto_increment,
					  `ticketname` varchar(25) NOT NULL default '',
					  `lifecycle` tinyint(1) NOT NULL default '0',
					  `datetime` timestamp NOT NULL default '0000-00-00 00:00:00',
					  `category` int(11) NOT NULL default '0',
					  `assign` int(11) default NULL,
					  PRIMARY KEY  (`ticketid`)
					);");*/
			$database->setQuery( "CREATE TABLE  `#__wats_ticket` (
					  `watsid` int(11) NOT NULL default '0',
					  `ticketid` int(11) NOT NULL auto_increment,
					  `ticketname` varchar(25) NOT NULL default '',
					  `lifecycle` tinyint(1) NOT NULL default '0',
					  `datetime` timestamp,
					  `category` int(11) NOT NULL default '0',
					  `assign` int(11) default NULL,
					  PRIMARY KEY  (`ticketid`)
					);");
			$database->query();
			$database->setQuery( "DROP TABLE IF EXISTS `#__wats_users`;");
			$database->query();
			$database->setQuery( "CREATE TABLE  `#__wats_users` (
					  `watsid` int(11) NOT NULL default '0',
					  `organisation` varchar(25) NOT NULL default '',
					  `agree` tinyint(1) NOT NULL default '0',
					  `grpid` int(11) NOT NULL default '0',
					  PRIMARY KEY  (`watsid`)
					);");
			$database->query();
			$database->setQuery( "DROP TABLE IF EXISTS `#__wats_agree`;");
			$database->query();			
			$database->setQuery( "INSERT INTO `#__wats_settings` (`name`,`value`) VALUES ('iconset','mdn_'),('notifyemail',''),('highlight','!'),('notifyusers','0'),('enhighlight','1'),('ticketsfront','5'),('ticketssub','15'),('sourceemail',''),('msgbox','bbcode'),('lang','english.php'),('name','WebAmoeba Ticket System'),('newpostmsg','a new message has arrived:'),('newpostmsg1','a new message has arrived:'),('newpostmsg2',''),('newpostmsg3',''),('users','0'),('agree','0'),('agreei',''),('agreelw','You must agree to the following terms to use this system'),('agreen','agreement'),('agreela','If you have read the terms please continue'),('agreeb','continue'),('view','a'),('msgboxh','10'),('msgboxw','58'),('msgboxt','1'),('dorganisation','individual'),('copyright','WebAmoeba Ticket System for Mambo and Joomla'),('date','j-M-Y (h:i)'),('defaultmsg','type here...'),('dateshort','j-M-Y'),('assignname','Assigned Tickets'),('assigndescription','Tickets assigned to you to answer'),('assignimage',''),('versionmajor','2'), ('versionminor','0'),('versionpatch','0'),('css','disable'),('versionname','stable'),('upgrade','0'),('userdefault','1'),('usersimport','0'),('debug','0'),('debugmessage','Continue >>');");
			$database->query();
			$database->setQuery( "INSERT INTO `#__wats_groups` (`grpid`,`name`,`image`,`userrites`) VALUES (1,'user','components/com_waticketsystem/images/mdn_userSmall.jpg','----'),(2,'advisor','components/com_waticketsystem/images/mdn_userSmallGreen.jpg','V---'),(3,'administrator','components/com_waticketsystem/images/mdn_userSmallRed.jpg','VMED');");
			$database->query();
			$database->setQuery( "INSERT INTO `#__wats_permissions` (`grpid`,`catid`,`type`) VALUES (1,1,'vmrcd---'),(2,1,'VmRCDPAO'),(3,1,'VmRCDPAO');");
			$database->query();
			$database->setQuery( "INSERT INTO `#__wats_category` (`catid`,`name`,`description`,`image`) VALUES (1,'Default Category','If there are no other suitable categories submit your tickets here ;)',NULL);");			
			$database->query();
		/**
		 * patch from 2.0.0 to 2.0.1
		 */	
		case '2.0.0':
			$database->setQuery( "INSERT INTO `#__wats_settings` (`name`,`value`) VALUES ('debug','0'),('debugmessage','Continue >>');");
			$database->query();
			$database->setQuery( "UPDATE `#__wats_settings` SET `value`='1' WHERE `name`='versionpatch';" );
			$database->query();
		/**
		 * patch from 2.0.1 to 2.0.2
		 */	
		case '2.0.1':
			$database->setQuery( "UPDATE `#__wats_settings` SET `value`='2' WHERE `name`='versionpatch';" );
			$database->query();
		/**
		 * patch from 2.0.2 to 2.0.3
		 */	
		case '2.0.2':
			$database->setQuery( "UPDATE `#__wats_settings` SET `value`='3' WHERE `name`='versionpatch';" );
			$database->query();
		/**
		 * patch from 2.0.3 to 2.0.4
		 */	
		case '2.0.3':
			$database->setQuery( "UPDATE `#__wats_settings` SET `value`='4' WHERE `name`='versionpatch';" );
			$database->query();
		/**
		 * patch from 2.0.4 to 2.0.5
		 */	
		case '2.0.4':
			$database->setQuery( "UPDATE `#__wats_settings` SET `value`='5' WHERE `name`='versionpatch';" );
			$database->query();
		/**
		 * patch from 2.0.5 to 2.0.6
		 */	
		case '2.0.5':
			$database->setQuery( "UPDATE `#__wats_settings` SET `value`='6' WHERE `name`='versionpatch';" );
			$database->query();
			break;
	}

	changeIcon( "WATicketSystem", "../components/com_waticketsystem/images/mdn_ticket1616.gif" );
	changeIcon("About", "js/ThemeOffice/controlpanel.png");
	changeIcon("Ticket Viewer", "../components/com_waticketsystem/images/mdn_ticket1616.gif");
	changeIcon("User Manager", "js/ThemeOffice/users.png");
	changeIcon("Configure", "js/ThemeOffice/config.png");
	changeIcon("CSS", "js/ThemeOffice/menus.png");
	changeIcon("Rites Manager", "js/ThemeOffice/globe3.png");
	changeIcon("Category Manager", "js/ThemeOffice/add_section.png");
	changeIcon("Database Maintenance", "js/ThemeOffice/sysinfo.png");
	return "<table class=\"adminlist\">
			<tr>
				<th>
					<div style=\"text-align: center;\">
						WebAmoeba Ticket System<br>
						2.0.6 ( stable )
					</div>
				</th>
			</tr>
			<tr>
				<td nowrap=\"true\" align=\"center\">
					<div style=\"text-align: center;\">
						<p><strong>Developers</strong><br />
						<a href=\"mailto:james@webamoeba.co.uk\">James Kennard</a></p>
						<p><strong>Web</strong><br />
						<a href=\"http://www.webamoeba.co.uk\" target=\"_blank\">www.webamoeba.co.uk</a></p>
						<p><strong>Libraries</strong><br />
						BBCode - Leif K-Brooks</p>
						<p><strong>Translations</strong><br />
						english - James Kennard <a href=\"mailto:james@webamoeba.com\">james@webamoeba.com</a> (<a href=\"http://www.webamoeba.co.uk\" target=\"_blank\">www.webamoeba.co.uk</a>)<br />
						french - Johan Aubry <a href=\"mailto:jaubry@a-itservices.com\">jaubry@a-itservices.com</a> (<a href=\"http://www.a-itservices.com\" target=\"_blank\">www.a-itservices.com</a>)<br />
						germanf - Chr.G&auml;rtner<br />
						portuguese - Jorge Rosado <a href=\"mailto:info@jrpi.pt\">info@jrpi.pt</a> (<a href=\"http://www.jrpi.pt\" target=\"_blank\">www.jrpi.pt</a>)<br />
						slovak - Daniel K·Ëer <a href=\"mailto:kacer@aceslovakia.sk\">kacer@aceslovakia.sk</a> (<a href=\"http://www.aceslovakia.sk\" target=\"_blank\">www.aceslovakia.sk</a>)<br />
						italian - Leonardo Lombardi (<a href=\"http://www.dimsat.unicas.it\" target=\"_blank\">www.dimsat.unicas.it</a>)<br />
						spanish - Urano Gonzalez <a href=\"mailto:urano@uranogonzalez.com\">urano@uranogonzalez.com</a> (<a href=\"http://www.uranogonzalez.com\" target=\"_blank\">www.uranogonzalez.com</a>)<br />
						swedish  - Thomas Westman <a href=\"mailto:Westman%20info@backupnow.se\">info@backupnow.se</a> (<a href=\"http://www.backupnow.se\" target=\"_blank\">www.backupnow.se</a>)</p>
						<p><strong>Beta Testers</strong><br />
						72dpi<br />
						ateul<br />
						backupnow<br />
						claudio<br />
						DanielMD<br />
						elmar<br />
						gaertner65<br />
						gdude66<br />
						jrpi<br />
						laurie_lewis<br />
						lexel<br />
						peternie<br />
						ravenswood<br />
						Skye<br />
						tvinhas<br />
						urano</p>
					</div>
				</td>
			</tr>
		</table>";
}

?>