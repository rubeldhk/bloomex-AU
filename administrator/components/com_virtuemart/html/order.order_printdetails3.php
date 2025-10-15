<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: order.order_printdetails.php,v 1.5.2.2 2006/03/10 15:55:15 soeren_nb Exp $
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
global $mosConfig_sitename;

require_once(CLASSPATH.'ps_checkout.php');
require_once(CLASSPATH.'ps_product.php');
$ps_product= new ps_product;

$order_id = mosgetparam( $_REQUEST, 'order_id', null);
$dbc = new ps_DB;
$dbs = new ps_DB;
if (!is_numeric($order_id))
    die ('Please provide a valid Order ID!');

$q = "SELECT * FROM #__{vm}_orders AS O, #__{vm}_order_occasion AS OC WHERE O.order_id='$order_id' AND O.customer_occasion = OC.order_occasion_code"; 
$db->query($q);
$db->next_record();
// if ($db->next_record()) {
?>
 
<!--Start formatting for envelope-->


<table border="0" cellspacing="0" cellpadding="2" width="100%">	
	<tr>	
		<td colspan="2" align="center"><h1><?php echo $mosConfig_sitename;?></h1></td>
	</tr>
	<tr>	
		<td colspan="2" align="center"><h2>ABN: 27 147 609 443</h2></td>
	</tr>
	<tr valign="top"> 
		<td width="50%" align="left"> 
			<!-- Begin BillTo -->
			<?php
				// Get bill_to information
				$dbbt = new ps_DB;
				$q  = "SELECT * FROM #__{vm}_order_user_info WHERE user_id='" . $db->f("user_id") . "'  AND order_id='$order_id' ORDER BY address_type, order_info_id ASC"; 
				$dbbt->query($q);
				$dbbt->next_record(); 
			?> 			
			<table width="100%" cellspacing="0" cellpadding="2" border="0">
				<tr> 
					<td width="20%" align="right" valign="top"><strong>To:</strong>&nbsp;</td>
					<td align="left">
						<?php 						
							if( $dbbt->f("company") ) echo  $dbbt->f("company")."<br/>"; 
							if( $dbbt->f("first_name") || $dbbt->f("middle_name") || $dbbt->f("last_name") ) echo  $dbbt->f("first_name")." ".$dbbt->f("middle_name")." ".$dbbt->f("last_name")." "."<br/>"; 
							if( $dbbt->f("address_1") ) echo $dbbt->f("address_1")."<br/>"; 
							if( $dbbt->f("address_2") ) echo $dbbt->f("address_2")."<br/>"; 
							if( $dbbt->f("city") ) echo $dbbt->f("city"). " "; 
							if( $dbbt->f("state_name") ) echo $dbbt->f("state_name")." "; 
							if( $dbbt->f("zip") ) echo $dbbt->f("zip"); 							
							/*$dbs->query("SELECT S.state_name FROM #__{vm}_state S, #__{vm}_country AS C WHERE C.country_id = S.country_id AND C.country_3_code = '".$dbbt->f("country")."' AND  S.state_2_code = '".$dbbt->f("state")."'" );
							$dbs->next_record();
							if( $dbs->f("state_name") ) echo $dbs->f("state_name")."<br/>"; */
						?>
					</td>
				</tr>
					<td align="right"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?>:</strong>&nbsp;</td>
					<td align="left"><?php $dbbt->p("phone_1"); ?></td>
				</tr>
				<tr> 
					<td align="right"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_FAX ?>:</strong>&nbsp;</td>
					<td align="left"><?php $dbbt->p("fax"); ?></td>
				</tr>
				<tr> 
					<td align="right"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL ?>:</strong>&nbsp;</td>
					<td align="left"><?php $dbbt->p("user_email"); ?></td>   
				</tr>
			</table>
			<!-- End BillTo -->
		</td>
		<td width="50%" align="left" valign="top">
			<table width="100%" cellspacing="0" cellpadding="2" border="0">
				<tr> 
					<td width="20%" align="right"><strong>Date:</strong>&nbsp;</td>
					<td align="left"><?php echo date("d/m/Y", $db->f("cdate")); ?></td>
				</tr>
					<td align="right"><strong>Tax Invoice:</strong>&nbsp;</td>
					<td align="left">No. BMX<?php printf("%08d", $db->f("order_id")); ?></td>
				</tr>
				<tr> 
					<td align="right"></td>
					<td align="center"><br/><img src="<?php echo $mosConfig_live_site; ?>/barcode/html/image.php?code=code128&o=1&dpi=72&t=30&r=1&rot=0&text=BMX<?php echo sprintf("%08d", trim($db->f("order_id")));?>&f1=Arial.ttf&f2=9&a1=&a2=NULL&a3=" /><br/><br/></td>
				</tr>
			</table>
		</td>
	</tr>

 
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <!-- Begin Order Items Information --> 
  <tr class="sectiontableheader"> 
    <th align="left" colspan="2"><h3><?php echo $VM_LANG->_PHPSHOP_ORDER_ITEM ?></h3></th>
  </tr>
  <tr> 
    <td colspan="2"> 
      <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr align="left"> 
		  <th align="left" valign="top" width="25%"><b>Description</b></th>
          <th align="center" valign="top" width="15%"><b>Item No.</b></th>
		  <th align="center" valign="top" width="12%"><b>QTY</b></th>
		  <th align="center" valign="top" width="12%"><b>Unit Price<br/>(excl GST)<b></th>
		  <th align="center" valign="top" width="12%"><b>Sub Total<br/>(excl GST)<b></th>
		  <th align="center" valign="top" width="12%"><b>GST Amount</b></th>
		  <th align="center" valign="top" width="12%"><b>Amount Payable<br/>(incl GST)</b></th>
        </tr>
        <?php 
      $dbcart = new ps_DB;
      $q  = "SELECT * FROM #__{vm}_order_item ";
      $q .= "WHERE #__{vm}_order_item.order_id='$order_id' ";
      $dbcart->query($q); 
      $total_tax 	= 0;
	  $subtotal 	= 0;
	  $total 		= 0;
      while ($dbcart->next_record()) {
?> 
        <tr align="left" valign="top"> 
		 	<td align="left"><?php $dbcart->p("order_item_name"); ?></td>
			<td align="center"><?php $dbcart->p("order_item_sku"); ?></td>
			<td align="center"><?php $dbcart->p("product_quantity"); ?></td>
			<td align="center"><?php echo $CURRENCY_DISPLAY->getFullValue($dbcart->f("product_item_price")); ?></td>
			<td align="center">
				<?php 
					$unit_subtotal 		= $dbcart->f("product_quantity") * $dbcart->f("product_item_price"); 
					$unit_total 		= $dbcart->f("product_quantity") * $dbcart->f("product_final_price"); 
					$unit_subtotal_tax 	= $unit_total - $unit_subtotal; 
					$total_tax			+= $unit_subtotal_tax;
					$subtotal			+= $unit_subtotal;
					$total				+= $unit_total;
					echo $CURRENCY_DISPLAY->getFullValue($unit_subtotal);
				?>
			</td>
          	<td align="center"><?php echo $CURRENCY_DISPLAY->getFullValue($unit_subtotal_tax); ?></td>
		  	<td align="center"><?php echo $CURRENCY_DISPLAY->getFullValue($unit_total); ?></td>
        </tr>
<?php
      }
	  
	  
?> 
		<tr> 
          <td colspan="7" align="right"><br/><br/></td>
        </tr>
		<tr align="left"> 
			<td><strong>Totals</strong></td>
			<td></td>
			<td></td>
			<td></td>
			<td align="center"><strong><?php echo $CURRENCY_DISPLAY->getFullValue($subtotal); ?></strong></td>
			<td align="center"><strong><?php echo $CURRENCY_DISPLAY->getFullValue($total_tax); ?></strong></td>
			<td align="center"><strong><?php echo $CURRENCY_DISPLAY->getFullValue($total); ?></strong></td> 	
        </tr>  
		<tr> 
          <td colspan="7" align="right"><br/><br/></td>
        </tr>  
		<?php 
			/* COUPON DISCOUNT */
			$coupon_discount = $db->f("coupon_discount");	
			if( $coupon_discount > 0 ) {
				$subtotal 	-= $coupon_discount;
				$total 		-= $coupon_discount;
				$total_tax	 = $total - $subtotal;
		?>
			<tr>
				<td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_COUPON_DISCOUNT ?>:</td> 
				<td align="right"><?php echo "- ".$CURRENCY_DISPLAY->getFullValue( $coupon_discount ); ?></td>
			</tr>
		<?php
			}
		?>    
        <tr> 
          <td colspan="4" align="right">Total (excl GST):</td>
          <td colspan="3" align="right"><strong><?php echo $CURRENCY_DISPLAY->getFullValue($subtotal); ?></strong></td>
        </tr>		
		<tr> 
          <td colspan="4" align="right">Total GST Amount Payable:</td>
          <td colspan="3" align="right"><strong><?php echo $CURRENCY_DISPLAY->getFullValue($total_tax); ?></strong></td>
        </tr>
		<tr> 
          <td colspan="4" align="right"><strong>Total Amount Payable(incl GST):</strong></td>
          <td colspan="3" align="right"><strong><?php echo $CURRENCY_DISPLAY->getFullValue($total); ?></strong></td>
        </tr>         
      	</table>
    </td>
  </tr>
 </table> 
  <table width="100%">
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr class="sectiontableheader">
        <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_NOTE ?></th>
      </tr>
      <tr>
         <td> <?php echo $PHPSHOP_LANG->_PHPSHOP_USER_FORM_OCCASION?> :</td>
        <td><?php echo nl2br($db->f("order_occasion_name"))."<br />"; ?>
       </td>
      </tr>
      
  <tr>
         <td ><?php echo $PHPSHOP_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_COMMENTS?> :</td>
        <td><?php echo nl2br($db->f("customer_comments"))."<br />"; ?>
       </td>
      </tr>
 


    </table>