<?php

/*  Micah's MySQL Database Class - Sample Usage 
 *  4.17.2005 - Micah Carrick, email@micahcarrick.com
 *
 *  This is a sample file on using my database class.  Hopefully it will provide
 *  you with enough information to use the class.  You should also look through
 *  the comments in the db.class.php file to get additional information about
 *  any specific function.
*/

require_once('../configuration.php');
require_once('db.class.php');
require_once("ftp.class.php");

$db = new db_class;
if (!$db->connect( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, true)) $db->print_last_error(false);


echo "Generate Product XML file:<br/><br/>";
$i		= 1;
$users 	= $db->select("SELECT * FROM tbl_partners WHERE published = 1");
while ( $row = $db->get_row($users, 'MYSQL_ASSOC') ) {
	
	$ftp = new ClsFTP( $row['ftp_username'], $row['ftp_password'], $row['domain_name'] );
	
	$sProductData	= getProductFiler($row['product_filter_id'], $row['product_updated_time']);
	
	
	if( $ftp->put_string( "/httpdocs/".date( "Ymd" , time())."_products.xml",  $sProductData ) ) {	
		echo "$i. <b>" . $row['partner_name'] . "</b>: successful.<br/>";
	}else {
		echo "$i. <b>" . $row['partner_name'] . "</b>: unsuccessful.<br/>";
	}

	$query = "	UPDATE tbl_partners SET product_updated_time = '".date( "Y-m-d H:i:s" , time())."' WHERE partner_name = '" . $row['partner_name'] . "'";
	$db->update_sql($query);
	
	//echo $db->row_count. "==========".$row['product_filter_id']."<br/><br/>";
			
	$ftp->close();
	$i++;
} 


function getProductFiler( $sID, $sTime ) {
	global $db, $mosConfig_absolute_path, $mosConfig_live_site;
	
	$aWhere	= array();	
	
	if( $sID ) $aWhere[] = "VM.product_id IN ($sID)";
	
	if( strtotime($sTime)  > 1259514000 ) $aWhere[] = "VM.mdate > ".strtotime($sTime);
	
	$sWhere	= count($aWhere) ? " WHERE " . implode( " AND ", $aWhere ) : "";
	
	
	$sProduct	='<?xml version="1.0" encoding="utf-8"?>
				<products version="1.0.0">';

	$sql		= " SELECT  VM.product_id, VM.product_sku, VM.product_name, VM.product_desc, VM.product_full_image,
							VMP.product_currency, VMP.product_price, VM.product_in_stock, VTR.tax_rate 
					FROM jos_vm_product AS VM LEFT JOIN jos_vm_product_price AS VMP ON VM.product_id = VMP.product_id
					LEFT JOIN  jos_vm_tax_rate AS VTR ON VM.product_tax_id = VTR.tax_rate_id
					LEFT JOIN  jos_vm_product_discount AS VPD ON VPD.discount_id = VM.product_discount_id $sWhere";
	$products 	= $db->select($sql);

//	echo $sql."==".$db->row_count."<br/><br/>";
	
	while ( $row = $db->get_row($products, 'MYSQL_ASSOC') ) {	
		if( is_file("$mosConfig_absolute_path/components/com_virtuemart/shop_image/product/".$row['product_full_image']) ) {
			$product_image	= "$mosConfig_live_site/components/com_virtuemart/shop_image/product/".$row['product_full_image'];
		}		
		
		if( $row['is_percent'] ) {
			$discount_type	= "percent";
		}else {
			$discount_type	= "total";
		}
		
		$product_image		= htmlentities( htmlspecialchars($product_image, ENT_QUOTES), ENT_QUOTES, 'UTF-8' );
		$product_sku		= htmlentities( htmlspecialchars(strip_tags($row['product_sku']), ENT_QUOTES), ENT_QUOTES, 'UTF-8' );
		$product_name		= htmlentities( htmlspecialchars(strip_tags($row['product_name'], "<br>"), ENT_QUOTES), ENT_QUOTES, 'UTF-8' );
		$product_desc		= htmlentities( htmlspecialchars(strip_tags($row['product_desc'], "<br>"), ENT_QUOTES), ENT_QUOTES, 'UTF-8' );
		$product_currency	= htmlentities( htmlspecialchars($row['product_currency'], ENT_QUOTES), ENT_QUOTES, 'UTF-8' );
		
		
		$sProduct	.= '<product>
							<id>'.$row['product_id'].'</id>
							<sku>'.$product_sku.'</sku>
							<name><![CDATA['.$product_name.']]></name>
							<desc><![CDATA['.$product_desc.']]></desc>
							<image><![CDATA['.$product_image.']]></image>				
							<tax>'.floatval($row['tax_rate']).'</tax>
							<discount>'.floatval($row['amount']).'</discount>				
							<discount_type>'.$discount_type.'</discount_type>				
							<price>'.floatval($row['product_price']).'</price>				
							<curency>'.$product_currency.'</curency>				
						</product>';
	}    
	$sProduct	.= '</products>';
	
	return $sProduct;
}