<?php
/**
* @version $Id: session_handler.php 11/17/2005 gharding@gmail.com $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/**
CREATE TABLE `#__sessions` (
 `id` varchar(40) NOT NULL,
 `time` TIMESTAMP NOT NULL DEFAULT NOW(),
 `data` text NOT NULL,
 PRIMARY KEY  (`id`)
 ) TYPE=MyISAM;
*/

/**

Edit /includes/joomla.php
You should enter this line after line 82 (It should follow 7 other require_once() lines):

require_once( $mosConfig_absolute_path . '/includes/session_handler.php'); // hack by gharding[@gmail.com]

You should place this file (session_handler.php) in /includes/

You should run this query in mySQL
####BE SURE TO REPLACE #__ WITH YOUR TABLE PREFIX!!!####

CREATE TABLE `#__sessions` (
 `id` varchar(40) NOT NULL,
 `time` TIMESTAMP NOT NULL DEFAULT NOW(),
 `data` text NOT NULL,
 PRIMARY KEY  (`id`)
 ) TYPE=MyISAM;
 
Then everything should be good to go!

**/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

function __sess_open ($path, $sessionid) { return true; }
function __sess_close () { return true; }

function __sess_read ($sessionid) {
	global $database;

	$sessionid = mysql_real_escape_string($sessionid);
	$sql = "SELECT `data` FROM #__sessions WHERE `id`='$sessionid'";
	$database->setQuery($sql);
	$row = $database->loadResult();
	
	return ($row ? $row : "");

}

function __sess_write ($sessionid,$data) {

	global $database;

	$sessionid = mysql_real_escape_string($sessionid);
	$data = mysql_real_escape_string($data);

	list($maj,$min) = explode('.',mysql_get_server_info());
	if ($maj.$min > 41) { $sql = "INSERT INTO #__sessions (`id`, `time`, `data`) VALUES ('$sessionid', NOW(), '$data') ON DUPLICATE KEY UPDATE"; }
	else {
		$_sql = "SELECT id FROM #__sessions WHERE `id`='$sessionid'";
		$database->setQuery($_sql);
		$database->query();
		if ($database->getNumRows() > 0) {
			$sql = "UPDATE #__sessions SET `data`='$data',`time`=NOW() WHERE `id`='$sessionid'";
		} else {
			$sql = "INSERT INTO #__sessions (`id`,`time`,`data`) VALUES ('$sessionid', NOW(), '$data')";
		}
	}

	$database->setQuery($sql);
	return $database->query();
	
}

function __sess_destroy ($sessionid) {

	global $database;

	$sessionid = mysql_real_escape_string($sessionid);
	$sql = "DELETE FROM TABLE WHERE `id`='$sessionid'";
	$database->setQuery($sql);
	return $database->query();

}

function __sess_gc ($lifetime) {

	global $database;

	$time = time() - intval($lifetime);
	$sql = "DELETE FROM #__sessions WHERE UNIX_TIMESTAMP(`time`) <= $time";
	$database->setQuery($sql);
	return $database->query();

}

session_set_save_handler('__sess_open', '__sess_close', '__sess_read', '__sess_write', '__sess_destroy', '__sess_gc');

?>
