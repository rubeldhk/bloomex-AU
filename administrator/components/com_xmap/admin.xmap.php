<?php 
/**
 * $Id: admin.xmap.php 129 2008-03-13 13:24:24Z root $
 * $LastChangedDate: 2008-03-13 07:24:24 -0600 (jue, 13 mar 2008) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas 
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );


// check access permissions (only superadmins & admins)
if ( !( $acl->acl_check('administration', 'config', 'users', $my->usertype) ) 
	||  $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_xmap') ) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}

$cid 		= mosGetParam( $_POST, 'cid', array(0) );
$task 		= mosGetParam( $_REQUEST, 'task', '' );

global $mosConfig_live_site;

if (defined('JPATH_ADMINISTRATOR')) {
	global $xmapComponentURL,$xmapSiteURL,$xmapComponentPath,$xmapAdministratorURL,$xmapLang,$xmapAdministratorPath;
	define ('_XMAP_JOOMLA15',1);
	$xmapLang = strtolower($mosConfig_lang);
	$xmapComponentPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xmap';
	$xmapComponentURL = $mosConfig_live_site.'/administrator/components/com_xmap';
	$xmapAdministratorPath = JPATH_ADMINISTRATOR;
	$xmapAdministratorURL = $mosConfig_live_site.'/administrator';
	$xmapSiteURL = $mosConfig_live_site;
} else {
	global $mosConfig_lang,$mosConfig_absolute_path,$xmapComponentURL,$xmapSiteURL,$xmapComponentPath,$xmapAdministratorURL,$xmapLang,$xmapAdministratorPath;
	define ('_XMAP_JOOMLA15',0);
	$xmapLang = strtolower($mosConfig_lang);
	$xmapComponentPath = $mosConfig_absolute_path.'/administrator/components/com_xmap';
	$xmapAdministratorPath = $mosConfig_absolute_path.'/administrator';
	$xmapComponentURL = $mosConfig_live_site.'/administrator/components/com_xmap';
	$xmapAdministratorURL = $mosConfig_live_site.'/administrator';
	$xmapSiteURL = $mosConfig_live_site;
}

// To determine if we are running on Mambo CMS
if ( !_XMAP_JOOMLA15 && !file_exists($mosConfig_absolute_path.'/includes/joomla.php') ) {
	define('_XMAP_MAMBO',1);
} else {
	define('_XMAP_MAMBO',0);
}

// load language file
if( file_exists( $xmapComponentPath .'/language/' . $xmapLang . '.php') ) {
	require_once( $xmapComponentPath .'/language/' . $xmapLang . '.php' );
} else {
	if ($task != 'ajax_request') {
		echo 'Language file [ '. $xmapLang .' ] not found, using default language: english<br />';
	}
	$xmapLang = 'english';
	require_once( $xmapComponentPath .'/language/english.php' );
}

require_once( $xmapComponentPath.'/classes/XmapAdmin.php' );

// load settings from database
require_once( $xmapComponentPath.'/classes/XmapConfig.php' );
require_once( $xmapComponentPath.'/admin.xmap.html.php' );
$config = new XmapConfig;
if( !$config->load() ) {
	$text = _XMAP_ERR_NO_SETTINGS."<br />\n";
	$link = 'index2.php?option=com_xmap&task=create';
	echo sprintf( $text, $link );
}

$admin = new XmapAdmin();
$admin->show( $config, $task, $cid );

