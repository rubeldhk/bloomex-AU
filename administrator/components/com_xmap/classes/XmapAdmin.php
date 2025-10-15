<?php
/**
 * $Id: XmapAdmin.php 137 2008-04-05 02:30:21Z root $
 * $LastChangedDate: 2008-04-04 20:30:21 -0600 (vie, 04 abr 2008) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_absolute_path;
if (!_XMAP_JOOMLA15) {
        require_once ($mosConfig_absolute_path .'/administrator/components/com_installer/installer.class.php');
        require_once ($mosConfig_absolute_path .'/includes/domit/xml_domit_lite_parser.php');
}

class XmapAdmin {
	
	var $config = null;
	
	/** Parses input parameters and calls appropriate function */
	function show( &$config, &$task, &$cid ) {
		$this->config = &$config;
		global $xmapComponentPath;
		switch ($task) {

			case 'save':
				$this->saveOptions( $config );
				break;
			
			case 'cancel':
				mosRedirect( 'index2.php' );
				break;

			case 'uploadfile':
				xmapUploadPlugin();
				break;

			case 'installfromdir':
				xmapInstallPluginFromDirectory();
				break;

			case 'ajax_request':
				include($xmapComponentPath . '/ajaxResponse.php');
				exit; 
				break;
			default:
				$success = mosGetParam($_REQUEST,'success','');
				$this->showSettingsDialog($success);
				break;
		}
	}

	/** Show settings dialog
	  * @param integer  configuration save success
	  */
	function showSettingsDialog( $success = 0 ) {
		global $mainframe, $database;

		$menus = $this->getMenus();
		# $this->sortMenus( $menus );
		
		$config = &$this->config;

	
	    // success messages
		switch( $success ) {
	    	case 1:
	    		$lists['msg_success'] = _XMAP_MSG_SET_BACKEDUP;
	    		break;
	    	case 2:
	    		$lists['msg_success'] = _XMAP_ERR_CONF_SAVE;
	    		break;
	    	default:
	    		$lists['msg_success'] =  _XMAP_CFG_COM_TITLE;
	    		break;
		}

		$pluginList = '';
		$xmlfile = '';
		loadInstalledPlugins($pluginList,$xmlfile);

		require_once( $mainframe->getPath( 'admin_html' ) );
		XmapAdminHtml::show( $config, $menus, $lists,$pluginList,$xmlfile );
	}

	/** Save settings handed via POST */
	function saveOptions( &$config ) {
		global $mosConfig_absolute_path;
	
		// save css
		$csscontent	= mosGetParam( $_POST, 'csscontent', '', _MOS_ALLOWHTML );	// CSS
		$file 		= $mosConfig_absolute_path .'/components/com_xmap/css/xmap.css';
		$enable_write	= mosGetParam( $_POST, 'enable_write', 0 );
		$oldperms	= fileperms($file);
		$success	= 1;
	
		
		$exclude_css	= mosGetParam( $_POST, 'exclude_css', 0 );
		$exclude_xsl	= mosGetParam( $_POST, 'exclude_xsl', 0 );

		$config->exclude_css = $exclude_css;
		$config->exclude_xsl = $exclude_xsl;
		$config->save();

		if ( $enable_write ) {
			@chmod( $file, $oldperms | 0222 );
		}
	
		clearstatcache();
		
		if( $fp = @fopen( $file, 'w' )) {
			fputs( $fp, stripslashes( $csscontent ) );
			fclose( $fp );
			if( $enable_write ) {
				@chmod( $file, $oldperms );
			}else{
				if( mosGetParam( $_POST, 'disable_write', 0 )){
					@chmod($file, $oldperms & 0777555);
				}
			}
		} else {
			if( $enable_write ){
				@chmod( $file, $oldperms );
			}
		}
		// end CSS
	
		
		mosRedirect('index2.php?option=com_xmap&success='.$success);
		exit;
	}

	/** 
	* 
	* get the complete list of menus in joomla 
	*/
	function &getMenus() {
		$config = &$this->config;
		
		if (defined('JPATH_ADMINISTRATOR')) {
			require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers'.DS.'helper.php' );
			$menutypes  = MenusHelper::getMenuTypeList();

			$allmenus = array();
			$i=0;
			foreach( $menutypes as $menu ) {
				$menutype = $menu->menutype;
				$allmenus[$menutype] = new stdclass;
				$allmenus[$menutype]->ordering = $i;
				$allmenus[$menutype]->show = false;
				$allmenus[$menutype]->showSitemap = false;
				$allmenus[$menutype]->priority = '0.5';
				$allmenus[$menutype]->changefreq = 'weekly';
				$allmenus[$menutype]->id = $i;
				$allmenus[$menutype]->type = $menutype;
				$i++;
			}
			
		} else {
			$menutypes  = mosAdminMenus::menutypes();

			$allmenus = array();
			foreach( $menutypes as $index => $menutype ) {
				$allmenus[$menutype] = new stdclass;
				$allmenus[$menutype]->ordering = $index;
				$allmenus[$menutype]->show = false;
				$allmenus[$menutype]->showSitemap = false;
				$allmenus[$menutype]->priority = '0.5';
				$allmenus[$menutype]->changefreq = 'weekly';
				$allmenus[$menutype]->id = $index;
				$allmenus[$menutype]->type = $menutype;
			}
		}
	
		return $allmenus;
	}
}

function loadInstalledPlugins( &$rows,&$xmlfile ) {
	global $database, $mosConfig_absolute_path;

	if (_XMAP_JOOMLA15 && !defined('DOMIT_INCLUDE_PATH') ) {
               	require_once ($mosConfig_absolute_path .'/libraries/domit/xml_domit_lite_parser.php');
	}

	$query = "SELECT id, extension, published"
	. "\n FROM #__xmap_ext"
	. "\n WHERE extension not like '%.bak'"
	. "\n ORDER BY extension";

	$database->setQuery( $query );
	$rows = $database->loadObjectList();

	$n = count( $rows );
	for ($i = 0; $i < $n; $i++) {
		$row =& $rows[$i];

		// path to module directory
		$extensionBaseDir	= mosPathName( mosPathName( $mosConfig_absolute_path ) . '/administrator/components/com_xmap/extensions/' );

		// xml file for module
		$xmlfile = $extensionBaseDir. "/" .$row->extension. ".xml";

		if (file_exists( $xmlfile )) {
			$xmlDoc = new DOMIT_Lite_Document();
			$xmlDoc->resolveErrors( true );
			if (!$xmlDoc->loadXML( $xmlfile, false, true )) {
				continue;
			}

			$root = &$xmlDoc->documentElement;

			if ($root->getTagName() != 'mosinstall') {
				continue;
			}
			if ($root->getAttribute( "type" ) != "xmap_ext") {
				continue;
			}


			$element 			= &$root->getElementsByPath( 'name', 1 );
			$row->name		 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'creationDate', 1 );
			$row->creationdate 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'author', 1 );
			$row->author 		= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'copyright', 1 );
			$row->copyright 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'authorEmail', 1 );
			$row->authorEmail 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'authorUrl', 1 );
			$row->authorUrl 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath( 'version', 1 );
			$row->version 		= $element ? $element->getText() : '';
		}else {
			echo "Missing file '$xmlfile'";
		}
	}
}

function showInstalledPlugins( $_option ) {
	$rows = '';
	$xmlfile = '';
	loadInstalledPlugins($rows,$xmlfile);
	XmapAdminHtml::showInstalledModules( $rows, $_option, $xmlfile, $lists );
}

/**
* Install a uploaded extension
*/
if ( !_XMAP_JOOMLA15 ) {
function xmapUploadPlugin( ) {
	global $mosConfig_absolute_path;
	$option ='com_xmap'; 
	$element = 'plugin';
	$client = '';
	require_once($mosConfig_absolute_path. '/administrator/components/com_xmap/classes/XmapPluginInstaller.php');
	$installer = new XmapPluginInstaller();

	// Check if file uploads are enabled
	if (!(bool)ini_get('file_uploads')) {
		XmapAdminHtml::showInstallMessage( "The installer can't continue before file uploads are enabled. Please use the install from directory method.",
			'Installer - Error', $installer->returnTo( $option, $element, $client ) );
		exit();
	}

	// Check that the zlib is available
	if(!extension_loaded('zlib')) {
		XmapAdminHtml::showInstallMessage( "The installer can't continue before zlib is installed",
			'Installer - Error', $installer->returnTo( $option, $element, $client ) );
		exit();
	}

	$userfile = mosGetParam( $_FILES, 'install_package', null );

	if (!$userfile) {
		XmapAdminHtml::showInstallMessage( 'No file selected', 'Upload new module - error',
			$installer->returnTo( $option, $element, $client ));
		exit();
	}

	$userfile_name = $userfile['name'];

	$msg = '';
	$resultdir = xmapUploadFile( $userfile['tmp_name'], $userfile['name'], $msg );

	if ($resultdir !== false) {
		if (!$installer->upload( $userfile['name'] )) {
			XmapAdminHtml::showInstallMessage( $installer->getError(), 'Upload '.$element.' - Upload Failed',
			$installer->returnTo( $option, $element, $client ) );
		}
		$ret = $installer->install();

		XmapAdminHtml::showInstallMessage( $installer->getError(), 'Upload '.$element.' - '.($ret ? 'Success' : 'Failed'),
			$installer->returnTo( $option, $element, $client ) );
		cleanupInstall( $userfile['name'], $installer->unpackDir() );
	} else {
		XmapAdminHtml::showInstallMessage( $msg, 'Upload '.$element.' -  Upload Error',
			$installer->returnTo( $option, $element, $client ) );
	}

} 

/**
* Install a extension from a directory
*/
function xmapInstallPluginFromDirectory() {
	global $mosConfig_absolute_path;
	$userfile = mosGetParam( $_REQUEST, 'userfile', '' );
	$option ='com_xmap'; 
	$element = 'plugin';
	$client = '';
	require_once($mosConfig_absolute_path. '/administrator/components/com_xmap/classes/XmapPluginInstaller.php');
	$installer = new XmapPluginInstaller();

	if (!$userfile) {
		mosRedirect( "index2.php?option=$option", "Please select a directory" );
	}

	$installer = new XmapPluginInstaller();

	$path = mosPathName( $userfile );
	if (!is_dir( $path )) {
		$path = dirname( $path );
	}

	$ret = $installer->install( $path );
	XmapAdminHtml::showInstallMessage( $installer->getError(), 'Upload new '.$element.' - '.($ret ? 'Success' : 'Error'), $installer->returnTo( $option, $element, $client ) );
}

} else {

require_once('XmapPluginInstallerJ15.php');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_installer'.DS.'models'.DS.'install.php');

function xmapUploadPlugin( ) {

	$installerModel = new InstallerModelInstall;

	 // Get an installer instance
	$installer =& JInstaller::getInstance();

	$xmapInstaller = new XmapPluginInstaller($installer);

	/* Fix for a small bug on Joomla on PHP 4 */
	if (version_compare(PHP_VERSION, '5.0.0', '<')) {
                // We use eval to avoid PHP warnings on PHP>=5 versions
                eval("\$installer->setAdapter('xmap_ext',&\$xmapInstaller);");
		$xmapInstaller->parent = &$installer;
		$install->_adapters['xmap_ext'] = &$xmapXinstaller;
        }else {
                $installer->setAdapter('xmap_ext',$xmapInstaller);
        }
	/* End of the fix for PHP <= 4 */

        if ($installerModel->install()) {
		XmapAdminHtml::showInstallMessage(_XMAP_EXT_INSTALLED_MSG, '', 'index.php?option=com_xmap');
        }
}

function xmapInstallPluginFromDirectory() {
	return xmapUploadPlugin();
}
}


/**
*
* @param
*/
function xmapUninstallPlugin( $extensionid ) {
	global $mosConfig_absolute_path;
	if ( !_XMAP_JOOMLA15 ) {
		require_once($mosConfig_absolute_path. '/administrator/components/com_xmap/classes/XmapPluginInstaller.php');
		$installer = new XmapPluginInstaller();
	}else {
		require_once($mosConfig_absolute_path. '/administrator/components/com_xmap/classes/XmapPluginInstallerJ15.php');
	 	// Get an installer instance
		$installer =& JInstaller::getInstance();
		$xmapInstaller = new XmapPluginInstaller($installer);
		$installer->setAdapter('xmap_ext',$xmapInstaller);
	}
	$result = false;
	if ($extensionid) {
		$result = $installer->uninstall('xmap_ext', $extensionid );
	}

	if (!$result) {
		echo $installer->getError();
	}
	return $result;
}

/**
* @param string The name of the php (temporary) uploaded file
* @param string The name of the file to put in the temp directory
* @param string The message to return
*/
function xmapUploadFile( $filename, $userfile_name, &$msg ) {
	global $mosConfig_absolute_path;
	$baseDir = mosPathName( $mosConfig_absolute_path . '/media' );

	if (file_exists( $baseDir )) {
		if (is_writable( $baseDir )) {
			if (move_uploaded_file( $filename, $baseDir . $userfile_name )) {
				if (mosChmod( $baseDir . $userfile_name )) {
					return true;
				} else {
					$msg = 'Failed to change the permissions of the uploaded file.';
				}
			} else {
				$msg = 'Failed to move uploaded file to <code>/media</code> directory.';
			}
		} else {
			$msg = 'Upload failed as <code>/media</code> directory is not writable.';
		}
	} else {
		$msg = 'Upload failed as <code>/media</code> directory does not exist.';
	}
	return false;
}
