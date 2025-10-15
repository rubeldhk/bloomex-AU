<?php
/**
* FileName: toolbar.waticketsystem.php
* Date: 17/05/2006
* License: GNU General Public License
* Script Version #: 2.0.0
* JOS Version #: 1.0.x
* Development James Kennard jg8949@aol.com (www.webamoeba.co.uk)
**/

// Don't allow direct linking
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once( $mainframe->getPath( 'toolbar_html' ) );



if ($act)
{
	switch ( $act )
	{
		case 'configure':
			menuWATS::WATS_EDIT();
			break;
		case 'ticket':
		case 'database':
		case 'about':
			// no menus
			break;
		case 'css':
			menuWATS::WATS_EDIT_BACKUP();
			break;
		default:
			switch ( $task )
			{
				case 'edit';
				case 'view';
					menuWATS::WATS_EDIT();
					break;
				case 'new';
					menuWATS::WATS_NEW();
					break;
				default:
					menuWATS::WATS_LIST();
					break;
			}
			break;
	}
}
?>