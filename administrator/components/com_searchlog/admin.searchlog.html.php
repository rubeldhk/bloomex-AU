<?php
/**
* @version $Id: admin.Category.html.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Category
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
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
* @subpackage Category
*/
class HTML_SearchLog {
	
	
	//============================================= Location OPTION ===============================================
	function showSearchLog( &$rows, &$pageNav, $option, $filter_key ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>Search Log Manager</th>
			</tr>
			<tr>
				<td align="right" style="padding:0px 20px 10px 0px;">
					<b>Filter By:&nbsp;</b>
					<input type="text" value="<?php echo $filter_key;?>" name="filter_key" size="30" />
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="40" align="center">#</th>
			<th class="title">Keywords</th>
			<th width="20%" nowrap="nowrap" align="center">Count</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><b style="font:bold 11px Tahoma;"><?php echo html_entity_decode($row->search_word, ENT_QUOTES ); ?></b></td>
				<td align="center"><?php echo $row->search_count; ?></td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
		<?php
	}

	
	
}
?>
