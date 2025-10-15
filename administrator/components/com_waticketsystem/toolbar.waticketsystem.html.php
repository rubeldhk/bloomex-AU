<?php
/**
* FileName: toolbar.waticketsystem.html.php
* Date: 10/05/2006
* License: GNU General Public License
* Script Version #: 2.0.0
* JOS Version #: 1.0.x
* Development James Kennard jg8949@aol.com (www.webamoeba.co.uk)
**/

// Don't allow direct linking
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class menuWATS
{

	function WATS_LIST()
	{
		mosMenuBar::startTable();
		mosMenuBar::addNew();
		mosMenuBar::endTable();
	}
	
	function WATS_EDIT()
	{
		mosMenuBar::startTable();
		mosMenuBar::apply();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}
	
	function WATS_EDIT_BACKUP()
	{
		mosMenuBar::startTable();
		mosMenuBar::apply();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		// $task='', $icon='', $iconOver='', $alt='', $listSelect=true
		mosMenuBar::custom('backup', 'download_f2.png', 'download_f2.png', $alt='Backup', false);
		mosMenuBar::endTable();
	}
	
	function WATS_NEW()
	{
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}
	
}
?>