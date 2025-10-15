<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* This is the Main Product Listing File!
*
* @version $Id: shop.browse.php,v 1.10.2.10 2006/04/23 19:40:07 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
mm_showMyFileName( __FILE__ );

global $manufacturer_id,$keyword1,$keyword2,$search_category,$DescOrderBy,$search_limiter;
global $search_op,$orderby,$product_type_id, $default, $vmInputFilter, $VM_BROWSE_ORDERBY_FIELDS, $mm_action_url, $my;

//echo "<h3>".$VM_LANG->_PHPSHOP_BROWSE_LBL."</h3>\n";
global $mm_action_url, $mosConfig_absolute_path, $my, $VM_LANG, $mosConfig_lang, $mosConfig_live_site;
?>


<script type="text/javascript">
	sVM_EDIT							= "<?php echo $VM_LANG->_VM_EDIT; ?>";
	sVM_DELETE 							= "<?php echo $VM_LANG->_VM_DELETE; ?>";
	sVM_DELETING						= "<?php echo $VM_LANG->_VM_DELETING; ?>";
	sVM_UPDATING						= "<?php echo $VM_LANG->_VM_UPDATING; ?>";
	sVM_ADD_ADDRESS						= "<?php echo $VM_LANG->_VM_ADD_ADDRESS; ?>";
	sVM_UPDATE_ADDRESS					= "<?php echo $VM_LANG->_VM_UPDATE_ADDRESS; ?>";
	
	sVM_ADD_PRODUCT_SUCCESSFUL			= "<?php echo $VM_LANG->_VM_ADD_PRODUCT_SUCCESSFUL; ?>";
	sVM_ADD_PRODUCT_UNSUCCESSFUL		= "<?php echo $VM_LANG->_VM_ADD_PRODUCT_UNSUCCESSFUL; ?>";
	sVM_CONFIRM_DELETE					= "<?php echo $VM_LANG->_VM_CONFIRM_DELETE; ?>";
	sVM_DELETE_SUCCESSFUL				= "<?php echo $VM_LANG->_VM_DELETE_SUCCESSFUL; ?>";
	sVM_DELETE_UNSUCCESSFUL				= "<?php echo $VM_LANG->_VM_DELETE_UNSUCCESSFUL; ?>";
	sVM_CONFIRM_QUANTITY				= "<?php echo $VM_LANG->_VM_CONFIRM_QUANTITY; ?>";
	sVM_UPDATE_CART_ITEM_SUCCESSFUL		= "<?php echo $VM_LANG->_VM_UPDATE_CART_ITEM_SUCCESSFUL; ?>";
	sVM_UPDATE_CART_ITEM_UNSUCCESSFUL	= "<?php echo $VM_LANG->_VM_UPDATE_CART_ITEM_UNSUCCESSFUL; ?>";
	
	sVM_CONFIRM_FIRST_NAME				= "<?php echo $VM_LANG->_VM_CONFIRM_FIRST_NAME; ?>";
	sVM_CONFIRM_LAST_NAME				= "<?php echo $VM_LANG->_VM_CONFIRM_LAST_NAME; ?>";
	sVM_CONFIRM_ADDRESS					= "<?php echo $VM_LANG->_VM_CONFIRM_ADDRESS; ?>";
	sVM_CONFIRM_CITY					= "<?php echo $VM_LANG->_VM_CONFIRM_CITY; ?>";
	sVM_CONFIRM_ZIP_CODE				= "<?php echo $VM_LANG->_VM_CONFIRM_ZIP_CODE; ?>";
	sVM_CONFIRM_VALID_ZIP_CODE			= "<?php echo $VM_LANG->_VM_CONFIRM_VALID_ZIP_CODE; ?>";
	sVM_CONFIRM_COUNTRY					= "<?php echo $VM_LANG->_VM_CONFIRM_COUNTRY; ?>";
	sVM_CONFIRM_STATE					= "<?php echo $VM_LANG->_VM_CONFIRM_STATE; ?>";
	sVM_CONFIRM_PHONE_NUMBER			= "<?php echo $VM_LANG->_VM_CONFIRM_PHONE_NUMBER; ?>";
	sVM_CONFIRM_EMAIL					= "<?php echo $VM_LANG->_VM_CONFIRM_EMAIL; ?>";
	sVM_CONFIRM_ADD_NICKNAME			= "<?php echo $VM_LANG->_VM_CONFIRM_ADD_NICKNAME; ?>";
	
	sVM_DELETING_DELIVER_INFO				= "<?php echo $VM_LANG->_VM_DELETING_DELIVER_INFO; ?>";
	sVM_DELETE_DELIVER_INFO_SUCCESSFUL		= "<?php echo $VM_LANG->_VM_DELETE_DELIVER_INFO_SUCCESSFUL; ?>";
	sVM_DELETE_DELIVER_INFO_UNSUCCESSFUL	= "<?php echo $VM_LANG->_VM_DELETE_DELIVER_INFO_UNSUCCESSFUL; ?>";
	sVM_UPDATING_DELIVER_INFO				= "<?php echo $VM_LANG->_VM_UPDATING_DELIVER_INFO; ?>";
	sVM_UPDATE_DELIVER_INFO_SUCCESSFUL		= "<?php echo $VM_LANG->_VM_UPDATE_DELIVER_INFO_SUCCESSFUL; ?>";
	sVM_UPDATE_DELIVER_INFO_UNSUCCESSFUL	= "<?php echo $VM_LANG->_VM_UPDATE_DELIVER_INFO_UNSUCCESSFUL; ?>";
	sVM_ADD_DELIVER_INFO_SUCCESSFUL			= "<?php echo $VM_LANG->_VM_ADD_DELIVER_INFO_SUCCESSFUL; ?>";
	sVM_ADD_DELIVER_INFO_UNSUCCESSFUL		= "<?php echo $VM_LANG->_VM_ADD_DELIVER_INFO_UNSUCCESSFUL; ?>";
	sVM_LOAD_DELIVER_INFO_FORM_UNSUCCESSFUL	= "<?php echo $VM_LANG->_VM_LOAD_DELIVER_INFO_FORM_UNSUCCESSFUL; ?>";
	
	sVM_UPDATING_BILLING_INFO				= "<?php echo $VM_LANG->_VM_UPDATING_BILLING_INFO; ?>";
	sVM_UPDATE_BILLING_INFO_SUCCESSFUL		= "<?php echo $VM_LANG->_VM_UPDATE_BILLING_INFO_SUCCESSFUL; ?>";
	sVM_UPDATE_BILLING_INFO_UNSUCCESSFUL	= "<?php echo $VM_LANG->_VM_UPDATE_BILLING_INFO_UNSUCCESSFUL; ?>";

</script>
<?php

$db_browse = new ps_DB;
$dbp = new ps_DB;

/* load important class files */
require_once (CLASSPATH."ps_product.php");
$ps_product = new ps_product;
require_once (CLASSPATH."ps_product_category.php");
$ps_product_category = new ps_product_category;
require_once (CLASSPATH."ps_reviews.php");

 function comparePrice($a, $b)  {
	 if ( floatval($a['product_price']) == floatval($b['product_price']) ) {
	        return 0;
	}
	return ( floatval($a['product_price']) > floatval($b['product_price']) ) ? 1 : -1;
}

?>

<script type="text/javascript">
	sSecurityUrl	= "<?php echo ( SECUREURL != "" ? SECUREURL : $mm_action_url );?>";
	bMember			= <?php echo $my->id; ?>;
</script>

<?php
$Itemid = mosgetparam($_REQUEST, "Itemid", null);
$keyword1 = $vmInputFilter->safeSQL( urldecode(mosGetParam( $_REQUEST, 'keyword1', null )));
$keyword2 = $vmInputFilter->safeSQL( urldecode(mosGetParam( $_REQUEST, 'keyword2', null )));
// possible values: [ASC|DESC]
$DescOrderBy = $vmInputFilter->safeSQL( mosGetParam( $_REQUEST, 'DescOrderBy', "ASC" ));
$search_limiter= $vmInputFilter->safeSQL( mosGetParam( $_REQUEST, 'search_limiter', null ));
$search_op= $vmInputFilter->safeSQL( mosGetParam( $_REQUEST, 'search_op', null ));
// possible values: 
// product_name, product_price, product_sku, product_cdate (=latest additions)
$orderby = $vmInputFilter->safeSQL( mosGetParam( $_REQUEST, 'orderby', VM_BROWSE_ORDERBY_FIELD ));

if (empty($category_id)) $category_id = $search_category;

$default['category_flypage'] = FLYPAGE;

//SET PAGE NUMBER
$limit = 50;

if (!empty($category_id) ) {
	/**
    * CATEGORY DESCRIPTION
    */
	$desc =  $ps_product_category->get_description($category_id);
	/* Prepend Product Short Description Meta Tag "description" when applicable */
	if( @$_REQUEST['output'] != "pdf") {
		$mainframe->prependMetaTag( "description", substr(strip_tags($desc ), 0, 255) );
	}
	if( trim(str_replace( "<br />", "" , $desc)) != "" ) {
		echo '<div style="width:100%;float:left;">';
		echo "<h3>".$desc."</h3>";
		echo '</div>
             <br style="clear:both;" />';
        }
	
        /**
    * PATHWAY - Navigation List
    */
	echo '<div style="text-align:left;">';
	$nav_list = $ps_product_category->get_navigation_list($category_id);

	if( @$_REQUEST['output'] != "pdf") {
		$mainframe->appendPathWay( $nav_list );
	}
	else {
		echo "<strong>".$nav_list ."</strong><br />";
	}
	$child_list = $ps_product_category->get_child_list($category_id);
	if (!empty( $child_list )) {
		echo $child_list;
	}
	echo '</div>';
	if (!empty( $child_list )) {
		echo '<br style="clear:both;" /><br />';
	}
	
	
}
// NEW: Include the query section from an external file
require_once( PAGEPATH. "shop_browse_queries.php" );

$db_browse->query( $count );

$num_rows = $db_browse->f("num_rows");

if( $limitstart > 0 && $limit >= $num_rows) {
	
	$list = str_replace( 'LIMIT '.$limitstart, 'LIMIT 0', $list );
}

/*** when nothing has been found
* we tell this here and say goodbye */

if ($num_rows == 0 && !empty($keyword)) {
	echo $VM_LANG->_PHPSHOP_NO_SEARCH_RESULT;
}
elseif( $num_rows == 0 && empty($product_type_id) ) {
	//echo _EMPTY_CATEGORY;
}
/*** NOW START THE PRODUCT LIST ***/
else {
	
	//================= SHOW EXTRA PRODUCT =================
	$sCartProductID	= "";
	if( count($_SESSION['cart']) ) {
		foreach($_SESSION['cart'] AS $item ) {
			if( !empty($item["product_id"]) ) {
				$sCartProductID	.= $item["product_id"] . ",";
			}
		}
	}
	$sCartProductID	= substr($sCartProductID, 0, strlen($sCartProductID) - 1);
	$sContinueLink	= "";
	if( !empty($sCartProductID) ) {
		$q = "SELECT COUNT(product_sku) FROM #__{vm}_product WHERE  product_id IN ($sCartProductID) AND product_sku LIKE 'RP%' ";
		$db->query( $q );
		$RP_products = $database->loadResult();
		
		//print_r($_SESSION['cart']);
		//echo "<br/><br/>$q";
		
		if($RP_products){
			$show_extra	= 2;//Show only Banner
		}else{
			$show_extra	= 1;//Show Extra product + Banner
		}
	}else{
		$show_extra	= -1;//Hidden Extra product + Banner
	}
	//================= END SHOW EXTRA PRODUCT =================	
	
	if( $category_id == 135  ) {
		switch ($mosConfig_lang) {	
                    	case 'french':  
                        	$sImage	=	"checkout_special_fr.png";
                        	break;

                    	case 'english':                      	 
                  	default:
                        	$sImage	=	"checkout_special.png";
                         	break;
		}
?>
		<div style="display:block;width:100%;text-align:center;padding5px;">
			<img src="/images/banners/<?php echo $sImage; ?>" />
		</div>
<?php			
	}
			
	if( $category_id != 135 || ( $category_id == 135 && $show_extra != 2) || $show_extra == -1 ) {
			/* Set Dynamic Page Title */
			if( $category_id ) {
				$db->query( "SELECT category_id, category_name FROM #__{vm}_category WHERE category_id='$category_id'");
				$db->next_record();
				$mainframe->setPageTitle( $db->f("category_name") );
			}
			elseif( $manufacturer_id) {
				$db->query( "SELECT manufacturer_id, mf_name FROM #__{vm}_manufacturer WHERE manufacturer_id='$manufacturer_id'");
				$db->next_record();
				$mainframe->setPageTitle( $db->f("mf_name") );
			}
			elseif( $keyword ) {
				$mainframe->setPageTitle( html_entity_decode( $VM_LANG->_PHPSHOP_SEARCH_TITLE ) );
			}
			else {
				$mainframe->setPageTitle( html_entity_decode($VM_LANG->_PHPSHOP_BROWSE_LBL) );
			}
		
			if (!empty($product_type_id) && @$_REQUEST['output'] != "pdf") {
		    ?>
		    <div align="right">
		    <form action="<?php echo $mm_action_url."index.php?option=com_virtuemart&page=shop.parameter_search_form&product_type_id=$product_type_id&Itemid=" . $_REQUEST['Itemid'] ?>" method="post" name="back">
		        <?php 
		        echo $ps_product_type->get_parameter_form($product_type_id);
		        ?>	  
		      		<strong><?php
		      		echo $VM_LANG->_PHPSHOP_PARAMETER_SEARCH_IN_CATEGORY.": ".$ps_product_type->get_name($product_type_id);
		        ?></strong>&nbsp;&nbsp;<br/>
			  <input type="submit" class="button" id="<?php echo $VM_LANG->_PHPSHOP_PARAMETER_SEARCH_CHANGE_PARAMETERS ?>" name="edit" value="<?php echo $VM_LANG->_PHPSHOP_PARAMETER_SEARCH_CHANGE_PARAMETERS ?>" />
			</form></div>
		    <?php 
			}
			if ( $num_rows > 1 && @$_REQUEST['output'] != "pdf") {
				// Prepare Page Navigation
				if ( $num_rows > $limit  || $num_rows > 5 ) {
					require_once( $mosConfig_absolute_path.'/includes/pageNavigation.php');
					$pagenav = new mosPageNav( $num_rows, $limitstart, $limit);
		
					$search_string = $mm_action_url."index.php?option=com_virtuemart&page=$modulename.browse&category_id=$category_id&keyword=".urlencode( $keyword )."&manufacturer_id=$manufacturer_id&Itemid=$Itemid";
					$search_string .= !empty($orderby) ? "&orderby=".urlencode($orderby) : "";
		
					if (!empty($keyword1)) {
						$search_string.="&keyword1=".urlencode($keyword1);
						$search_string.="&search_category=$search_category";
						$search_string.="&search_limiter=$search_limiter";
						if (!empty($keyword2)) {
							$search_string.="&keyword2=".urlencode($keyword2);
							$search_string.="&search_op=$search_op";
						}
					}
				}
				
			if( $category_id != 135  ) {				
		?>
		    <!-- ORDER BY .... FORM -->
		    <form action="<?php echo $mm_action_url."index.php" ?>" method="get" name="order">
		    <?php 
		    if( !empty( $VM_BROWSE_ORDERBY_FIELDS )) {
		        echo $VM_LANG->_PHPSHOP_ORDERBY ?>: 
		              <select class="inputbox" name="orderby" onchange="order.submit()">
		                <option value="product_name" >
		                 <?php echo $VM_LANG->_PHPSHOP_SELECT ?></option>
		              <?php
		// SORT BY MY OWN ORDER
				if( in_array( 'product_list', $VM_BROWSE_ORDERBY_FIELDS)) { ?>
				        <option value="product_list" <?php echo $orderby=="product_list" ? "selected=\"selected\"" : "";?>>
				        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_ORDER_DEFAULT_MODIFIED_LBL ?></option>
				<?php
				}
				
		              // SORT BY PRODUCT NAME
		              if( in_array( 'product_name', $VM_BROWSE_ORDERBY_FIELDS)) { ?>
		                        <option value="product_name" <?php echo $orderby=="product_name" ? "selected=\"selected\"" : "";?>>
		                        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_NAME_TITLE ?></option>
		              <?php
		              }
		              // SORT BY PRODUCT SKU
		              if( in_array( 'product_sku', $VM_BROWSE_ORDERBY_FIELDS)) { ?>
		                        <option value="product_sku" <?php echo $orderby=="product_sku" ? "selected=\"selected\"" : "";?>>
		                        <?php echo $VM_LANG->_PHPSHOP_CART_SKU ?></option>
		                        <?php
		              }
		              // SORT BY PRODUCT PRICE
		                  if (_SHOW_PRICES == '1' && $auth['show_prices'] && in_array( 'product_price', $VM_BROWSE_ORDERBY_FIELDS)) { ?>
		                                <option value="product_price" <?php echo $orderby=="product_price" ? "selected=\"selected\"" : "";?>>
		                        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_PRICE_TITLE ?></option><?php 
		                  } 
		                  // SORT BY PRODUCT CREATION DATE
		              if( in_array( 'product_cdate', $VM_BROWSE_ORDERBY_FIELDS)) { ?>?>
		                        <option value="product_cdate" <?php echo $orderby=="product_cdate" ? "selected=\"selected\"" : "";?>>
		                        <?php echo $VM_LANG->_PHPSHOP_LATEST ?></option>
		                        <?php
		              }
		              ?>
		              </select>
		          <?php
		    }
		        if ($DescOrderBy == "DESC") {
		                $icon = "sort_desc.png";
		                $selected = Array( "selected=\"selected\"", "" );
			  	$asc_desc = Array( "DESC", "ASC" );
			}
			else {
			  	$icon = "sort_asc.png";
		                $selected = Array( "", "selected=\"selected\"" );
		                $asc_desc = Array( "ASC", "DESC" );
		        }
		        echo mm_writeWithJS('<input type="hidden" name="DescOrderBy" value="'.$asc_desc[0].'" /><a href="javascript: document.order.DescOrderBy.value=\''.$asc_desc[1].'\'; document.order.submit()"><img src="'. $mosConfig_live_site."/images/M_images/$icon"  .'" border="0" alt="'. $VM_LANG->_PHPSHOP_PARAMETER_SEARCH_DESCENDING_ORDER .'" title="'.$VM_LANG->_PHPSHOP_PARAMETER_SEARCH_DESCENDING_ORDER .'" width="12" height="12"/></a>',
		          '<select class="inputbox" name="DescOrderBy">
		                                <option '.$selected[0].' value="DESC">'.$VM_LANG->_PHPSHOP_PARAMETER_SEARCH_DESCENDING_ORDER.'</option>
		                                <option '.$selected[1].' value="ASC">'.$VM_LANG->_PHPSHOP_PARAMETER_SEARCH_ASCENDING_ORDER.'</option>
		                            </select>
		                            <input class="button" type="submit" value="'.$VM_LANG->_PHPSHOP_SUBMIT.'" />');
		
				?>
		        <input type="hidden" name="Itemid" value="<?php echo @$_REQUEST['Itemid'] ?>" />
		        <input type="hidden" name="option" value="com_virtuemart" />
		        <input type="hidden" name="page" value="shop.browse" />
		        <input type="hidden" name="category_id" value="<?php echo $category_id ?>" />
		        <input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer_id ?>" />
		        <input type="hidden" name="keyword" value="<?php echo urlencode( $keyword ) ?>" />
		        <input type="hidden" name="keyword1" value="<?php echo urlencode( $keyword1 ) ?>" />
		        <input type="hidden" name="keyword2" value="<?php echo urlencode( $keyword2 ) ?>" />
		<?php 
				if( !empty( $product_type_id )) {
					echo $ps_product_type->get_parameter_form($product_type_id);
				}
				
				if( PSHOP_SHOW_TOP_PAGENAV =='1' && ($num_rows > $limit || $num_rows > 5)) {
					echo "&nbsp;&nbsp;&nbsp;&nbsp;"._PN_DISPLAY_NR."&nbsp;&nbsp;";
					//echo "<form action=\"$search_string\" method=\"post\">";
					$pagenav->writeLimitBox( $search_string );
					echo "<noscript><input type=\"submit\" value=\"".$VM_LANG->_PHPSHOP_SUBMIT."\" /></noscript></form>";
				}
				else {
					echo "</form>\n";
				}
		                if( PSHOP_SHOW_TOP_PAGENAV =='1' && $num_rows > $limit ) {
		                        // PAGE NAVIGATION AT THE TOP
		                        echo "<br/><div style=\"text-align:center;\">";
		                        echo $pagenav->writePagesLinks( $search_string );
		                        echo "</div><br/>";
		                }
		        }
		        }
			
			
			
			$use_tables = @$_REQUEST['output'] == "pdf" ? true : false;
		
			if( $use_tables ) {
				echo '<table width="100%"><tr>';
			}
			else {
		               echo '<div id="product-list" class="product-list-items">';
		
			}
		
			$i = 0;
			$row = 0;
			$tmp_row = 0;
			$db_browse->query( $list );
			$db_browse->next_record();
		
			$products_per_row = (!empty($category_id)) ? $db_browse->f("products_per_row") : PRODUCTS_PER_ROW;
			if( $products_per_row < 1 ) {
				$products_per_row = 1;
			}
			/**
		  *   Read the template file into a String variable.
		  *   Then replace the placeholders with HTML formatted product details
		  *
		  * function read_file( $file, $defaultfile='') ***/
			if(@$_REQUEST['output'] != "pdf") {
				$templatefile = (!empty($category_id)) ? $db_browse->f("category_browsepage") : CATEGORY_TEMPLATE;
			}
			else {
				$templatefile = "browse_lite_pdf";
			}
		
			$template = read_file( PAGEPATH."templates/browse/$templatefile.php", PAGEPATH."templates/browse/".CATEGORY_TEMPLATE.".php");
			$db_browse->reset();
		
			/*** Start printing out all products (in that category) ***/
			//CALCULATE SHOPPER GROUP DISCOUNT
			global $database, $my;					
			$ShopperGroupDiscount	= 0;
			$query 		= " SELECT SG.shopper_group_discount 
							FROM #__vm_shopper_vendor_xref AS SVX INNER JOIN #__vm_shopper_group AS SG ON SG.shopper_group_id = SVX.shopper_group_id  	
							WHERE  SVX.user_id = ". $my->id ." LIMIT 1"; 
			$database->setQuery($query);
			$ShopperGroupDiscount	= $database->loadResult();	
			
			$nShopperGroupDiscount	= 0;
			if( !empty($ShopperGroupDiscount) ) {										
				if( !empty($ShopperGroupDiscount) && $ShopperGroupDiscount > 0 ) {
					$nShopperGroupDiscount	= floatval($ShopperGroupDiscount) / 100;
				}
			}
			//print_r($ShopperGroupDiscount);
			//echo $query."<br/><br/>";
				
				
			//#6063: sort by price feature...
			//IMPLEMENT the sort price product follow discount price again
			$aProductList	= array();
			$nStep			= 0;
			while ($db_browse->next_record()) {// LOOP TO PRINT PRODUCTS
				$aProductList[$nStep]['product_parent_id']		= $db_browse->f("product_parent_id");
				$aProductList[$nStep]['category_flypage']		= $db_browse->sf("category_flypage");
				$aProductList[$nStep]['product_id']				= $db_browse->f("product_id");
				$aProductList[$nStep]['category_id']				= $db_browse->f("category_id");
				$aProductList[$nStep]['product_thumb_image']	= $db_browse->f("product_thumb_image");
				$aProductList[$nStep]['product_full_image']		= $db_browse->f("product_full_image");
				$aProductList[$nStep]['product_name']			= $db_browse->f("product_name");
				$aProductList[$nStep]['product_publish']			= $db_browse->f("product_publish");
				$aProductList[$nStep]['product_s_desc']			= $db_browse->f("product_s_desc");
				
				$product_price	= 0;
				
				//IMPLEMENT #5055
				$aPrice 			= $ps_product->get_retail_price($db_browse->f("product_id"));	
				/*echo $db_browse->f("product_id");
				print_r($aPrice)			;
				echo "<br/>"*/
				
				if( !empty($aPrice["saving_price"]) && $aPrice["saving_price"] > 0 && $aPrice["product_price"] >= 0 ) {
					$product_price	= $aPrice["product_price"] - $aPrice["saving_price"];
				}else{
					$product_price	= $aPrice["product_price"];
				}
				
				$product_price 	= floatval($product_price)  - ( floatval($product_price) * floatval($nShopperGroupDiscount) );	
				//echo $db_browse->f("product_sku"). "=====--$nShopperGroupDiscount--=========".$product_price. "========". ( floatval($product_price) * floatval($ShopperGroupDiscount) ) ."<br/>";				
							
				$aFind 			= array( '<span style="font-weight:bold">', '</span>', ' ', '$');
				$aReplace   		= array("", "", "", "");
				$product_price 	= str_replace($aFind, $aReplace, $product_price);
				
				
				$aProductList[$nStep]['product_price']	= $product_price;
				$aProductList[$nStep]['product_sku']	= $db_browse->f("product_sku");
				$aProductList[$nStep]['num_rows']		= $db_browse->num_rows();
				
				$nStep++;
			}
			
  			// sort by price
  			usort($aProductList, 'comparePrice');
			//print_r($aProductList);
			
			
			$nPCount	= 0;
			foreach($aProductList as $product_item ) {// LOOP TO PRINT PRODUCTS
				$nPCount++;
				// If it is item get parent:
				$product_parent_id = $product_item["product_parent_id"];
				if ($product_parent_id != 0) {
					$dbp->query("SELECT product_full_image,product_thumb_image,product_name,product_s_desc FROM #__{vm}_product WHERE product_id='$product_parent_id'" );
					$dbp->next_record();
				}
		
				// Set the flypage for this product based on the category.
				// If no flypage is set then use the default as set in virtuemart.cfg.php
				$flypage = $db_browse->sf("category_flypage");
				
				if (empty($flypage)) {
					$flypage = FLYPAGE;
				}
		
		        $url = $sess->url( $mm_action_url."index.php?page=shop.product_details&flypage=$flypage&product_id=" . $product_item["product_id"] . "&category_id=" . $product_item["category_id"]. "&manufacturer_id=" . $manufacturer_id);
		
		        if( $product_item["product_thumb_image"] ) {
		                $product_thumb_image = $product_item["product_thumb_image"];
				}
				else {
					if( $product_parent_id != 0 ) {
						$product_thumb_image = $dbp->f("product_thumb_image"); // Use product_thumb_image from Parent Product
					}
					else {
						$product_thumb_image = 0;
					}
				}
				if( $product_thumb_image ) {
					if( substr( $product_thumb_image, 0, 4) != "http" ) {
						if(PSHOP_IMG_RESIZE_ENABLE == '1') {
							$product_thumb_image = $mosConfig_live_site."/components/com_virtuemart/show_image_in_imgtag.php?filename=".urlencode($product_thumb_image)."&newxsize=".PSHOP_IMG_WIDTH."&newysize=".PSHOP_IMG_HEIGHT."&fileout=";
						}
						else {
							if( file_exists( IMAGEPATH."product/".$product_thumb_image )) {
		                        $product_thumb_image = IMAGEURL."product/".$product_thumb_image;
		                    }
		                    else {
		                        $product_thumb_image = IMAGEURL.NO_IMAGE;
		                    }
						}
					}
				}
				else {
					$product_thumb_image = IMAGEURL.NO_IMAGE;
				}
		
				if( $product_item["product_full_image"] ) {
					$product_full_image = $product_item["product_full_image"];
				}
				else {
					if( $product_parent_id != 0 ) {
						$product_full_image = $dbp->f("product_full_image"); // Use product_full_image from Parent Product
					}
					else {
						$product_full_image = "..".NO_IMAGE;
					}
				}
				if( file_exists( IMAGEPATH."product/$product_full_image" )) {
					$full_image_info = getimagesize( IMAGEPATH."product/$product_full_image" );
					$full_image_width = $full_image_info[0]+40;
					$full_image_height = $full_image_info[1]+40;
				}
				else {
					$full_image_width = $full_image_height = "";
				}
				$product_name = $product_item["product_name"];
				if( $product_item["product_publish"] == "N" ) {
					$product_name .= " (".vmHtmlEntityDecode(_CMN_UNPUBLISHED).")";
				}
		
				if( empty($product_name) && $product_parent_id!=0 ) {
					$product_name = $dbp->f("product_name"); // Use product_name from Parent Product
				}
				$product_s_desc = $product_item["product_s_desc"];
				if( empty($product_s_desc) && $product_parent_id!=0 ) {
					$product_s_desc = $dbp->f("product_s_desc"); // Use product_s_desc from Parent Product
				}
				$product_details = $VM_LANG->_PHPSHOP_FLYPAGE_LBL;
		
				if (PSHOP_ALLOW_REVIEWS == '1' && @$_REQUEST['output'] != "pdf") {
					/**
		        *   Average customer rating: xxxxx
		        *   Total votes: x
		        */
					$product_rating = $VM_LANG->_PHPSHOP_CUSTOMER_RATING .": <br />";
					$product_rating .= ps_reviews::allvotes( $product_item["product_id"] );
				}
				else
				$product_rating = "";
		
		
				$product_price	= "$".number_format(($product_item["product_price"]), 2, '.', '');
				
				switch ($mosConfig_lang) {	
		                    case 'french':  
		                        $sBtnImage	=	"button_fr.gif";
		                        break;
		
		                    case 'english':                      	 
		                  	default:
		                        $sBtnImage	=	"button.gif";
		                         break;
		                 }
				
				/*** Add-to-Cart Button ***/
				if (USE_AS_CATALOGUE != '1' && $product_price != "" && !stristr( $product_price, $VM_LANG->_PHPSHOP_PRODUCT_CALL )) {
					$form_addtocart = "<form action=\"". $mm_action_url ."index.php\" method=\"post\" name=\"addtocart\" id=\"formAddToCart_".$product_item["product_id"]."\">\n
		                <input name=\"quantity_".$product_item["product_id"]."\" class=\"inputbox\" type=\"text\" size=\"3\" value=\"1\" /> 			
		                <img style='cursor:pointer;'  align='absbottom' class='add-to-cart'  name='".$product_item["product_id"]."' height='17' border='0' width='80' src='components/com_virtuemart/shop_image/ps_image/$sBtnImage'>
		                <input type=\"hidden\" name=\"category_id_". $product_item["product_id"] ."\" value=\"". @$_REQUEST['category_id'] ."\" />\n
		                <input type=\"hidden\" name=\"product_id_". $product_item["product_id"] ."\" value=\"". $product_item["product_id"] ."\" />\n
		                <input type=\"hidden\" name=\"price_". $product_item["product_id"] ."\" value=\"".$product_item['product_price']."\" />\n
		              </form>\n";
				}
				else
				$form_addtocart = "";
		
				/*** Now fill the template
				* Customizing:
				*   a. Define your own placeholders(e.g. {product_weight} )
				*   b. Add a line below like this (must be below first str_replace call!):
				$product_cell = str_replace( "{product_weight}", $db_browse->f("product_weight"), $product_cell );
				*   c. put the placeholder {product_weight} somewhere in the template (/html/templates)
				<tr><td>Product Weight: {product_weight}</td></tr>
				*   d. save the template file under a new name (e.g. browse_weight.php )
				*   e. Assign the browse page "browse_weight" to the categories,
				*       you want to have using that template file (do that in the category form!)
				**/
				
				$product_cell = str_replace( "{product_id}", $url, $template );
				$product_cell = str_replace( "{product_flypage}", $url, $template );
				$product_cell = str_replace( "{div_product_id}", "div_".$product_item["product_id"], $product_cell );
				$product_cell = str_replace( "{product_thumb_image}", $product_thumb_image, $product_cell );
				$product_cell = str_replace( "{product_full_image}", $product_full_image, $product_cell );
				$product_cell = str_replace( "{full_image_width}", $full_image_width, $product_cell );
				$product_cell = str_replace( "{full_image_height}", $full_image_height, $product_cell );
		
				if( substr( $product_full_image, 0, 4) == "http" )
				$product_cell = str_replace( "{image_url}product/", "", $product_cell );
		
				else
				$product_cell = str_replace( "{image_url}", IMAGEURL, $product_cell );
		
				if( PSHOP_IMG_RESIZE_ENABLE=='1' ) {
					$product_cell = str_replace( "{image_width}", "", $product_cell );
					$product_cell = str_replace( "{image_height}", "", $product_cell );
				}
				else {
					if( file_exists( str_replace( IMAGEURL, IMAGEPATH, $product_thumb_image))) {
						$arr = @getimagesize( str_replace( IMAGEURL, IMAGEPATH, $product_thumb_image) );
						$height_greater = $arr[0] < $arr[1];
					}
					if( @$height_greater === false ) {
						$product_cell = str_replace( "{image_width}", "width=\"".PSHOP_IMG_WIDTH."\"", $product_cell );
						$product_cell = str_replace( "{image_height}", "", $product_cell );
					}
					else {
						$product_cell = str_replace( "{image_width}", "", $product_cell );
						$product_cell = str_replace( "{image_height}", "height=\"".PSHOP_IMG_HEIGHT."\"", $product_cell );
					}
				}
				
				global $mosConfig_lang;
				if( $mosConfig_lang == "french" ) {
					$product_cell = str_replace( "{product_name}", $product_name, $product_cell );
				}else{
					$product_cell = str_replace( "{product_name}", shopMakeHtmlSafe( $product_name ), $product_cell );
				}
				
				$product_cell = str_replace( "{product_s_desc}", $product_s_desc, $product_cell );
				$product_cell = str_replace( "{product_details...}", $product_details, $product_cell );
				$product_cell = str_replace( "{product_rating}", $product_rating, $product_cell );
				$product_cell = str_replace( "{product_price}", $product_price, $product_cell );
				$product_cell = str_replace( "{form_addtocart}", $form_addtocart, $product_cell );
				$product_cell = str_replace( "{product_sku}", $product_item["product_sku"], $product_cell );
		
				/*** Now echo the filled cell ***/
				if( $tmp_row != $row || $row == 0 ) {
					if ( $db_browse->num_rows() - ($i) < $products_per_row ) {
						$cell_count = $db_browse->num_rows() - ($i);
					}
					else {
						$cell_count = $products_per_row;
					}
					$row++;
					$tmp_row = $row;
				}
				$colspan = $products_per_row - $cell_count + 1;
				if( $cell_count < 1 ) {
					$cell_count = 1;
				}
		   if( $use_tables ){
		 	 echo "<td colspan=\"$colspan\" width=\"". intval(round(100/$cell_count)-4) ."%\"align=\"center\" >";
		  }else{
		 	//#6057: Add "View all Gift Baskets" banner
			$category_id = intval(mosGetParam( $_REQUEST, 'category_id', "0" ));
		 	if( $nPCount == 7 && $category_id == 171 ) {
				echo "<a href='$mosConfig_live_site/index.php?page=shop.browse&category_id=83&option=com_virtuemart&Itemid=73' style='display:block;width:100%;text-align:center;border:0;'>
						<img border='0' src='$mosConfig_live_site/templates/bloomex7/images/View_All_Gift_Baskets.png' />
					</a>";
			}
		   	
			echo "<div id=\"".uniqid( "row_" ) ."\">";
		   }
		    
		
			
		
				echo $product_cell;
		
				$i++;
				/*** START NEXT ROW ??? ***/
				if ( ($i) % $products_per_row == 0) {
					$row++;
					/** if yes, close the current row and print out a horizontal bar ***/
					if( $use_tables ) {
						echo "\n</td></tr><tr>";
					}
					else {
						echo "\n</div><br style=\"clear:both;\" />";
					}
				}
				else {
					if( $use_tables ) {
						echo "\n</td>";
					}
					else {
						echo "\n</div>";
					}
				}
			} /*** END OF while loop ***/
		
			echo '<br style="clear:both;" />';
			if( $use_tables ) {
				echo '</tr></table>';
			}
			else {
				echo '</div>';
			}
		?>
		<!-- BEGIN PAGE NAVIGATION -->
		<div align="center">
		<?php
		
		if ( $num_rows > $limit && @$_REQUEST['output'] != "pdf") {
			if( !isset($pagenav) ) {
		                require_once( $mosConfig_absolute_path.'/includes/pageNavigation.php');
		                $pagenav = new mosPageNav( $num_rows, $limitstart, $limit);
		        }
		        echo $pagenav->writePagesLinks( $search_string );
		}
		
		if( !(($show_extra == 1 || $show_extra == 2) && $category_id == 135)  ) {			
			if( $num_rows > 5 && @$_REQUEST['output'] != "pdf") {
			        echo "<br/><br/><form action=\"$search_string\" method=\"post\">"._PN_DISPLAY_NR."&nbsp;&nbsp;";
				$pagenav->writeLimitBox( $search_string );
				echo "<noscript><input class=\"button\" type=\"submit\" value=\"".$VM_LANG->_PHPSHOP_SUBMIT."\" /></noscript></form>";
			}
		}
		?>
		</div>
		<!-- END PAGE NAVIGATION -->
	
<?php 
	}
	
	if( ($show_extra == 1 || $show_extra == 2) && $category_id == 135  ) {		
 
		if( $mosConfig_lang == "french" ) {
			$sBtnCheckout	= "checkout_green_fr.png";
			$sBtnImage		= "button_fr.gif";
			$sProductBanner1	= "product_banner1_fr.png";
			$sProductBanner2	= "product_banner2_fr.png";
		}else {
			$sBtnCheckout	= "checkout_green.png";
			$sBtnImage		= "button.gif";
			$sProductBanner1	= "product_banner1.png";
			$sProductBanner2	= "product_banner2.png";
		}
		
		$q = "SELECT product_publish, product_id FROM #__vm_product WHERE  product_id IN (951, 953)";
		$database->setQuery( $q );
		$oSpecialProducts = $database->loadObjectList();		
		
		$aSpecialProducts		= array();
		if( count($oSpecialProducts) ) {
			foreach($oSpecialProducts as $item) {
				$aSpecialProducts[$item->product_id]	= $item->product_publish;
			}
		}
		
		
		//========================== GET PRICES ==========================
		global $database, $my;					
		$ShopperGroupDiscount	= 0;
		$query 		= " SELECT SG.shopper_group_discount 
						FROM #__vm_shopper_vendor_xref AS SVX INNER JOIN #__vm_shopper_group AS SG ON SG.shopper_group_id = SVX.shopper_group_id  	
						WHERE  SVX.user_id = ". $my->id ." LIMIT 1"; 
		$database->setQuery($query);
		$ShopperGroupDiscount	= $database->loadResult();	
		
		$nShopperGroupDiscount	= 0;
		if( !empty($ShopperGroupDiscount) ) {										
			if( !empty($ShopperGroupDiscount) && $ShopperGroupDiscount > 0 ) {
				$nShopperGroupDiscount	= floatval($ShopperGroupDiscount) / 100;
			}
		}
		
		$aPrice	= array();
		//PRICE FOR "Platinum Club"
		$aPrice 			= $ps_product->get_retail_price(951);		
		if( !empty($aPrice["saving_price"]) && $aPrice["saving_price"] > 0 && $aPrice["product_price"] >= 0 ) {
			$product_price_951	= $aPrice["product_price"] - $aPrice["saving_price"];
		}else{
			$product_price_951	= $aPrice["product_price"];
		}		
		$product_price_951 	= floatval($product_price_951)  - ( floatval($product_price_951) * floatval($nShopperGroupDiscount) );
		
		
		//PRICE FOR "Voucher"
		unset($aPrice);
		$aPrice 			= $ps_product->get_retail_price(953);		
		if( !empty($aPrice["saving_price"]) && $aPrice["saving_price"] > 0 && $aPrice["product_price"] >= 0 ) {
			$product_price_953	= $aPrice["product_price"] - $aPrice["saving_price"];
		}else{
			$product_price_953	= $aPrice["product_price"];
		}		
		$product_price_953 	= floatval($product_price_953)  - ( floatval($product_price_953) * floatval($nShopperGroupDiscount) );
		
		/*echo $q;
		print_r($oSpecialProducts);
		print_r($aSpecialProducts);*/
?>
		<table width="100%" cellpadding="0" cellspacing="10" border="0" style="margin:20px 0px 20px 0px;">
			<tr>
				<td style="text-align:center;vertical-align:top" width="50%"> 
					<?php if( !empty($aSpecialProducts[951]) && $aSpecialProducts[951] == "Y" ) { ?>
					<div class="product-image">	
						<a href="index.php?page=shop.product_details&product_id=951&option=com_virtuemart&Itemid=80">
						<img src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/<?php echo $sProductBanner1; ?>" align="middle" alt="Product Banner " border="0" />
						</a>
					</div>
					<div class="product-detail">    	
				     		<a href="index.php?page=shop.product_details&product_id=951&option=com_virtuemart&Itemid=80">Product Details</a>
				    	</div>
					<div id="div_951" class="form-add-cart" style="text-align:center;display:block;">
						<form id="formAddToCart_951" name="addtocart" method="post" action="index.php">
					                <input type="text" value="1" size="3" class="inputbox" name="quantity_951"> 			
					                <img align="absbottom" width="80" border="0" height="17" src="components/com_virtuemart/shop_image/ps_image/<?php echo $sBtnImage; ?>" name="951" class="add-to-cart" style="cursor:pointer;">
					                <input type="hidden" value="62" name="category_id_951">			
					                <input type="hidden" value="951" name="product_id_951">			
					                <input type="hidden" value="<?php echo $product_price_951; ?>" name="price_951">		
				              </form>
					 </div>
					<?php } ?>
				</td>
				<td style="text-align:center;vertical-align:top"  width="50%">	
					<?php if( !empty($aSpecialProducts[953]) && $aSpecialProducts[953] == "Y" ) { ?>				
					<div class="product-image">
						<a href="index.php?page=shop.product_details&product_id=953&option=com_virtuemart&Itemid=80">
							<img src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/<?php echo $sProductBanner2; ?>" align="middle" alt="Product Banner " border="0" />
						</a>
					</div>
					<div class="product-detail">    	
				     		<a href="index.php?page=shop.product_details&product_id=953&option=com_virtuemart&Itemid=80">Product Details</a>
				    	</div>
					<div id="div_953" class="form-add-cart" style="text-align:center;display:block;">
						<form id="formAddToCart_953" name="addtocart" method="post" action="index.php">
					                <input type="text" value="1" size="3" class="inputbox" name="quantity_953"> 			
					                <img align="absbottom" width="80" border="0" height="17" src="components/com_virtuemart/shop_image/ps_image/<?php echo $sBtnImage; ?>" name="953" class="add-to-cart" style="cursor:pointer;">
					                <input type="hidden" value="62" name="category_id_953">			
					                <input type="hidden" value="953" name="product_id_953">			
					                <input type="hidden" value="<?php echo $product_price_953; ?>" name="price_953">		
				              </form>
					 </div>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">				
					<div style="margin:15px 0px 0px 0px;float:right;">			
					     <a href="<?php $sess->purl( $mm_action_url . "index.php?page=checkout.index&ssl_redirect=1"); ?>">
					   	  <img src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/<?php echo $sBtnCheckout; ?>" align="middle" alt="Check Out" border="0" />
					     </a>
					</div>
				</td>
			</tr>
		</table>
<?php		
	}
}
return $mainframe;
?>
