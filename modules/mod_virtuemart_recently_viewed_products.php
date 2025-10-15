<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/*
* Order Product Relations module for VirtueMart
* @version $Id: mod_virtuemart_opr.php,v 1.4 2005/10/19 09:01:09 codename-matrix Exp $
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) Marty Tennison (marty@freeison.com)
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*
* 2006-07-20 Version 0.1
* 0.1  - Initial version....
*
* 2006-07-21 Version 0.2
* Added {link_url} and {image_path} to variables
* Fixed a typo that kept the thumb from displaying (Thanks CiPHeR)
*
***********************************************************************
* DEVELOPER NOTES:
* The primary concern with this module is performance.  Since the data
* is read on each page load we need to keep DB querys to a minimum.
* There was two way to approach this.  
* 1) Save only the product_id in an array in a cookie. 
* 2) Save all product information in an array in a cookie.
* Each solution has it drawbacks.  Solution (1) suffers from
* having to query the database on each page for item information.
* Option (2) suffers from the 4K cookie limit. 
* I chose option 2 because it improves performance.  this may change.
***********************************************************************
*/
global $mosConfig_absolute_path, $sess, $dd_list;

/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );

require_once(CLASSPATH.'ps_product.php');
$ps_product = new ps_product;

require_once(CLASSPATH.'ps_product_category.php');
$ps_product_category = new ps_product_category;

$db=new ps_DB;

// Get the params
$cookie_expire = trim($params->get ('cookie_expire', 31536000));
$num_prod_display = trim($params->get ('num_prod_display', 9));
$num_prod_remember = trim($params->get ('num_prod_remember', 25));
$no_products_msg = trim($params->get ('no_products_msg', "No products viewed"));
$image_path = trim($params->get ('image_path', "/components/com_virtuemart/shop_image/product"));
$pre_content_template = trim($params->get ('pre_content_template', " "));
$rvp_content_template = trim($params->get ('content_template', "<img style=\"float: left;\" src=\"{image_path}/{thumb_image}\" /><a href=\"{link_url}\">{name}</a> <br style=\"clear: both;\" /> "));
$post_content_template = trim($params->get ('post_content_template', " "));

// Get the product_id (if any)
$product_id = intval( mosgetparam($_REQUEST, "product_id", null) );

$flypage = $ps_product->get_flypage($product_id);

// Get the cookie contents.
$rvp_list = mosGetParam( $_COOKIE, "rvp", '' );

//echo "cookie = $rvp_list<br>";

// If we have a product_id then add it to the top of list
// Otherwise just display the cookie contents
if ( $product_id ) {
	
	// If the product has a parent, use it
	$q  = "select product_parent_id FROM #__{vm}_product ";
	$q .= "WHERE product_id = '".$product_id."' ";
	$q .= "and product_publish = 'Y'";
	$db->query( $q );
	$db->next_record();
	$pid = $db->f("product_parent_id");
	if ( $pid != 0 ) {
		$product_id = $pid;
	}
	
	$first_id =explode(":", $rvp_list);
	
	if ( $product_id != $first_id[0]) {
		
		$new_list = "$product_id:";
		
		// cycle through the list and if we run accross this product
		// then do not load it again.  This avoids duplicates
		
		$rvp_list =explode(":", $rvp_list );
		$cntr = 0;
			
		foreach( $rvp_list as $key=>$value) {
			
			// Continue if we do not have any data
			if ( $product_id == $value || ! $value ) {
				continue;
			}
				
			// Stop if we are past the limit
			if ( ++$cntr >= $num_prod_remember ) {
				break;
			}
			
			// Add it to the list
			$new_list .= "$value:";
		
		}
		
		$rvp_list = $new_list;
		
		setcookie('rvp', $new_list, time()+$cookie_expire);
		// setcookie('rvp', "", time()+$cookie_expire);
		
	}
	
}



// Show the list

$rvp_content_out = " ";
$dd_list = "";
$cntr = 1;

$rvp_list =explode(":", $rvp_list );

foreach( $rvp_list as $key=>$value) {
	
	$rvp_id = $value;
	
	$cid = $ps_product_category->get_cid( $rvp_id );
	
	// Create another array out of the array value but split this
	// one at the :
	//$detail_list =explode(":", $value);
	
	// Do a sanity check
	if ( strlen($rvp_id) < 1 ) {
		continue;
	}
	
	//echo "rvp_id=$rvp_id<br>";
	
	
	// Grab the information from the DB
	$q  = "select product_full_image, product_thumb_image, product_name, product_s_desc FROM #__{vm}_product ";
	$q .= "WHERE product_id = '".$rvp_id."' ";
	$q .= "and product_publish = 'Y'";
	$db->query( $q );
	$db->next_record();
	$full_image = $db->f("product_full_image");
	$thumb_image = $db->f("product_thumb_image");
	$name = $db->f("product_name");
	$s_desc = $db->f("product_s_desc");
	
	
	//echo "stuff=$full_image  $thumb_image  $name<br>";
	
	//Build the link url
	$link_url  = "index.php?";
	$link_url .= "page=shop.product_details&";
	$link_url .= "flypage=".$ps_product->get_flypage($rvp_id)."&";
	$link_url .= "product_id=$rvp_id&";
	$link_url .= "category_id=$cid&";
	//$link_url .= "manufacturer_id=$detail_list[2]&";
	$link_url .= "option=com_virtuemart";
	//$link_url .= "Itemid=$detail_list[3]";
	
	$rvp_content = $rvp_content_template;
	
	$rvp_content = str_replace( "{full_image}", $full_image, $rvp_content );
	$rvp_content = str_replace( "{name}", $name, $rvp_content );
	$rvp_content = str_replace( "{s_desc}", $s_desc, $rvp_content );
	$rvp_content = str_replace( "{product_id}", $rvp_id, $rvp_content );
	$rvp_content = str_replace( "{category_id}", $cid, $rvp_content );
	//$rvp_content = str_replace( "{manufacturer_id}", $detail_list[2], $rvp_content );
	//$rvp_content = str_replace( "{itemid}", $detail_list[3], $rvp_content );
	$rvp_content = str_replace( "{thumb_image}", $thumb_image, $rvp_content );
	$rvp_content = str_replace( "{link_url}", $link_url, $rvp_content );
	$rvp_content = str_replace( "{image_path}", $image_path, $rvp_content );
	$rvp_content = str_replace( "{cntr}", $cntr, $rvp_content );
	
	// Local mod
 	//$dd_list .= "<a onMouseover=\"ddrivetip('<img  width=68 height=68 id=ttimg src=phpThumb/phpThumb.php?src=$image_path/$full_image&w=68&h=68&iar=1 /> $name', 155)\" onMouseout=\"hideddrivetip()\" href=\"$link_url\">$name</a>";
	
	$dd_list .= "<li><a href=\'$link_url\'>$name</a></li>";
	// End local mod
	
	// Stop if we are past the limit
	if ( $cntr > $num_prod_display ) {
		continue;
	}
	
	// Do not diplay the item we are on
	if ( $product_id == $rvp_id ) {
		continue;
	}
	
	$rvp_content_out .= $rvp_content;
	
	++$cntr;
	
}

if ( $cntr >= 2 ) {
	echo $pre_content_template.$rvp_content_out;
	// Local mod
	if ( $cntr >= 25 ) {
		//echo "<div id=\"rvp-last25\"><a href=\"http://joomla-test\" onClick=\"return clickreturnvalue()\" onMouseover=\"dropdownmenu(this, event, 'anylinkmenu1')\">show more</a></div>";
		//echo "<div id=\"anylinkmenu1\" class=\"anylinkcss\">";
		//echo "<h3>Products you have recently viewied</h3>";
		//echo $dd_list;
		//echo "</div>";
		
		
		echo "<br /><a style=\"text-decoration: underline;\" href=\"images/blank.png\" onclick=\"return wrapContent(event, this.href, 0, 0, '<h3>Products you have recently viewed</h3><ul>".$dd_list."</ul></div>', 300, -300)\">show more</a>";
		
		
	}
	echo $post_content_template;
	
	
	// End local mod
}else{
	echo $pre_content_template."no products viewed".$post_content_template;
}


// This content uses phpThumb.php to generate the thumbnails.
// <img width="35" height="35" style="float: left;" src="phpThumb/phpThumb.php?src={image_path}/{full_image}&w=35$h=35&iar=1" /><a href="{link_url}">{name}</a> <br style="clear: both;" />

?>


<!--Recently Viewed Products Module End-->

