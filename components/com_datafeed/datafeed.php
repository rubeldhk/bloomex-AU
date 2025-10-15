<?php
/**
* @version $Id: admin.Category.php 10002 2008-02-08 10:56:57Z willebil $
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

require_once( $mainframe->getPath( 'front_html' ) );

switch ($task) {
	case 'all_products':
	default:
		FeedProudcts( );
		break;
}


function FeedProudcts() {
	global $database, $iso_client_lang, $mosConfig_live_site, $mosConfig_absolute_path;
	
	$query				= "   SELECT VM.product_id, VM.product_full_image, VM.product_name, VM.product_sku, VM.product_sku, VM.product_desc, VMP.product_price, VMP.product_currency, VM.product_in_stock
							FROM #__vm_product AS VM LEFT JOIN #__vm_product_price AS VMP ON VM.product_id = VMP.product_id 
							WHERE VM.product_publish = 'Y' 
							ORDER BY VM.product_name ASC";
	$database->setQuery($query);
	$rows				= $database->loadObjectList();	
	
	$sString	= "";
	if( count($rows) ) {
		foreach( $rows AS $p ) {			
			$query				= "   SELECT C.category_name, C.category_id
									FROM #__vm_category AS C INNER JOIN #__vm_product_category_xref AS PCX ON C.category_id = PCX.category_id  
									WHERE PCX.product_id = " . $p->product_id;
			$database->setQuery($query);
			$oCategory			=  $database->loadObjectList();	
			if( !empty($oCategory[0]->category_id)) {
				$category_name		=  clearData($oCategory[0]->category_name);	
				$category_id			=  $oCategory[0]->category_id;	
			}else {
				$category_name		=  "";	
				$category_id			=  "";	
			}
			
			
			//$sStock	= (!empty($p->product_in_stock) && $p->product_in_stock > 0 ) ? "In Stock" : "Out Stock";
			$sRoot	=  str_replace( array("dev1.", "stage1."), array("", ""), $mosConfig_live_site );
			$sStock	= "In Stock";
			$nPrice	= number_format( $p->product_price, 4, "." , "" );
			$sLink	= $sRoot . htmlspecialchars("/index.php?page=shop.product_details&category_id=$category_id&flypage=shop.flypage&product_id=".$p->product_id."&option=com_virtuemart&Itemid=1", ENT_QUOTES);
			$sLink	= str_replace( array("dev1.", "stage1."), array("", ""), $sLink );
			
			if( !empty($p->product_full_image) && is_file("$mosConfig_absolute_path/components/com_virtuemart/shop_image/product/".$p->product_full_image) ) {
				$sImage	= "$sRoot/components/com_virtuemart/shop_image/product/".$p->product_full_image;
			}else {
				$sImage	= "";
			}
			
			
			$sString	.= "\t<item>\n\t\t<productname><![CDATA[". clearData(strip_tags($p->product_name)) ."]]></productname>\n\t\t<url><![CDATA[$sLink]]></url>\n\t\t<imageurl><![CDATA[$sImage]]></imageurl>\n\t\t<price>$nPrice</price>\n\t\t<model><![CDATA[".$p->product_sku."]]></model>\n\t\t<productid>".$p->product_id."</productid>\n\t\t<manufacturer><![CDATA[Bloomex]]></manufacturer>\n\t\t<stock>$sStock</stock>\n\t\t<category><![CDATA[$category_name]]></category>\n\t\t<description><![CDATA[". clearData(strip_tags($p->product_desc)) ."]]></description>\n\t</item>\n";
		}
	}
	
	if( !empty($sString) ) 	$sString	= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<items>\n$sString</items>";
	
	
	header("Content-type: text/xml");
	header("Content-Disposition: attachment; filename=products.xml");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo $sString;
	
	exit(0);
}

function clearData ($sString) {
	if(!empty($sString)) {
		$sString	= trim(strip_tags(nl2br($sString)));
		$healthy 	= array(",", "\r\n", "\n", "  " );
		$yummy = array(" ", "",  "", " " );
		
		$sString 	= str_replace($healthy, $yummy, $sString);	
		
		//$sString	=  htmlspecialchars_decode( $sString, ENT_NOQUOTES);	
		return $sString;	
	}
}
?>
