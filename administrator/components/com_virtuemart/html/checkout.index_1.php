<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

mm_showMyFileName(__FILE__);
require_once( CLASSPATH . "ps_checkout.php" );
global $database, $mm_action_url, $my;

global $VM_LANG;
?>


<script type="text/javascript">
    sVM_EDIT = "<?php echo $VM_LANG->_VM_EDIT; ?>";
    sVM_DELETE = "<?php echo $VM_LANG->_VM_DELETE; ?>";
    sVM_DELETING = "<?php echo $VM_LANG->_VM_DELETING; ?>";
    sVM_UPDATING = "<?php echo $VM_LANG->_VM_UPDATING; ?>";
    sVM_ADD_ADDRESS = "<?php echo $VM_LANG->_VM_ADD_ADDRESS; ?>";
    sVM_UPDATE_ADDRESS = "<?php echo $VM_LANG->_VM_UPDATE_ADDRESS; ?>";

    sVM_ADD_PRODUCT_SUCCESSFUL = "<?php echo $VM_LANG->_VM_ADD_PRODUCT_SUCCESSFUL; ?>";
    sVM_ADD_PRODUCT_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_ADD_PRODUCT_UNSUCCESSFUL; ?>";
    sVM_CONFIRM_DELETE = "<?php echo $VM_LANG->_VM_CONFIRM_DELETE; ?>";
    sVM_DELETE_SUCCESSFUL = "<?php echo $VM_LANG->_VM_DELETE_SUCCESSFUL; ?>";
    sVM_DELETE_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_DELETE_UNSUCCESSFUL; ?>";
    sVM_CONFIRM_QUANTITY = "<?php echo $VM_LANG->_VM_CONFIRM_QUANTITY; ?>";
    sVM_UPDATE_CART_ITEM_SUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_CART_ITEM_SUCCESSFUL; ?>";
    sVM_UPDATE_CART_ITEM_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_CART_ITEM_UNSUCCESSFUL; ?>";

    sVM_CONFIRM_FIRST_NAME = "<?php echo $VM_LANG->_VM_CONFIRM_FIRST_NAME; ?>";
    sVM_CONFIRM_LAST_NAME = "<?php echo $VM_LANG->_VM_CONFIRM_LAST_NAME; ?>";
    sVM_CONFIRM_ADDRESS = "<?php echo $VM_LANG->_VM_CONFIRM_ADDRESS; ?>";
    sVM_CONFIRM_CITY = "<?php echo $VM_LANG->_VM_CONFIRM_CITY; ?>";
    sVM_CONFIRM_ZIP_CODE = "<?php echo $VM_LANG->_VM_CONFIRM_ZIP_CODE; ?>";
    sVM_CONFIRM_VALID_ZIP_CODE = "<?php echo $VM_LANG->_VM_CONFIRM_VALID_ZIP_CODE; ?>";
    sVM_CONFIRM_COUNTRY = "<?php echo $VM_LANG->_VM_CONFIRM_COUNTRY; ?>";
    sVM_CONFIRM_STATE = "<?php echo $VM_LANG->_VM_CONFIRM_STATE; ?>";
    sVM_CONFIRM_PHONE_NUMBER = "<?php echo $VM_LANG->_VM_CONFIRM_PHONE_NUMBER; ?>";
    sVM_CONFIRM_EMAIL = "<?php echo $VM_LANG->_VM_CONFIRM_EMAIL; ?>";
    sVM_CONFIRM_ADD_NICKNAME = "<?php echo $VM_LANG->_VM_CONFIRM_ADD_NICKNAME; ?>";

    sVM_DELETING_DELIVER_INFO = "<?php echo $VM_LANG->_VM_DELETING_DELIVER_INFO; ?>";
    sVM_DELETE_DELIVER_INFO_SUCCESSFUL = "<?php echo $VM_LANG->_VM_DELETE_DELIVER_INFO_SUCCESSFUL; ?>";
    sVM_DELETE_DELIVER_INFO_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_DELETE_DELIVER_INFO_UNSUCCESSFUL; ?>";
    sVM_UPDATING_DELIVER_INFO = "<?php echo $VM_LANG->_VM_UPDATING_DELIVER_INFO; ?>";
    sVM_UPDATE_DELIVER_INFO_SUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_DELIVER_INFO_SUCCESSFUL; ?>";
    sVM_UPDATE_DELIVER_INFO_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_DELIVER_INFO_UNSUCCESSFUL; ?>";
    sVM_ADD_DELIVER_INFO_SUCCESSFUL = "<?php echo $VM_LANG->_VM_ADD_DELIVER_INFO_SUCCESSFUL; ?>";
    sVM_ADD_DELIVER_INFO_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_ADD_DELIVER_INFO_UNSUCCESSFUL; ?>";
    sVM_LOAD_DELIVER_INFO_FORM_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_LOAD_DELIVER_INFO_FORM_UNSUCCESSFUL; ?>";

    sVM_UPDATING_BILLING_INFO = "<?php echo $VM_LANG->_VM_UPDATING_BILLING_INFO; ?>";
    sVM_UPDATE_BILLING_INFO_SUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_BILLING_INFO_SUCCESSFUL; ?>";
    sVM_UPDATE_BILLING_INFO_UNSUCCESSFUL = "<?php echo $VM_LANG->_VM_UPDATE_BILLING_INFO_UNSUCCESSFUL; ?>";
    sVM_CONFIRM_ADDRESS_TYPE_2 = "<?php echo $VM_LANG->_VM_CONFIRM_ADDRESS_TYPE_2; ?>";
    sVM_VALIDATING_CART = "<?php echo $VM_LANG->_VM_VALIDATING_CART; ?>";
    sVM_VALIDATING_CART2 = "<?php echo $VM_LANG->_VM_VALIDATING_CART2; ?>";
    sVM_VALIDATING_CART3 = '<?php echo $VM_LANG->_VM_VALIDATING_CART3; ?>';

</script>




<?php

function listMonth($list_name, $selected_item = "", $extra = "") {
    $sString = "";
    $list = array("" => "Month",
        "01" => "January",
        "02" => "February",
        "03" => "March",
        "04" => "April",
        "05" => "May",
        "06" => "June",
        "07" => "July",
        "08" => "August",
        "09" => "September",
        "10" => "October",
        "11" => "November",
        "12" => "December");

    $sString = "<select name='{$list_name}' {$extra}>";
    foreach ($list as $key => $value) {
        $sString .= "<option value='{$key}'>{$value}</option>";
    }

    $sString .= "</select>";
    return $sString;
}

function listYear($list_name, $selected_item = "", $extra = "", $max = 7, $from = 2009, $direct = "up") {
    $sString = "";

    $sString = "<select name='{$list_name}' {$extra}>";
    for ($i = 0; $i < $max; $i++) {
        $value = $from + $i;
        $text = $from + $i;
        if ($selected_item == $value) {
            $sString .= "<option selected value='" . $value . "'>" . $text . "</option>";
        } else {
            $sString .= "<option value='" . $value . "'>" . $text . "</option>";
        }
    }

    $sString .= "</select>";
    return $sString;
}

echo '<h3 class="new_checkout_header" style="width:97%;">EASY CHECKOUT</h3>';
$my->id = true;
$show_basket = true;
include(PAGEPATH . 'basket.php');

$sql = "SELECT purchase FROM jos_vm_purchase ";
$database->setQuery($sql);
$purchase = $database->loadResult();
?>


<form action="<?php echo SECUREURL ?>index.php" method="post" name="adminForm" id="adminForm">
    <?php
    $sProductId = "";
    $sProductQuantity = "";
    $sProductCoupon = "";
    foreach ($_SESSION['cart'] as $item) {
        if (!empty($item['product_id'])) {
            $sProductId .= $item['product_id'] . ",";
            if (!empty($item['product_coupon_discount'])) {
                $sProductCoupon .= $item['product_coupon_discount'] . ",";
            }
        }

        if (!empty($item['quantity'])) {
            $sProductQuantity .= $item['quantity'] . ",";
        }
        if (!empty($item['select_bouquet'])) {
            $sProductSelectBouquet .= $item['select_bouquet'] . ",";
        }
    }

    $sProductId = substr($sProductId, 0, strlen($sProductId) - 1);
    $sProductSelectBouquet = substr($sProductSelectBouquet, 0, strlen($sProductSelectBouquet) - 1);
    $sProductQuantity = substr($sProductQuantity, 0, strlen($sProductQuantity) - 1);
    $sProductCoupon = substr($sProductCoupon, 0, strlen($sProductCoupon) - 1);

    $sql = "SELECT * FROM #__vm_product_options WHERE product_id IN ($sProductId)";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();

    $no_tax_product_id = "";
    $no_tax_product_price = 0;
    $no_delivery_product_id = "";
    if (count($rows)) {
        foreach ($rows as $row) {
            if (!empty($row->no_delivery))
                $no_delivery_product_id .= $row->product_id . ",";
            if (!empty($row->no_tax))
                $no_tax_product_id .= $row->product_id . ",";

            foreach ($_SESSION['cart'] as $item) {
                if (!empty($item['product_id']) && !empty($row->no_tax) && $item['product_id'] == $row->product_id) {
                    $no_tax_product_price += ( floatval($item['price']) * intval($item['quantity']) );
                }
            }
        }
    }

    if (!empty($no_delivery_product_id))
        $no_delivery_product_id = substr($no_delivery_product_id, 0, strlen($no_delivery_product_id) - 1);
    if (!empty($no_tax_product_id))
        $no_tax_product_id = substr($no_tax_product_id, 0, strlen($no_tax_product_id) - 1);
//    echo "<pre>";print_r($my);die;
    ?>

    <input type="hidden" name="product_number_items" id="product_number_items" value="<?php echo count($_SESSION['cart']); ?>" />
    <input type="hidden" name="no_delivery_product_id" id="no_delivery_product_id" value="<?php echo $no_delivery_product_id; ?>" />
    <input type="hidden" name="no_tax_product_id" id="no_tax_product_id" value="<?php echo $no_tax_product_id; ?>" />
    <input type="hidden" name="no_tax_product_price" id="no_tax_product_price" value="<?php echo $no_tax_product_price; ?>" />
    <input type="hidden" name="Itemid" value="" />
    <input type="hidden" name="zone_qty" value="<?php echo $zone_qty ?>" />
    <input type="hidden" name="customer_occasion" value="<?php echo $customer_occasion ?>" />
    <input type="hidden" name="customer_note" value="<?php echo $customer_note ?>" />
    <input type="hidden" name="customer_signature" value="<?php echo $customer_signature ?>" />
    <input type="hidden" name="select_bouquet" value="<?php echo $sProductSelectBouquet; ?>" />
    <input type="hidden" name="customer_comments" value="<?php echo $customer_comments ?>" />
    <input type="hidden" name="find_us" value="<?php echo $find_us ?>" />
    <input type="hidden" name="delivery_day" value="<?php echo $delivery_day ?>" />
    <input type="hidden" name="delivery_month" value="<?php echo $delivery_month ?>" />
    <input type="hidden" name="total_price" value="" />
    <input type="hidden" name="product_id_string" value="<?php echo $sProductId; ?>" />
    <input type="hidden" name="quantity_string" value="<?php echo $sProductQuantity; ?>" />
    <input type="hidden" name="product_coupon_string" value="<?php echo $sProductCoupon; ?>" />
    <input type="hidden" name="vendor_currency_string" value="<?php echo $_SESSION['vendor_currency']; ?>" />

    <input type="hidden" name="coupon_code" value="<?php echo isset($_SESSION['coupon_code']) ? $_SESSION['coupon_code'] : ""; ?>" />
    <input type="hidden" name="coupon_code_type" value="<?php echo isset($_SESSION['coupon_code_type']) ? $_SESSION['coupon_code_type'] : ""; ?>" />
    <input type="hidden" id="coupon_value" name="coupon_value" value="<?php echo isset($_SESSION['coupon_value']) ? $_SESSION['coupon_value'] : ""; ?>" />
<?php
$coupon_code_string = (!empty($_SESSION['coupon_code']) ? $_SESSION['coupon_code'] : "");
if (strpos($coupon_code_string, "PC-") === false) {
    $discount_shipping = 0;
} else {
    $discount_shipping = 1;
}
?>
    <input type="hidden" name="discount_shipping" id="discount_shipping" value="<?php echo $discount_shipping; ?>" />
    <input type="hidden" name="free_shipping" id="free_shipping" value="0" />






    <input type="hidden" name="page" value="checkout.index" />
    <input type="hidden" name="func" value="checkoutProcess" />
    <input type="hidden" id="current_state_tax" name="current_state_tax" value="" />
    <input type="hidden" id="wa_purchase" value="<?php echo $purchase; ?>" />
    <input type="hidden" id="post_code_zip_now" value="<?php echo $real_zip_now; ?>" />
         
    <table border="0" cellspacing="0" cellpadding="5" width=100%"  class="checkout-standart">
        <tr class="sectiontableheader">
            <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_SHIPPING_LBL ?></th>
        </tr>
        <tr>
            <td colspan="2">
<?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_SHIPPING_LBL_ADDR; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div id="msgReportDeliverInfo" class="msgReport" style="text-align:left;display:none;">&nbsp;</div>
                <div id="listDeliverInfo">
<?php

//LEGACY TICKETS
function get_data1($url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}



    if (!$_SESSION['legacy_id']) {
            $user_name = "Legacy_".time();
            $user_email = "Legacy_".time()."@bloomex.ca";
            $query = "INSERT INTO #__users( name, username, email, usertype, block, gid ) VALUES( '{$user_name}', '{$user_name}', '{$user_email}' , 'Registered' , 0, 18 )";
            $database->setQuery($query);
            $database->query();
            $_SESSION['legacy_id'] = $user_id  = $database->insertid();
            $_SESSION['legacy_email'] = $user_email ;


            $query = "INSERT INTO #__core_acl_aro( section_value, value, order_value, name, hidden ) VALUES( 'users', {$user_id}, 0, '{$user_name}', 0 )";
            $database->setQuery($query);
            $database->query();
            $aro_id = $database->insertid();

            $query = "INSERT INTO #__core_acl_groups_aro_map( group_id, section_value, aro_id ) VALUES( 18, '', {$aro_id} )";
            $database->setQuery($query);
            $database->query();
            
            
             $user_info_id = md5($user_id . time());
             
              $query = "INSERT INTO #__vm_user_info( user_info_id,
															user_id,
															address_type,
															address_type_name,
															company,
															last_name,
															first_name,
															middle_name,
															phone_1,
                                                                                                                        phone_2,
															fax,
															address_1,
															address_2,
															city,
															state,
															country, zip,
															user_email,
                                                                                                                        extra_field_1,
															perms, suite, street_number, street_name )
						   	   VALUES(  '" . $user_info_id . "',
						   	   			{$user_id},
						   	   			'BT',
						   	   			'-default-',
						   	   			'{$user_name}',
						   	   			'{$user_name}',
						   	   			'{$user_name}',
						   	   			'',
						   	   			'',
                                                                                '',
						   	   			'',
						   	   			'',
						   	   			' ',
						   	   			'',
						   	   			'',
						   	   			'',
						   	   			'',
						   	   			'',
                                                                                '',
										'shopper',
                                                                                '', '','')";
        $database->setQuery($query);
        $database->query();

            
            
            
        }else{
           $user_id  =$_SESSION['legacy_id'];
            $user_email =$_SESSION['legacy_email'];
        }     
?>
    <input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
    <input type="hidden" name="user_name" value="<?php echo $user_email;  ?>" />
    <input type="hidden" name="account_email" value="<?php echo $user_email; ?>" />
<?php
      include_once 'add_shipping_address.php';
function addFuneralDeliveryAddress($fhid = 0, $cobrand = "", $pid = 0,$user_id) {
    global $database;

    if ($fhid) {
        if (empty($cobrand) && empty($pid)) {
            $url = "http://www.legacy.com/webservices/ns/FuneralInfo.svc/GetFuneralInfoJson?fhid=$fhid";
        } else {
            $url = "http://www.legacy.com/webservices/ns/FuneralInfo.svc/GetFuneralInfoJson?fhid=$fhid&cobrand=$cobrand&pid=$pid";
        }

        $aData = json_decode(get_data1($url));

        //print_r($aData);
        $sObituaryFHPhone = !empty($aData->FuneralHome->FHPhone) ? htmlentities(trim($aData->FuneralHome->FHPhone), ENT_QUOTES) : "";
        $sObituaryFHKnownBy1 = !empty($aData->FuneralHome->FHKnownBy1) ? htmlentities(trim($aData->FuneralHome->FHKnownBy1), ENT_QUOTES) : "";

        if (!empty($aData->Obituary->FullName)) {
            $aObituaryFullName = explode(" ", $aData->Obituary->FullName, 2);
            $sObituaryFN = htmlentities(trim($aObituaryFullName[0]), ENT_QUOTES);
            $sObituaryLN = htmlentities(trim($aObituaryFullName[1]), ENT_QUOTES);
        } else {
            $sObituaryFN = $sObituaryFHKnownBy1; // Neu $cobrand = "", $pid = 0 set First Name = FuneralHome Address
            $sObituaryLN = "";
        }

        $sObituaryFHAddress1 = !empty($aData->FuneralHome->FHAddress1) ? htmlentities(trim($aData->FuneralHome->FHAddress1), ENT_QUOTES) : "";
        $legacy_street_number = '';
        $legacy_street_name = '';
        if ($sObituaryFHAddress1 != "") {
            $legacy_street_number= substr($sObituaryFHAddress1, 0, strpos($sObituaryFHAddress1, ' '));
            $legacy_street_name= substr($sObituaryFHAddress1, strlen($legacy_street_number));
            $sObituaryFHAddress1 = "$sObituaryFHKnownBy1, $sObituaryFHAddress1";
        }      
        $sObituaryFHAddress2 = !empty($aData->FuneralHome->FHAddress2) ? htmlentities(trim($aData->FuneralHome->FHAddress2), ENT_QUOTES) : "";
        if ($sObituaryFHAddress2 != "") {
            $sObituaryFHAddress2 = "$sObituaryFHKnownBy1, $sObituaryFHAddress2";
        }
        $sObituaryFHCity = !empty($aData->FuneralHome->FHCity) ? htmlentities(trim($aData->FuneralHome->FHCity), ENT_QUOTES) : "";
        $sObituaryFHState = !empty($aData->FuneralHome->FHState) ? htmlentities(trim($aData->FuneralHome->FHState), ENT_QUOTES) : "";
        $sObituaryFHZip = !empty($aData->FuneralHome->FHZip) ? htmlentities(trim($aData->FuneralHome->FHZip), ENT_QUOTES) : "";

        if(strlen($sObituaryFHState)>2){
            
        $sql = " SELECT state_2_code
                    FROM jos_vm_state
                    WHERE state_name = '$sObituaryFHState' AND country_id='13' ";
        $database->setQuery($sql);
        $sObituaryFHState = $database->loadResult();

        }
        
        $hash_secret = "VirtueMartIsCool";

        $sql = " SELECT COUNT(*)
									FROM #__vm_user_info
									WHERE address_type = 'ST' AND user_id='$user_id' AND last_name = '$sObituaryLN' AND first_name = '$sObituaryFN' AND
									( address_1 = '$sObituaryFHAddress1' OR address_2 = '$sObituaryFHAddress2' ) AND
									city = '$sObituaryFHCity' AND state = '$sObituaryFHState' AND zip = '$sObituaryFHZip' ";
        $database->setQuery($sql);
        $bExist = $database->loadResult();
        //echo $sql . "<br/>$bExist<br/>";

        if (!$bExist) {
            $sql = "INSERT INTO #__vm_user_info(user_info_id, user_id,address_type, last_name,first_name, phone_1,address_1, address_2,city,state,street_number,street_name,country,zip, extra_field_3,address_type2,cdate,mdate,company)
									     VALUES ('" . md5(uniqid($hash_secret)) . "', " . $user_id. ", 'ST', '$sObituaryLN', '$sObituaryFN', '$sObituaryFHPhone',
									   			'$sObituaryFHAddress1',  '$sObituaryFHAddress2', '$sObituaryFHCity', '$sObituaryFHState','$legacy_street_number','$legacy_street_name',
									   			'AUS', '$sObituaryFHZip', 'FuneralInfo|$fhid|$cobrand|$pid', 'B', '" . time() . "', '" . time() . "', '" . $sObituaryFHKnownBy1 . "') ";

            //echo $sql . "<br/><br/>";;
            $database->setQuery($sql);
            $database->query();
        }
       
    }
}



    $fhid = $_COOKIE['funeral_FHID'];
    $pid = $_COOKIE['funeral_PID'];
    $cobrand = $_COOKIE['funeral_COBRAND'];

    addFuneralDeliveryAddress($fhid, $cobrand, $pid,$user_id);

    setcookie("funeral_FHID", "", time() - 36000);
    setcookie("funeral_PID", "", time() - 36000);
    setcookie("funeral_COBRAND", "", time() - 36000);


//echo "<pre>";print_r($my);die;
$sShippingAddressRadio = $ps_checkout->ship_to_addresses_radio($user_id, "ship_to_info_id", $ship_to_info_id);
$aShippingAddressRadio = explode("[--2--]", $sShippingAddressRadio);




if ($aShippingAddressRadio[0] == "error") {
    $shippingAddressRadio = false;
} else {
    $shippingAddressRadio = true;
}
echo $aShippingAddressRadio[1];

if ($aShippingAddressRadio[2] == "none") {
    $bIsValidZipCodeFirst = 1;
} else {
    $bIsValidZipCodeFirst = intval($aShippingAddressRadio[2]);
}


?>
                </div>
                <div class="msgReport" id="msgReportShippingAddressRadio" style="display: block;text-align:left;"><?php echo $VM_LANG->_VM_NO_SHIPPING_ADDRESS; ?></div>
            </td>
        </tr>

    </table>
    <br/><br/>

    
<?php


echo $ps_html->dynamic_state_lists("country_shipping", "state_shipping", "AUS", "", 'country_shipping', true);
echo "<noscript>\n";
$ps_html->list_states("state_shipping", "", "", "id='state_shipping'");
echo "</noscript>\n";
?>
    <script type="text/javascript">
        changeStateList("state_shipping", "AUS");
    </script>
    <table border="0" cellspacing="0" cellpadding="5" width=100%"  class="checkout-standart">

        <tr class="sectiontableheader">
            <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_BILLING_LBL ?></th>
        </tr>
        <tr><td>



<?php


$shopper_fields = array();

$shopper_fields['email'] = _REGISTER_EMAIL;
$shopper_fields['phone_1'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE;
$shopper_fields['password'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PASSWORD_1;
$shopper_fields['company'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_COMPANY_NAME;
$shopper_fields['first_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME;
$shopper_fields['last_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_LAST_NAME;
$shopper_fields['address_suite'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_SUITE; //'Suite/Apt';//$VM_LANG->_PHPSHOP_SHOPPER_FORM_ADDRESS_1;
$shopper_fields['address_street_number'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STREET_NUMBER; //'Street Number *';//$VM_LANG->_PHPSHOP_SHOPPER_FORM_ADDRESS_1;
$shopper_fields['address_street_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STREET_NAME; // 'Street Name *';//$VM_LANG->_PHPSHOP_SHOPPER_FORM_ADDRESS_1;
$shopper_fields['city'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_CITY;
$shopper_fields['zip'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_ZIP;
$shopper_fields['country'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_COUNTRY;
$shopper_fields['state'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STATE;
?>

                <script language="javascript" type="text/javascript" src="includes/js/mambojavascript.js"></script>

                <fieldset style="border: none;">
<?php
foreach ($shopper_fields as $fieldname => $label) {
    echo '<div id="' . $fieldname . '_div" class="new-checkout-login-left ';

    echo '">';
    echo '<label for="' . $fieldname . '_field">' . $label . ':</label>';
    echo ' </div><div class="new-checkout-login-right">' . "\n";

    switch ($fieldname) {

        case 'phone_1':
            echo '<input type="text" placeholder="10 digits, no spaces" id="' . $fieldname . '_field" name="legacy_'.$fieldname.'" size="30"  class="inputbox" maxlength="10" />' . "\n";
            break;
         case 'country':
                                if (CAN_SELECT_STATES) {
                                    $onchange = "onchange=\"changeStateList();\"";
                                } else {
                                    $onchange = "";
                                }
                                $ps_html->list_country("legacy_country", 'AUS', "id=\"country_field\" $onchange");
                                break;

                            case 'state':
                                echo $ps_html->dynamic_state_lists("country", "state", $country, $state);
                                echo "<noscript>\n";
                                $ps_html->list_states("legacy_state", $state, "", "id=\"state\"");
                                echo "</noscript>\n";
                                break;
        default:
            echo '<input type="text" id="' . $fieldname . '_field" name="legacy_'.$fieldname.'" size="30"  class="inputbox" maxlength="32" />' . "\n";
            break;
    }

    echo '</div>';
}
?>
                </fieldset>
            </td></tr></table>
    
    
    
    <div id="mainCheckOutForm">
        <table border="0" cellspacing="0" cellpadding="5" width="100%" class="checkout-standart">
            <tr class="sectiontableheader">
                <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_SHIPPING_LBL; ?></th>
            </tr>
            <tr>
                <td width=28%><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_OCCASION; ?></td>
                <td><?php $ps_html->list_user_occasion("customer_occasion", $db->sp("customer_occasion")) ?></td>
            </tr>
<?php
global $iso_client_lang;


$query = "SELECT shipping_rate_id, CONCAT( '$', shipping_rate_value, ' - ', shipping_rate_name) AS shipping_rate FROM #__vm_shipping_rate ORDER BY shipping_rate_list_order ASC";
$database->setQuery($query);
$rows = $database->loadObjectList();

$aInfomation['shipping_method'] = "";
$j = 0;
foreach ($rows as $item) {
    if ($j == 0) {
        $sChecked = "checked='checked'";
    } else {
        $sChecked = "";
    }

    if ($iso_client_lang != "en") {
        $query = "SELECT `value` FROM #__jf_content AS JC, #__languages AS L WHERE JC.language_id = L.id AND L.iso = '$iso_client_lang' AND JC.reference_field = 'shipping_rate_name' AND JC.reference_id = $item->shipping_rate_id ";
        $database->setQuery($query);
        $item->shipping_rate = $database->loadResult();
    }

    $aInfomation['shipping_method'] .= '<div><input name="shipping_method" id="shipping_method' . $item->shipping_rate_id . '" value="' . $item->shipping_rate_id . '" ' . $sChecked . '  type="radio"><label for="shipping_method' . $item->shipping_rate_id . '">' . $item->shipping_rate . '</label></div>';
    $j++;
}


$query = "SELECT shipping_rate_id, CONCAT( '$', shipping_rate_value, ' - ', shipping_rate_name) AS shipping_rate FROM #__vm_shipping_rate ORDER BY shipping_rate_list_order ASC";
$database->setQuery($query);
$rows = $database->loadObjectList();
$ShippingMethod = $rows[0]->shipping_rate_id;
?>
            <input type="hidden" name="shipping_method" id="shipping_method" value="<?php echo $ShippingMethod; ?>"/>
           <!-- <tr>
                                 <td><b><?php echo $VM_LANG->_VM_DELIVERY; ?></b></td>
                                 <td class="shipping-method"><?php echo $aInfomation['shipping_method']; ?></td>
                    </tr>-->
            <tr>
            <?php
            $query_unavailable = "SELECT name,options FROM tbl_options WHERE type='special_deliver' ";
            $database->setQuery($query_unavailable);
            $aSpecialDeliver = $database->loadObjectList();

            $sSpecialDeliver = "";
            foreach ($aSpecialDeliver as $item) {
                $sSpecialDeliver .= $item->name . "/" . $item->options . "[--1--]";
            }

            $query_unavailable = "SELECT name FROM tbl_options WHERE type='deliver_option' ";
            $database->setQuery($query_unavailable);
            $aUnAvailableDate = $database->loadObjectList();

            $sUnAvailableDate = "";
            foreach ($aUnAvailableDate as $item) {
                $sUnAvailableDate .= $item->name . "[--1--]";
            }


            $query_limittime = "SELECT options FROM tbl_options WHERE type='cut_off_time' ";
            $database->setQuery($query_limittime);
            $sOptionParam = $database->loadResult();
            $aOptionParam = explode("[--1--]", $sOptionParam);
            $nTimeLimit = $aOptionParam[0] * 60 + $aOptionParam[1];

            if (intval($aOptionParam[0]) >= 12) {
                $sTime = (intval($aOptionParam[0]) - 12) . ":" . $aOptionParam[1] . " PM";
            } else {
                $sTime = intval($aOptionParam[0]) . ":" . $aOptionParam[1] . " AM";
            }
            $default_tz = date_default_timezone_get();
            date_default_timezone_set('Australia/Sydney'); // Normal zone

            $nDayNow = intval(date('j', time()));
            $nMonthNow = intval(date('m', time()));
            $nYearNow = intval(date('Y', time()));
            $nHourNow = intval(date('H', time()));
            $nMinuteNow = intval(date('i', time()));
            $nTimeNow = $nHourNow * 60 + $nMinuteNow;

            date_default_timezone_set($default_tz);


            if ($nTimeNow >= $nTimeLimit) {
                $cutofftime = 1;
            } else {
                $cutofftime = 0;
            }

            //  echo $nTimeNow . "===" . $nHourNow . "===" . $nMinuteNow . "===" . $nTimeLimit . "===";

            function getMonthDays($Month, $Year) {
                if (is_callable("cal_days_in_month")) {
                    return cal_days_in_month(CAL_GREGORIAN, $Month, $Year);
                } else {
                    return date("d", mktime(0, 0, 0, $Month + 1, 0, $Year));
                }
            }

            if ($mosConfig_lang == 'french') {
                $sCalendarImg = "calendar2_fr.png";
            } else {
                $sCalendarImg = "calendar2.png";
            }
            ?>
                <td width=28%><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_DELIVERYDATE2; ?>:</td>
                <td valign=top>
                    <div style="display:none;" id="selectDeliveryOption" >&nbsp;</div>
                    <input type="text" name="delivery_date_2" id="delivery_date_2" readonly="readonly" maxlength="10" />
                    <input id="btnSelectDeliveryOption" class="new_checkout_register_button" type="button" value="<?php echo $VM_LANG->_PHPSHOP_SELECT_DELIVERY_DATE; ?>"/>


<?php
echo "<br/>" . $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_DELIVERYNOTE2;
//echo '<input type="hidden" id="post_code_zip_now" value=""/>';
echo "	<div id='deliver_extra_post_code' style='padding:5px;color:red;display:none;'>&nbsp;</div>
                                    <div id='message_by_post_code' style='padding:5px;color:red;display:none;'>&nbsp;</div>
		               			<div style='display:none;'><div id='deliver_extra_same_day' style='padding:5px;color:red;display:none;'>&nbsp;</div></div>";
?>

                    <input type="hidden" name="daynow" value="<?php echo $nDayNow; ?>" />
                    <input type="hidden" name="daysofmonthnow" value="<?php echo getMonthDays($nMonthNow, $nYearNow); ?>" />
                    <input type="hidden" name="monthnow" value="<?php echo $nMonthNow; ?>" />
                    <input type="hidden" name="hournow" value="<?php echo $nHourNow; ?>" />
                    <input type="hidden" name="minutenow" value="<?php echo $nMinuteNow; ?>" />
                    <input type="hidden" name="cutofftime" value="<?php echo $cutofftime; ?>" />
                    <input type="hidden" name="deliver_extra_day" value="0" />
                    <input type="hidden" name="deliver_extra_price" value="0" />
                    <input type="hidden" name="deliver_reduce_surcharge" value="0" />
                    <input type="hidden" name="zip_checked_value" value="" />
                    <input type="hidden" name="total_deliver_tax_fee" value="" />
                    <input type="hidden" name="deliver_fee" value="" />
                    <input type="hidden" name="total_price" value="" />
                    <input type="hidden" name="state_tax" value="" />

                    <script type="text/javascript">
    $j("input[name='current_state_tax']").val($j("input[name='current_state_tax_tmp']").val());
                    </script>

<?php
$query = "SELECT shipping_rate_id, shipping_rate_value, tax_rate FROM #__vm_shipping_rate LEFT JOIN #__vm_tax_rate ON shipping_rate_vat_id = tax_rate_id ORDER BY shipping_rate_list_order ASC";
$database->setQuery($query);
$rows = $database->loadObjectList();

$aInfomation['shipping_method_list_fee'] = "";
if (count($rows)) {
    foreach ($rows as $item) {
        $aInfomation['shipping_method_list_fee'] .= $item->shipping_rate_id . "[--1--]" . floatval($item->shipping_rate_value) . "[--1--]" . floatval($item->tax_rate) . "[--2--]";
    }
}

$query = "SELECT * FROM jos_vm_tax_rate";
$database->setQuery($query);
$rows = $database->loadObjectList();

$sStateTax = "";
if (count($rows)) {
    foreach ($rows as $item) {
        $sStateTax .= $item->tax_country . "[--1--]" . $item->tax_state . "[--1--]" . floatval($item->tax_rate) . "[--2--]";
    }
}

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
                        var nDaysValid = 0;
                        var nMonthsValid = 0;
                        var nYearsValid = 0;
                        var aOption = new Array();
                        var aUnAvailableDate = new Array();
                        var aUnAvailableItem = new Array();
                        var nIndex = parseFloat(oForm.daynow.value);
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
                        var bIsValidZipCodeFirst = <?php echo $bIsValidZipCodeFirst; ?>;
                        var bIsValidZipCode = 1;
                        var sDeliverMethodFee = "<?php echo $aInfomation['shipping_method_list_fee']; ?>";
                        var nDayNow = "<?php echo date("d"); ?>";
                        var nMonthNow = "<?php echo date("m"); ?>";
                        var nYearNow = "<?php echo date("Y"); ?>";

                        $j(document).ready(function ()
                        {
                            changDeliver(oForm.zip_checked.value, $j("input[name='current_state_tax']").val(), $j('#post_code_zip_now').val());
                        });

                    </script>


                </td>
            </tr>
            <tr>
                <td  width=28% valign="top"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_NOTE ?></td>
                <td><textarea title="<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_NOTE ?>" cols="40" rows="3" name="card_msg"></textarea></td>
            </tr>
            <!--<tr>
                <td  width=28% valign="top"><?php //echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_SIGNATURE  ?>: </td>
                <td><textarea title="<?php //echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_SIGNATURE  ?>" cols="40" rows="2" name="signature"></textarea></td>
            </tr>-->
            <tr>
                <td  width=28% valign="top"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_COMMENTS ?>:</td>
                <td><textarea title="<?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_COMMENTS ?>" cols="40" rows="2" name="card_comment"></textarea></td>
            </tr>
            <tr>
                <td  width=28% valign="top"><?php echo $PHPSHOP_LANG->_PHPSHOP_ORDER_PRINT_FIND_US_2 ?></td>
                <td>
<?php echo mosHTML::yesnoRadioList("find_us", "", $find_us); ?>
                </td>
            </tr>
        </table>
        <!-- END Customer Ship To -->
        <br/>
        <table border="0" cellspacing="0" cellpadding="5" width="100%" class="checkout-standart">
            <tr class="sectiontableheader">
                <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PAYMENT_LBL; ?> ( <font color="red">* <?php echo $VM_LANG->_VM_REQUIRED; ?></font> )</th>
            </tr>
            <tr>
                <td align="left" colspan="2">
                    <input name="payment_method_state" id="<?php echo $VM_LANG->_VM_CHECKOUT_CARD_LABEL; ?>" value="live" checked="checked" type="radio"><label for="<?php echo $VM_LANG->_VM_CHECKOUT_CARD_LABEL; ?>"><?php echo $VM_LANG->_VM_CHECKOUT_CARD_LABEL; ?></label><br>
                    <input name="payment_method_state" id="<?php echo $VM_LANG->_VM_CHECKOUT_CARD_LABEL2; ?>" value="offline" type="radio"><label for="<?php echo $VM_LANG->_VM_CHECKOUT_CARD_LABEL2; ?>"><?php echo $VM_LANG->_VM_CHECKOUT_CARD_LABEL2; ?></label>
                </td>
            </tr>
<?php
$query = "SELECT creditcard_code,creditcard_name FROM #__vm_creditcard";
$database->setQuery($query);
$rows = $database->loadObjectList();
$aInfomation['payment_method'] = mosHTML::selectList($rows, "payment_method", "size='1'", "creditcard_code", "creditcard_name");
?>
            <tr>
                <td width="30%" ><b><?php echo $VM_LANG->_VM_CREDIT_CARD_TYPE; ?><font color="red">*</font></b>:</td>
                <td width="70%" ><?php echo $aInfomation['payment_method']; ?></td>
            </tr>
            <tr>
                <td><b><?php echo $VM_LANG->_VM_NAME_ON_CARD; ?><font color="red">*</font></b>:</td>
                <td><input type="text" name="name_on_card" value="" size="30" /></td>
            </tr>
            <tr>
                <td><b><?php echo $VM_LANG->_VM_CREDIT_CARD_NUMBER; ?><font color="red">*</font></b>:</td>
                <td><input type="text" name="credit_card_number" value="" size="30" maxlength="16" /></td>
            </tr>
            <tr>
                <td><b><?php echo $VM_LANG->_VM_CREDIT_CARD_SECURITY_CODE; ?><font color="red">*</font></b>:</td>
                <td><input type="text" name="credit_card_security_code" value="" size="30" maxlength="4" /></td>
            </tr>
<?php
$aInfomation['expire_month'] = listMonth("expire_month", null, " size='1' ");
$aInfomation['expire_year'] = listYear("expire_year", date("Y"), " size='1' ", 30, date("Y"));
?>
            <tr>
                <td><b><?php echo $VM_LANG->_VM_EXPIRY_MONTH; ?><font color="red">*</font></b>:</td>
                <td><?php echo $aInfomation['expire_month']; ?>&nbsp;/&nbsp;<?php echo $aInfomation['expire_year']; ?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class="calculate-price" style="font-style:italic;">
<?php echo $VM_LANG->_VM_CREDIT_CARD_NOTE; ?>
                </td>
            </tr>
        </table>
        <table border="0" cellspacing="0" cellpadding="5" width="100%" class="checkout-standart">
            <tr class="sectiontableheader">
                <th colspan="2" align="left"><?php echo $VM_LANG->_VM_ORDER_PRICE_DETAIL; ?></th>
            </tr>
            <tr>
                <td width="30%" ><b><?php echo $VM_LANG->_VM_TOTAL_ITEMS_PRICE; ?></b> </td>
                <td width="70%" class="calculate-price" id="calcualte-total-items-price">N/A</td>
            </tr>
            <tr>
                <td><b><?php echo $VM_LANG->_VM_DELIVERY_FEE; ?></b></td>
                <td class="calculate-price" id="calcualte-total-deliver-fee">N/A</td>
            </tr>

            <script>//<tr>
                //<td width="30%" ><b><?php echo $VM_LANG->_VM_TOTAL_ITEMS_TAX; ?></b> </td>
                //<td width="70%" class="calculate-price" id="calcualte-tax">N/A</td>
                //</tr>
            </script>
            <tr>
                <td><b><?php echo $VM_LANG->_VM_TOTAL_PRICE; ?></b> </td>
                <td class="calculate-price" id="calcualte-total-price">N/A</td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <div class="msgReport" id="msgCheckoutReport" style="text-align:left;">
<?php if (!empty($_REQUEST['reload_msg'])) echo $VM_LANG->_VM_VALIDATING_CART3; ?>&nbsp;
                    </div><br/>
                    <div id="order-action-button">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <span style="font-size: 13px; font-style: italic;"><?php echo $VM_LANG->_VM_TERMS_AND_CONDITIONS_1; ?><a href="<?php echo $mosConfig_live_site; ?>/About-Us/Terms-and-Conditions.html"><?php echo $VM_LANG->_VM_TERMS_AND_CONDITIONS_2; ?></a></span>
                                    <br><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="padding-left: 40px;">
                                        <input type="button" value="<?php echo $VM_LANG->_VM_CALCULATE_BUTTON; ?>" id="calculateOrderPrice" class="new_checkout_upd_address_button" style="margin: 0px 0px !important;" />&nbsp;&nbsp;&nbsp;
                                    </div>
                                </td>
                                <td>
                                    <input type="button" value="<?php echo $VM_LANG->_VM_SUBMIT_BUTTON; ?>" id="saveOrder" class="new_checkout_upd_address_button" style="padding:5px; margin: 0px 0px !important;"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
<?php if (!empty($_REQUEST["cart_msg"])) { ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery("#msgReport").css('display', 'block');
                jQuery("#msgReport").html('<?php echo $_REQUEST["cart_msg"]; ?>');
            });
        </script>
<?php } ?>
    <!--Ticket #4969-->
<?php
@setcookie("just_change", "", time() - 3600);
?>
    <input type="hidden" name="reload_page" id="reload_page" value="<?php echo ( $_REQUEST["reload"] == 1 ? '1' : '0' ); ?>" />
    <?php if ((isset($_REQUEST["reload"])) && ($_REQUEST["reload"] == 1)) { ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                CalculateOrderPrice();
            });
        </script>
    <?php } ?>
    <!--Ticket #4969 END-->

<?php
if (!$shippingAddressRadio) {
    ?>
        <script type="text/javascript">
            $j(document).ready(function () {
                $j("#mainCheckOutForm").css("display", "none");
                $j("#msgReportShippingAddressRadio").css("display", "block");
            });
        </script>
    <?php
} else {
    ?>
        <script type="text/javascript">
            $j(document).ready(function () {
                $j("#msgReportShippingAddressRadio").css("display", "none");
                $j("#mainCheckOutForm").css("display", "block");
            });
        </script>
    <?php
}
?>


    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr >
            <td><?php if (!defined('_MIN_POV_REACHED')) { ?>
                    <div align="center">
                        <script type="text/javascript">alert('<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_ERR_MIN_POV ?>');</script>
                        <strong><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_ERR_MIN_POV ?></strong><br />
                        <strong><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_ERR_MIN_POV2 . " " . $CURRENCY_DISPLAY->getFullValue($_SESSION['minimum_pov']) ?></strong>
                    </div><?php
} elseif ($checkout_this_step == CHECK_OUT_GET_FINAL_CONFIRMATION) {
    ps_checkout::final_info();
    //MMMMMMMMMMMMMMMMMM
    ?>

                </td></tr></table>
        <br/>


                    <?php if (PSHOP_AGREE_TO_TOS_ONORDER == '1') { ?>
            <tr><td colspan=2>
                    <input type="checkbox" name="agreed" value="1" class="inputbox" />&nbsp;&nbsp;
        <?php
        $link = $mosConfig_live_site . '/index2.php?option=com_virtuemart&amp;page=shop.tos&amp;pop=1&amp;Itemid=' . $_REQUEST['Itemid'];
        $text = $VM_LANG->_PHPSHOP_I_AGREE_TO_TOS;
        echo vmPopupLink($link, $text);
        echo '</td>';
    }
    ?>
        </tr>
                <?php
                if (@VM_ONCHECKOUT_SHOW_LEGALINFO == '1') {
                    $link = sefRelToAbs('index2.php?option=com_content&amp;task=view&amp;id=' . VM_ONCHECKOUT_LEGALINFO_LINK);
                    $jslink = "window.open('$link', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;";
                    if (@VM_ONCHECKOUT_LEGALINFO_SHORTTEXT == '' || !defined('VM_ONCHECKOUT_LEGALINFO_SHORTTEXT')) {
                        $text = $VM_LANG->_VM_LEGALINFO_SHORTTEXT;
                    } else {
                        $text = VM_ONCHECKOUT_LEGALINFO_SHORTTEXT;
                    }
                    ?>
            <div class="legalinfo"><?php
            echo sprintf($text, $link, $jslink);
            ?>
            </div><br />
            <?php
        }
        ?>
        <tr><td colspan=2 align=center>
                <input type="submit" onclick="return(submit_order(this.form));" class="button" name="submit" value="<?php echo $VM_LANG->_PHPSHOP_ORDER_CONFIRM_MNU ?>" />
            </td></tr>
        <?php
    } elseif ($checkout_this_step != CHECK_OUT_GET_FINAL_CONFIRMATION) {
        ?>
        <tr>
            <td colspan=2 align=center>
                <!--<?php if ($_REQUEST["page"] == "checkout.index" && $checkout_this_step == CHECK_OUT_GET_SHIPPING_ADDR) { ?>
                                                                    <input type="button" class="button" name="submit100" value="<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_NEXT; ?> &gt;&gt;" onclick="validDeliverDate(true);" />
        <?php } else { ?>
                                                                    <input type="submit" class="button" name="submit" value="<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_NEXT; ?> &gt;&gt;"/>
    <?php } ?>-->
            </td>
        </tr>
            <?php }
            ?>
</td>
</tr>
</table>
</form>
<!-- Body ends here -->
    <?php
    if ($checkout_this_step == CHECK_OUT_GET_FINAL_CONFIRMATION && PSHOP_AGREE_TO_TOS_ONORDER == '1') {
        echo "<script type=\"text/javascript\"><!--
                    function submit_order( form ) {
                        if (!form.agreed.checked) {
                            alert( \"" . $VM_LANG->_PHPSHOP_AGREE_TO_TOS . "\" );
                            return false;
                        }
                        else {
                            return true;
                        }
                    }
                    --></script>";
    } else {
        echo "<script type=\"text/javascript\"><!--
                    function submit_order( form ) { return true; }
                    --></script>";
    }
    ?>
