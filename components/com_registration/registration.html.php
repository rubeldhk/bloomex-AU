<?php
/** 
* @version $Id: registration.html.php,v 1.4 2005/01/06 01:13:27 eddieajau Exp $
* @package Mambo
* @subpackage Users
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package Mambo
* @subpackage Users
*/
class HTML_registration {

    static function newPassCreate ($option, $hash) {
        ?>
        <div class="container white">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h3 class="new_checkout_header"><?php echo _PROMPT_PASSWORD; ?></h3>
                    <span>Write a new password and confirm</span>
                    <br><br><br>
                    <form class="form-horizontal" role="form" action="index.php" method="post" name="login" id="login">
                        <div class="form-group">
                            <label for="new_password" class="col-sm-2 col-md-2 control-label">New password</label>
                            <div class="col-sm-10  col-md-4">
                                <input type="password" class="form-control" name="new_password" id="new_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new_password_2" class="col-sm-2  col-md-2 control-label">Confirm password</label>
                            <div class="col-sm-10  col-md-4">
                                <input type="password" class="form-control" name="new_password_2" id="new_password_2">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="show-password"> Show password
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-default" id="lostpass_button_new">Update</button>
                            </div>
                        </div>
                        <input type="hidden" id="user_hash" name="hash" value="<?php echo $hash;?>" />
                    </form>
                    <div id="lostpass_loader" style="display: none;">
                        <img src="/images/checkout_loader.gif" alt="Loading..." />
                    </div>
                    <div id="lostpass_info" style="display: none;">
                        <div id="lostpass_error">
                        </div>
                        <div id="lostpass_success">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="/templates/bloomex7/js/hideShowPassword.min.js"></script>
        <script type="text/javascript">
            $('#show-password').change(function(){
                $('#new_password').hideShowPassword($(this).prop('checked'));
                $('#new_password_2').hideShowPassword($(this).prop('checked'));
            });
        </script>
        <?php
    }
    static function updatePass ($option) {

        ?>
        <div class="container white">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h3 class="new_checkout_header"><?php echo _UPDATE_PASSWORD; ?></h3>
                    <span>Write old and new password and confirm</span>
                    <br><br><br>
                    <form class="form-horizontal" role="form" action="" method="post" name="login" id="login">
                        <div class="form-group">
                            <label for="old_password" class="col-sm-2 col-md-2 control-label">Current password</label>
                            <div class="col-sm-10 col-md-4">
                                <input type="password" class="form-control" name="old_password" id="old_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new_password" class="col-sm-2  col-md-2 control-label">New password</label>
                            <div class="col-sm-10  col-md-4">
                                <input type="password" class="form-control" name="new_password" id="new_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new_password_2" class="col-sm-2  col-md-2 control-label">Confirm password</label>
                            <div class="col-sm-10  col-md-4">
                                <input type="password" class="form-control" name="new_password_2" id="new_password_2">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="show-password"> Show password
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-default" id="updatePass_button">Update</button>
                            </div>
                        </div>
                    </form>
                    <div id="updatePass_loader" style="display: none;">
                        <img src="/images/checkout_loader.gif" alt="Loading..." />
                    </div>
                    <div id="updatePass_info" style="display: none;">
                        <div id="updatePass_error">
                        </div>
                        <div id="updatePass_success">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="/templates/bloomex7/js/hideShowPassword.min.js"></script>
        <script type="text/javascript">
            $('#show-password').change(function(){
                $('#old_password').hideShowPassword($(this).prop('checked'));
                $('#new_password').hideShowPassword($(this).prop('checked'));
                $('#new_password_2').hideShowPassword($(this).prop('checked'));
            });
        </script>
        <?php
    }

    static function lostPassForm($option) {
        global $mosConfig_live_site;
        ?>
        <div class="container white">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <h3 class="new_checkout_header"><?php echo _PROMPT_PASSWORD; ?></h3>
                    <span><?php echo _NEW_PASS_DESC; ?></span>
                    <br><br><br>
                    <form class="d-flex align-items-center gap-2" role="form" action="index.php" method="post" name="login" id="login">
                        <div>

                            <input type="text" class="form-control" name="confirmEmail" id="confirmEmail" placeholder="Enter email">
                        </div>
                        <button type="submit" class="btn btn-secondary" id="lostpass_button"><?php echo _BUTTON_SEND_PASS; ?></button>
                        <input type="hidden" name="option" value="<?php echo $option;?>" />
                        <input type="hidden" name="task" value="sendNewPass" />
                    </form>
                    <div id="lostpass_loader" style="display: none;">
                        <img src="/templates/bloomex7/images/loading.gif" alt="Loading..." />
                    </div>
                    <div id="lostpass_info" style="display: none;">
                        <div id="lostpass_error">
                        </div>
                        <div id="lostpass_success">
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <h3 class="new_checkout_header"><?php echo _STILL_HAVE_TROUBLE; ?>?</h3>
                    <p><?=_UPDATE_PASSWORD_TIME_TO_TIME ?></p>
                    <p style="margin-left:6px;margin-top:-3px;">1. <?=_CHECK_SPAM ?></p>
                    <p style="margin-left:6px;margin-top:-3px;">2. <?=_CONTACT_SUPPORT ?></p>
                    <div class="text-end" style="margin-top: 5px;">
                        <div class="d-flex gap-3">
                            <img src="<?=$mosConfig_live_site?>/templates/bloomex_adaptive/images/phone.png" alt="Phone" class="img-fluid" style="width: 35px;margin:3px;">
                            <img src="<?=$mosConfig_live_site?>/templates/bloomex_adaptive/images/email.png" alt="Email" class="img-fluid" style="width: 35px;margin:3px;">
                            <img src="<?=$mosConfig_live_site?>/templates/bloomex_adaptive/images/chat.png" alt="Live Chat" class="img-fluid" style="width: 42px;margin:3px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    static function registerForm($option, $useractivation) {
        ?>
        <script language="javascript" type="text/javascript">
                function submitbutton() {
                        var form = document.mosForm;
                        var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

                        // do field validation
                        if (form.name.value == "") {
                                alert( "<?php echo html_entity_decode(_REGWARN_NAME);?>" );
                        //} else if (form.username.value == "") {
                        //	alert( "<?php //echo html_entity_decode(_REGWARN_UNAME);?>//" );
                        //} else if (r.exec(form.username.value) || form.username.value.length < 3) {
                        //	alert( "<?php //printf( html_entity_decode(_VALID_AZ09), html_entity_decode(_PROMPT_UNAME), 2 );?>//" );
                        } else if (form.email.value == "") {
                                alert( "<?php echo html_entity_decode(_REGWARN_MAIL);?>" );
                        } else if (form.password.value.length < 6) {
                                alert( "<?php echo html_entity_decode(_REGWARN_PASS);?>" );
                        } else if (form.password2.value == "") {
                                alert( "<?php echo html_entity_decode(_REGWARN_VPASS1);?>" );
                        } else if ((form.password.value != "") && (form.password.value != form.password2.value)){
                                alert( "<?php echo html_entity_decode(_REGWARN_VPASS2);?>" );
                        } else if (r.exec(form.password.value)) {
                                alert( "<?php printf( html_entity_decode(_VALID_AZ09), html_entity_decode(_REGISTER_PASS), 6 );?>" );
                        } else {
                                form.submit();
                        }
                }
        </script>

        <div class="container white">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h3 class="new_checkout_header"><?php echo _REGISTER_TITLE; ?></h3>
                    <span><?php echo _REGISTER_REQUIRED; ?></span>
                    <br><br><br>
                    <form class="form-horizontal" role="form" action="/save-registration/" method="post" name="mosForm">
                        <div class="form-group">
                            <label for="name" class="col-sm-offset-2 col-sm-2 control-label"><?php echo _REGISTER_NAME; ?> *</label>
                            <div class="col-xs-12 col-sm-4">
                                <input type="text" class="form-control" name="name" id="name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-sm-offset-2 col-sm-2 control-label"><?php echo _REGISTER_EMAIL; ?> *</label>
                            <div class="col-xs-12 col-sm-4">
                                <input type="email" class="form-control" name="email" id="email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-sm-offset-2 col-sm-2 control-label"><?php echo _REGISTER_PASS; ?> *</label>
                            <div class="col-xs-12 col-sm-4">
                                <input type="password" class="form-control" name="password" id="password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password2" class="col-sm-offset-2 col-sm-2 control-label"><?php echo _REGISTER_VPASS; ?> *</label>
                            <div class="col-xs-12 col-sm-4">
                                <input type="password" class="form-control" name="password2" id="password2">
                            </div>
                        </div>
                        <input type="hidden" name="id" value="0" />
                        <input type="hidden" name="gid" value="0" />
                        <input type="hidden" name="useractivation" value="<?php echo $useractivation;?>" />
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <button type="submit" class="btn btn-default" class="new_checkout_register_button" onclick="submitbutton();"><?php echo _BUTTON_SEND_REG; ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

}
?>
