<?php
/**
* @version $Id: toolbar.martlanguages.html.php,v 1.1 2005/09/27 19:50:18 soeren_nb Exp $
* @package Mambo
* @subpackage Languages
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package Mambo
* @subpackage Languages
*/
class TOOLBAR_martlanguages {
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::custom( 'cancel', 'copy.png', 'copy_f2.png', 'List Languages', false );
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::addNew();
		mosMenuBar::spacer();
		mosMenuBar::editListX( 'edit_source' );
		mosMenuBar::spacer();
		mosMenuBar::deleteList();
		mosMenuBar::divider();
		mosMenuBar::custom( 'list_tokens', 'copy.png', 'copy_f2.png', 'List all Tokens', false );
		mosMenuBar::custom( 'edit_tokens', 'edit.png', 'edit_f2.png', 'Edit Tokens', false );
		mosMenuBar::endTable();
	}
	function _NEW() {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	function _EDIT_SOURCE(){
		mosMenuBar::startTable();
		mosMenuBar::save( 'save_source' );
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	function _EDIT_TOKEN(){
		global $mosConfig_live_site;
		mosMenuBar::startTable();
		mosMenuBar::save( 'save_tokens' );
		mosMenuBar::spacer();
		?>
		<td><a href="index2.php?option=com_martlanguages&task=list_tokens" class="toolbar" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('cancel','','<?php echo $mosConfig_live_site."/administrator/images/cancel_f2.png" ?>',1);">
			<img src="<?php echo $mosConfig_live_site."/administrator/images/cancel.png" ?>" name="cancel" border="0" />
			&nbsp;Cancel
		  </a></td>
		  <?php
		mosMenuBar::endTable();
	}

}
?>