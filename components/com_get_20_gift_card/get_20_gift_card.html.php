<?php
/**
* @version $Id: contact.html.php 4157 2006-07-02 17:58:51Z stingrey $
* @package Joomla
* @subpackage Contact
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class HTML_get_20_gift_card {
	static function viewForm($option) {
    global $database;
        if( isset($_REQUEST['msg']) && $_REQUEST['msg']=='success'){?>
                        <div class="container thankyou_page_wrapper">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 inner">
                                    <center>

                                        <div>
                                            <img style="width: 100px" alt="Bloomex Australia" src="/templates/bloomex_adaptive/images/bloomexlogo.svg">
                                        </div>
                                        <h4 class="text-success"><?php echo $_REQUEST['user']?
                                                'Account already exists - we have sent the $20.00 Gift Code to your email.':
                                                'Thank you - your $20.00 Gift Code will be emailed to you for immediate redemption.'; ?></h4><br><br>

                                    </center>
                                </div>
                            </div>
                        </div>

            <?php } else {
            $query = "SELECT country_3_code,country_name FROM #__vm_country  ORDER BY country_name ASC";
            $database->setQuery($query);
            $rows = $database->loadObjectList();
            $oState = new stdClass;
            $oState->country_3_code = "";
            $oState->country_name = "";
            $aState = array();
            $aState[] = $oState;
            $rows = array_merge($aState, $rows);


            ?>

            <div class="container white">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <form class="get_gift_form form form-horizontal" id="giftForm" name="get_gift_form" method="POST" action="" >
                <h2 class="text-center">Submit form to receive your $20.00 Gift Card</h2>
                <div class="form-group">
                    <label for="first_name" class="col-sm-2 control-label">First Name:<font color="#ff0000">*</font></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="first_name" id="first_name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="last_name" class="col-sm-2 control-label">Last Name:<font color="#ff0000">*</font></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="last_name" id="last_name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="uname" class="col-sm-2 control-label">Email:<font color="#ff0000">*</font></label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="upass" class="col-sm-2 control-label">Password:<font color="#ff0000">*</font></label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" name="upass" id="upass" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone" class="col-sm-2 control-label">Phone:<font color="#ff0000">*</font></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="phone" id="phone" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="state" class="col-sm-2 control-label">Country:<font color="#ff0000">*</font></label>
                    <div class="col-sm-10">
                        <?php echo  mosHTML::selectList($rows, "country", "size='1' required class='form-control'", "country_3_code", "country_name");?>
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" onclick="checkForm()" class="submit_button btn btn-success">Submit</button>
                        <button style="visibility: hidden" class="g-recaptcha capcha_validate"
                                data-sitekey="6LdJvGgUAAAAAM_cyb03MYOn5oxYZlGwAonw7Npi"
                                data-callback="submitform">
                        </button>
                        <input type="hidden" name="task" value="get_20_gift_card" >
                        <input type="hidden" name="option" value="<?php echo $option;?>" >

                    </div>
                </div>
                <h4 class="text-danger text-center"><?php echo $_REQUEST['msg']??''; ?></h4>
            </form>
            <script type="text/javascript">
                function submitform() {
                    document.getElementById("giftForm").submit();
                }
                function checkForm() {
                    $('.submit_button').attr('disabled','true');
                    oForm	= document.get_gift_form;


                    if( oForm.first_name.value == ""  ) {
                        alert("Please enter your first name.");
                        oForm.first_name.focus();
                        $('.submit_button').removeAttr('disabled');
                        return false;
                    }
                    if( oForm.last_name.value == ""  ) {
                        alert("Please enter your last name.");
                        oForm.last_name.focus();
                        $('.submit_button').removeAttr('disabled');
                        return false;
                    }
                    if( oForm.email.value == ""  ) {
                        alert("Please enter your email address.");
                        oForm.email.focus();
                        $('.submit_button').removeAttr('disabled');
                        return false;
                    }
                    if( !checkEmailValid(oForm.email.value)  ) {
                        alert("Your email address is incorrect.");
                        oForm.email.focus();
                        $('.submit_button').removeAttr('disabled');
                        return false;
                    }

                    if(oForm.upass.value == "") {
                        oForm.upass.focus();
                        $('.submit_button').removeAttr('disabled');
                        return false;
                    }

                    if( oForm.phone.value == ""  ) {
                        alert("Please enter your phone number.");
                        oForm.phone.focus();
                        $('.submit_button').removeAttr('disabled');
                        return false;
                    }
                    if( oForm.country.value == ""  ) {
                        alert("Please select your country.");
                        oForm.country.focus();
                        $('.submit_button').removeAttr('disabled');
                        return false;
                    }


                    jQuery('.capcha_validate').click()

                }

                function checkEmailValid(email){
                    emailRegExp = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/;
                    if(emailRegExp.test(email)){
                        return true;
                    }else{
                        return false;
                    }
                }
                function checkPasswordValidation(password){
                    if( password == ""  ) {
                        alert("Please enter your password.");
                        return false;
                    }
                    var digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                    var uppercase = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
                    var lowercase = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
                    var character = ['!', '@', '#', '$', '^',  '?', '+', '=', '(', ')'];

                        if (password.length < 8 || password.length > 20) {
                            alert('The length of the password must be between 8 and 20 characters.');

                            return false;
                        }


                        var isset_digits = false;
                        var isset_uppercase = false;
                        var isset_lowercase = false;
                        var isset_character = false;

                        password.split('').forEach(function (item, i, arr) {
                            if (digits.indexOf(item) >= 0) {
                                isset_digits = true;
                            }
                            if (uppercase.indexOf(item) >= 0) {
                                isset_uppercase = true;
                            }
                            if (lowercase.indexOf(item) >= 0) {
                                isset_lowercase = true;
                            }
                            if (character.indexOf(item) >= 0) {
                                isset_character = true;
                            }
                        });

                        if (!isset_digits) {
                            alert('The password must contain at least one number.');

                            return false;
                        }

                        if (!isset_uppercase) {
                            alert('The password must contain at least one uppercase letter.');

                            return false;
                        }

                        if (!isset_lowercase) {
                            alert('The password must contain at least one lowercase letter.');

                            return false;
                        }

                        if (!isset_character) {
                            alert('The password must contain at least one non-alphanumeric character.');

                            return false;
                        }
                    return true
                }

            </script>
        </div>
        </div>
    </div><br>

            <script src='https://www.google.com/recaptcha/api.js'></script>
<?php  }

	}
}