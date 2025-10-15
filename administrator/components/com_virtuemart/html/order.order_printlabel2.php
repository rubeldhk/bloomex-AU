<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 * @version $Id: order.order_printdetails.php,v 1.7 2005/05/10 18:45:04 soeren_nb Exp $
 * @package mambo-phpShop
 * @subpackage HTML
 * Contains code from PHPShop(tm):
 * 	@copyright (C) 2000 - 2004 Edikon Corporation (www.edikon.com)
 * 	Community: www.phpshop.org, forums.phpshop.org
 * Conversion to Mambo and the rest:
 * 	@copyright (C) 2004-2005 Soeren Eberhardt
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * mambo-phpShop is Free Software.
 * mambo-phpShop comes with absolute no warranty.
 *
 * www.mambo-phpshop.net
 */
mm_showMyFileName(__FILE__);
require_once(CLASSPATH . 'ps_checkout.php');
require_once(CLASSPATH . 'ps_product.php');
$ps_product = new ps_product;

$orders = mosgetparam($_REQUEST, 'order_id', null);
$dbc = new ps_DB;
$orders = explode(",", $orders);

?>
<style>
    #loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url(/images/103.gif) center no-repeat #fff;
    }
</style>
<div id="loader"></div>

<script type="text/javascript">
    window.onload = function() {
                document.getElementById('loader').style.display = "none";
            };
</script>
<?php
$lastElement = end($orders);
foreach($orders as $order_id){
if (!is_numeric($order_id))
    die('Please provide a valid Order ID!');

$q = "SELECT * FROM #__{vm}_orders WHERE order_id='$order_id'";
$db->query($q);

if ($db->next_record()) {
    if($my->prevs->warehouse_only &&( $my->prevs->warehouse_only!=$db->f("warehouse"))) {
     die('Order is not assigned to your warehouse');
}
    // Get ship_to information
    $mbbt = new ps_DB;
    $q = "SELECT * 
  		FROM #__{vm}_order_user_info AS OUI ,
  		#__{vm}_state AS S, 
		#__{vm}_country  AS C  
  		WHERE S.state_2_code = OUI.state AND 
				OUI.address_type = 'ST' AND 
				C.country_3_code=OUI.country AND 
				OUI.order_id='" . $order_id . "' ORDER BY OUI.order_info_id ASC LIMIT 1";
    $mbbt->query($q);
    $mbbt->next_record();

    //echo $q;
    ?>
    <br/><br/>
    <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" valign="middle">
        <tr>
            <td align="center" ><big>
            <br/>&nbsp;<br /><p>&nbsp;</p><br/><p>&nbsp;</p><br/><p>&nbsp;</p><br/>&nbsp;<br />
            <p>&nbsp;</p>
            <div style="width:300px;">
                <b>
                    <?php
                    if ($db->f("customer_note")) {
                        echo str_replace("\\", "", htmlspecialchars_decode(nl2br($db->f("customer_note"))));
                    } else {
                        echo " ./. ";
                    }
                    ?><br/>&nbsp;
                    <?php
                    if ($db->f("customer_signature")) {
                        echo str_replace("\\", "", htmlspecialchars_decode(nl2br($db->f("customer_signature"))));
                    } else {
                        echo " ./. ";
                    }
                    ?>
                </b>
            </div></big>

    </td>
    </tr>
    <tr>
        <td valign="bottom">
            <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
            <table width="55%" cellspacing="0" cellpadding="5" border="0" align="center" valign="top">
                <tr> 
                    <td width="40%"><strong style="white-space:nowrap;"><?php echo $PHPSHOP_LANG->_PHPSHOP_ORDER_PRINT_PO_NUMBER ?>:</strong></td>
                    <td width="60%"><?php printf("%08d", $db->f("order_id")); ?></td>
                </tr>
                <tr> 
                    <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_DELIVERYDATE ?>:</strong></td>
                    <td><?php echo $db->f("ddate"); ?></td>
                </tr>

                <tr> 
                    <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?>:</strong></td>
                    <td><?php $mbbt->p("company"); ?></td>
                </tr>
                <tr> 
                    <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_SHOPPER_LIST_NAME ?>:</strong></td>
                    <td><?php
                $mbbt->p("first_name");
                echo " ";
                $mbbt->p("middle_name");
                echo " ";
                $mbbt->p("last_name");
                    ?></td>
                </tr>
                <?php if ($mbbt->f("suite")) { ?>
                    <tr valign="top"> 
                        <td><?php echo $VM_LANG->_PHPSHOP_SUITE ?> :</td>
                        <td><?php
            $mbbt->p("suite");
                    ?></td>
                    </tr>
                <?php } ?>
                <?php if ($mbbt->f("street_number")) { ?>
                    <tr valign="top"> 
                        <td><?php echo $VM_LANG->_PHPSHOP_STREET_NUMBER ?> :</td>
                        <td><?php
            $mbbt->p("street_number");
                    ?></td>
                    </tr>
                    <tr valign="top"> 
                        <td><?php echo $VM_LANG->_PHPSHOP_STREET_NAME ?> :</td>
                        <td><?php
                    $mbbt->p("street_name");
                    ?></td>
                    </tr>         
                <?php } else { ?>
                    <tr valign="top"> 
                        <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?>:</strong></td>
                        <td><?php
            $mbbt->p("address_1");
            echo "<br />";
            $mbbt->p("address_2");
                    ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><strong style="white-space:nowrap;">District :</strong></td>
                    <td><?php $mbbt->p("district"); ?></td>
                </tr>
                <tr> 
                    <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CITY ?>:</strong></td>
                    <td><?php $mbbt->p("city"); ?></td>
                </tr>
                <tr> 
                    <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_STATE ?>:</strong></td>
                    <td><?php $mbbt->p("state_name"); ?></td>
                </tr>
                <tr> 
                    <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP ?>:</strong></td>
                    <td><?php $mbbt->p("zip"); ?></td>
                </tr>
                <tr> 
                    <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY ?>:</strong></td>
                    <td><?php $mbbt->p("country_name"); ?></td>
                </tr>
                <tr> 
                    <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?>:</strong></td>
                    <td><?php $mbbt->p("phone_1"); ?></td>
                </tr>
                <tr> 
                    <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE2 ?>:</strong></td>
                    <td><?php $mbbt->p("phone_2"); ?></td>
                </tr>
                <tr> 
                    <td><strong style="white-space:nowrap;"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE3 ?>:</strong></td>
                    <td><?php $mbbt->p("phone_3"); ?></td>
                </tr>
            </table>
        </td></tr></table>
    <?php
}
?>


    <?php if($order_id != $lastElement) { ?>
        <div class="hr_order"></div>
    <?php } ?>
<style>
    .hr_order {
        width: 100%;
        background-color: black;
        height: 1px;
    }
    @media print {
    .hr_order {page-break-after: always;}
}
</style>
        <?php
}

?>
   