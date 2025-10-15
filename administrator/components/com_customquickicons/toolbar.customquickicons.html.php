<?php

/**
* @version 1.0
* @package Custom QuickIcons
* @copyright (C) 2005 Halil Köklü <halilkoklu at gmail dot com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or
  die( 'Direct Access to this location is not allowed.' );

/**
 * @package Custom QuickIcons
 */
class QI_Toolbar {
	
	function _edit() {
		mosMenuBar::startTable();
		mosMenuBar::save('save', _QI_SAVE);
		mosMenuBar::spacer();
		mosMenuBar::apply('apply', _QI_APPLY);
		mosMenuBar::spacer();
		mosMenuBar::cancel('', _QI_CANCEL);
		mosMenuBar::endTable();
	}
	
	function _show() {
		mosMenuBar::startTable();
		mosMenuBar::publishList('publish', _QI_PUBLISH);
		mosMenuBar::spacer();
		mosMenuBar::unpublishList('unpublish', _QI_UNPUBLISH);
		mosMenuBar::spacer();
		mosMenuBar::addNew('new',_QI_NEW);
		mosMenuBar::spacer();
		mosMenuBar::editList('edit', _QI_EDIT);
		mosMenuBar::spacer();
		mosMenuBar::deleteList('', 'delete', _QI_DELETE);
		mosMenuBar::endTable();
	}

}

?>