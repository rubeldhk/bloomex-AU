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
class HTML_Partners {
	
	
	//============================================= Location OPTION ===============================================
	function showPartners( &$rows, &$pageNav, $option, $lists ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>Partner Manager</th>
			</tr>
			<tr>
				<td align="right" style="padding:0px 20px 10px 0px;">
						<b>Filter By:&nbsp;</b>
					<input type="text" value="<?php echo $lists['filter_key'];?>" name="filter_key" size="30" />
					
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="20">#</th>
			<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title">Name</th>
			<th width="20%" nowrap="nowrap" align="left">Email</th>
                        <th width="20%" nowrap="nowrap" align="left">Phone</th>
			<th width="20%" nowrap="nowrap" align="left">Note</th>
			<th width="10%" nowrap="nowrap" align="left">Price percents</th>
		</tr>
		<?php
               // var_dump($rows);
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			$link 	= 'index2.php?option=com_partners&task=editA&hidemainmenu=1&partner_id='. $row->partner_id;
			$checked 	= mosHTML::idBox( $i, $row->partner_id);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td> 
				<td><?php echo $checked; ?></td>
				<td><a href="<?php echo $link; ?>" title="Edit Partner"><b style="font:bold 11px Tahoma;"><?php echo $row->partner_name; ?></b></a></td>
				<td align="left"><?php echo $row->partner_email; ?></td>
                                <td align="left"><?php echo $row->partner_phone; ?></td>
				<td align="left"><?php echo $row->note; ?></td>
				<td align="left"><?php echo $row->partner_price; ?></td>
	
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


	function editPartners( &$row, $option, &$lists ) {
		global $mosConfig_live_site, $mosConfig_absolute_path;		
		?>
		<script language="javascript" type="text/javascript">
		//<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if ( form.partner_name.value == ""  ) {
				alert( "Please enter Name!" );
				return;
			} 
			
			if ( form.partner_email.value == ""  ) {
				alert( "Please enter Email!" );
				return;
			} 
                        
                        if ( form.partner_phone.value == ""  ) {
				alert( "Please enter Phone!" );
				return;
			} 
                        /*
                        if (!form.partner_phone.value.match(/^\d+$/))
                        {
                            alert( "Phone must contain only numbers!" );
                            return;
                        }
			*/
			submitform( pressbutton );
		}
		//-->
		</script>

		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
				Partner Manager:
			<small>
			<?php echo $row->partner_id ? 'Edit' : 'New';?>
			</small>
			</th>
		</tr>
		</table>

		<table width="100%" class="adminform">
			<tr>
				<th colspan="2">Partner Detail</th>
			<tr>	
			<tr>
				<td width="15%"><b>Name:</b></td>
				<td><input class="inputbox" type="text" name="partner_name" size="40" maxlength="255" value="<?php echo $row->partner_name; ?>" /></td>
			</tr>
			<tr>
				<td><b>Email:</b></td>
				<td><input class="inputbox" type="text" name="partner_email" size="40" maxlength="255" value="<?php echo $row->partner_email; ?>" /></td>
			</tr>
                        <tr>
				<td><b>Phone:</b></td>
				<td><input class="inputbox" type="text" name="partner_phone" size="40" maxlength="255" value="<?php echo $row->partner_phone; ?>" /></td>
			</tr>
			<tr>
				<td><b>Note:</b></td>
				<td><input class="inputbox" type="text" name="note" size="40" maxlength="255" value="<?php echo $row->note; ?>" /></td>
			</tr>
			<tr>
				<td><b>Price percents:</b></td>
				<td><input class="inputbox" type="text" name="partner_price" size="40" maxlength="255" value="<?php echo $row->partner_price; ?>" /></td>
			</tr>
			
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="partner_id" value="<?php echo $row->partner_id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
	
	
}
