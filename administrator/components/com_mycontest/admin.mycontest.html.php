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
class HTML_MyContest {
	
	//============================================= POSTAL CODE OPTION ===============================================
	function showMyContest( &$rows, &$pageNav, $option) {
		global $mosConfig_live_site;
		mosCommonHTML::loadOverlib();
		?>		
		<style type="text/css">
			table tr.items td {
				padding:8px;
			}
		</style>			
		<table class="adminheading">
			<tr>
				<th>Contests Manager</th>
			</tr>
		</table>
		<table class="adminlist" cellpadding="5">
		<tr>
			<th width="80" align="center">#</th>
			<th width="20%" align="left">Full Name</th>
			<th width="20%" align="left">Email Address</th>
			<th width="28%" align="left">Address</th>
			<th width="10%" align="center">Telephone</th>
			<th width="10%" align="center">Notification</th>
			<th width="10%" align="left">Created Date</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			?>
			<tr class="items <?php echo "row$k"; ?>">
				<td align="center"><b><?php echo $pageNav->rowNumber( $i ); ?></b></td>
				<td style="text-align:left;font:bold 11px 'Tahoma';color:red;">
					<?php echo $row->first_name . "" . $row->last_name ; ?>
				</td>
				<td style="text-align:left;font:bold 11px 'Tahoma';color:blue;">
					<?php echo $row->email_address ; ?>
				</td>
				<td style="text-align:left;"><?php echo $row->address.", ".$row->city.", ".$row->province.", ".$row->postal_code; ?></td>				
				<td style="text-align:center;"><?php echo $row->telephone; ?></td>
				<td style="text-align:center;"><?php echo ( $row->notification ? "Yes" : "No"); ?></td>		
				<td><?php echo date( "Y/d/m H:i:s", strtotime($row->created_date)); ?></td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		<?php
	}
}
?>
