<?php
/**
 * SEF module for Joomla!
 * Originally written for Mambo as 404SEF by W. H. Welch.
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007
 * @package     sh404SEF
 * @version     $Id: toolbar.sef.html.php 236 2008-01-27 19:40:53Z silianacom-svn $
 * {shSourceVersionTag: Version x - 2007-09-20}
 */
/** ensure this file is being included by a parent file */

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// Setup paths.
$sef_config_class = $GLOBALS['mosConfig_absolute_path']."/administrator/components/com_sef/sh404sef.class.php";
$sef_config_file  = $GLOBALS['mosConfig_absolute_path']."/administrator/components/com_sef/config/config.sef.php";
// Make sure class was loaded.
if (!class_exists('SEFConfig')) {   // V 1.2.4.T was wrong variable name $SEFConfig_class instead of $sef_config_class
  if (is_readable($sef_config_class)) require_once($sef_config_class);
    else die(_COM_SEF_NOREAD."( $sef_config_class )<br />"._COM_SEF_CHK_PERMS);
}

// V 1.2.4.t include language file
shIncludeLanguageFile();

class TOOLBAR_sef  {
	function _NEW() {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function _EDIT() {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function _INFO() {
		mosMenuBar::startTable();
		mosMenuBar::back();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function _DEFAULT() {
	  if (!defined('_COM_SEF_NEW_HOME_META')) shIncludeLanguageFile();  // quick fix for mambo 4.6.2
		mosMenuBar::startTable();
		mosMenuBar::addNew();
		mosMenuBar::editList();
		mosMenuBar::deleteList();
		mosMenuBar::divider();
		mosMenuBar::addNew('homeAlias', _COM_SEF_HOME_ALIAS);  // V 1.3.1
		mosMenuBar::divider();
		mosMenuBar::addNew('newHomeMetaFromSEF', _COM_SEF_NEW_HOME_META);  // V 1.2.4.t
		mosMenuBar::addNew('newMetaFromSEF', _COM_SEF_NEW_META); 
		mosMenuBar::divider();
		mosMenuBar::addNew('viewDuplicates', _COM_SEF_MANAGE_DUPLICATES_BUTTON);
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function _META() {
	  if (!defined('_COM_SEF_NEW_HOME_META')) shIncludeLanguageFile();  // quick fix for mambo 4.6.2
		mosMenuBar::startTable();
		mosMenuBar::addNew('newHomeMeta', _COM_SEF_NEW_HOME_META);  // V 1.2.4.t
		mosMenuBar::addNew('newMeta', _COM_SEF_NEW_META);
		mosMenuBar::divider();
		mosMenuBar::editList();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function _DUPLICATES() {
	  if (!defined('_COM_SEF_MANAGE_MAKE_MAIN_URL')) shIncludeLanguageFile();  // quick fix for mambo 4.6.2
		mosMenuBar::startTable();
		mosMenuBar::addNew('makeMainUrl', _COM_SEF_MANAGE_MAKE_MAIN_URL);
		mosMenuBar::divider();
		mosMenuBar::back();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function _EDIT_HOME_META($task) {
		mosMenuBar::startTable();
		mosMenuBar::save();
		$command = $task == 'newHomeMeta' ? 'deleteHomeMeta' : 'deleteHomeMetaFromSEF'; 
		mosMenuBar::custom($command , 'delete.png', 'delete_f2.png', _CMN_DELETE, false);  // V 1.2.4.t
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	
}
?>
