<?php
/**
 * $Id: toolbar.xmap.html.php 24 2007-09-29 16:46:17Z root $
 * $LastChangedDate: 2007-09-29 10:46:17 -0600 (sáb, 29 sep 2007) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/** Administrator Toolbar output */
class TOOLBAR_xmap {
	/**
	* Draws the toolbar
	*/
	function _DEFAULT() {
		mosMenuBar::startTable();
		/*
			//Testing
			mosMenuBar::custom('backup', 'archive.png', 'archive_f2.png', "Backup Settings", false);
			mosMenuBar::custom('restore', 'restore.png', 'restore_f2.png', "Restore Settings", false);
			mosMenuBar::spacer();
		*/
		if (_XMAP_JOOMLA15) {
			JToolBarHelper::title( 'Xmap', 'addedit.png' );
		}
		mosMenuBar::save('save', _XMAP_TOOLBAR_SAVE);
		mosMenuBar::spacer();
		mosMenuBar::cancel('cancel', _XMAP_TOOLBAR_CANCEL);
		mosMenuBar::endTable();
	}
}
