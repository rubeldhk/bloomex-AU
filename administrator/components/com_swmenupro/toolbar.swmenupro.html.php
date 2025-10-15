<?php
/**
* swmenupro v2.0
* http://swonline.biz
* Copyright 2004 Sean White
**/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class menucontact {
        /**
        * Draws the menu for a New Contact
        */
        
		
		function MODULE_EDIT_MENU() {
                 mosMenuBar::startTable();
                 mosMenuBar::save();
                 mosMenuBar::cancel();
				
              
				//mosMenuBar::publish();
                //mosMenuBar::unpublish();
                //mosMenuBar::divider();
               // mosMenuBar::addNew();
               // mosMenuBar::editList();
               // mosMenuBar::deleteList();
                //mosMenuBar::divider();
                //mosMenuBar::spacer();
                mosMenuBar::endTable();
        }
		function IMAGE_EDIT_MENU() {
                 mosMenuBar::startTable();
                 mosMenuBar::savenew();
                 mosMenuBar::cancel();
				
              
				//mosMenuBar::publish();
                //mosMenuBar::unpublish();
                //mosMenuBar::divider();
               // mosMenuBar::addNew();
               // mosMenuBar::editList();
                mosMenuBar::deleteList();
                //mosMenuBar::divider();
                //mosMenuBar::spacer();
                mosMenuBar::endTable();
        }
		
		function MODULE_MENU() {
                 mosMenuBar::startTable();
                // mosMenuBar::save();
                // mosMenuBar::cancel();
				
              
				mosMenuBar::publish();
                mosMenuBar::unpublish();
                mosMenuBar::divider();
                mosMenuBar::addNew();
                mosMenuBar::editList();
                mosMenuBar::deleteList();
                //mosMenuBar::divider();
                //mosMenuBar::spacer();
                mosMenuBar::endTable();
        }
		
		
		
		function SHOW_DHTML_MENU() {
                mosMenuBar::startTable();
                mosMenuBar::editlist();
                mosMenuBar::cancel();
                mosMenuBar::spacer();
                mosMenuBar::endTable();
		}
		function DHTML_MENU() {
                mosMenuBar::startTable();
				 ?>
			<td><a class="toolbar" href="#" onClick="doPreviewWindow();" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('preview','','images/preview_f2.png',1);"><img src="images/preview.png" alt="Preview" border="0" name="preview" align="middle">&nbsp;Preview</a></td>
		<?php
                mosMenuBar::saveedit();
                mosMenuBar::cancel();
                mosMenuBar::spacer();
                mosMenuBar::endTable();
        }
        function MANUAL_CSS() {
                mosMenuBar::startTable();
				 ?>
			<td><a class="toolbar" href="#" onClick="doPreviewWindow();" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('preview','','images/preview_f2.png',1);"><img src="images/preview.png" alt="Preview" border="0" name="preview" align="middle">&nbsp;Preview</a></td>
		<?php
                ?>
			<td><a class="toolbar" href="#" onClick="document.adminForm.submit();" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('save','','images/save_f2.png',1);"><img src="images/save.png" alt="save" border="0" name="save" align="middle">&nbsp;Save</a></td>
		<?php
                mosMenuBar::cancel();
                mosMenuBar::spacer();
                mosMenuBar::endTable();
        }

        function SAVE_MENU() {
                mosMenuBar::startTable();
                //mosMenuBar::saveedit();
               // mosMenuBar::cancel();
                mosMenuBar::spacer();
                mosMenuBar::endTable();
        }

		function CANCEL_MENU() {
                mosMenuBar::startTable();
                //mosMenuBar::saveedit();
               // mosMenuBar::cancel();
                mosMenuBar::spacer();
                mosMenuBar::endTable();
        }

        function DEFAULT_MENU() {
                 mosMenuBar::startTable();
               //  mosMenuBar::save();
               //  mosMenuBar::cancel();
				// mosMenuBar::divider();
                mosMenuBar::addNew();
                mosMenuBar::editList();
               mosMenuBar::divider();
				mosMenuBar::publish();
                mosMenuBar::unpublish();
               mosMenuBar::divider();
                mosMenuBar::deleteList();
                mosMenuBar::divider();
                mosMenuBar::spacer();
                mosMenuBar::endTable();
        }


}?>
