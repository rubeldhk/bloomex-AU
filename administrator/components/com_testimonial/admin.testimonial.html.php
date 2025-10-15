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
class HTML_Testimonial {	
	//============================================= DELIVER OPTION ===============================================
	function showTestimonial( &$rows, &$pageNav, $option, $filter_keywords ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>Testimonial Manager</th>
			</tr>
			<tr>
				<td align="right" style="padding-right:10px;">
					Keywords:&nbsp;
					<input type="text" name="filter_keywords" id="filter_keywords" value="<?php echo $filter_keywords; ?>" size="50" />
					<br/><br/>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="20">#</th>
			<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title">Testimonial Text</th>
			<th width="20%" nowrap="nowrap" align="left">Client Name</th>
			<th width="20%" nowrap="nowrap" align="left">City</th>
			<th width="5%" nowrap="nowrap" align="center">Published</th>
			<th width="3%" align="left">&nbsp;</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			$link 	= 'index2.php?option=com_testimonial&task=editA&hidemainmenu=1&id='. $row->id;
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			
			$img 	= $row->published ? 'tick.png' : 'publish_x.png';
			$task 	= $row->published ? 'unpublish' : 'publish';
			$alt 		= $row->published ? 'Published' : 'Unpublished';
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><?php echo $checked; ?></td>
				<td><a href="<?php echo $link; ?>" title="Edit Testimonial Text"><b style="font:bold 11px Tahoma;"><?php echo $row->msg; ?></b></a></td>
				<td align="left"><strong><?php echo $row->client_name; ?></strong></td>
				<td align="left"><?php echo $row->city_name; ?></td>
				<td align="center">
					<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
						<img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
					</a>
				</td>
				<td>&nbsp;</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
		<?php
	}


	function editTestimonial( &$row, $option, &$lists ) {
		global $mosConfig_live_site;		
		
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
			if ( form.msg.value == "" ) {
				alert( "Please enter Testimonial Text!" );
			}else( form.client_name.value == ""  ) {
				alert( "Please enter Client Name!" );
			}else( form.city_name.value == ""  ) {
				alert( "Please enter City Name!" );
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
			Testimonial:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
			</small>
			</th>
		</tr>
		</table>

		<table width="100%" class="adminform">
			<tr>
				<th colspan="2">Testimonial Detail</th>
			</tr>			
			<tr>
				<td width="10%"><b>Testimonial Text:</b></td>
				<td><input class="inputbox" type="text" name="msg" size="70" maxlength="250" value="<?php echo $row->msg; ?>" /></td>
			</tr>				
			<tr>
				<td><b>Client Name:</b></td>
				<td><input class="inputbox" type="text" name="client_name" size="50" maxlength="50" value="<?php echo $row->client_name; ?>" /></td>
			</tr>				
			<tr>
				<td><b>City:</b></td>
				<td><input class="inputbox" type="text" name="city_name" size="50" maxlength="100" value="<?php echo $row->city_name; ?>" /></td>
			</tr>			
			<tr>
				<td><b>Publish:</b></td>
				<td><?php echo $lists['publish'];?></td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}	
}
?>
