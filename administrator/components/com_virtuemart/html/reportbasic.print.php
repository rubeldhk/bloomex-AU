<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: reportbasic.index.php,v 1.4.2.1 2006/03/21 19:38:23 soeren_nb Exp $
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
$nh_report = new nh_report();
$show_products = mosGetParam( $_REQUEST, "show_products" );
$show_products_type = mosGetParam( $_REQUEST, "show_products_type" );
$interval = mosGetParam( $_REQUEST, "interval", "byMonth" );
$show = mosGetParam( $_REQUEST, "show","ALL");
$start_date = mosGetParam( $_REQUEST, "start_date");
$end_date = mosGetParam( $_REQUEST, "end_date");

$i=0;

 
$query_date_line = "";
 /* get the interval and set the date line for the query */
 switch ($interval) {
    case 'byMonth':
     $query_date_line = "FROM_UNIXTIME(cdate, '%M, %Y') as order_date, ";
     $query_group_line = "GROUP BY order_date";
     break;
   case 'byWeek':
     $query_date_line .= "WEEK(FROM_UNIXTIME(cdate, '%Y-%m-%d')) as week_number, ";
     $query_date_line .= "FROM_UNIXTIME(cdate, '%M %d, %Y') as order_date, ";
     $query_group_line = "GROUP BY week_number";
     break;
   case 'byDay':
   /* query for days */
     $query_date_line = "FROM_UNIXTIME(cdate, '%M %d, %Y') as order_date, ";
     $query_group_line = "GROUP BY order_date";
     break;
   default:
     $query_date_line = '';
     $query_group_line = '';
    break;
  }
  /* better way of setting up query */
  $q  = "SELECT ";
  $r  = $q;
  $u  = $q;
  $t  =  $q;
  $query_between_line = "WHERE cdate BETWEEN '" . $start_date . "' AND '" . $end_date . "' ";
  if ($query_date_line) {
    $q .= $query_date_line;
  }
  $q .= "FROM_UNIXTIME(cdate, '%Y%m%d') as date_num, ";
  $q .= "COUNT(order_id) as number_of_orders, ";
  $q .= "SUM(order_subtotal) as revenue ";
  $q .= "FROM #__{vm}_orders ";
  $q .= $query_between_line;
  if(!($show=='ALL')){
  $q .= "AND #__{vm}_orders.order_status='".$show."' ";
  }
  if ($query_group_line) {
    $q .= $query_group_line;
  }
  $q .= " ORDER BY date_num ASC";

  /** setup items sold query */
  if ($query_date_line) {
    $r .= $query_date_line;
  }
  $r .= "FROM_UNIXTIME(cdate, '%Y%m%d') as date_num, ";
  $r .= "SUM(product_quantity) as items_sold ";
  $r .= "FROM #__{vm}_order_item ";
  $r .= $query_between_line;
  if(!($show=='ALL')){
  $r .= "AND #__{vm}_orders.order_status='".$show."' ";
  }
  if ($query_group_line) {
    $r .= $query_group_line;
  }
  $r .= " ORDER BY date_num ASC";

// added for v0.2 PRODUCT LISTING QUERY!
if (!empty($show_products)) {
/* setup end of product listing query */
  $u .= "product_name, product_sku,#__{vm}_warehouse.warehouse_name as warehouse, ";
  if ($query_date_line) {
    $u .= str_replace ("cdate", "#__{vm}_order_item.cdate", $query_date_line);
  }
  $u .= "FROM_UNIXTIME(#__{vm}_order_item.cdate, '%Y%m%d') as date_num, ";
  $u .= "SUM(product_quantity) as items_sold ";
  $u .= "FROM #__{vm}_order_item, #__{vm}_orders, #__{vm}_product,#__{vm}_warehouse  ";
  $u .= str_replace ("cdate", "#__{vm}_order_item.cdate", $query_between_line);
  $u .= "AND #__{vm}_orders.order_id=#__{vm}_order_item.order_id ";
  $u .= "AND #__{vm}_order_item.product_id=#__{vm}_product.product_id ";
  $u .= "AND #__{vm}_order_item.warehouse=#__{vm}_warehouse.warehouse_code ";
  if(!($show=='ALL')){
  $u .= "AND #__{vm}_orders.order_status='".$show."' ";
  }
  $u .= "GROUP BY warehouse,product_sku, product_name, order_date ";
  $u .= " ORDER BY warehouse,date_num, product_name ASC";

  $dbpl = new ps_DB;
  $dbpl->query($u);

}

if (!empty($show_products_type)) {
  $t .= " #__{vm}_order_product_type.product_type_name, #__{vm}_order_product_type.quantity, #__{vm}_order_product_type.price,#__{vm}_warehouse.warehouse_name as warehouse, ";
  if ($query_date_line) {
    $t .= str_replace ("cdate", "#__{vm}_order_item.cdate", $query_date_line);
  }
  $t .= "FROM_UNIXTIME(#__{vm}_order_item.cdate, '%Y%m%d') as date_num, ";
  $t .= "SUM(product_quantity) as items_sold ";
  $t .= "FROM #__{vm}_order_item, #__{vm}_orders, #__{vm}_product,#__{vm}_order_product_type,#__{vm}_warehouse  ";
  $t .= str_replace ("cdate", "#__{vm}_order_item.cdate", $query_between_line);
  $t .= "AND #__{vm}_orders.order_id=#__{vm}_order_item.order_id ";
  $t .= "AND #__{vm}_order_item.product_id=#__{vm}_product.product_id ";
  $t .= "AND #__{vm}_orders.order_id=#__{vm}_order_product_type.order_id ";
  $t .= "AND #__{vm}_order_item.product_id=#__{vm}_order_product_type.product_id ";
  $t .= "AND #__{vm}_order_item.warehouse=#__{vm}_warehouse.warehouse_code ";
  if(!($show=='ALL')){
  $t .= "AND #__{vm}_orders.order_status='".$show."' ";
  }
  $t .= "GROUP BY product_type_name, order_date ";
  $t .= " ORDER BY warehouse,date_num, product_type_name ASC";

}
/* setup the db and query */
  $db = new ps_DB;
  $dbis = new ps_DB;
  
  $db->query($q);
  $dbis->query($r);
 ?>
    <h4><?php 
    echo $VM_LANG->_PHPSHOP_RB_REPORT_FOR ." ";
    echo date("M j, Y", $start_date)." --&gt; ". date("M j, Y", $end_date); 
    ?></h4>

<?php
  while ($db->next_record()) {
    $dbis->next_record();
    
    if ($i++ % 2) {
      $bgcolor=SEARCH_COLOR_1;
    }
    else {
      $bgcolor=SEARCH_COLOR_2;
    }
        ?> 
    <table class="adminlist">
      <tr>
        <th><?php echo $VM_LANG->_PHPSHOP_RB_DATE ?></th>

        <th><?php echo $VM_LANG->_PHPSHOP_RB_ORDERS ?></th>

        <th><?php echo $VM_LANG->_PHPSHOP_RB_TOTAL_ITEMS ?></th>
        <th><?php echo $VM_LANG->_PHPSHOP_RB_STATUS_ORDERS ?></th>

        <th><?php echo $VM_LANG->_PHPSHOP_RB_REVENUE ?></th>
      </tr>                                 

    <tr bgcolor="<?php echo $bgcolor ?>"> 
      <td><?php $db->p("order_date"); ?></td>
      <td><?php $db->p("number_of_orders"); ?></td>
      <td><?php $dbis->p("items_sold"); ?></td>
      <td align=center>
<?php 
      switch( $show ) {
       case "P":
           echo "Pending";
           break;
       case "C":
           echo "Confirmed";
           break;
       case "X":
           echo "Cancelled";
           break;
       case "R":
           echo "Refunded ";
           break;
       case "S":
           echo "Shipped";
           break;
       default:        
           echo "ALL";
              break;
       }   
 
?></td>
      <td><?php $db->p("revenue"); ?>&nbsp;</td>
    </tr>
  <?php
    // BEGIN product listing
    if (!empty($show_products)) {
    ?>
    <tr><td>&nbsp;</td><td colspan="2">
      <table class="adminlist">
        <tr>
          <td colspan="3" align="left"><h3><?php echo $VM_LANG->_PHPSHOP_RB_PRODLIST ?></h3></td>
        </tr>
        <tr bgcolor="#ffffff">
          <th>#</th>
          <th>Warehouse</th>
          <th>SKU</th>
          <th><?php echo $VM_LANG->_PHPSHOP_PRODUCT_NAME_TITLE ?></th>
          <th><?php echo $VM_LANG->_PHPSHOP_CART_QUANTITY ?></th>
        </tr>
      <?php
        $i = 1;
        $has_next = $dbpl->next_record();
        
        while ( $has_next) {
        	if( $dbpl->f("order_date") == $db->f("order_date")) {
	          echo "<tr bgcolor=\"#ffffff\">\n";
	          echo "<td>".$i++."</td>\n";
          echo "<td align=center>". $dbpl->f("warehouse") ."</td>\n";
          echo "<td align=center>". $dbpl->f("product_sku") ."</td>\n";
	          echo '<td align="left">' . $dbpl->f("product_name") . " (" . $dbpl->f("product_sku") . ")</td>\n";
	          echo '<td align="center">' . $dbpl->f("items_sold"). "</td>\n";
	          echo "</tr>\n";
        	}
         	$has_next = $dbpl->next_record();
        }
        $dbpl->reset();
      ?>

      </table>

      <?php
      }
      ?>
</table>      

      <?php
}

      if (!empty($show_products_type)) {
      ?>
      <?php 
      $dbtype = new ps_DB;
      $dbtype->query($t);

      ?>
      <tr><td>&nbsp;</td><td colspan="2">
      <table class="adminlist">
        <tr>
          <td colspan="4" align="center"><h3>

   <?php echo "<i>".$VM_LANG->_PHPSHOP_RB_PRODLISTTYPE; 
    echo date("M j, Y", $start_date)." --&gt; ". date("M j, Y", $end_date)."</i>"; 
   ?>
</h3></td>
        </tr>
        <tr bgcolor="#ffffff">
          <th>#</th>
          <th>Warehouse</th>
          <th><?php echo $VM_LANG->_PHPSHOP_PRODUCT_NAME_TITLE ?></th>
          <th><?php echo $VM_LANG->_PHPSHOP_CART_QUANTITY ?></th>
          <th><?php echo $VM_LANG->_PHPSHOP_RB_REVENUE ?></th>
        </tr>
      <?php
        $i = 1;
        $has_next = $dbtype->next_record();
        $total=0;
        while ( $has_next) {
        	if( $dbtype->f("order_date") == $dbtype->f("order_date")) {
	          echo "<tr bgcolor=\"#ffffff\">\n";
	          echo "<td>".$i++."</td>\n";
          echo "<td align=center>". $dbtype->f("warehouse") ."</td>\n";
                  echo '<td align="left">' . $dbtype->f("product_type_name") . "</td>\n";
	          echo '<td align="center">' . $dbtype->f("quantity")* $dbtype->f("items_sold") . "</td>\n";
	          echo '<td align="center">' . $dbtype->f("quantity")* $dbtype->f("price")*$dbtype->f("items_sold") . "</td>\n";
                  $total=$total+$dbtype->f("quantity")* $dbtype->f("price")*$dbtype->f("items_sold");
	          echo "</tr>\n";
        	}
         	$has_next = $dbtype->next_record();
        }
        $dbtype->reset();
      ?>
      </tr>
      <tr><td colspan=5 align=right><b>TOTAL</b>=<?php echo $total;?></td>
      </table>


      </tr>

    <?php
    
    }
    // END product listing

   ?>
 
        
<!-- end output of report -->
<!-- END body -->

