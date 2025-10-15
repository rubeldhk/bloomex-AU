<?php

defined('_VALID_MOS') or die('Direct access is not permitted');

/**
 * 
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 *
 * $Rev::                 $
 * $Author::              $
 * $Date::                $
 *
 * This file is the control file for the administrative backend
 *
 */


// Include most important DBQ files for admin interface
global $mosConfig_absolute_path;
	
// New method of settings
require_once ($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/settings.class.php');
// Old method of settings
require_once ($mosConfig_absolute_path.'/components/com_dbquery/dbquery.def.php');
// Common admin class
//DBQ_Settings::includeClassFileForAdminType('DBQ_admin_common');
require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/common.class.php');

// Check for permissions
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') | $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_frontpage'))) {
	mosRedirect('index2.php', _NOT_AUTH);
}

DBQ_Settings::init();
DBQ_common::debugInit();

$XHTML = DBQ_Settings::getPath('xhtml');

// Variables used on most forms
$source = _DBQ_SOURCE_ADMIN;
$limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 10);
$limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
$task = mosGetParam($_REQUEST, 'task');
$act = mosGetParam($_REQUEST, 'act');
$obj = NULL;
$id = NULL;


// Glue so that we can preview queries within the admin screens
if ($task == 'PrepareQuery' || $task == 'ExecuteQuery')
	$act = 'preview';

// Place where debug messages can go
echo '<div id="DBQMessages" style="width: 80%;"></div>';
echo '<div id="DBQDebug" style="width: 80%;"></div>';

// Determine what type of action should be performed
switch ($act) {
case 'config' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/config.class.php');
	DBQ_Settings::includeClassFileForAdminType('DBQ_admin_config');
	$obj = new DBQ_admin_config();
	break;

case 'consulting' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/query.class.php');
	$obj = new DBQ_admin_query();
	include ($XHTML.'consulting.html');
	break;

case 'database' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/database.class.php');
	$obj = new DBQ_admin_database();
	break;

case 'errors' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/error.class.php');
	$obj = new DBQ_admin_error();
	break;
		
case 'help' :
	echo _LANG_OPENING_HELP_WINDOW;
	echo '<script language="JavaScript1.2">window.open(\''._DBQ_HELP_URL.'\');</script>';
	include ($XHTML.'support.html.php');
	break;

case 'license' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/query.class.php');
	$obj = new DBQ_admin_query();
	include ($XHTML.'gpl.html');
	break;

case 'preview' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/query.class.php');
	global $dbq;
	$obj = new DBQ_admin_query();
	@ include_once ($mosConfig_absolute_path.'/includes/sef.php');
	require ($dbq_user_path.'dbquery.php');
	include_once ($XHTML.'preview.html.php');
	break;

case 'query' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/query.class.php');
	$obj = new DBQ_admin_query();
	break;

case 'stats' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/stats.class.php');
	$obj = new DBQ_admin_stats();
	break;

case 'substitution' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/substitution.class.php');
	$obj = new DBQ_admin_substitution();
	break;

case 'template' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/template.class.php');
	$obj = new DBQ_admin_template('DBQ_admin_template');
	break;

case 'variable' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/variable.class.php');
	$obj = new DBQ_admin_variable();
	break;

case 'web' :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/query.class.php');
	$obj = new DBQ_admin_query();
	include ($XHTML.'web.html.php');
	break;

default :
	require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/query.class.php');
	$obj = new DBQ_admin_query();
}

// I hate to say that we're still using this
$globals = new stdClass();
$globals->limit = $limit;
$globals->limitstart = $limitstart;
$globals->task = $task;
$globals->act = $act;
$globals->option = $option;


if (isset ($obj)) {
	$identifier = $obj->getIdentifierForObjectType();
	$id = mosGetParam($_REQUEST, $identifier);
}


// ensure user has access to this function
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') | $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_dbquery'))) {
	mosRedirect('index2.php', _NOT_AUTH);
}

$cid = mosGetParam($_REQUEST, 'cid');

if ( (!$id) && isset ($cid[0])) {
	$id = $cid[0];
}


if (DBQ_common :: debug()) {
	echo "cid is ";
	print_r($cid).'<br/>';
	echo "id is $id<br/>";
}


switch ($task) {
case 'accesspublic' :
	$obj->adminAccessMenu($cid[0], 0);
	$obj->adminShow();
	break;
	
case 'accessregistered' :
	$obj->adminAccessMenu($cid[0], 1);
	$obj->adminShow();
	break;
	
case 'accessspecial' :
	$obj->adminAccessMenu($cid[0], 2);
	$obj->adminShow();
	break;
	
case 'apply' :
	$obj->adminSave($id);
	$obj->adminEdit($id);
	break;
	
case 'cancel' :
	$obj->adminCancel($cid);
	$obj->adminShow();
	break;

case 'copy' :
	$obj->adminCopy($cid);
	$obj->adminShow();
	break;

case 'edit' :
	if ( !$obj->adminEdit($id)) $obj->adminShow();
	break;
		
case 'help' :
	echo _LANG_OPENING_HELP_WINDOW;
	echo '<script language="JavaScript1.2">window.open(\''._DBQ_HELP_URL.'\');</script>';
	$obj->adminShow($id);
	break;
		
case 'new' :
	$obj->adminEdit(0);
	break;

case 'move' :
	// Currently used by the template manager
	$obj->adminMove($id);
	$obj->adminShow();
	break;
		
case 'orderup' :
	$obj->adminOrderUp($id, -1);
	$obj->adminShow();
	break;

case 'orderdown' :
	$obj->adminOrderDown($id, 1);
	$obj->adminShow();
	break;

case 'parse' :
	$obj->adminParseQuery($id);
	break;

case 'publish' :
	$obj->adminPublish($cid, 1);
	$obj->adminShow();
	break;

case 'remove' :
	$obj->adminRemove($cid);
	$obj->adminShow();
	break;

case 'reparse' :
	$obj->adminParseQueryUpdate($id);
	$obj->adminShow();
	break;
		
case 'save' :
	$obj->adminSave() ? $obj->adminShow() : $obj->setTask() && $obj->adminEdit($id);
break;
	
case 'saveorder' :
	$obj->adminSaveOrder($cid);
	$obj->adminShow();
	break;

case 'saveparse' :
	$obj->adminSaveParse($id, $cid) ? $obj->adminShow() : $obj->setTask('parse') && $obj->adminParseQuery($id);
break;

case 'show' :
	$obj->adminShow($id);
	break;

case 'toggle_default':
	$obj->adminToggleDefault($id);
	$obj->adminShow();
	break;
				
case 'unpublish' :
	$obj->adminPublish($cid, 0);
	$obj->adminShow();
	break;

case 'consulting' :
	include ($XHTML.'consulting.html');
	break;

default :
	break;
}

// Include the helper menu
if ( $task != 'edit' && $task != 'apply' && $task != 'new' && $task != 'parse' )
	require ($XHTML.'admin.menu.html.php');

if (DBQ_common :: debug() && false) 
	include($XHTML.'debug.html.php');

?>



