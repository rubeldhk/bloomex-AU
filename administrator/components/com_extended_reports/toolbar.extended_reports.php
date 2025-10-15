<?php


// ensure this file is being included by a parent file
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// ensure user has access to this function
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') || $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_dailymessage'))) {
    mosRedirect('index2.php', _NOT_AUTH);
}

// include support libraries
require_once( $mainframe->getPath( 'toolbar_html' ) );

TOOLBAR_ExtendedReports::_reports();
?>
