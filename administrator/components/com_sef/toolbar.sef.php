<?php

/**
 * SEF module for Joomla!
 * Originally written for Mambo as 404SEF by W. H. Welch.
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007
 * @package     sh404SEF
 * @version     $Id: toolbar.sef.php 236 2008-01-27 19:40:53Z silianacom-svn $
 * {shSourceVersionTag: V 1.2.4.x - 2007-09-20}
 */
 
/** ensure this file is being included by a parent file */

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
require_once( $mainframe->getPath( 'toolbar_html' ) );
require_once( $mainframe->getPath( 'toolbar_default' ) );
switch ( $task ) {
	case 'view':
		if (@$_GET['section'] == "config")
			TOOLBAR_sef::_NEW();	
		else
			TOOLBAR_sef::_DEFAULT();
		break;
	case 'viewMeta':
	  TOOLBAR_sef::_META();
	break;
	case 'showconfig':
	case 'edit':
	case 'editMeta':
	case 'newMetaFromSEF':
	case 'homeAlias':
		TOOLBAR_sef::_EDIT();
	break;
	case 'new':
	case 'newMeta':
		TOOLBAR_sef::_NEW();
	break;
	case 'viewDuplicates':
		TOOLBAR_sef::_DUPLICATES();
	break;	
	case 'newHomeMeta':  // V 1.2.4.t
	case 'newHomeMetaFromSEF':  // V 1.2.4.t
		TOOLBAR_sef::_EDIT_HOME_META($task);
	break;	
	default:
		TOOLBAR_sef::_INFO();
		break;
}
?>
