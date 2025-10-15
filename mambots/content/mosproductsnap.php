<?php
/**
* VirtueMart Show-Product-Snapshop Mambot
*
* @version $Id: mosproductsnap.php,v 1.4.2.1 2006/03/14 18:42:24 soeren_nb Exp $
* @package VirtueMart
* @subpackage mambots
*
* @copyright (C) 2004 Soeren Eberhardt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* http://virtuemart.net
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* VirtueMart Show-Product-Snapshop Mambot
*
* <b>Usage:</b>
* <code>{product_snapshot:id=XX,showprice,showdesc,sowaddtocart,align}</code>
* int id (product_id)
* boolean showprice (show the product price?)
* boolean showdesc (show the product short description?)
* boolean showaddtocart (show an "Add-to-cart" link?)
* string align (defines the align of the table with the product snapshot)
*/

$_MAMBOTS->registerFunction( 'onPrepareContent', 'mosProductSnapshotPlugin_onPrepareContent' );


function mosProductSnapshotPlugin_onPrepareContent( $published, &$row, &$params, $page=0  ) {
  global $ps_product, $mosConfig_absolute_path;
  
  require_once( $mosConfig_absolute_path . "/components/com_virtuemart/virtuemart_parser.php" );
  include_class("product");
  
  $pshop_productsnap_entrytext = $row->text;
  $pshop_productsnap_matches = array();
  if (preg_match_all("/{product_snapshot:id=.+?}/", $pshop_productsnap_entrytext, $pshop_productsnap_matches, PREG_PATTERN_ORDER) > 0) {
    
    foreach ($pshop_productsnap_matches[0] as $pshop_productsnap_match) {
      $pshop_productsnap_output = "";
      $pshop_productsnap_match = str_replace("{product_snapshot:id=", "", $pshop_productsnap_match);
      $pshop_productsnap_match = str_replace("}", "", $pshop_productsnap_match);
      
      // Get Bot Parameters
      $pshop_productsnap_params = array();
      $pshop_productsnap_params = explode(",", $pshop_productsnap_match);
      
      // Assign Bot Parameters
      $id = $pshop_productsnap_params[0];
      $showprice = @$pshop_productsnap_params[1]=='true' ? true : false;
      $showdesc = @$pshop_productsnap_params[2]=='true' ? true : false;
      $showaddtocart = @$pshop_productsnap_params[3]=='true' ? true : false;
      $align  = @$pshop_productsnap_params[4];
      if( !empty( $id )) {
        $showsnapshot = return_snapshot( $id, $showprice, $showdesc, $showaddtocart, $align);
      }
          else {
                $showsnapshot = 'Error: '.__FUNCTION__.' received no product ID<br/>';
          }
      $pshop_productsnap_entrytext = preg_replace("/{product_snapshot:id=.+?}/", $showsnapshot, $pshop_productsnap_entrytext, 1);
    }
    $row->text = $pshop_productsnap_entrytext;
  
  }
}

/**************************************************************************
   ** name: return_snapshot($product_id)
   ** created by: soeren
   ** description: return the html code to show a snapshot of a 
   **               product based on the product id.
   ** parameters: int product_id
   ** returns: $html code
   ***************************************************************************/
  function return_snapshot($product_id, $showprice, $showdesc, $showaddtocart, $align) {
  
    global  $sess,$PHPSHOP_LANG, $mosConfig_absolute_path, $ps_product;
    $ps_vendor_id = $_SESSION["ps_vendor_id"];
    $auth = $_SESSION["auth"];
    $db = new ps_DB;
    $html = "";
    
    $q = "SELECT product_name,product_id,product_parent_id,product_thumb_image,product_s_desc 
			FROM #__{vm}_product WHERE product_id=$product_id ";
    $db->setQuery($q); $db->query();
    
    if ($db->next_record()) {
      $html .= "<table width=\"100\" ";
      $html .= !empty($align) ? "align=\"$align\">" : ">";
      
      $html .= "<tr><td align='center'><strong>".$db->f("product_name")."</strong></td></tr>\n";
      
      $url = "index.php?page=".$ps_product->get_flypage($db->f("product_id"));
      if ($db->f("product_parent_id")) {
          $url = "index.php?page=shop.product_details&flypage=".$ps_product->get_flypage($db->f("product_parent_id"));
          $url .= "&product_id=" . $db->f("product_parent_id");
      } 
      else {
          $url = "index.php?page=shop.product_details&flypage=".$ps_product->get_flypage($db->f("product_id"));
          $url .= "&product_id=" . $db->f("product_id");
      }
      
      $html .= "<tr><td align=\"center\"><a title=\"".$db->f("product_name")."\" href=\"". $sess->url(URL . $url)."\">";
      $html .= $ps_product->image_tag( $db->f("product_thumb_image"), "alt=\"".$db->f("product_name")."\" hspace=\"7\"" );
      
      $html .= "</a></td></tr>\n";
      
      if ($showdesc)
          $html .= "<tr><td>".$db->f("product_s_desc")."</td></tr>\n";
  
      if ($showprice) 
          $html .= "<tr><td><strong>".$PHPSHOP_LANG->_PHPSHOP_CART_PRICE .": </strong>".str_replace( "$", "\\$", $ps_product->show_price($db->f("product_id")))."<br /></td></tr>\n";
      
      if ($showaddtocart) {
          $html .= "<tr><td>";
          $url = "index.php?page=shop.cart&func=cartAdd&product_id=" .  $db->f("product_id");
          $html .= "<a href=\"". $sess->url(URL . $url)."\">&gt; ".$PHPSHOP_LANG->_PHPSHOP_CART_ADD_TO." &lt;</a><br /></td></tr>\n";
       }
      $html .= "</table>";
       return( $html );
    }

    else {
      // product_id not found
      return("");
    }
}
?>
