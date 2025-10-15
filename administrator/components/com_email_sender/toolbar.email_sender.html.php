<?php
/**
* @version $Id: toolbar.content.html.php 4675 2006-08-23 16:55:24Z stingrey $
* @package Joomla
* @subpackage Content
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* @package Joomla
* @subpackage Content
*/
class TOOLBAR_content {
	static function _EDIT() {
		global $id;

		mosMenuBar::startTable();
		mosMenuBar::preview( 'contentwindow', true );
		mosMenuBar::spacer();

		mosMenuBar::save();
		mosMenuBar::spacer();

		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancel', 'Close' );
		} else {
			mosMenuBar::cancel();
		}
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.content.edit' );
		mosMenuBar::endTable();
	}


	static function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::trash();
		mosMenuBar::spacer();
		mosMenuBar::editListX( 'editA' );
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.content' );
		mosMenuBar::endTable();
	}
}
?>