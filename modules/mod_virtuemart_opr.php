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
*----------------------------------------------------------------------
* This code creates a list of products that are related to a 
* particular product_id based on wheter or not the product was
* ordered (in the same order) at the same time.
* This is a "Customers who bought this item also bought" type of thing
*----------------------------------------------------------------------
*
* 2006-06-27 Version 0.1
* 0.1  - Initial version.
*
* 0.2 - bug fix plus a few new features
* Fixed typo in mod_virtuemart_opr.xml for table creation.
* Fixed div_id call.  Was mistakenly div_class
*
*/

if ( array_key_exists("product_id", $_REQUEST) ) {

global $mosConfig_absolute_path, $sess;

/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );

// Get the params
$build_interval = trim($params->get ('build_interval', 86400));
$order_history_limit = trim($params->get ('order_history_limit', 100));
$num_id_save = trim($params->get ('num_id_save', 25));
$num_id_display = trim($params->get ('num_id_display', 5));
$image_path = trim($params->get ('image_path', "/components/com_virtuemart/shop_image/product"));
$no_cat_text = trim($params->get ('no_cat_text', ""));
$pre_content_template = trim($params->get ('pre_content_template', " "));
$opr_content_template = trim($params->get ('content_template', "<img style=\"float: left;\" src=\"{image_path}/{thumb_image}\" /><a href=\"{link_url}\">{name}</a> <br style=\"clear: both;\" /> "));
$post_content_template = trim($params->get ('post_content_template', " "));

require_once(CLASSPATH.'ps_product.php');
$ps_product = new ps_product;

require_once(CLASSPATH.'ps_product_category.php');
$ps_product_category = new ps_product_category;

$db=new ps_DB;

$product_id = $_REQUEST["product_id"];


// REMOVE ME AFTER TESTING
//$q  = "delete from #__{vm}_order_product_relations ";
//$q .= "where product_id = '".$product_id."'";
//$db->query( $q );

// Grab the formated_results and last_build_time from the table.
// If we do not need to rebuild it, then just display it.
$q  = "select * from #__{vm}_order_product_relations ";
$q .= "where product_id = '".$product_id."'";
$db->query( $q );
$db->next_record();
if ( $db->f("product_id") >= 1 ) {
	$build_delta = ( date('U') - $db->f("last_build_time") );
	if ( $build_delta >= $build_interval ) {
		$create_new_list = 1;
	}else{
		$id_list = $db->f("related_products");
	}
}else{
	$q  = "insert into #__{vm}_order_product_relations ";
	$q .= "values(\"$product_id\", \"\", \"\")";
	$db->query( $q );
	$create_new_list = 1;
}


// This builds and loads items that have been ordered
// together.  It is only called if a new build is needed. 
if ( isset($create_new_list) ) {
	
	// Initialize the array
	$id_array[] = '';
	
	$id_list = '';
	
	
	// Grab the parent_id of this product (if it has one)
	$q  = "select product_parent_id, product_name FROM #__{vm}_product ";
	$q .= "WHERE product_id = '".$product_id."' ";
	$q .= "and product_publish = 'Y'";
	$db->query( $q );
	$db->next_record();
	$product_name = $db->f("product_name");
	$pid = $db->f("product_parent_id");
	if ( $pid == 0 ) {
		$pid = $product_id;
	}
	
	// Now grab all the product_id of the parent_id.
	$q  = "select product_id FROM #__{vm}_product ";
	$q .= "WHERE product_parent_id = '".$pid."' ";
	$q .= "and product_publish = 'Y'";
	$db->query( $q );
	$cnt = 1;
	$id_list = '';
	while( $db->next_record() ) {
		
		if ($cnt >= 2 ) {
			$id_list .= " OR ";
		}
		
		$id_list .= "prodcut_id = '".$db->f("product_id")."'";
		
		++$cnt;
		
	}
	
	// If id_list is empty it means we have no children
	if ( $id_list == '' ) {
		$id_list = "product_id = '".$product_id."'";
	}
	
	// Select the most recent orders that have $id_list 
	// as one of the line items.  Limit the selection to 
	// $order_history_limit
	$q  = "select DISTINCT(order_id) FROM #__{vm}_order_item ";
	$q .= "WHERE ".$id_list." ";
	$q .= "ORDER BY cdate ";
	$q .= "LIMIT " .$order_history_limit;
	$db->query( $q );
	
	while( $db->next_record() ) {
		
		// Cycle through each order and get all product_id that were
		// purchased with this order
		$qa  = "select product_id from #__{vm}_order_item ";
		$qa .= "WHERE order_id = '".$db->f("order_id")."' ";
		$qa .= "and product_id is not null ";
		$qa .= "and product_id <> \"$product_id\"";
		$dba = &new ps_DB;
		$dba->query( $qa );
		
		while( $dba->next_record() ) {
			// If this product has a parent_id, use the parent_id instead
			// of the product_id.
			$qb  = "select product_parent_id FROM #__{vm}_product ";
			$qb .= "WHERE product_id =  '".$dba->f("product_id")."'";
			$dbb = &new ps_DB;
			$dbb->query( $qb );
			$dbb->next_record();
			$ppid = $dbb->f("product_parent_id");
			if ( $ppid <= 0) { 
				$ppid = $dba->f("product_id");
			}
			
			// If the array exists, increment it by 1.
			// Otherwise, create it with an initial value of 1
			if (array_key_exists("$ppid", $id_array)) {
				++$id_array["$ppid"];
			}else{
				$id_array["$ppid"] = 1;
			}
		}
	}
		
	// Now we have an array built which contains the product_id
	// as the key and the number of times the product has been
	// ordered as the value. 
	
	// Sort the array in reverse order. This puts the most often
	// purchaed item at the top of the array.
	arsort($id_array);
	
	$cnt = 0;
	$id_list = "";
	
	// Cycle through the array and build $related_products	
	foreach( $id_array as $key => $value ) {
		
		if ( $key == 0 ) {
			continue;
		}
	
		$id_list .= $key."|";
		
		if ( ++$cnt >= $num_id_save ) { 
			break; 
		}
	}
	
	$id_list = substr($id_list,0,(strlen($id_list)-1));
	
	if ( $id_array <> '' ) {
		$dbu = &new ps_DB;
		$qu  =  "update #__{vm}_order_product_relations ";
		$qu .= "set related_products = '$id_list', ";
		$qu .= "last_build_time = '".date('U')."' ";
		$qu .= "where product_id = '$product_id'";
		$dbu->query( $qu );
	}
	
}
	
	
	
// echo "a = $id_list";


//$html .= "<a href=\"showallopr.html\" onClick=\"return clickreturnvalue()\" onMouseover=\"dropdownmenu(this, event, 'opr')\">Customers also bought...</a><div id=\"opr\" class=\"anylinkcss\">";

//$html .= "<a href=\"#\"><h3>Customers who bought $product_name  also bought these items</h3></a>";

$cntr = 0;
$opr_content_out = "";
$product_id_array =explode("\|", $id_list );

foreach ( $product_id_array as $key => $value ) {
	
	//echo "key=$key - value=$value<BR>\n";
		
	$product_id = $value;
	
	// Do a sanity check
	if ( strlen($product_id) < 1 ) {
		continue;
	}
	
	$cid = $ps_product_category->get_cid( $product_id );
	$mid = $ps_product->get_manufacturer_id($product_id);
	$flypage = $ps_product->get_flypage($product_id);
	
	
	$db->query("SELECT product_name, product_full_image, product_thumb_image product_s_desc FROM #__{vm}_product WHERE product_id = '$product_id'");
	$db->next_record();
	$name = $db->f("product_name");		
	$full_image = $db->f("product_full_image");		
	$thumb_image = $db->f("product_full_image");		
	$s_desc = $db->f("product_s_desc");
	
	
	//Build the link url
	$link_url  = "index.php?";
	$link_url .= "page=shop.product_details&";
	$link_url .= "flypage=$flypage&";
	$link_url .= "product_id=$product_id&";
	$link_url .= "category_id=$cid&";
	$link_url .= "manufacturer_id=$mid&";
	//$link_url .= "Itemid=$detail_list[3]&";
	$link_url .= "option=com_virtuemart";
	
	
	$opr_content = $opr_content_template;
	
	$opr_content = str_replace( "{full_image}", $full_image, $opr_content );
	$opr_content = str_replace( "{name}", $name, $opr_content );
	$opr_content = str_replace( "{s_desc}", $s_desc, $opr_content );
	$opr_content = str_replace( "{product_id}", $product_id, $opr_content );
	$opr_content = str_replace( "{category_id}", $cid, $opr_content );
	$opr_content = str_replace( "{manufacturer_id}", $mid, $opr_content );
	//$opr_content = str_replace( "{itemid}", $detail_list[3], $opr_content );
	$opr_content = str_replace( "{thumb_image}", $thumb_image, $opr_content );
	$opr_content = str_replace( "{link_url}", $link_url, $opr_content );
	$opr_content = str_replace( "{image_path}", $image_path, $opr_content );
	$opr_content = str_replace( "{cntr}", $cntr, $opr_content );
	
	$opr_content_out .= $opr_content;
	
	if ( ++$cntr >= $num_id_display ) { 
		break; 
	}
}
		
if ( $cntr >= 1 ) {
	echo $pre_content_template.$opr_content_out.$post_content_template;
	// Local mod
	if ( $cntr >= 5 ) {
		//echo "<div id=\"bs-last25\"><a href=\"http://joomla-test\" onClick=\"return clickreturnvalue()\" onMouseover=\"dropdownmenu(this, event, 'anylinkmenu1')\">show more</a></div>";
		//echo "<div id=\"anylinkmenu1\" class=\"anylinkcss\">";
		//echo "<h3>Products you have recently viewied</h3>";
		//echo $dd_list;
		//echo "</div>";
		
		
		
		
	}
	// End local mod
}

//echo "end of OPR";

}
?>





<!--OPR End-->


