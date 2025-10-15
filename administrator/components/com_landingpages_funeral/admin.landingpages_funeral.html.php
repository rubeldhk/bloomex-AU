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
class HTML_LandingPages_Funeral {
	
	
	//============================================= Location OPTION ===============================================
	function showLandingPages_Funeral( &$rows, &$pageNav, $option, $lists ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>Location Manager</th>
			</tr>
			<tr>
				<td align="right" style="padding:0px 20px 10px 0px;">
					<a href="index2.php?option=com_landingpages_funeral&act=setup_category" style="float:left;margin-left:50px;color:#0C00CA;font-size:14px;">Category Product Configuration</a>
					<b>Filter By:&nbsp;</b>
					<input type="text" value="<?php echo $lists['filter_key'];?>" name="filter_key" size="30" />
					<?php echo $lists['filter_lang']?>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="20">#</th>
			<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title">Funeral Home</th>                        
			<th class="title">City</th>
			<th width="20%" nowrap="nowrap" align="left">Province</th>
			<th width="20%" nowrap="nowrap" align="left">Url</th>
			<th width="10%" nowrap="nowrap" align="left">Language</th>
			<th width="10%" nowrap="nowrap" align="center">Enable Location?</th>
			<th width="10%" nowrap="nowrap" align="center">Category ID</th>
			<th width="5%" align="left">&nbsp;</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			$link 	= 'index2.php?option=com_landingpages_funeral&task=editA&hidemainmenu=1&id='. $row->id;
			$checked 	= mosHTML::idBox( $i, $row->id);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><?php echo $checked; ?></td>
				<td><a href="<?php echo $link; ?>" title="Edit Location"><b style="font:bold 11px Tahoma;"><?php echo $row->city; ?></b></a></td>
				<td align="left"><?php echo $row->funeral_home; ?></td>                                
				<td align="left"><?php echo $row->province; ?></td>
				<td align="left"><?php echo $row->url; ?></td>
				<td align="left"><?php if( $row->lang == 1 ) echo "English"; else echo "French"; ?></td>
				<td align="center"><?php if( $row->enable_location > 0 ) echo "<b>Yes</b>"; else echo "No"; ?></td>
				<td align="center"><?php echo $row->category_id; ?></td>
				<td>&nbsp;</td>
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


	function editLandingPages_Funeral( &$row, $option, &$lists ) {
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
			if ( form.city.value == ""  ) {
				alert( "Please enter City!" );
				return;
			} 
			
			if ( form.url.value == ""  ) {
				alert( "Please enter Url!" );
				return;
			} 
			
			submitform( pressbutton );
		}
		//-->
		</script>

		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
				Location Manager:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
			</small>
			</th>
		</tr>
		</table>

		<table width="100%" class="adminform">
			<tr>
				<th colspan="2">Location Detail</th>
			<tr>	
			<tr>
				<td width="15%"><b>City:</b></td>
				<td><input class="inputbox" type="text" name="city" size="40" maxlength="255" value="<?php echo $row->city; ?>" /></td>
			</tr>
                        <tr>
				<td width="15%"><b>Funeral Home:</b></td>
				<td><input class="inputbox" type="text" name="city" size="40" maxlength="255" value="<?php echo $row->funeral_home; ?>" /></td>
			</tr>
			<tr>
				<td><b>Url:</b></td>
				<td><input class="inputbox" type="text" name="url" size="40" maxlength="255" value="<?php echo $row->url; ?>" /></td>
			</tr>
			<tr>
				<td><b>Province:</b></td>
				<td><input class="inputbox" type="text" name="province" size="40" maxlength="255" value="<?php echo $row->province; ?>" /></td>
			</tr>
			<tr>
				<td><b>Telephone:</b></td>
				<td><input class="inputbox" type="text" name="telephone" size="40" maxlength="255" value="<?php echo $row->telephone; ?>" /></td>
			</tr>
			<tr>
				<td><b>Language:</b></td>
				<td><?php echo $lists['lang']; ?></td>
			</tr>
			<tr>
				<td><b>Enable Location:</b></td>
				<td><?php echo $lists['enable_location']; ?></td>
			</tr>
			<tr>
				<td><b>Location Address:</b></td>
				<td><input class="inputbox" type="text" name="location_address" size="40" maxlength="255" value="<?php echo $row->location_address; ?>" /></td>
			</tr>
			<tr>
				<td><b>Location Country:</b></td>
				<td><?php echo $lists['location_country']; ?></td>
			</tr>
			<tr>
				<td><b>Location Postcode:</b></td>
				<td><input class="inputbox" type="text" name="location_postcode" size="40" maxlength="255" value="<?php echo $row->location_postcode; ?>" /></td>
			</tr>
			<tr>
				<td><b>Category ID:</b></td>
				<td>
					<input class="inputbox" type="text" name="category_id" size="40" maxlength="255" value="<?php echo $row->category_id; ?>" /><br/>
					The products show on front-end is belong to this Category ID.
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
	
	function setupCategory( $option ) {
		global $mosConfig_live_site;
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				location.href = "index2.php?option=com_landingpages_funeral";
			}
			
			if ( parseInt(form.category_id.value) <= 0 ) {
				alert( "You must enter a number of Category ID." );
				return;
			}
			
			submitform( pressbutton );
		}
		//-->
		</script>

		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
				Category Product Configuration:<small>Setup</small>
			</th>
		</tr>
		</table>
		<table width="100%" class="adminform">
			<tr>
				<th colspan="2"Category Product Configuration Detail</th>
			<tr>	
			<tr>
				<td width="10%" valign="top" ><b>Category ID:</b></td>
				<td>
					<input class="inputbox" type="text" name="category_id" size="40" maxlength="255" value="" /><br/><br/>
					This setup will update "<b>Category ID</b>" field of all records in "<b>tbl_landing_pages</b>" table.
				</td>
			</tr>
		</table>		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="setup_category" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
	
}
?>
