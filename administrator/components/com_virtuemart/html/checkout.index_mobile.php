<?php

echo '<PRE>';
    echo 'HEEEEEEEEEEEELO';
echo '</PRE>';


$query = "SELECT * FROM `jos_postcode_warehouse`";
$database->setQuery($query);
$rows = $database->loadObjectList();

$warehouses_array = array();

if (count($rows)) {
    foreach ($rows as $item) {
        $warehouses_array[] = '[' . $item->postal_code . ', ' . $item->m_purchase . ']';
    }
}

?>

<script type="text/javascript">
    oForm = document.adminForm;
    var warehouses_array = [<?php echo implode(',', $warehouses_array); ?>];

    var bDefault = 1;
    var nExtraDayMonth = 0;
    var nDaysValid = "<?php echo date("d"); ?>"; //0;
    var nMonthsValid = "<?php echo date("m"); ?>"; //0;
    var nYearsValid = "<?php echo date("Y"); ?>"; //0;
    var aOption = new Array();
    var aUnAvailableDate = new Array();
    var aUnAvailableItem = new Array();
    //var nIndex = parseFloat(oForm.daynow.value);
    var sEnterDeliverDay = "<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_DELIVERYDATE5; ?>";
    var sEnterDeliverMonth = "<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_DELIVERYDATE6; ?>";
    var sDeliverExtra = "<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_DELIVERYDATE7; ?>";
    var sDeliverExtra2 = "<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_DELIVERYDATE77; ?>";
    var sNoDeliveryService = "<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_DELIVERYDATE8; ?>";
    var sUnAvailableDate = "<?php echo $sUnAvailableDate; ?>";
    var sSpecialDeliver = "<?php echo $sSpecialDeliver; ?>";
    var sStateTax = "<?php echo $sStateTax; ?>";
    var nDeliverFeeExtraSameDay = "<?php echo $aOptionParam[2]; ?>";
    var sDeliverFeeExtraSameDay = "<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_DELIVERYDATE9; ?>";
    var sInValidDateTime = "<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_DELIVERYDATE4; ?>";
    var sEmptyCart = "<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_ERR_EMPTY_CART; ?>";
    var sCannotDeliver = "<?php echo $VM_LANG->_VM_CANNOT_DELIVER; ?>";
    var nDeliverExtraPrice = 0;
    var nSpecialDeliverExtraPrice = 0;
    var bIsValidDate = 1;
    var bIsValidZipCodeFirst = "<?php echo $bIsValidZipCodeFirst; ?>";
    var bIsValidZipCode = 1;
    var sDeliverMethodFee = "<?php echo $aInfomation['shipping_method_list_fee']; ?>";
    var nDayNow = "<?php echo date("d"); ?>";
    var nMonthNow = "<?php echo date("m"); ?>";
    var nYearNow = "<?php echo date("Y"); ?>";

    $j(document).ready(function ()
    {
        //changDeliver(oForm.zip_checked.value, $j("input[name='current_state_tax']").val(), $j('#post_code_zip_now').val());
    });
</script>

<style>
    .checkout_mobile_step {
        margin-bottom: 10px;
    }
    .checkout_mobile_title {
        padding: 5px 0px 5px 15px;
        line-height: 36px;
        height: 40px;
        min-width: 10px;
        background-color: #f1e1ed;
        color: #52214c;
        font-size: 15px;
        font-weight: bold;
        margin-left: 10px;
        margin-right: 10px;
    }
    .checkout_mobile_title.active:hover {
        cursor: pointer; 
    }
    .checkout_mobile_div {
        display: none;
        border-bottom: 1px solid #f1e1ed;
        border-left: 1px solid #f1e1ed;
        border-right: 1px solid #f1e1ed;
        padding: 10px;
        margin-left: 15px;
        margin-right: 10px;
        font-size: 15px;
        color: #8e828b;
    }
    .checkout_mobile_div.active {
        display: block;
    }
    #customer_occasion, #delivery_date_2 {
        font-size: 15px;
    }
</style>

<script type="text/javascript">
    $j(document).ready(function ()
    {
        
        $j('#customer_occasion').change(function(event) {
            if ($j(this).val() != '00000') {
                $j('#step_2_title').addClass('active');
                $j('#step_2_div').show();
                
                $j('html, body').animate({ scrollTop: $j('#checkout_mobile_step_2').offset().top }, 500);
            }
            else {
                $j('#step_2_title').removeClass('active');
                $j('#step_2_div').hide();
            }
        });
        
    });
</script>

<div id="checkout_mobile_step_1" class="checkout_mobile_step">
    <div id="step_1_title" class="checkout_mobile_title active">
        Step 1
    </div>
    <div id="step_1_div" class="checkout_mobile_div active">
        Occassion:
        <?php $ps_html->list_user_occasion('customer_occasion', 'id="customer_occasion"'); ?>
    </div>
</div>

<div id="checkout_mobile_step_2" class="checkout_mobile_step">
    <div id="step_2_title" class="checkout_mobile_title">
        Step 2
    </div>
    <div id="step_2_div" class="checkout_mobile_div">
        Delivery date:
        <div style="display:none;" id="selectDeliveryOption" >&nbsp;</div>
        <input type="text" name="delivery_date_2" id="delivery_date_2" readonly="readonly" maxlength="10" />
        <input id="btnSelectDeliveryOption" class="new_checkout_register_button" type="button" value="Select"/>
    </div>
</div>

<div id="checkout_mobile_step_3" class="checkout_mobile_step">
    <div id="step_3_title" class="checkout_mobile_title">
        Step 3
    </div>
    <div id="step_3_div" class="checkout_mobile_div">
        Card message:
        <textarea title="<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_NOTE ?>" cols="40" rows="3" name="card_msg"></textarea>
    </div>
</div>

<div id="checkout_mobile_step_4" class="checkout_mobile_step">
    <div id="step_4_title" class="checkout_mobile_title">
        Step 4
    </div>
    <div id="step_4_div" class="checkout_mobile_div">
        Instructions and comments 
    </div>
</div>

<div id="checkout_mobile_step_5" class="checkout_mobile_step">
    <div id="step_5_title" class="checkout_mobile_title">
        Step 5
    </div>
    <div id="step_5_div" class="checkout_mobile_div">
    </div>
</div>

<div id="checkout_mobile_step_6" class="checkout_mobile_step">
    <div id="step_6_title" class="checkout_mobile_title">
        Step 6
    </div>
    <div id="step_6_div" class="checkout_mobile_div">
    </div>
</div>

<input type="hidden" id="current_state_tax" name="current_state_tax" value="" />
