<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/*
* Best Sellers module for VirtueMart
* @version $Id: mod_virtuemart_best_sellers.php,v 1.4 2005/10/19 09:01:09 codename-matrix Exp $
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
*----------------------------------------------------------------------
* This code creates a list of products that are related to a 
* particular category_id based on wheter or not the product was
* ordered (in the same order) at the same time.
* This is a "Customers who bought this item also bought" type of thing
*----------------------------------------------------------------------
*
* 2006-06-27 Version 0.1
* 0.1  - Initial version.
*
*/

global $mosConfig_absolute_path, $sess;

/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );

// Get the params
$build_interval = trim($params->get ('build_interval', 86400));
$order_history_limit = trim($params->get ('order_history_limit', 100));

$num_prod_display = trim($params->get ('num_prod_display', 9));
$num_prod_remember = trim($params->get ('num_prod_remember', 25));

$len_product_name = trim($params->get ('len_product_name', 0));
$len_product_s_desc = trim($params->get ('len_product_s_desc', 0));


$image_path = trim($params->get ('image_path', "/components/com_virtuemart/shop_image/product"));
$no_cat_text = trim($params->get ('no_cat_text', ""));
$pre_content_template = trim($params->get ('pre_content_template', " "));
$bs_content_template = trim($params->get ('content_template', "<img style=\"float: left;\" src=\"{image_path}/{thumb_image}\" /><a href=\"{link_url}\">{name}</a> <br style=\"clear: both;\" /> "));
$post_content_template = trim($params->get ('post_content_template', " "));

require_once(CLASSPATH.'ps_product.php');
$ps_product = new ps_product;

require_once(CLASSPATH.'ps_product_category.php');
$ps_product_category = new ps_product_category;

$db=new ps_DB;

if ( array_key_exists("category_id", $_REQUEST) ) {
	$category_id = $_REQUEST["category_id"];
	$q  = "select category_name from #__{vm}_category ";
	$q .= "where category_id = '".$category_id."'";
	$db->query( $q );
	$db->next_record();
	$category_name = $db->f("category_name");
}else{
	$category_id = "0";
	$category_name = $no_cat_text;
}


// REMOVE ME AFTER TESTING
//$q  = "delete from #__{vm}_best_sellers ";
//$q .= "where category_id = '".$category_id."'";
//$db->query( $q );


// Grab the last_build_time from the table.
// If we do not need to rebuild it, then just display it.
$q  = "select * from #__{vm}_best_sellers ";
$q .= "where category_id = '".$category_id."'";
$db->query( $q );
$db->next_record();
if ( $db->f("last_build_time") ) {
	
	$build_delta = ( date('U') - $db->f("last_build_time") );
	
	if ( $build_delta >= $build_interval ) {
		$create_new_list = 1;
	}else{
		$product_id_array = $db->f("product_id_array");
	}
	
}else{

	$q  = "insert into #__{vm}_best_sellers ";
	$q .= "values(\"$category_id\", \"\", \"\")";
	$db->query( $q );
	$create_new_list = 1;
	
}

// This builds and loads the best sellers for each category.
// If no category is defined it builds it for all categories.
// It is only called if a new build is needed. 
if ( isset($create_new_list) ) {
	
	// Find out if this category has child categories.  If it does
	// then decend to the next level until no more child categories
	// are found. 
	
	$cat_id_array[0] = $category_id;
	$cat_while_array[0] = $category_id;
	
	while ( array_key_exists("0", $cat_while_array) ) {
			
		$q  = "SELECT category_child_id from #__{vm}_category_xref WHERE category_parent_id = \"$cat_while_array[0]\"";
		$db->query( $q );
		
		array_shift($cat_while_array);
		
		while( $db->next_record() ) {
			array_push($cat_while_array, $db->f("category_child_id"));
			array_push($cat_id_array, $db->f("category_child_id"));
		}
	}

	// Now we have an array with this category and all 
	// (if any) child categories.  
	
	
	// Initialize the array
	$item_array = array();
	
	foreach ( $cat_id_array as $key => $value ) {
	
		// SELECT #__{vm}_order_item.product_id, #__{vm}_product_category_xref.category_id FROM #__{vm}_order_item, #__{vm}_product_category_xref WHERE #__{vm}_order_item.product_id = #__{vm}_product_category_xref.product_id AND #__{vm}_order_item.cdate >= 9 AND #__{vm}_product_category_xref.category_id = 47;
		
		
		$q  = "SELECT #__{vm}_order_item.product_id as product_id, #__{vm}_product_category_xref.category_id ";
		$q .= "FROM #__{vm}_order_item, #__{vm}_product_category_xref ";
		$q .= "WHERE #__{vm}_order_item.product_id = #__{vm}_product_category_xref.product_id ";
		$q .= "AND #__{vm}_order_item.cdate >= 9 "; // FIXME - variable
		$q .= "AND #__{vm}_product_category_xref.category_id = '$value' ";
		$q .= "ORDER BY #__{vm}_order_item.cdate DESC LIMIT $limit";
		$db->query( $q );
		while( $db->next_record() ) {
		
			$product_id = $db->f("product_id");
			
			
			// Grab the parent_id of this product (if it has one) and
			// use that as the product_id. If no parent_id exists
			// use product_id.
			$dba = &new ps_DB;
			$qa  = "select product_parent_id FROM #__{vm}_product ";
			$qa .= "WHERE product_id = '$product_id' ";
			$qa .= "and product_publish = 'Y'";
			$dba->query( $qa );
			$dba->next_record();
			$pid = $dba->f("product_parent_id");
			if ( $pid != 0 ) {
				$product_id = $pid;
			}
			
			// If this item is not published, skip it
			$dbb = &new ps_DB;
			$qb  = "select product_publish FROM #__{vm}_product ";
			$qb .= "WHERE product_id = '$product_id' ";
			$dbb->query( $qb );
			$dbb->next_record();
			$publish = $dbb->f("product_publish");
			if ( $publish != "Y" ) {
				continue;
			}
			
			
			// If the array exists, increment it by 1.
			// Otherwise, create it with an initial value of 1
			if (array_key_exists("$product_id", $item_array)) {
				//echo "yep ";
				++$item_array["$product_id"];
			}else{
				//echo "nope ";
				$item_array["$product_id"] = 1;
			}
		
		}
		
	}

	
	
	// Now we have an array built which contains the product_id
	// as the key and the number of times the product has been
	// ordered as the value. 
	
	// Sort the array in reverse order. This puts the most often
	// purchaed item at the top of the array.
	arsort($item_array);
	
	$product_id_array = '';
	$cnt = 0;
	
	// Cycle through the array and build the $product_id_array
	foreach ( $item_array as $key => $value ) {
		
		if ( $key == 0 ) {
			continue;
		}
		
		$product_id_array .= $key . "|";
		
		if ( ++$cnt >= $num_prod_remember ) { 
			break; 
		}
	}
	
	// Chop off the last pipe
	$product_id_array = substr($product_id_array,0,(strlen($product_id_array)-1));
	
	//echo "pid = $product_id_array";
	
	if ( $product_id_array <> '' ) {
		
		// Update the DB.
		$dbu = &new ps_DB;
		$qu  = "update #__{vm}_best_sellers ";
		$qu .= "set product_id_array = '$product_id_array', ";
		$qu .= "last_build_time = '".date('U')."' ";
		$qu .= "where category_id = '$category_id'";
		$dbu->query( $qu );
		
	}
	
}
	
$pre_content_template = str_replace( "{category_name}", $category_name, $pre_content_template );

// Show the list

$bs_content_out = " ";
$dd_list = "";
$cntr = 1;

$product_id_array =explode("\|", $product_id_array );

foreach( $product_id_array as $key=>$value) {
	
	$product_id = $value;
	
	$cid = $ps_product_category->get_cid( $product_id );
	$mid = $ps_product->get_manufacturer_id($product_id);
	$flypage = $ps_product->get_flypage($product_id);
	
	
	//echo "pid = $product_id";


	// Not sure about this yet
	//if ( $category_name ) {
	//	$html .= "<h3>Best Selling Items in $category_name</h3>";
	//}else{
	//	$html .= "<h3>Best Selling Items at Sediva.com</h3>"; //FIXME - use variable
	//}

	
	// Create another array out of the array value but split this
	// one at the :
	//$detail_list =explode(":", $value);
	
	// Do a sanity check
	if ( strlen($product_id) < 1 ) {
		continue;
	}
	
	//echo "product_id=$product_id<br>";
	
	
	// Grab the information from the DB
	$q  = "select product_full_image, product_thumb_image, product_name, product_s_desc FROM #__{vm}_product ";
	$q .= "WHERE product_id = '".$product_id."' ";
	$q .= "and product_publish = 'Y'";
	$db->query( $q );
	$db->next_record();
	$product_full_image = $db->f("product_full_image");
	$product_thumb_image = $db->f("product_thumb_image");
	$product_name = $db->f("product_name");
	$product_s_desc = $db->f("product_s_desc");
	
	if ( $len_product_name >= 1 ) {
		$product_name = substr($product_name, 0, $len_product_name);
	}
	if ( $len_product_s_desc >= 1 ) {
		$product_s_desc = substr($product_s_desc, 0, $len_product_s_desc);
	}
	
	$product_name = htmlentities($product_name);
	$product_s_desc = htmlentities($product_s_desc);
	$product_price = $ps_product->show_price($product_id);
	
	//echo "stuff=$full_image  $thumb_image  $name<br>";
	
	//Build the link url
	$product_flypage  = "index.php?";
	$product_flypage .= "page=shop.product_details&";
	$product_flypage .= "flypage=$flypage&";
	$product_flypage .= "product_id=$product_id&";
	$product_flypage .= "category_id=$cid&";
	$product_flypage .= "manufacturer_id=$mid&";
	//$product_flypage .= "Itemid=$detail_list[3]&";
	$product_flypage .= "option=com_virtuemart";
	
	
	$bs_content = $bs_content_template;
	
	$bs_content = str_replace( "{product_full_image}", $product_full_image, $bs_content );
	$bs_content = str_replace( "{product_name}", $product_name, $bs_content );
	$bs_content = str_replace( "{product_s_desc}", $product_s_desc, $bs_content );
	$bs_content = str_replace( "{product_id}", $product_id, $bs_content );
	$bs_content = str_replace( "{category_id}", $cid, $bs_content );
	$bs_content = str_replace( "{manufacturer_id}", $mid, $bs_content );
	//$bs_content = str_replace( "{itemid}", $detail_list[3], $bs_content );
	$bs_content = str_replace( "{product_thumb_image}", $product_thumb_image, $bs_content );
	$bs_content = str_replace( "{product_flypage}", $product_flypage, $bs_content );
	$bs_content = str_replace( "{image_path}", $image_path, $bs_content );
	$bs_content = str_replace( "{cntr}", $cntr, $bs_content );
	$bs_content = str_replace( "{category_name}", $category_name, $bs_content );
	$bs_content = str_replace( "{product_price}", $product_price, $bs_content );
	
	// Local mod
	// $dd_list .= "<a onMouseover=\"ddrivetip('<img  width=68 height=68 id=ttimg src=phpThumb/phpThumb.php?src=$image_path/$full_image&w=68&h=68&iar=1 /> $name', 155)\" onMouseout=\"hideddrivetip()\" href=\"$link_url\">$name</a>";
	
	$dd_list .= "<li><a href=\'$link_url\'>$name</a></li>";
	// End local mod
	
	$bs_content_out .= $bs_content;
	
	// Stop if we are past the limit
	if ( ++$cntr > $num_prod_display ) {
		break;
	}
	
}

if ( $cntr >= 1 ) {
	echo $pre_content_template.$bs_content_out.$post_content_template;
	// Local mod
	if ( $cntr >= 5 ) {
		//echo "<div id=\"bs-last25\"><a href=\"http://joomla-test\" onClick=\"return clickreturnvalue()\" onMouseover=\"dropdownmenu(this, event, 'anylinkmenu1')\">show more</a></div>";
		//echo "<div id=\"anylinkmenu1\" class=\"anylinkcss\">";
		//echo "<h3>Products you have recently viewied</h3>";
		//echo $dd_list;
		//echo "</div>";
		
		
		
		
	}
	// End local mod
}else{
	echo $pre_content_template."no best sellers for this category".$post_content_template;
}


// This content uses phpThumb.php to generate the thumbnails.
// <img width="35" height="35" style="float: left;" src="phpThumb/phpThumb.php?src={image_path}/{full_image}&w=35$h=35&iar=1" /><a href="{link_url}">{name}</a> <br style="clear: both;" />







?>


<!--Best Sellers End-->


