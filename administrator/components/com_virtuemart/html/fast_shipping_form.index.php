<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: checkout.index.php,v 1.5.2.3 2006/04/27 19:35:52 soeren_nb Exp $
 * @package VirtueMart
 * @subpackage html
 * @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU Gen eral Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */

//print_r($_SESSION['coupon_discount']);
mm_showMyFileName(__FILE__);
global $database, $mm_action_url, $my, $cur_template,$mosConfig_show_compare_at_price,$mosConfig_offset;



if (isset($_REQUEST['shippingParams'])) {
    $shippingParams = str_rot13($_REQUEST['shippingParams']);
    $shippingParamsArr = explode(";", $shippingParams);
    if ($shippingParamsArr[1]) {
        $_SESSION['checkout_ajax']['thankyou'] = md5('thankyou' . $shippingParamsArr[1]);
        $_SESSION['checkout_ajax']['thankyou_order_id'] = $shippingParamsArr[1];
        $_SESSION['checkout_ajax']['user_id'] = $shippingParamsArr[3];
    }

}

?>


<style>

    .autocomplete-form-group {
        clear: both;
        padding: 0 15px;
    }

    .address_type_hidden, .top_1, .top_3, .breadcrumbs_wrapper, .bottom_1, .bottom_2, .bottom_3,#calculate {
        display: none;
    }

    .clear-left {
        clear: left;
    }

    div.update_shipping_info_wrapper {
        display: block;
    }

    .clear-left span.title {
        margin-bottom: 5px;
        display: block;
    }
</style>
<div class="checkout_wrapper">

    <?php

    echo '<script src="/templates/'.$cur_template.'/js/googleaddress.js?ref=10"></script>';
    echo '<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">';
    echo '<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>';
    //redirect on TYP if virtual product in cart
    if (isset($_SESSION['checkout_ajax']['virtual'])) {
        mosRedirect("/purchase-thankyou/");
        unset($_SESSION['checkout_ajax']['virtual']);
        exit;
    }

    ?>
        <div class="container shipping_wrapper">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-8 offset-md-2 update_shipping_info_wrapper">
                    <h3 class="text-center"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_BILLING_LBL_STEP3; ?></h3>
                    <?php
                    $required_fields = array('delivery_date','first_name', 'last_name', 'street_name', 'street_number', 'city', 'zip', 'country', 'state', 'phone_1');
                    $shopper_fields = array();


                    $shopper_fields['first_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME;
                    $shopper_fields['last_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_LAST_NAME;
                    $shopper_fields['phone_1'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE;

                    $shopper_fields['user_email'] = $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL;
                    $shopper_fields['company'] = $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY;
                    $shopper_fields['suite'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_SUITE;
                    $shopper_fields['street_number'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STREET_NUMBER;
                    $shopper_fields['street_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STREET_NAME;
                    $shopper_fields['city'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_CITY;
                    $shopper_fields['zip'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_ZIP;
                    $shopper_fields['state'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STATE;
                    $shopper_fields['country'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_COUNTRY;
                    $shopper_fields['delivery_date'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_DELIVERY_DATE;
                    $query = "SELECT * FROM `jos_vm_orders` 
                                    WHERE `order_id`=" . $_SESSION['checkout_ajax']['thankyou_order_id'];

                    $database->setQuery($query);
                    $order_obj = false;
                    $database->loadObject($order_obj);


                    if($my->id > 0) {

                    $checkSimilarAddressQuery = "SELECT * FROM `jos_vm_user_info` WHERE `address_type` = 'ST' AND user_id=" . (int) $my->id;
                    $database->setQuery($checkSimilarAddressQuery);
                    $checkSimilarAddress = $database->loadObjectList();

                        if ($checkSimilarAddress && count($checkSimilarAddress) > 1) {
                            echo '<div class="shipping_addresses_wrapper"><table class="table shipping_addresses mb-4">';
                            foreach ($checkSimilarAddress as $row) {
                                echo '<tr user_info_id="' . $row->user_info_id . '">
                                <td>
                                    <label class="radio_container"><input type="radio"  name="radio_shipping_address" value="' . $row->user_info_id . '" ' . ($_SESSION['checkout_ajax']['user_info_id'] == $row->user_info_id ? 'checked' : '') . '><span class="checkmark"></span></label>
                                </td>
                                <td>
                                    <div class="data">
                                        <span>Name:</span> ' . $row->first_name . ' ' . $row->middle_name . ' ' . $row->last_name . '
                                        <br/>
                                        <span>Address:</span> ' . $row->suite . ' ' . $row->street_number . ' ' . $row->street_name . ', ' . $row->city . ', ' . $row->zip . ', ' . $row->state . ', ' . $row->country . '
                                    </div>
                                </td>
                            </tr>';
                            }
                            echo '</table></div>';
                         }

                    }

                    ?>

                    <form role="form" action="index.php" method="post" id="update_shipping_info_form">
                        <div class="row">
                        <?php
                        foreach ($shopper_fields as $key => $value) {
                            ?>
                            <div class="form-group  col-md-6  col-sm-12">
                                <label for="shipping_info_<?php echo $key; ?>"><?php echo $value; ?><?php echo in_array($key, $required_fields) ? '*' : ''; ?>
                                    :</label>
                                <?php
                                Switch ($key) {
                                    case 'delivery_date':
                                        echo '<input type="text" name="shipping_info_delivery_date"  class="form-control" id="shipping_info_delivery_date"/>';
                                        break;
                                    case 'state':
                                        echo $ps_html->list_states("shipping_info_state", '', '13', "id='shipping_info_state' class='form-control'", false);
                                        break;
                                    case 'country':
                                        echo "<input type='text' class=\"form-control\" readonly value='Australia'>";
                                        break;
                                    case 'user_email':
                                        ?>
                                        <input type="email" autocomplete="new-password" class="form-control"
                                               id="shipping_info_<?php echo $key; ?>"
                                               name="shipping_info_<?php echo $key; ?>"
                                               value="" placeholder="">
                                        <?php
                                        break;

                                    case 'title':
                                        echo $ps_html->list_user_title('', 'name="shipping_info_title" autocomplete="new-password"  id="shipping_info_title"');
                                        break;

                                    case 'zip':
                                        ?>
                                        <input type="text" autocomplete="new-password" class="form-control"
                                               id="shipping_info_zip"  maxlength="4" name="shipping_info_zip"
                                               value="" placeholder="">
                                        <?php
                                        break;
                                    default:
                                        ?>
                                        <input type="text" autocomplete="new-password" class="form-control"
                                               id="shipping_info_<?php echo $key; ?>"
                                               name="shipping_info_<?php echo $key; ?>"
                                               value="" placeholder="">
                                        <?php
                                        break;
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>

                        <script type="text/javascript">
                            var shipping_info_fields = <?php echo json_encode($shopper_fields); ?>;
                            var shipping_info_required_fields = <?php echo json_encode($required_fields); ?>;
                        </script>
                        <div class="form-group col-md-6 col-sm-12 clear-left">
                            <span class="title h"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_OCCASION; ?>:</span>
                            <?php $ps_html->list_user_occasion('customer_occasion', 'id="customer_occasion"', ($order_obj->customer_occasion)??''); ?>
                        </div>
                        <div class="form-group col-md-6 col-sm-12 clear-left">
                            <span class="title h"
                                  id="card_msg_title"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_NOTE ?>:</span>
                            <textarea class="form-control"
                                      title="<?php echo strip_tags($VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_NOTE) ?>" rows="4"
                                      name="card_msg" id="card_msg"><?php echo ($order_obj->customer_note)??''; ?></textarea>
                        </div>
                        <div class="form-group col-md-6 col-sm-12 clear-left">
                            <span class="title h"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_SIGNATURE ?>:</span>
                            <textarea class="form-control"
                                      title="<?php echo strip_tags($VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_SIGNATURE) ?>" rows="3"
                                      name="signature"
                                      id="signature"><?php echo ($order_obj->customer_signature)??''; ?></textarea>
                        </div>
                        <div class="form-group col-md-6 col-sm-12 clear-left">
                            <span class="title h"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_COMMENTS ?>:</span>
                            <textarea class="form-control"
                                      title="<?php echo strip_tags($VM_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_COMMENTS) ?>" rows="3"
                                      name="card_comment"
                                      id="card_comment"><?php echo ($order_obj->customer_comments)??''; ?></textarea>
                        </div>
                        <div class="clearfix"></div>
                            <div class="col-12">
                                <button type="submit" class="btn shipping_address_btn"
                                        onclick="return checkUpdateShippingInfoFormFast(event);">COMPLETE ORDER
                                </button>
                            </div>
                        <input type="hidden" id="shipping_info_user_info_id" name="shipping_info_user_info_id"
                               value="<?php echo $_SESSION['checkout_ajax']['user_info_id']??''; ?>"/>
                        <input type="hidden" id="order_id" name="order_id"
                               value="<?php echo $_SESSION['checkout_ajax']['thankyou_order_id']; ?>"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <script>
        function loadScript(url, callback) {
            var script = document.createElement('script');
            script.setAttribute('type', 'text/javascript');
            if (typeof callback === 'function') {
                script.addEventListener('load', callback, false);
            }
            script.setAttribute('src', url);
            document.body.appendChild(script);
        }

        window.addEventListener(
            'load',
            function () {
                loadScript('https://maps.googleapis.com/maps/api/js?key=AIzaSyDFRP59njojtx0eXlHmvYyGAtWZFwvRSLU&libraries=places&callback=initAutocomplete&language=en')
            },
            false);
        $('#shipping_info_company').change(function () {
            if ($(this).val() != '') {
                $('#shipping_info_address_type2').val('Business').change()
            } else {
                $('#shipping_info_address_type2').val('Home/Residence').change()
            }
        })



            var disabled_dates = [];
            $("#shipping_info_delivery_date").datepicker({
                dateFormat: "dd-mm-yy",
                minDate: 0,
                onChangeMonthYear: function (y,m) {
                    disabled_dates = getValidCalendarDates(m+'/01/'+y)
                },
                beforeShowDay: function(date)
                {
                    var string = jQuery.datepicker.formatDate('dd-mm-yy', date);
                    return [disabled_dates.indexOf(string) == -1];
                },
                beforeShow: function(){
                    disabled_dates = getValidCalendarDates()
                }
            });

    </script>

</div>
