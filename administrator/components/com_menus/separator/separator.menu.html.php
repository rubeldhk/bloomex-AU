<?php
/**
* @version $Id: separator.menu.html.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @subpackage Menus
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
* Writes the edit form for new and existing content item
*
* A new record is defined when <var>$row</var> is passed with the <var>id</var>
* property set to 0.
* @package Joomla
* @subpackage Menus
*/
class separator_menu_html {

	static function edit( $menu, $lists, $params, $option ) {
		global $mosConfig_live_site;
		?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			var form = document.adminForm;
			submitform( pressbutton );
		}
		</script>

		<form action="index2.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th>
			<?php echo $menu->id ? 'Edit' : 'Add';?> Menu Item :: Separator / Placeholder
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr valign="top">
			<td width="60%">
				<table class="adminform">
				<tr>
					<th colspan="2">
					Details
					</th>
				</tr>
				<tr>
					<td align="right">
					Pattern/Name:
					</td>
					<td>
					<input class="inputbox" type="text" name="name" size="50" maxlength="100" value="<?php echo $menu->name; ?>" />
					</td>
				</tr>
				<tr>
					<td align="right">
					Parent Item:
					</td>
					<td>
					<?php echo $lists['parent']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					Ordering:
					</td>
					<td>
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					Access Level:
					</td>
					<td>
					<?php echo $lists['access']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">Published:</td>
					<td>
					<?php echo $lists['published']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				</table>
			</td>
			<td width="40%">
				<table class="adminform">
				<tr>
					<th>
					Parameters
					</th>
				</tr>
				<tr>
					<td>
					<?php echo $params->render();?>
					</td>
				</tr>
				</table>
				<br/>
				<table class="adminform">
				<tr>
					<th colspan="2">
					Meta Information
					</th>
				</tr>
				<tr>
					<td>
						<b>English Version:</b>
					</td>
				</tr>
				<tr>
					<td>
					Page Title:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="page_title"><?php echo str_replace('&','&amp;', (isset($menu->page_title) ? $menu->page_title : "")); ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
					Description:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="metadesc"><?php echo str_replace('&','&amp;',(isset($menu->metadesc) ? $menu->metadesc : "")); ?></textarea>
					</td>
				</tr>
					<tr>
					<td>
					Keywords:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="metakey"><?php echo str_replace('&','&amp;',(isset($menu->metakey) ? $menu->metakey : "" )); ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<b>Frech Version:</b>
					</td>
				</tr>
				<tr>
					<td>
					Page Title:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="page_title_fr"><?php echo str_replace('&','&amp;',(isset($menu->page_title_fr) ? $menu->page_title_fr : "")); ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
					Description:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="metadesc_fr"><?php echo str_replace('&','&amp;',(isset($menu->metadesc_fr) ? $menu->metadesc_fr : "")); ?></textarea>
					</td>
				</tr>
					<tr>
					<td>
					Keywords:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="metakey_fr"><?php echo str_replace('&','&amp;',(isset($menu->metakey_fr) ? $menu->metakey_fr : "")); ?></textarea>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="id" value="<?php echo $menu->id; ?>" />
		<input type="hidden" name="link" value="" />
		<input type="hidden" name="menutype" value="<?php echo $menu->menutype; ?>" />
		<input type="hidden" name="type" value="<?php echo $menu->type; ?>" />
		<input type="hidden" name="browserNav" value="3" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
		<?php
	}
}
?>