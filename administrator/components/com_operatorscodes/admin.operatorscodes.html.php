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
class HTML_OperatorsCodes {
	
	
	//============================================= Location OPTION ===============================================
	function showOperatorsCodes( &$rows, &$pageNav, $option ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>Operators Codes</th>
			</tr>
		<tr>
<!--				<td align="right" style="padding:0px 20px 10px 0px;">
					
					<input style="float:left;margin-left:30px;font-size:14px;"  type="button" value="Update History" name="Update History" onclick="location.href='index2.php?option=com_operatorscodes&act=updHist';"/>
					
				</td>-->
			</tr>	
		</table>
		<table class="adminlist">
		<tr>
			<th width="20">#</th>
			<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title">Operator</th>
			<th width="20%" nowrap="nowrap" align="left">Code</th>
			
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			$link 	= 'index2.php?option=com_operatorscodes&task=editA&hidemainmenu=1&id='. $row->id;
			$checked 	= mosHTML::idBox( $i, $row->id);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><?php echo $checked; ?></td>
				<td><a href="<?php echo $link; ?>" title="Edit code"><b style="font:bold 11px Tahoma;"><?php echo $row->name; ?></b></a></td>
				<td align="left"><?php echo $row->code; ?></td>
	
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


	function editOperatorsCodes( &$row, $option ) {
		global $mosConfig_live_site, $mosConfig_absolute_path;		
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
			
			
			submitform( pressbutton );
		}
		//-->
		</script>

		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
				Operator code Manager:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
			</small>
			</th>
		</tr>
		</table>

		<table width="100%" class="adminform">
			<tr>
				<th colspan="2">Operator Detail</th>
			<tr>	
			<tr>
				<td width="15%"><b>Name:</b></td>
				<td><input class="inputbox" type="text" name="name" size="40" maxlength="255" value="<?php echo $row->name; ?>" /></td>
			</tr>
			<tr>
				<td><b>Code:</b></td>
				<td><input class="inputbox" type="text" name="code" size="40" maxlength="255" value="<?php echo $row->code; ?>" /></td>
			</tr>
			
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
	
	
	
}
?>
