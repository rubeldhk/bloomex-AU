<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 

mm_showMyFileName( __FILE__ );

$query = "SELECT 
    * 
FROM `jos_vm_user_info` AS `ui`
WHERE 
    `ui`.`user_id`=".(int)$auth["user_id"]."
AND
    `ui`.`address_type`='BT'
";
$database->setQuery($query);
$bt_obj = false; 
$database->loadObject($bt_obj);
?>

<div class="container billing_wrapper">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="viewBillingInfoForm">
            <h3><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_BILLING_LBL ?></h3>
            <div class="billing_info_wrapper">
                <hr/>
                <?php
                if (!empty($bt_obj->company)) {
                    ?>
                    <div class="billing_row">
                        <span class="title">
                            <?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?>:
                        </span>
                        <span class="billing_company">
                            <?php echo $bt_obj->company;?>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <div class="billing_row">
                    <span class="title">
                        Full name:
                    </span>
                    <span class="billing_full_name">
                        <?php echo $bt_obj->first_name; ?> <?php echo $bt_obj->middle_name; ?> <?php echo $bt_obj->last_name; ?>
                    </span>
                </div>
                <div class="billing_row">
                    <span class="title">
                        Address:
                    </span>
                    <span class="billing_address">
                        <?php echo $bt_obj->address_1; ?>, <?php echo $bt_obj->city; ?>, <?php echo $bt_obj->state; ?> , <?php echo $bt_obj->country; ?>
                    </span>
                </div>
                <?php
                if (!empty($bt_obj->phone_1)) {
                    ?>
                    <div class="billing_row">
                        <span class="title">
                            Phone:
                        </span>
                        <span class="billing_phone_1">
                            <?php echo $bt_obj->phone_1; ?>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (!empty($bt_obj->fax)) {
                    ?>
                    <div class="billing_row">
                        <span class="title">
                            <?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_FAX ?>:
                        </span>
                        <span class="billing_fax">
                            <?php echo $bt_obj->fax; ?>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <div class="billing_row">
                    <span class="title">
                        <?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL ?>:
                    </span>
                    <span class="billing_user_email">
                        <?php echo $bt_obj->user_email; ?>
                    </span>
                </div>
                <button type="submit" class="btn btn-default"><?php echo $VM_LANG->_PHPSHOP_UDATE_ADDRESS ?></button>
                <input type="hidden" name="user_info_id_billing" value="<?php echo $bt_obj->user_info_id; ?>" />
                <input type="hidden" name="id_billing" value="<?php echo $bt_obj->id; ?>" />
                <input type="hidden" name="user_id_billing" value="<?php echo $bt_obj->user_id; ?>" />
            </div>
            <?php
            $required_fields = array('first_name', 'last_name', 'street_name', 'street_number', 'city', 'zip', 'country', 'state', 'phone_1', 'user_email');
            $shopper_fields = array();

            $shopper_fields['company'] = $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY;
            $shopper_fields['title'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_TITLE;
            $shopper_fields['first_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME;
            $shopper_fields['last_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_LAST_NAME;
            $shopper_fields['middle_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_MIDDLE_NAME;
            $shopper_fields['user_email'] = $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL;
            $shopper_fields['suite'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_SUITE;
            $shopper_fields['street_number'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STREET_NUMBER;
            $shopper_fields['street_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STREET_NAME;
            $shopper_fields['city'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_CITY;
            $shopper_fields['zip'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_ZIP;
            $shopper_fields['country'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_COUNTRY;
            $shopper_fields['state'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STATE;
            $shopper_fields['phone_1'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE;
            $shopper_fields['phone_2'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE2;
            $shopper_fields['fax'] = $VM_LANG->_PHPSHOP_ORDER_PRINT_FAX;
            ?>
            <div class="update_billing_info_wrapper">
                <div class="close_form"></div>
                <form role="form" action="index.php" method="post" id="update_billing_info_form">
                    <?php
                    foreach ($shopper_fields as $key => $value) {
                        ?>
                        <div class="form-group">
                            <label for="login"><?php echo $value; ?><?php echo in_array($key, $required_fields) ? '*' : ''; ?>:</label>
                            <?php
                            Switch ($key) {
                                case 'state':
                                    echo $ps_html->dynamic_state_lists('billing_info_country', 'billing_info_state', $bt_obj->country, $bt_obj->state);
                                    ?>
                                    <noscript>
                                    <?php
                                    echo $ps_html->list_states('billing_info_state', $bt_obj->state, '', 'id="billing_info_state" autocomplete="new-password" ');
                                    ?>
                                    </noscript>
                                    <?php
                                break;

                                case 'country':
                                    echo $ps_html->list_country('billing_info_country', $bt_obj->country, 'id="billing_info_country" autocomplete="new-password" onchange="changeStateList(\'billing_info_state\', \'billing_info_country\');"', 'billing_info_country');
                                break;

                                case 'user_email':
                                    ?>
                                    <input type="email" autocomplete="new-password" class="form-control" id="billing_info_<?php echo $key; ?>" name="billing_info_<?php echo $key; ?>" value="<?php echo htmlspecialchars($bt_obj->$key); ?>" placeholder="">
                                    <?php
                                break;

                                case 'password':
                                    ?>
                                    <input type="password" autocomplete="new-password" class="form-control" id="billing_info_<?php echo $key; ?>" name="billing_info_<?php echo $key; ?>" placeholder="">
                                    <?php
                                break;
                            
                                case 'title':
                                    echo $ps_html->list_user_title($bt_obj->title, 'name="billing_info_title" id="billing_info_title" autocomplete="new-password" ');
                                break;

                                default:
                                    ?>
                                    <input type="text" autocomplete="new-password" class="form-control" id="billing_info_<?php echo $key; ?>" name="billing_info_<?php echo $key; ?>" value="<?php echo htmlspecialchars($bt_obj->$key); ?>" placeholder="">
                                    <?php
                                break;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <button type="submit" class="btn billing_address_btn btn-default" onclick="return checkUpdateBillingInfoForm(event);"><?php echo $VM_LANG->_PHPSHOP_UDATE_ADDRESS ?></button>
                    <input type="hidden" name="option" value="com_ajaxorder" />
                    <input type="hidden" name="task" value="UpdateAddress" />
                    <input type="hidden" name="address_type" value="BT" />
                    <input type="hidden" name="user_info_id" value="<?php echo $bt_obj->user_info_id; ?>" />
                </form>
                <script type="text/javascript">
                    var billing_info__fields = <?php echo json_encode($shopper_fields); ?>;
                    var billing_info_required_fields = <?php echo json_encode($required_fields); ?>;
                </script>
            </div>
        </div>
    </div>
</div>