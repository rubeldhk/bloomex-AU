<?php

/***************************************
 * $Id: toolbar.dbquery.html.php,v 1.5 2005/05/25 14:16:35 tcp Exp $
 * 
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.5 $
 **/

defined( '_VALID_MOS' ) or die( _LANG_NO_ACCESS );


class menuDBQuery {

	function NEW_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}

	function BACK_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::back();
		mosMenuBar::endTable();
	}
	
	function EDIT_MENU() {
		global $id;
		$html = new mosMenuBar();
		mosMenuBar::startTable();
		mosMenuBar::save();
		method_exists( $html, 'apply') 
			? mosMenuBar::apply()
			: mosMenuBar::custom('apply',"save.png",'save_f2.png','Apply',false);
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	function EMPTY_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}

	function FILE_MENU() {
		mosMenuBar::startTable();
		menuDBQuery::customPrompt( 'copy', 'copy.png', 'copy_f2.png', 'Copy', 'Enter the new template name' );
		mosMenuBar::editHtmlX('edit');
		menuDBQuery::customPrompt( 'move', 'move.png', 'move_f2.png', 'Move', 'Enter the new template name'  );
		mosMenuBar::deleteList();
		mosMenuBar::custom('help',"help.png",'help_f2.png','Help',false);
		mosMenuBar::endTable();
	}
	
	function PARSE_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::custom('saveparse',"save.png",'save_f2.png','Save',false);
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}

	
	function QUERY_MENU() {
		$html = new mosMenuBar();
		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		method_exists($html, 'addNewX') 
			? mosMenuBar::addNewX()
			: mosMenuBar::addNew();
		mosMenuBar::custom( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
		method_exists($html, 'customX') 
			? mosMenuBar::customX( 'parse', '../components/com_dbquery/images/parse.gif', '../components/com_dbquery/images/parse_f2.gif', 'Parse' )
			: mosMenuBar::custom( 'parse', '../components/com_dbquery/images/parse.gif', '../components/com_dbquery/images/parse_f2.gif', 'Parse' );
		//mosMenuBar::custom( 'preview', 'peeview.gif', 'peeview_f2.png', 'Preview' );
		//mosMenuBar::custom( 'show-stats', 'stats.gif', 'stats_f2.gif', 'Stats' );					
		method_exists($html, 'editListX') 
			? mosMenuBar::editListX()
			: mosMenuBar::editList();
		mosMenuBar::deleteList();
		mosMenuBar::custom('help',"help.png",'help_f2.png','Help',false);
		mosMenuBar::endTable();
	}
	
	function DEFAULT_MENU() {
		$html = new mosMenuBar();
		mosMenuBar::startTable();
		method_exists($html, 'addNewX') 
			? mosMenuBar::addNewX()
			: mosMenuBar::addNew();
		mosMenuBar::custom( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
		method_exists($html, 'editListX') 
			? mosMenuBar::editListX()
			: mosMenuBar::editList();
		mosMenuBar::deleteList();
		mosMenuBar::custom('help',"help.png",'help_f2.png','Help',false);
		mosMenuBar::endTable();
	}
	
	/**
	* Writes a custom option and task button for the button bar.
	* Extended version of custom() calling hideMainMenu() before submitbutton().
	* @param string The task to perform (picked up by the switch($task) blocks
	* @param string The image to display
	* @param string The image to display when moused over
	* @param string The alt text for the icon image
	* @param boolean True if required to check that a standard list item is checked
	*/
	function customPrompt( $task='', $icon='', $iconOver='', $alt='', $message='Enter a Value', $text='' ) {
		$href = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('Please make a selection from the list to $alt');}else{document.adminForm.userinput.value = prompt('$message', '$text');submitbutton('$task')}";

		?>
		<td>
			<a class="toolbar" href="<?php echo $href;?>">
				<img name="<?php echo $task;?>" src="images/<?php echo $iconOver;?>" alt="<?php echo $alt;?>" border="0" align="middle" />
				<br /><?php echo $alt; ?></a>
		</td>

		<?php
	}
}
?>
