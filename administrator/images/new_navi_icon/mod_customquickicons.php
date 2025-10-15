<?php
/**
* @version $Id: mod_quickicon.php 1004 2005-11-13 17:18:18Z stingrey $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
/**
* @version 1.0
* @package Custom QuickIcons
* @copyright (C) 2005 Halil K�kl� <halilkoklu at gmail dot com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

if (!defined( '_JOS_QUICKICON_MODULE' )) {
	/** ensure that functions are declared only once */
	define( '_JOS_QUICKICON_MODULE', 1 );	

	function quickiconButton( $link, $image, $text, $pre, $post, $newWindow ) {
		?>
		<div style="float:left;">
			<div class="icon">
				<a href="<?php echo $link; ?>"<?php echo $newWindow; ?>>
					<?php echo mosAdminMenus::imageCheckAdmin( $image, '/administrator/images/new_navi_icon/', NULL, NULL, $text ); ?>
					<span><?php echo $pre.$text.$post; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
	
	?>
	<div id="cpanel">
	
		<?php
			global $database, $my;
			$aIconLinks 		=  array();
			$aIconLinks[0]		= "Manage Orders[--1--]manage_orders.png[--1--]index2.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart";		
			$aIconLinks[1]		= "Manage Products[--1--]manage_products.png[--1--]index2.php?pshop_mode=admin&page=product.product_list&option=com_virtuemart";		
			$aIconLinks[2]		= "Manage Coupons[--1--]manage_coupons.png[--1--]index2.php?pshop_mode=admin&page=coupon.coupon_list&option=com_virtuemart";		
			
			$aIconLinks[3]		= "Manage Content[--1--]manage_content.png[--1--]index2.php?option=com_content";
			$aIconLinks[4]		= "Manage Static Content[--1--]manage_static_content.png[--1--]index2.php?option=com_typedcontent";
			$aIconLinks[5]		= "Manage Content Sections[--1--]manage_section.png[--1--]index2.php?option=com_sections&scope=content";
			$aIconLinks[6]		= "Manage Content Categories[--1--]manage_category.png[--1--]index2.php?option=com_categories&section=content";			
			
			$aIconLinks[7]		= "View Reports[--1--]view_reports.png[--1--]index2.php?option=com_brightreporter";
			$aIconLinks[8]		= "Manage Joomfish[--1--]manage_joomfish.png[--1--]index2.php?option=com_joomfish";		
			$aIconLinks[9]		= "Manage Users[--1--]manage_user.png[--1--]index2.php?option=com_users";		
			$aIconLinks[10]		= "Manage Virtuemart Users[--1--]manage_user.png[--1--]index2.php?pshop_mode=admin&page=admin.user_list&option=com_virtuemart";		
			$aIconLinks[11]		= "Manage Deliveries[--1--]manage_deliveries.png[--1--]index2.php?option=com_deliver";
			$aIconLinks[12]		= "Postal code Warehouse Manager[--1--]postal_code_warehouse_manager.png[--1--]index2.php?option=com_assignorder";
			$aIconLinks[13]		= "Proflowers Order Manager[--1--]proflowers_order_manager.png[--1--]index2.php?option=com_proflower";
			$aIconLinks[14]		= "Phone Order Manager[--1--]phone_order_manager.png[--1--]index2.php?option=com_phoneorder";
			$aIconLinks[15]		= "User Group Manager[--1--]user_group.png[--1--]index2.php?option=com_newusergroup";
			$aIconLinks[16]		= "Change Password[--1--]private_profile.png[--1--]index2.php?option=com_privateprofile";
			$aIconLinks[17]		= "XML Order Manager[--1--]xml_icon.gif[--1--]index2.php?option=com_xmlorder";
			$aIconLinks[18]		= "Produce Order[--1--]produce_order.png[--1--]index2.php?option=com_ajaxorder&task=searchOrderForm";
			$aIconLinks[19]		= "Package Order[--1--]package_order.png[--1--]index2.php?option=com_ajaxorder&task=packageOrder";
			$aIconLinks[20]		= "Ship Order[--1--]ship_order.png[--1--]index2.php?option=com_ajaxorder&task=shipOrder";
			$aIconLinks[21]		= "Packaging Delivery[--1--]packaging_delivery.png[--1--]index2.php?option=com_ajaxorder&task=packagingDelivery";
			$aIconLinks[22]		= "Postal Code Manager[--1--]postal_code.png[--1--]index2.php?option=com_deliver&act=postal_code";
			$aIconLinks[23]		= "Driver Option Manager[--1--]driver_option.png[--1--]index2.php?option=com_deliver&act=driver_option";
			$aIconLinks[24]		= "Manage Virtuemart Tax[--1--]tax.png[--1--]index2.php?pshop_mode=admin&page=tax.tax_list&option=com_virtuemart";	
			$aIconLinks[25]		= "Meta Tag Configuration[--1--]metatag_cfg.png[--1--]index2.php?option=com_phpmagicmetatag";
			$aIconLinks[26]		= "SEO Links Manager[--1--]seo_link.png[--1--]index2.php?option=com_sef";
			$aIconLinks[27]		= "Location Manager[--1--]landding_page.png[--1--]index2.php?option=com_landingpages";	
			$aIconLinks[28]		= "Search Log Manager[--1--]search.png[--1--]index2.php?option=com_searchlog";	
			$aIconLinks[29]		= "Free Shipping Manager[--1--]freeshipping.png[--1--]index2.php?option=com_deliver&act=free_shipping";
			$aIconLinks[30]		= "Testimonial Manager[--1--]testimonial.png[--1--]index2.php?option=com_testimonial";

			$aAccessAreas =  array();
			$aAccessAreas['full_menus']						= "0;18;19;20;21;1;2;3;4;5;6;7;8;15;9;10;11;12;23;24;25;26;22;13;14;17;27;28;29; 30";		
			$aAccessAreas['manage_orders']					= "0";		
			$aAccessAreas['manage_products']				= "1";		
			$aAccessAreas['manage_coupons']					= "2";		
			$aAccessAreas['manage_content']					= "3;4;5;6";
			$aAccessAreas['view_reports']					= "7";
			$aAccessAreas['manage_joomfish']				= "8";		
			$aAccessAreas['add_user']						= "9;10";		
			$aAccessAreas['edit_user']						= "9;10";		
			$aAccessAreas['manage_deliveries']				= "11";
			$aAccessAreas['postal_code_warehouse_manager']	= "12";
			$aAccessAreas['proflowers_order_manager']		= "13";
			$aAccessAreas['phone_order_manager']			= "14";
			$aAccessAreas['produce_order']					= "18";
			$aAccessAreas['package_order']					= "19";
			$aAccessAreas['ship_order']						= "20";
			$aAccessAreas['packaging_delivery']				= "21";
			$aAccessAreas['postal_code']					= "22";
			$aAccessAreas['driver_option']					= "23";
			$aAccessAreas['tax_manager']					= "24";
			$aAccessAreas['metatag_cfg']					= "25";
			$aAccessAreas['seo_link']						= "26";
			$aAccessAreas['location_manager']				= "27";
			$aAccessAreas['searchlog']						= "28";
			$aAccessAreas['free_shipping']					= "29";
			$aAccessAreas['com_xmlorder']					= "17";
			$aAccessAreas['com_testimonial']				= "30";

			$aPulicAccess									= array(16);
			
			
		
			$query 		= "SELECT area_name FROM tbl_new_user_group AS NUG, tbl_mix_user_group AS MUG WHERE NUG.id = MUG.user_group_id AND MUG.user_id = $my->id";
			$database->setQuery( $query );
			$area_name 	= $database->loadResult();
			
			if( $area_name ) {
				$aAreaName	= explode( '[--1--]', $area_name);
				
				if( count($aAreaName) ) {
					$bBreak	= false;
					foreach($aAreaName as $itemArea ) {
						
						if( $itemArea == 'edit_user' && $bBreak ) continue;
						
						if( $itemArea == 'add_user' )  $bBreak = true;
						
						$aTemp	= explode( ';', $aAccessAreas[$itemArea] );
						
						if( count($aTemp) ) {
							foreach( $aTemp as $menuItem ) {
								$row = explode( '[--1--]', $aIconLinks[$menuItem] );								
								quickiconButton( $row[2], $row[1], $row[0], "", "", "");
							}	
						}
						
						if( $itemArea == 'full_menus' )  break;	
					}
				}
			}
			
			
			foreach($aPulicAccess as $Item ) {
				$row = explode( '[--1--]', $aIconLinks[$Item] );								
				quickiconButton( $row[2], $row[1], $row[0], "", "", "");
			}
			
			
		?>
	</div>
	<?php
}
?>