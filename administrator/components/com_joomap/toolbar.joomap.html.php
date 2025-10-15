<?php
	/**
	 * The Joomap component administrator toolbar
	 * @author Daniel Grothe
	 * @see admin.joomap.php
	 * @package Joomap_Admin
	 */
	 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/** Administrator Toolbar output */
class TOOLBAR_joomap {
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
		mosMenuBar::save('save', _JOOMAP_TOOLBAR_SAVE);
		mosMenuBar::spacer();
		mosMenuBar::cancel('cancel', _JOOMAP_TOOLBAR_CANCEL);
		mosMenuBar::endTable();
	}

}
?>