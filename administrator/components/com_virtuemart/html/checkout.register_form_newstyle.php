<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
#ticket 10147
# LANG CONSTANTS  - TO BE MOVED TO LANG FILES AFTER TRANSACTION TO AMAZON (UTF-8)
define('_LOGIN_PROBLEMS', 'Login Problems?');
define('_RESET_PASSWORD', 'Reset Password?');
define('_RETURNING_CUSTOMER', 'Returning Customer? Sign In');
define('_NEW_BUTTON_SEND_REG', 'Register');
define('_GET_STARTED_GUEST', 'Proceed as Guest');
define('_GET_STARTED', 'Create Account');



$return = '/checkout/1/';
$country = mosGetParam($_REQUEST, 'country', $vendor_country_3_code);
$state = mosGetParam($_REQUEST, 'state', '');

if(isset($_REQUEST['shippingParams'])){
    $return = $_SERVER['REQUEST_URI'];
}

global $database, $my;
?>
<!-- LOGIN FORM -->
<script language="javascript" type="text/javascript" src="/includes/js/mambojavascript.js"></script>

<div class="container checkout_login_form p-0">
    <div class="checkout-grid">
        <div class=" wrapper">

                <h3 class="mt-4 mb-2 checkout_form_title"><?php echo  _GUEST_CHECKOUT; ?></h3>

                <p class="checkout_form_subtitle"><?php echo  _CREATE_GUEST_ORDER; ?></p>
                <?php
                //'username',
                $required_fields_guest = array('email', 'first_name',  'phone_1');

                $shopper_fields = array();

                $shopper_fields['email'] = _REGISTER_EMAIL;
                $shopper_fields['first_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME;
                $shopper_fields['phone_1'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE;

                ?>
                <form role="form" action="" method="post" id="guest_form">
                    <?php
                    foreach ($shopper_fields as $key => $value) {
                        ?>
                        <div class="form-group">
                            <label for="<?php echo $key; ?>"><?php echo $value; ?><?php echo in_array($key, $required_fields_guest) ? '*' : ''; ?></label>
                            <?php

                            Switch ($key) {

                                case 'email':
                                    ?>
                                    <input type="email" class="checkout_form_input" id="<?php echo $key; ?>" name="<?php echo $key; ?>" placeholder="e.g., yourname@example.com">
                                    <span class="help-block error">Please enter a valid email address. For example johndoe@domain.com</span>
                                    <?php
                                    break;

                                case 'password':

                                    ?>
                                    <input type="password" class="checkout_form_input" id="<?php echo $key; ?>" name="<?php echo $key; ?>" placeholder="">
                                    <?php

                                    break;

                                case 'phone_1':
                                    ?>
                                    <input type="text" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 && /^\d{0,15}$/.test(this.value));" class="checkout_form_input" id="<?php echo $key; ?>" name="<?php echo $key; ?>" placeholder="Enter your phone number">
                                    <?php
                                    break;

                                default:
                                    ?>
                                    <input type="text" class="checkout_form_input" id="<?php echo $key; ?>" name="<?php echo $key; ?>" placeholder="Enter your <?php echo str_replace('_',' ',$key); ?>">
                                    <?php
                                    break;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <button type="submit" class="btn btn-default" onclick="return checkGuestForm(event);"><?php echo _GET_STARTED_GUEST; ?></button>
                    <input type="hidden" name="Itemid" value="<?php echo @$_REQUEST['Itemid']; ?>" />
                    <input type="hidden" name="gid" value="<?php echo $my->gid; ?>" />
                    <input type="hidden" name="id" value="<?php echo $my->id; ?>" />
                    <input type="hidden" name="user_id" value="<?php echo $my->id; ?>" />
                    <input type="hidden" name="option" value="com_virtuemart" />
                    <input type="hidden" name="guest_registration" value="1" />
                    <input type="hidden" name="returnUrl" value="<?php echo $return ?>" />
                    <input type="hidden" name="useractivation" value="<?php echo $mosConfig_useractivation; ?>" />
                    <input type="hidden" name="func" value="shopperadd" />
                    <input type="hidden" name="page" value="checkout.index" />
                </form>

        </div>
        <div class="wrapper"<?php echo isset($_SESSION['social_info']) ? ' style="display: none;"' : ''; ?>>

                <h3 class="mt-4 mb-2 checkout_form_title"><?php echo _RETURNING_CUSTOMER; ?></h3>

                <p class="checkout_form_subtitle">Welcome back! Sign in for a faster checkout.</p>
                <form role="form" action="/" method="post" id="login">
                    <div class="form-group">
                        <label for="username_login"><?php echo _REGISTER_EMAIL; ?>*</label>
                        <input type="text" class="checkout_form_input" id="username_login" name="username" placeholder="Enter your email" onchange="localStorage.setItem('user_email', this.value); identifyKlaviyo(this.value)">
                    </div>
                    <div class="form-group">
                        <label for="password_login"><?php echo _PASSWORD; ?>*</label>
                        <input type="password" class="checkout_form_input" id="password_login" name="passwd" placeholder="Enter your password">
                    </div>
                     <a class="float-end" href="/lost-password/"><?php echo _RESET_PASSWORD ?></a>
                    <input type="hidden" name="op2" value="login" />
                    <input type="hidden" name="option" value="login">
                    <input type="hidden" name="lang" value="<?php echo $mosConfig_lang; ?>" />
                    <input type="hidden" name="return" value="<?php echo $return ?>" />
                    <?php
                    // used for spoof hardening
                    $validate = vmSpoofValue(1);
                    ?>
                    <input type="hidden" name="<?php echo $validate; ?>" value="1" />
                    <button type="submit" class="btn btn-default"><?php echo _BUTTON_LOGIN; ?></button>
                </form>


        </div>
        <div class=" wrapper">

                <h3 class="checkout_form_title"><?php echo isset($_SESSION['social_info']) ? 'Add information' : _CREATE_ACCOUNT; ?></h3>

                <p class="checkout_form_subtitle"><?php echo isset($_SESSION['social_info']) ? 'To your own Bloomex account' : 'Save your details and track orders with a full account.'; ?></p>
                <?php
                //'username',
                $required_fields = array('email', 'password','first_name',  'phone_1');
                if (isset($_SESSION['social_info'])) {
                    unset($required_fields[1]);
                    $required_fields = array_values($required_fields);
                }
                
                $shopper_fields = array();

                $shopper_fields['email'] = _REGISTER_EMAIL;

                $shopper_fields['first_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME;
                $shopper_fields['last_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_LAST_NAME;
                $shopper_fields['phone_1'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE;
                $shopper_fields['password'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PASSWORD_1;

                ?>
                <style>
                    .registraton_autocomplete{
                        display: none;
                    }
                </style>
                <form role="form" action="" method="post" id="registration_form">
                    <?php
                    foreach ($shopper_fields as $key => $value) {
                        ?>
                        <div class="form-group">
                            <?php
                            if (isset($_SESSION['social_info']) AND $key == 'password') {
                            }
                            else {
                                ?>
                                <label for="login"><?php echo $value; ?><?php echo in_array($key, $required_fields) ? '*' : ''; ?></label>
                                <?php
                            }
                            Switch ($key) {

                                case 'email':
                                    ?>
                                    <input type="email" class="checkout_form_input" id="<?php echo $key; ?>" name="<?php echo $key; ?>" placeholder="e.g., yourname@example.com">
                                    <span class="help-block error">Please enter a valid email address. For example johndoe@domain.com</span>
                                    <?php
                                break;

                                case 'password':
                                    if (isset($_SESSION['social_info'])) {
                                        ?>
                                        <input type="password" style="display: none;" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo base64_encode((string)mt_rand()); ?>" placeholder="Create a secure password">
                                        <?php
                                    }
                                    else {
                                        ?>
                                        <input type="password" class="checkout_form_input" id="<?php echo $key; ?>" name="<?php echo $key; ?>" placeholder="Create a secure password">
                                        <?php
                                    }
                                break;

                                case 'phone_1':
                                    ?>
                                    <input type="text" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 && /^\d{0,15}$/.test(this.value));" class="checkout_form_input" id="<?php echo $key; ?>" name="<?php echo $key; ?>" placeholder="Enter your phone number">
                                    <?php
                                break;

                                default:
                                    ?>
                                    <input type="text" class="checkout_form_input" id="<?php echo $key; ?>" name="<?php echo $key; ?>" placeholder="Enter your <?php echo str_replace('_',' ',$key); ?>">
                                    <?php
                                break;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <button type="submit" class="btn btn-default" onclick="return checkRegistrationForm(event);"><?php echo _GET_STARTED; ?></button>
                    <input type="hidden" name="Itemid" value="<?php echo @$_REQUEST['Itemid']; ?>" />
                    <input type="hidden" name="gid" value="<?php echo $my->gid; ?>" />
                    <input type="hidden" name="id" value="<?php echo $my->id; ?>" />
                    <input type="hidden" name="user_id" value="<?php echo $my->id; ?>" />
                    <input type="hidden" name="option" value="com_virtuemart" />
                    <input type="hidden" name="returnUrl" value="<?php echo $return ?>" />

                    <input type="hidden" name="useractivation" value="<?php echo $mosConfig_useractivation; ?>" />
                    <input type="hidden" name="func" value="shopperadd" />
                    <input type="hidden" name="page" value="checkout.index" />
                </form>

        </div>
        <div class="wrapper"<?php echo isset($_SESSION['social_info']) ? ' style="display: none;"' : ''; ?>>
            <div class="corporate_pannel">
                <h3 class="checkout_form_title"><?php echo _CORPORATE_CLIENT; ?></h3>

                <p class="checkout_form_subtitle"><?php echo _CORPORATE_CLIENT_DESC; ?></p>
                <a href="/apply-for-20-corporate-account/" target="_blank"><button type="submit" class="btn btn-default corporate"><?php echo _APPLY_NOW; ?></button></a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var shopper_fields = <?php echo json_encode($shopper_fields); ?>;
    var required_fields = <?php echo json_encode($required_fields); ?>;
    var required_fields_guest = <?php echo json_encode($required_fields_guest); ?>;

    document.addEventListener("DOMContentLoaded", function () {
        const loginForm = document.getElementById("login");
        const loginButton = loginForm ? loginForm.querySelector("button[type='submit']") : null;
        const usernameInput = document.getElementById("username_login");
        const passwdInput = document.getElementById("password_login");


        if (loginForm && loginButton) {
            usernameInput.addEventListener('keydown',function (){
                loginButton.disabled = false;
            })
            passwdInput.addEventListener('keydown',function (){
                loginButton.disabled = false;
            })
            loginForm.addEventListener("submit", function (event) {
                loginButton.disabled = true;
                loginButton.textContent = "Logging in";
            });
        }
    });
</script>
