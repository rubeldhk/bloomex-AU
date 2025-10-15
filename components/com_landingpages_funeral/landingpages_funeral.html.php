<?php
/**
* @version $Id: contact.html.php 4157 2006-07-02 17:58:51Z stingrey $
* @package Joomla
* @subpackage Contact
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
* @package Joomla
* @subpackage Contact
*/
class HTML_LandingPages {
	function viewPage( $Page ) {
		global $database,$Itemid, $mosConfig_live_site, $mosConfig_absolute_path, $mm_action_url, $my, $VM_LANG, $mainframe,$mosConfig_lang;

		/* Load the virtuemart main parse code */
		require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );		
		$url 	= trim(mosGetParam( $_GET, "url" ));
		
		$healthy = array("{city}", "{province}");
		$yummy   = array($Page->city, $Page->province);
		
		if( str_replace( "/", "", strtolower($url)) == "winnipeg" ) { 
			$mainframe->setPageTitle2(str_replace($healthy, $yummy, _PAGE_TITLE_WINNIPEG));
			$mainframe->addMetaTag2('keywords', str_replace($healthy, $yummy, _PAGE_META_DESC_WINNIPEG));
			$mainframe->addMetaTag2('description', str_replace($healthy, $yummy, _PAGE_META_KEYWORDS_WINNIPEG));
		}else{
			$mainframe->setPageTitle2(str_replace($healthy, $yummy, _PAGE_TITLE));
			$mainframe->addMetaTag2('keywords', str_replace($healthy, $yummy, _PAGE_META_DESC));
			$mainframe->addMetaTag2('description', str_replace($healthy, $yummy, _PAGE_META_KEYWORDS));
		}
			
                 $direng=array();
                 $direng['english']='en';
                 $direng['french']='fr';
                 $sku=array();
                 $href=array();
                 $sProductRandom='';
                 $sql = " SELECT href FROM jos_vm_landing_page_funeral_banner where lang='" . $direng[$mosConfig_lang] . "' AND position='categ_land';"; 
                 $database->setQuery( $sql );
                 $check_categ_land = $database->loadResult();
                 $sql = " SELECT href FROM jos_vm_landing_page_funeral_banner where lang='" . $direng[$mosConfig_lang] . "' AND position='categ_product';"; 
                 $database->setQuery( $sql );
                 $check_categ_product = $database->loadResult();
                 if($check_categ_product=='1'){
                   $sql = " SELECT sku FROM jos_vm_landing_products_funeral where lang='" . $direng[$mosConfig_lang] . "' ORDER BY num;"; 
                    $database->setQuery( $sql );
                    $rows = $database->loadObjectList();
                    if ($rows) {
                            foreach ($rows as $row) {
                                $sku[]=$row->sku;
                               }       
                        }                               //landing_to
                 }
                 else{
                     $sql = " SELECT href FROM jos_vm_landing_page_funeral_banner where lang='" . $direng[$mosConfig_lang] . "' AND position='body' ORDER BY num;"; 
                    $database->setQuery( $sql );
                    $rows = $database->loadObjectList();
                    if ($rows) {
                            foreach ($rows as $row) {
                                $href[]=$row->href;
                               }       
                        }    
                 }
                 
		if( $Page->enable_location ) {

                                ?>
           
            <script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
            <?php
			
		}		
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
		<script type="text/javascript">
			sSecurityUrl	= "<?php echo ( SECUREURL != "" ? SECUREURL : $mm_action_url );?>";
			bMember			= <?php echo $my->id; ?>;
		</script>
 
        <script>
                     function initialize() {
                var geocoder;
                var map;
                // Create an object containing LatLng, population.
                geocoder = new google.maps.Geocoder();
                var address = "<?php echo $Page->location_address . ", " . $Page->location_postcode . ", " . $Page->location_country; ?>";
                geocoder.geocode({'address': address}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var mapOptions = {
                            disableDefaultUI: true,
                            zoom: 10,
                            center: results[0].geometry.location,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        }
                        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
                        //map.setCenter(results[0].geometry.location);
                        var marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location,
                            title: address
                        });
                        var populationOptions = {
                            strokeColor: '#000000',
                            strokeOpacity: 0.8,
                            strokeWeight: 1,
                            fillColor: '#ffff00',
                            fillOpacity: 0.20,
                            map: map,
                            center: results[0].geometry.location,
                            radius: 10000
                        };
                        cityCircle = new google.maps.Circle(populationOptions);
                    } else {
                        alert("Adress: " + address + " \nGeocode was not successful for the following reason: " + status);
                    }
                });
            }

            jQuery(document).ready(function() {
                var marginLeft = 250;
                document.getElementById("map_canvas").style.display = "block";
                document.getElementById("map_canvas").style.height = (document.getElementById("new-main-banner-banner").offsetHeight)+"px";
                document.getElementById("map_canvas").style.width = (document.getElementById("new-main-banner-banner").offsetWidth - marginLeft)+"px";//"435px";
                document.getElementById("map_canvas").style.marginLeft = marginLeft+"px";
                document.getElementById("map_canvas").style.styleFloat="right";
                document.getElementById("map_canvas").style.position="absolute";
                
                //height:191px;display:block;width:397px;margin-left:180px; 
               // initialize();
            });
        </script>

		<?php
			$max_items			= 9;
			$products_per_row	= 3;
			$show_addtocart		= 1;
			$show_price			= 1;
			$category_id =     $Page->category_id;
                 
                if($check_categ_land=='1'){
                   
		require_once( CLASSPATH. 'ps_product.php');
		$ps_product = new ps_product;

		$db	= new ps_DB;
		if ( $category_id ) {
			$q  = "SELECT DISTINCT product_sku FROM #__{vm}_product, #__{vm}_product_category_xref, #__{vm}_category WHERE ";
			$q .= "product_parent_id=''";
			$q .= "AND #__{vm}_product.product_id=#__{vm}_product_category_xref.product_id ";
			$q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
			$q .= "AND #__{vm}_category.category_id='$category_id'";
			$q .= "AND #__{vm}_product.product_publish='Y' ";
			if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
				$q .= " AND product_in_stock > 0 ";
			}
		  $q .= "ORDER BY product_name DESC";
		}
		else {
			$q  = "SELECT DISTINCT product_sku FROM #__{vm}_product WHERE ";
			$q .= "product_parent_id='' AND vendor_id='".$_SESSION['ps_vendor_id']."' ";
			$q .= "AND #__{vm}_product.product_publish='Y' ";
			if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
				$q .= " AND product_in_stock > 0 ";
			}
			$q .= "ORDER BY product_name DESC";
		}
		$db->query($q);

		$i=0;
		while($db->next_record()){
		  $prodlist[$i]=$db->f("product_sku");
		  $i++;
		}

		if($db->num_rows() > 0) { ?>
		  <table border="0" cellpadding="3" cellspacing="5" width="96%">
		    <?php

			srand ((double) microtime() * 10000000);
			
		    if (sizeof($prodlist) < $max_items) {
		    	$max_items = sizeof($prodlist);
		    }
		    if (sizeof($prodlist)>1) {
		    	$rand_prods = array_rand ($prodlist, $max_items);
		    }
		  	else {
		  		$rand_prods = rand (4545.3545, $max_items);
		  	}
			
		      if ($max_items==1) { ?>
		        <tr align="center" class="sectiontableentry1">
		          <td><?php
		            $ps_product->show_snapshot2($prodlist[$rand_prods], $show_price, $show_addtocart);
		            ?><br />
		            </td>
		          </tr><?php
		      }
		      
		      else { 
		        for($i=0; $i<$max_items; $i++) {
		          if ($i%2)
		              $sectioncolor = "sectiontableentry2";
		          else
		              $sectioncolor = "sectiontableentry1";
		              
		          if( $i == 0 )
		              echo "<tr>\n";
		            echo "<td align=\"center\">";
		            $ps_product->show_snapshot2($prodlist[$rand_prods[$i]], $show_price, $show_addtocart);
		            echo "</td>\n";
		            if ( ($i+1) % $products_per_row == 0)
		              echo "</tr><tr>\n";
		            if( ($i+1) == $max_items )
		              echo "</tr>\n";
			  }
		  }
		        ?>
		  </table>
    <?php
                }
            }
            
            else{//do not use category from location manager
               if($check_categ_product=='1') {  
                   require_once( CLASSPATH. 'ps_product.php');
		$ps_product = new ps_product;
                $max_items=count($sku);
                ?>
                     <table border="0" cellpadding="3" cellspacing="5" width="96%">
		    <?php	
                       $j=0;
		        for($i=0; $i<$max_items; $i++) {
                           // echo $i." ";
		          if ($j%2)
		              $sectioncolor = "sectiontableentry2";
		          else
		              $sectioncolor = "sectiontableentry1";
		           
                          $sql="SELECT product_sku FROM jos_vm_product where product_sku='".$sku[$i]."'";
                            $database->setQuery( $sql );
                            $check_product_sku = $database->loadResult();  
		          if( $j == 0 ){
		              echo "<tr>\n";
                          }
                         
                            if(isset($check_product_sku)){
		            echo "<td align=\"center\">";
		            $ps_product->show_snapshot2($sku[$i], $show_price, $show_addtocart);
		            echo "</td>\n";
                            $j++;
                            }
                           
		            if ( $j % $products_per_row == 0)
		              echo "</tr><tr>\n";
		            if( $i == $max_items )
		              echo "</tr>\n";
                            
			 
		  }
		        ?>
		  </table>
        <?php
               } 
                if($check_categ_product=='0') {  
                 $max_items=count($href);
                ?>
                     <table border="0" cellpadding="3" cellspacing="5" width="96%">
		    <?php	
		        for($i=0; $i<$max_items; $i++) {
		          if ($i%2)
		              $sectioncolor = "sectiontableentry2";
		          else
		              $sectioncolor = "sectiontableentry1";
		              
		          if( $i == 0 )//'landing_mid_' . $l . $i . '.jpg';
		              echo "<tr>\n";
		            echo "<td align=\"center\">";
		            echo '<a href="'.$href[$i].'">';  
                             echo '   <img width="100%" style="margin: 0px 0px 0px 1px" src="'.$mosConfig_live_site.'/images/'.$direng[$mosConfig_lang].'/landing_mid_'.$direng[$mosConfig_lang].$i.'.png" alt=" " border="0">  ';
                              echo '  </a>  ';                           
		            echo "</td>\n";
		            if ( ($i+1) % $products_per_row == 0)
		              echo "</tr><tr>\n";
		            if( ($i+1) == $max_items )
		              echo "</tr>\n";
			 
		  }
		        ?>
		  </table>
        <?php
               }   
           }
           
	}        
}
?>