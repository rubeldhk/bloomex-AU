<?php
/**
* @version $Id: mod_todo.php 1 2006-09-07 0:43:10C hackwar $
* @package mod_todo
* @copyright Copyright (C) 2006 Hannes Papenberg
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* mod_ToDo is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );
global $mosConfig_lang;
if ($params->get('installtodo', 0)) {
	$database->setQuery("CREATE TABLE `#__todo` (
	  `id` int(11) NOT NULL auto_increment,
	  `uid` int(11) NOT NULL default '0',
	  `message` text NOT NULL,
	  `msg_priority` tinyint(2) NOT NULL default '0',
	  `msg_status` tinyint(2) NOT NULL default '0',
	  PRIMARY KEY  (`id`)
	) TYPE=MyISAM AUTO_INCREMENT=1 ;");
	$database->Query();
}

if (file_exists($mosConfig_absolute_path.'/administrator/modules/todo/'.$mosConfig_lang.'.php')) {
	include($mosConfig_absolute_path.'/administrator/modules/todo/'.$mosConfig_lang.'.php');
} else {
	include($mosConfig_absolute_path.'/administrator/modules/todo/english.php');
}

$action = mosGetParam( $_REQUEST, "todo_action", "show" );
switch( $action ) {
	case "add":
		add();
		mosRedirect( 'index2.php', 'Listentry added!' );
		break;
	case "remove":
		remove();
		mosRedirect( 'index2.php', 'Listentry removed!' );
		break;
	case "edit":
		show($params, mosGetParam($_REQUEST, 'todo_id'));
		break;
	case "show":
	default:
		show($params);
		break;
}
function show(&$params, $id = NULL) {
	global $database, $my, $mosConfig_live_site;
	
	$priority = $params->get( 'prioritize', 0 );
	$status = $params->get( 'use_status', 0 );
	$personified = $params->get( 'personifiedlist', 0 );
	$allow_sadmin = $params->get( 'superadmin', 0 );

	if( !$personified ) {
		$database->setQuery('SELECT * FROM #__todo WHERE uid = 0;');
		$results = $database->loadAssocList();

		$output = '<table class="adminlist"><tr>
				<th class="title">'. _TODO_MESSAGE_TITLE .'</th>
				<th class="title" width="5%">'. _TODO_MESSAGE_ACTIONS .'</th></tr>';

		if( isset( $results[0] ) ) {
			foreach( $results as $message ) {
				$output .= '<tr><td>'. $message['message'] .'</td><td valign="top">
						<a href="index2.php?todo_action=edit&todo_id='. $message['id'] .'">
						<img src="'. $mosConfig_live_site .'/administrator/components/com_media/images/edit_pencil.gif" border="0">
						<a href="index2.php?todo_action=remove&todo_id='. $message['id'] .'">
						<img src="'. $mosConfig_live_site .'/administrator/images/publish_x.png" border="0"></td></tr>';
				if($message['id'] == $id) {
					$edit_message = $message;
				}
			}
		} else {
			$output .= '<tr><td colspan="2">'. _TODO_NOITEM .'</td></tr>';
		}
		if(isset($edit_message)) {
		$output .= '<tr><td colspan="2"><form action="index2.php" method="post" name="todo_box">
			<textarea name="todo_message" cols="50" rows="10">'. $edit_message['message'] .'</textarea><br />
			<input type="submit" value="'. _TODO_ADD_BUTTON .'">
			<input type="hidden" name="todo_action" value="add">
			<input type="hidden" name="todo_id" value="'. $edit_message['id'] .'"></form></td></tr></table>';
		
		} else {
		$output .= '<tr><td colspan="2"><form action="index2.php" method="post" name="todo_box">
			<textarea name="todo_message" cols="50" rows="10"></textarea><br />
			<input type="submit" value="'. _TODO_ADD_BUTTON .'">
			<input type="hidden" name="todo_action" value="add"></form></td></tr></table>';
		}
	} else {
		$showothers = $params->get('showothers', 0);
		if($showothers) {
			$database->setQuery("SELECT * FROM #__todo");
		} else {
			$database->setQuery("SELECT * FROM #__todo WHERE uid in (0, ". $my->id .");");
		}
		$results = $database->loadAssocList();

		$output = '<table class="adminlist"><tr>
				<th class="title" width="15%">'. _TODO_USER .'</th>
				<th class="title">'. _TODO_MESSAGE_TITLE .'</th>
				<th class="title" width="5%">'. _TODO_MESSAGE_ACTIONS .'</th></tr>';
		$priority_msgs = array(_TODO_PRIORITY_NONE, _TODO_PRIORITY_LOWEST, _TODO_PRIORITY_LOW, _TODO_PRIORITY_MEDIUM, _TODO_PRIORITY_HIGH, _TODO_PRIORITY_HIGHEST);
		$status_msgs = array(_TODO_STATUS_NONE, _TODO_STATUS_ALERT, _TODO_STATUS_WARNING, _TODO_STATUS_NOT_STARTED, _TODO_STATUS_OK, _TODO_STATUS_COMPLETED);
		if(isset($results[0])) {
			$database->setQuery('SELECT id, username FROM #__users WHERE gid > 22');
			$users = $database->loadAssocList('id');
			foreach($results as $message) {
				if($message['uid'] == 0) {
					$usertodo = _TODO_ALL;
				} else {
					$usertodo = $users[$message['uid']]['username'];
				}
				if($message['id'] == $id) {
					$edit_message = $message;
				}
				$output .= '<tr><td>'. $usertodo .'</td>
						<td>'. $message['message'] .'</td><td valign="top">
						<a href="index2.php?todo_action=edit&todo_id='. $message['id'] .'">
						<img src="'. $mosConfig_live_site .'/administrator/components/com_media/images/edit_pencil.gif" border="0">
						<a href="index2.php?todo_action=remove&todo_id='. $message['id'] .'">
						<img src="'. $mosConfig_live_site .'/administrator/images/publish_x.png" border="0"></td></tr>';
				if($priority) {
					$priority_colors = array('#ffffff','#00FF00','#C0FF00','#FFFF00','#FF8000','#ff0000');
					$output .= '<tr><td>'. _TODO_PRIORITY .'</td><td colspan="2" bgcolor="'. $priority_colors[$message['msg_priority']] .'">'. $priority_msgs[$message['msg_priority']] .'</td></tr>';
				}
				if($status) {
					$output .= '<tr><td>'. _TODO_STATUS .'</td><td colspan="2">'. $status_msgs[$message['msg_status']]
					 .'</td></tr>';
				}
				$output .= '<tr><td colspan="3">&nbsp;</td></tr>';
			}
		} else {
			$output .= "<tr><td colspan=\"2\">No Items to show</td></tr>";
		}
		if(isset($edit_message)) {
			$output .= '<tr><td colspan="3"><form action="index2.php" method="post" name="todo_box">
				<textarea name="todo_message" cols="50" rows="10">'. $edit_message['message'] .'</textarea><br />';
			if($priority) {
				$output .= _TODO_PRIORITY .':&nbsp;<select name="todo_priority" size="1">';
				for( $i = 0; $i <= 5; $i++) {
					$output .= '<option value="'. $i .'"';
					if($edit_message['msg_priority'] == $i) {
						$output .= ' selected="selected"';
					}
					$output .= '>'. $priority_msgs[$i] .'</option>';
				}
				$output .= '</select><br />';
			}
			if($status) {
				$output .= _TODO_STATUS .':&nbsp;<select name="todo_status" size="1">';
				for( $i = 0; $i <= 5; $i++) {
					$output .= '<option value="'. $i .'"';
					if($edit_message['msg_status'] == $i) {
						$output .= ' selected="selected"';
					}
					$output .= '>'. $status_msgs[$i] .'</option>';
				}
				$output .= '</select><br />';
			}
			if($personified) {
				if($allow_sadmin && ($my->gid == 25)) {
					$database->setQuery("SELECT id, username FROM #__users WHERE gid > 22");
					$results = $database->loadAssocList();
					if($edit_message['uid'] == 0) {
						$output .= '<select name="todo_uid" size="1"><option value="0" selected="selected">All</option>';
					} else {
						$output .= '<select name="todo_uid" size="1"><option value="0">All</option>';
					}
					foreach($results as $user) {
						if($edit_message['uid'] == $user['id']) {
							$output .= '<option value="'. $user['id'] .'" selected="selected">'. $user['username'] .'</option>';
						} else {
							$output .= '<option value="'. $user['id'] .'">'. $user['username'] .'</option>';
						}
					}
					$output .= '</select><br />';
				} else {
					$output .= '<select name="todo_uid" size="1">';
					if($edit_message['uid'] == 0) {
						$output .= '<option value="0" selected="selected">All</option>';
					} else {
						$output .= '<option value="0">All</option>';
					}
					if($edit_message['uid'] == $my->id) {
						$output .= '<option value="'. $my->id .'" selected="selected">'. $my->username .'</option></select><br />';
					} else {
						$output .= '<option value="'. $my->id .'">'. $my->username .'</option></select><br />';
					}
				}
			}
			$output .= '<input type="submit" value="'. _TODO_ADD_BUTTON .'">
				<input type="hidden" name="todo_id" value="'. $edit_message['id'] .'">
				<input type="hidden" name="todo_action" value="add"></form></td></tr></table>';
		} else {
			$output .= '<tr><td colspan="3"><form action="index2.php" method="post" name="todo_box">
				<textarea name="todo_message" cols="50" rows="10"></textarea><br />';
			if($priority) {
				$output .= _TODO_PRIORITY .':&nbsp;<select name="todo_priority" size="1">';
				for( $i = 0; $i <= 5; $i++) {
					$output .= '<option value="'. $i .'">'. $priority_msgs[$i] .'</option>';
				}
				$output .= '</select><br />';
			}
			if($status) {
				$output .= _TODO_STATUS .':&nbsp;<select name="todo_status" size="1">';
				for( $i = 0; $i <= 5; $i++) {
					$output .= '<option value="'. $i .'">'. $status_msgs[$i] .'</option>';
				}
				$output .= '</select><br />';
			}	
			if($personified) {
				if($allow_sadmin && ($my->gid == 25)) {
					$database->setQuery("SELECT id, username FROM #__users WHERE gid > 22");
					$results = $database->loadAssocList();
					$output .= _TODO_USER .':&nbsp;<select name="todo_uid" size="1"><option value="0">All</option>';
					foreach($results as $user) {
						$output .= '<option value="'. $user['id'] .'">'. $user['username'] .'</option>';
					}
					$output .= '</select><br />';
				} else {
					$output .= '<select name="todo_uid" size="1"><option value="0">All</option>
						<option value="'. $my->id .'">'. $my->username .'</option></select><br />';
				}
			}
			$output .= '<input type="submit" value="'. _TODO_ADD_BUTTON .'">
				<input type="hidden" name="todo_action" value="add"></form></td></tr></table>';
		}
	}
	echo $output;
}

function add() {
	global $database, $my;
	$message = mosGetParam( $_REQUEST, "todo_message" );
	$uid = mosGetParam( $_REQUEST, "todo_uid", 0 );
	$priority = mosGetParam( $_REQUEST, "todo_priority", 0 );
	$status = mosGetParam( $_REQUEST, "todo_status", 0 );
	$id = mosGetParam( $_REQUEST, "todo_id", 0 );
	if($id == 0) {
		$database->setQuery("INSERT INTO #__todo VALUES (0,". $uid .",'". $message ."',". $priority .",". $status .");");
	} else {
		$database->setQuery("UPDATE #__todo SET uid = ". $uid .", message = '". $message ."', msg_priority = ". $priority .", msg_status = ". $status ." WHERE id = ". $id);
	}
	$database->Query();
}

function remove() {
	global $database;
	$id = mosGetParam($_REQUEST, "todo_id");
	if(isset($id)) {
		$database->setQuery("DELETE FROM #__todo WHERE id = ". $id );
		$database->Query();
	}
}
?>
