<?php
/**
* @version $Id: admin.banners.html.php 1596 2005-12-31 05:40:31Z stingrey $
* @package Joomla
* @subpackage Banners
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
* Banner clients
* @package Joomla
*/
class HTML_NewUserGroups {

	function showNewUserGroups( &$rows, &$pageNav, $option ) {
		global $my;

		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>User Groups Manager</th>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="20">
			#
			</th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th align="left" nowrap>
			User Groups
			</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 		= &$rows[$i];
			$link 		= 'index2.php?option=com_newusergroup&task=editA&hidemainmenu=1&id='. $row->id;
			$checked 	= mosHTML::idBox( $i, $row->id);
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center">
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td>
				<?php echo $checked; ?>
				</td>
				<td>
				<a href="<?php echo $link; ?>" title="User Group Name"><?php echo $row->departments_name; ?></a>
				</td>
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
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}
	

	function NewUserGroupForm( &$row, $option ) {
		mosMakeHtmlSafe( $row, ENT_QUOTES, 'extrainfo' );
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
			if (form.departments_name.value == "") {
				alert( "Please fill in the User Group Name." );
			}else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>
		<table class="adminheading">
		<tr>
			<th>
			User Group:
			<small>
			<?php echo ($row->id ? 'Edit' : 'New');?>
			</small>
			</th>
		</tr>
		</table>

		<form action="index2.php" method="post" name="adminForm">
		<table class="adminform">
		<tr>
			<th colspan="2">
			Details
			</th>
		</tr>
		<tr>
			<td width="10%">
			User Group Name:
			</td>
			<td>
			<input class="inputbox" type="text" name="departments_name" size="50" maxlength="255" valign="top" value="<?php echo $row->departments_name; ?>" />
			</td>
		</tr>
		<tr>
			<td valign="top">
			Area Name:
			</td>
			<td>
			<?php
				$aAreaName	= explode( "[--1--]", $row->area_name );
			?>
			<input type=checkbox name=area_name[] value='full_menus' <?php if( in_array('full_menus', $aAreaName) ) echo "checked"; ?> />Full Menus &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='manage_orders' <?php if( in_array('manage_orders', $aAreaName) ) echo "checked"; ?> />Manage Orders &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='manage_products' <?php if( in_array('manage_products', $aAreaName) ) echo "checked"; ?> />Manage Products &nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='manage_coupons' <?php if( in_array('manage_coupons', $aAreaName) ) echo "checked"; ?> />Manage Coupons &nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='manage_content' <?php if( in_array('manage_content', $aAreaName) ) echo "checked"; ?> />Manage Content &nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
			<input type=checkbox name=area_name[] value='view_reports' <?php if( in_array('view_reports', $aAreaName) ) echo "checked"; ?> />View Reports &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='manage_joomfish' <?php if( in_array('manage_joomfish', $aAreaName) ) echo "checked"; ?> />Manage Joomfish &nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='add_user' <?php if( in_array('add_user', $aAreaName) ) echo "checked"; ?> />Add User &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='edit_user' <?php if( in_array('edit_user', $aAreaName) ) echo "checked"; ?> />Edit User &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='manage_deliveries' <?php if( in_array('manage_deliveries', $aAreaName) ) echo "checked"; ?> />Manage Deliveries &nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
			<input type=checkbox name=area_name[] value='produce_order' <?php if( in_array('produce_order', $aAreaName) ) echo "checked"; ?> />Produce Order &nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='package_order' <?php if( in_array('package_order', $aAreaName) ) echo "checked"; ?> />Package Order  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='ship_order' <?php if( in_array('ship_order', $aAreaName) ) echo "checked"; ?> />Ship Order &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='packaging_delivery' <?php if( in_array('packaging_delivery', $aAreaName) ) echo "checked"; ?> />Packaging Delivery  &nbsp;
			<input type=checkbox name=area_name[] value='show_account_number' <?php if( in_array('show_account_number', $aAreaName) ) echo "checked"; ?> />Show Account Number &nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>			
			<input type=checkbox name=area_name[] value='postal_code_warehouse_manager' <?php if( in_array('postal_code_warehouse_manager', $aAreaName) ) echo "checked"; ?> />Postal code Warehouse Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='proflowers_order_manager' <?php if( in_array('proflowers_order_manager', $aAreaName) ) echo "checked"; ?> />Proflowers Order Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='phone_order_manager' <?php if( in_array('phone_order_manager', $aAreaName) ) echo "checked"; ?> />Phone Order Manager &nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
			<input type=checkbox name=area_name[] value='postal_code' <?php if( in_array('postal_code', $aAreaName) ) echo "checked"; ?> />Postal Code Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='driver_option' <?php if( in_array('driver_option', $aAreaName) ) echo "checked"; ?> />Driver Option Manager &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=checkbox name=area_name[] value='tax_manager' <?php if( in_array('tax_manager', $aAreaName) ) echo "checked"; ?> />Tax Manager <br/><br/>
			<input type=checkbox name=area_name[] value='location_manager' <?php if( in_array('location_manager', $aAreaName) ) echo "checked"; ?> />Location Manager
			</td>
		</tr>
		<tr>
			<td colspan="3">
			</td>
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