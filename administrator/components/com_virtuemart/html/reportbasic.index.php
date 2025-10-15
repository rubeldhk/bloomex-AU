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

foreach (array ('thisMonth', 'lastMonth', 'last60', 'last90', 'sbmt') as $button_name) {
	$$button_name = mosGetParam( $_REQUEST, $button_name );
}

$selected_begin["day"] = $sday = mosGetParam( $_REQUEST, "sday", 1 );
$selected_begin["month"] = $smonth = mosGetParam( $_REQUEST, "smonth", date("m"));
$selected_begin["year"] = $syear = mosGetParam( $_REQUEST, "syear", date("Y"));

$selected_end["day"] = $eday = mosGetParam( $_REQUEST, "eday", date("d") );
$selected_end["month"] = $emonth = mosGetParam( $_REQUEST, "emonth", date("m"));
$selected_end["year"] = $eyear = mosGetParam( $_REQUEST, "eyear", date("Y"));

$i=0;

?>
<!-- BEGIN body -->
&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGEURL ?>ps_image/report.gif" border="0" />&nbsp;&nbsp;&nbsp;
<span class="sectionname"><?php echo $VM_LANG->_PHPSHOP_REPORTBASIC_MOD ?></span><br /><br />
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    <input type="hidden" name="page" value="reportbasic.index" />
    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="pshop_mode" value="admin" />
    <table class="adminform" width="100%" border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td><?php echo $VM_LANG->_PHPSHOP_VIEW ?></td>
          <td><input type="checkbox" name="show_products" id="show_products" value="show_products"<?php
          if (!empty($show_products)) { echo ' checked="checked"'; } ?> />
          <label for="show_products"><?php echo $VM_LANG->_PHPSHOP_RB_INDIVIDUAL ?></label> &nbsp; &nbsp; 
          </td>
        </tr>
        <tr>
          <td><?php echo $VM_LANG->_PHPSHOP_VIEW ?></td>
          <td><input type="checkbox" name="show_products_type" id="show_products_type" value="show_products_type"<?php
          if (!empty($show_products_type)) { echo ' checked="checked"'; } ?> />
          <label for="show_products_type"><?php echo $VM_LANG->_PHPSHOP_RB_TYPE ?></label> &nbsp; &nbsp; 
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <hr noshade="noshade" size="2" color="#000000" />
          </td>
        </tr>
        <tr>
          <td><?php echo $VM_LANG->_PHPSHOP_RB_INTERVAL_TITLE; ?></td>

          <td><input type="radio" id="byMonth" name="interval" value="byMonth" <?php if($interval=="byMonth") echo "checked='checked'" ?> />
          <label for="byMonth"><?php echo $VM_LANG->_PHPSHOP_RB_INTERVAL_MONTHLY_TITLE ?></label> &nbsp; &nbsp; 
          <input type="radio" name="interval" id="byWeek" value="byWeek" <?php if($interval=="byWeek") echo "checked='checked'" ?> />
          <label for="byWeek"><?php echo $VM_LANG->_PHPSHOP_RB_INTERVAL_WEEKLY_TITLE; ?></label> &nbsp; &nbsp;
          <input type="radio" name="interval" id="byDay" value="byDay" <?php if($interval=="byDay") echo "checked='checked'" ?> />
          <label for="byDay"><?php echo $VM_LANG->_PHPSHOP_RB_INTERVAL_DAILY_TITLE; ?></label></td>
        </tr>

        <tr>
          <td colspan="2">
            <hr noshade="noshade" size="2" color="#000000" />
          </td>
        </tr>

        <tr>
          <td><?php echo $VM_LANG->_PHPSHOP_SHOW ?></td>

          <td>
          <input type="submit" class="button" name="thisMonth" value="<?php echo $VM_LANG->_PHPSHOP_RB_THISMONTH_BUTTON; ?>" /> &nbsp; 
          <input type="submit" class="button" name="lastMonth" value="<?php echo $VM_LANG->_PHPSHOP_RB_LASTMONTH_BUTTON; ?>" /> &nbsp; 
          <input type="submit" class="button" name="last60" value="<?php echo $VM_LANG->_PHPSHOP_RB_LAST60_BUTTON; ?>" /> &nbsp;
          <input type="submit" class="button" name="last90" value="<?php echo $VM_LANG->_PHPSHOP_RB_LAST90_BUTTON; ?>" />
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <hr noshade="noshade" size="2" color="#000000" />
          </td>
        </tr>

        <tr valign="top">
          <td width="100"><?php echo $VM_LANG->_PHPSHOP_RB_START_DATE_TITLE; ?></td>

          <td><?php
          $nh_report->make_date_popups("s", $selected_begin );
          ?></td>
        </tr>

        <tr>
          <td width="100"><?php echo $VM_LANG->_PHPSHOP_RB_END_DATE_TITLE; ?></td>

          <td><?php $nh_report->make_date_popups("e", $selected_end ); ?></td>
        </tr>

        <tr>
          <td>&nbsp;</td>

          <td><input type="submit" class="button" name="sbmt" value="<?php echo $VM_LANG->_PHPSHOP_RB_SHOW_SEL_RANGE ?>" /> </td>
        </tr>
      </table>
    </form>
<!-- begin output of report -->
<?php 
 /* assemble start date */
 if (isset($thisMonth)) {
   $start_date = mktime(0,0,0,date("n"),1,date("Y"));
   $end_date = mktime(23,59,59,date("n")+1,0,date("Y"));
 }
 else if (isset($lastMonth)) {
   $start_date = mktime(0,0,0,date("n")-1,1,date("Y"));
   $end_date = mktime(23,59,59,date("n"),0,date("Y"));
 }
 else if (isset($last60)) {
   $start_date = mktime(0,0,0,date("n"),date("j")-60,date("Y"));
   $end_date = mktime(23,59,59,date("n"),date("j"),date("Y"));
 }
 else if(isset ($last90)) {
   $start_date = mktime(0,0,0,date("n"),date("j")-90,date("Y"));
   $end_date = mktime(23,59,59,date("n"),date("j"),date("Y"));
 }
 elseif (isset($sbmt)) {
   /* start and end dates should have been given, assign accordingly */
   $start_max_day = date("j",mktime(0,0,0,$smonth+1,0,$syear));
   if (! (intval($sday) <= $start_max_day)) {
     $sday = $start_max_day;
   }
   $start_date = mktime(0,0,0,intval($smonth),intval($sday),$syear);

   $end_max_day = date("j",mktime(0,0,0,intval($smonth)+1,0,$syear));
   if (! (intval($eday) <= $end_max_day)) {
     $eday = $end_max_day;
   }
   $end_date = mktime(23,59,59,intval($emonth),intval($eday),$eyear);
 }
 else {
 /* nothing was sent to the page, so create default inputs */
   $start_date = mktime(0,0,0,date("n"),1,date("Y"));
   $end_date = mktime(23,59,59,date("n")+1,0,date("Y"));
   $interval = "byMonth";
 }
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
  $q .= "SUM(order_subtotal) as revenue, SUM(order_shipping) as shipping, SUM(order_tax+order_shipping_tax) as taxes, SUM(order_total) as total, SUM(coupon_discount) as discounts ";
  $q .= "FROM #__{vm}_orders ";
  $q .= $query_between_line;
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
  if ($query_group_line) {
    $r .= $query_group_line;
  }
  $r .= " ORDER BY date_num ASC";

// added for v0.2 PRODUCT LISTING QUERY!
if (!empty($show_products)) {
/* setup end of product listing query */
  $u .= "product_name, product_sku, ";
  if ($query_date_line) {
    $u .= str_replace ("cdate", "#__{vm}_order_item.cdate", $query_date_line);
  }
  $u .= "FROM_UNIXTIME(#__{vm}_order_item.cdate, '%Y%m%d') as date_num, ";
  $u .= "SUM(product_quantity) as items_sold ";
  $u .= "FROM #__{vm}_order_item, #__{vm}_orders, #__{vm}_product ";
  $u .= str_replace ("cdate", "#__{vm}_order_item.cdate", $query_between_line);
  $u .= "AND #__{vm}_orders.order_id=#__{vm}_order_item.order_id ";
  $u .= "AND #__{vm}_order_item.product_id=#__{vm}_product.product_id ";
  $u .= "GROUP BY product_sku, product_name, order_date ";
  $u .= " ORDER BY date_num, items_sold ASC";

  $dbpl = new ps_DB;
  $dbpl->query($u);

}

if (!empty($show_products_type)) {
  $t .= " #__{vm}_order_product_type.product_type_name, #__{vm}_order_product_type.quantity, #__{vm}_order_product_type.price, ";
  if ($query_date_line) {
    $t .= str_replace ("cdate", "#__{vm}_order_item.cdate", $query_date_line);
  }
  $t .= "FROM_UNIXTIME(#__{vm}_order_item.cdate, '%Y%m%d') as date_num, ";
  $t .= "SUM(product_quantity) as items_sold ";
  $t .= "FROM #__{vm}_order_item, #__{vm}_orders, #__{vm}_product,#__{vm}_order_product_type ";
  $t .= str_replace ("cdate", "#__{vm}_order_item.cdate", $query_between_line);
  $t .= "AND #__{vm}_orders.order_id=#__{vm}_order_item.order_id ";
  $t .= "AND #__{vm}_order_item.product_id=#__{vm}_product.product_id ";
  $t .= "AND #__{vm}_orders.order_id=#__{vm}_order_product_type.order_id ";
  $t .= "AND #__{vm}_order_item.product_id=#__{vm}_order_product_type.product_id ";
  $t .= "GROUP BY product_type_name, order_date ";
  $t .= " ORDER BY date_num, items_sold ASC";

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

    <table class="adminlist">
      <tr>
        <th><left><?php echo $VM_LANG->_PHPSHOP_RB_DATE ?></left></th>

        <th><left><?php echo $VM_LANG->_PHPSHOP_RB_ORDERS ?></left></th>

        <th><?php echo $VM_LANG->_PHPSHOP_RB_TOTAL_ITEMS ?></th>

        <th>Subtotal</th>
<th>Shipping (w/o taxes)</th>
<th>Taxes total (product and delivery)</th>
<th>Total (tax icluded)</th>
<th>Total discounts</th>
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
    <tr bgcolor="<?php echo $bgcolor ?>"> 
      <td><?php $db->p("order_date"); ?></td>
      <td><?php $db->p("number_of_orders"); ?></td>
      <td><?php $dbis->p("items_sold"); ?></td>
      <td><?php $db->p("revenue"); ?>&nbsp;</td>
 <td><?php $db->p("shipping"); ?></td>
<td><?php $db->p("taxes"); ?></td>
<td><?php $db->p("total"); ?></td>
<td><?php $db->p("discounts"); ?></td> 
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
	          echo '<td align="left">' . $dbpl->f("product_name") . " (" . $dbpl->f("product_sku") . ")</td>\n";
	          echo '<td align="left">' . $dbpl->f("items_sold") . "</td>\n";
	          echo "</tr>\n";
        	}
         	$has_next = $dbpl->next_record();
        }
        $dbpl->reset();
      ?>
     <tr><td colspan="3"><hr width="85%"></td></tr>
      </table>

      <?php
      }
      ?>
      
     
      <?php
      if (!empty($show_products_type)) {
      ?>
      <?php 
      $dbtype = new ps_DB;
      $dbtype->query($t);

      ?>
      <tr><td>&nbsp;</td><td colspan="2">
      <table class="adminlist">
        <tr>
          <td colspan="4" align="left"><h3><?php echo $VM_LANG->_PHPSHOP_RB_PRODLISTTYPE ?></h3></td>
        </tr>
        <tr bgcolor="#ffffff">
          <th>#</th>
          <th><?php echo $VM_LANG->_PHPSHOP_PRODUCT_NAME_TITLE ?></th>
          <th><?php echo $VM_LANG->_PHPSHOP_CART_QUANTITY ?></th>
          <th><?php echo $VM_LANG->_PHPSHOP_RB_REVENUE ?></th>
        </tr>
      <?php
        $i = 1;
        $has_next = $dbtype->next_record();
        
        while ( $has_next) {
        	if( $dbtype->f("order_date") == $dbtype->f("order_date")) {
	          echo "<tr bgcolor=\"#ffffff\">\n";
	          echo "<td>".$i++."</td>\n";
	          echo '<td align="left">' . $dbtype->f("product_type_name") . "</td>\n";
	          echo '<td align="left">' . $dbtype->f("quantity")*$dbtype->f("items_sold") . "</td>\n";
	          echo '<td align="left">' . $dbtype->f("price")*$dbtype->f("items_sold") . "</td>\n";
	          echo "</tr>\n";
        	}
         	$has_next = $dbtype->next_record();
        }
        $dbtype->reset();
      ?>

      <tr><td colspan="4"><hr width="85%"></td></tr>
      </table>
      </td><td>&nbsp;</td>
      </tr>

    <?php

    }
    // END product listing

  } ?>
  </table>
        
<!-- end output of report -->
<!-- END body -->
