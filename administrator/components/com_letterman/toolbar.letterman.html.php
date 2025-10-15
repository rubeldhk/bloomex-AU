<?php
/**
* Letterman Newsletter Component
* 
* @package Letterman
* @author: Soeren
* @copyright Soeren Eberhardt <soeren@virtuemart.net>
    (who just needed an easy and *working* Newsletter component for Mambo 4.5.1 and mixed up Newsletter and YaNC)
* @copyright Mark Lindeman <mark@pictura-dp.nl> 
    (parts of the Newsletter component by Mark Lindeman; Pictura Database Publishing bv, Heiloo the Netherland)
* @copyright Adam van Dongen <adam@tim-online.nl>
    (parts of the YaNC component by Adam van Dongen, www.tim-online.nl)
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
*/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class MENU_letterman {
	/**
	* Draws the menu for a New Contact
	*/
	function EDIT_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'save', _E_SAVE);
		mosMenuBar::spacer();
		mosMenuBar::cancel( 'cancel', _E_CANCEL);
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function COMPOSE_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'composeNow', _E_SAVE);
		mosMenuBar::spacer();
		mosMenuBar::cancel( 'cancel', _E_CANCEL);
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function CONFIRM_COMPOSE_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'save', _E_SAVE);
		mosMenuBar::spacer();
		mosMenuBar::cancel( 'compose', _E_CANCEL);
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}	
	function SEND_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::publish( "sendMail" );
		mosMenuBar::cancel( 'cancel', _CMN_CANCEL);
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}

	function DEFAULT_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::divider();
		mosMenuBar::spacer();
		mosMenuBar::addNew( 'new', _CMN_NEW);
		mosMenuBar::spacer();
		mosMenuBar::addNew( 'compose', 'Compose Newsletter');
		mosMenuBar::spacer();
		mosMenuBar::editList( 'edit', _E_EDIT);
		mosMenuBar::spacer();
		mosMenuBar::deleteList( '', 'remove', _E_REMOVE);
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function SUBSCRIBE_MENU() {
	  global $mosConfig_live_site;
		mosMenuBar::startTable();
		mosMenuBar::addNew( "editSubscriber", LM_NEW_SUBSCRIBER );
		mosMenuBar::spacer();
		mosMenuBar::editList( "editSubscriber", LM_EDIT_SUBSCRIBER );
		mosMenuBar::spacer();
		mosMenuBar::deleteList( "", "deleteSubscriber", _E_REMOVE );
		mosMenuBar::spacer();
		mosMenuBar::divider();
		mosMenuBar::spacer();
		$href = "javascript:submitbutton('assignUsers')";
		?>
		<td>
			<a class="toolbar" href="<?php echo $href;?>" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('users','','<?php echo $mosConfig_live_site."/components/com_letterman/images/user_f2.png" ?>',1);">
			<img name="users" src="<?php echo $mosConfig_live_site."/components/com_letterman/images/user.png" ?>" alt="assignUsers" border="0" align="middle" />
			&nbsp;<?php echo LM_ASSIGN_USERS; ?></a>
		</td>
  <?php
  		mosMenuBar::spacer();
  		
		if( function_exists('getmxrr')) {
			mosMenuBar::divider();
			mosMenuBar::spacer();
			$href = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('Please make a selection from the list to validate emails');}else{ if( document.adminForm.boxchecked.value > 5 ) { if( confirm('You have selected more than 5 items for validation. \\nValidation can take very long and result in a timout error.\\nDo you still want to continue?')) { submitbutton('validateEmails')}}}";
			?>
			<td>
				<a class="toolbar" href="<?php echo $href;?>" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('validateEmails','','<?php echo $mosConfig_live_site."/components/com_letterman/images/validate_f2.gif" ?>',1);">
				<img name="validateEmails" src="<?php echo $mosConfig_live_site."/components/com_letterman/images/validate.gif" ?>" alt="validateEmails" border="0" align="middle" />
				&nbsp;Validate</a>
			</td>
	  		<?php
	  		mosMenuBar::spacer();
		}
		mosMenuBar::divider();
		mosMenuBar::spacer();
		mosMenuBar::custom( 'importSubscribers', 'upload.png', 'upload_f2.png', LM_IMPORT_USERS, false );
		mosMenuBar::spacer();
		mosMenuBar::custom( 'exportSubscribers', 'archive.png', 'archive_f2.png', LM_EXPORT_USERS, false );
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function SUBSCRIBER_EDIT_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( "saveSubscriber", _E_SAVE );
		mosMenuBar::spacer();
		mosMenuBar::cancel( "subscribers", _E_CANCEL );
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function SUBSCRIBER_IMPORT_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( "importSubscribers", _E_SAVE );
		mosMenuBar::spacer();
		mosMenuBar::cancel( "subscribers", _E_CANCEL );
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function CONFIG_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save('saveconfig', _E_SAVE);
		mosMenuBar::spacer();
		mosMenuBar::cancel('cancelconfig', _E_CANCEL);
		mosMenuBar::endTable();
	}
}?>
