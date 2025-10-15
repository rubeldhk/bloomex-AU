<?php
/**
 * Enter description here...
 *
 * @package JL
 */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Contains all the menus
 *
 * @package JL
 */
class MENU_joomlalib
{
	function DEFAULT_MENU() 
	{
		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::back();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	
	function JLCONF_DEFAULT(){
		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::saveedit();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	
	function JLCONF_SAVEEDIT(){
		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::saveedit();
		mosMenuBar::back();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	
	function JLAPP_EDIT(){
		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::saveedit();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::endTable();	
	}
	
	function JLAPP_DEFAULT(){
		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::editList();
		mosMenuBar::deleteList();
		mosMenuBar::back();
		mosMenuBar::spacer();
		mosMenuBar::endTable();	
	}
	
	function JLLOG_MAINTAIN(){
		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::back();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	
	function JLLOG_DEFAULT(){
		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::custom( 'maintain', 'tool.png', 'tool_f2.png', 'Maintain', $listSelect=false );
		mosMenuBar::back();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
}
?>