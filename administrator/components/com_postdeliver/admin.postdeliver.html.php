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
class HTML_postDeliver {
	

	//============================================= POSTAL CODE OPTION ===============================================
	function showpPostalCode( &$rows, &$pageNav, $option ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>Postal Code Manager</th>
			</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="20">#</th>
			<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title">Postal Code</th>
			<th width="20%" nowrap="nowrap" align="left">Location Name</th>
			<th style="display:none;" width="8%" nowrap="nowrap" align="center">Deliver Day(s)</th>			
			<th style="display:none;" width="12%" nowrap="nowrap" align="center">Price</th>
			<th style="display:none;" width="8%" nowrap="nowrap" align="center">Undeliverable Post Code</th>
			<th width="12%" nowrap="nowrap" align="center">Published</th>
			<th colspan="2" nowrap="nowrap" width="5%">Reorder</th>
			<th width="5%" align="left">&nbsp;</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			$link 	= 'index2.php?option=com_postdeliver&act=postal_code&task=editA&hidemainmenu=1&id='. $row->id;

			$img 	= $row->published ? 'tick.png' : 'publish_x.png';
			$task 	= $row->published ? 'unpublish' : 'publish';
			$alt 	= $row->published ? 'Published' : 'Unpublished';
			
			$aOption	= explode( "[--1--]", $row->options );
			
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><?php echo $checked; ?></td>
				<td><a href="<?php echo $link; ?>" title="Edit Postal Code Option"><?php echo $row->name; ?></a></td>
				<td style="display:none;" align="left"><b><?php echo $aOption[0]; ?></b></td>
				<td style="display:none;" align="center"><b><?php echo $aOption[1]; ?></b></td>
				<td style="display:none;" align="center"><b>$<?php echo $aOption[2]; ?></b></td>
				<td style="display:none;" align="center">
					<b>
					<?php 
						if ( $aOption[3] == 0 && $aOption[3] != null ) {
							echo "<font color='red'>Yes</font>";
						}else {
							echo "No";
						}
					?>
					</b>
				</td>
				<td align="center">
					<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
						<img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
					</a>
				</td>
				<td><?php echo $pageNav->orderUpIcon( $i, true ); ?></td>
				<td><?php echo $pageNav->orderDownIcon( $i, true ); ?></td>
				<td>&nbsp;</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="postal_code" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
		<?php
	}


	function editPostalCode( &$row, $option, &$lists ) {
		global $mosConfig_live_site;		
		$aOption	= explode( "[--1--]", $row->options );
	?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if ( form.name.value == "" ) {
				alert( "You must provide a name." );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>

		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
			Postal Code Option:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
			</small>
			</th>
		</tr>
		</table>

		<table width="100%" class="adminform">
			<tr>
				<th colspan="2">Deliver Option Detail</th>
			<tr>	
			<tr>
				<td width="10%"><b>Postcode:</b></td>
				<td><input class="inputbox" type="text" name="name" size="10" maxlength="10" value="<?php echo $row->name;?>" /></td>
			</tr>
			<tr style="display:none;">
				<td width="10%"><b>Location Name:</b></td>
				<td><input class="inputbox" type="text" name="location_name" size="50" maxlength="255" value="<?php echo $aOption[0];?>" /></td>
			</tr>
			<tr style="display:none;">
				<td width="10%"><b>Deliver day(s):</b></td>
				<td><input class="inputbox" type="text" name="deliver_day" size="10" maxlength="2" value="<?php  if( !$aOption[1] ) echo "0"; else echo  $aOption[1];?>" /></td>
			</tr>
			<tr style="display:none;">
				<td width="10%"><b>Price:</b></td>
				<td><b>$</b><input class="inputbox" type="text" name="price" size="9" maxlength="9" value="<?php  if( !$aOption[2] ) echo "0"; else echo $aOption[2];?>" /></td>
			</tr>
			<tr>
				<td><b>Publish:</b></td>
				<td><?php echo $lists['publish'];?></td>
			</tr>
			<tr style="display:none;">
				<td><b>Undeliverable Postcode:</b></td>
				<td><input id="undeliver1" type="radio" checked="checked" name="undeliver" value="0"></input></td>
			</tr>	
			<tr>
				<td><b>Ordering:</b></td>
				<td><input class="inputbox" type="text" name="ordering" size="10" maxlength="8" value="<?php echo $row->ordering;?>" /></td>
			</tr>	
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="postal_code" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
 }
?>
