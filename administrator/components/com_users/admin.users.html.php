<?php
/**
 * @version $Id: admin.users.html.php 3513 2006-05-15 20:52:25Z stingrey $
 * @package Joomla
 * @subpackage Users
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_VALID_MOS') or die('Restricted access');

/**
 * @package Joomla
 * @subpackage Users
 */
class HTML_users {

    function showUsers(&$rows, $pageNav, $search, $option, $lists) {
        global $database;
        ?>
        <form action="index2.php" method="post" name="adminForm">

            <table class="adminheading">
                <tr>
                    <th class="user">
                        User Manager
                    </th>
                    <td>
                        Filter:
                    </td>
                    <td>
                        <input type="text" name="search" value="<?php echo $search; ?>" class="inputbox" onChange="document.adminForm.submit();" />
                    </td>
                    <td width="right">
                        <?php echo $lists['type_new']; ?>
                    </td>
                    <td width="right">
                        <?php echo $lists['type_new_2']; ?>
                    </td>
                    <!--
                    <td width="right">
                    <?php echo $lists['type']; ?>
                    </td>-->
                    <td width="right">
                        <?php echo $lists['logged']; ?>
                    </td>
                </tr>
            </table>

            <table class="adminlist">
                <tr>
                    <th width="2%" class="title">
                        #
                    </th>
                    <th width="3%" class="title">
                        <input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" />
                    </th>
                    <th class="title">
                        Name
                    </th>
        <!--			<th width="15%" class="title" >-->
                    <!--			Username-->
                    <!--			</th>-->
                    <th width="30%" class="title">
                        E-Mail
                    </th>
                    <th width="5%" class="title" nowrap="nowrap">
                        Logged In
                    </th>
                    <th width="5%" class="title">
                        Enabled
                    </th>
                    <th width="15%" class="title">
                        Group
                    </th>

                    <th width="10%" class="title">
                        Last Visit
                    </th>
                    <th width="1%" class="title">
                        ID
                    </th>			
                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = & $rows[$i];

                    $img = $row->block ? 'publish_x.png' : 'tick.png';
                    $task = $row->block ? 'unblock' : 'block';
                    $alt = $row->block ? 'Enabled' : 'Blocked';
                    $link = 'index2.php?option=com_users&amp;task=editA&amp;id=' . $row->id . '&amp;hidemainmenu=1';
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td>
                            <?php echo $i + 1 + $pageNav->limitstart; ?>
                        </td>
                        <td>
                            <?php echo mosHTML::idBox($i, $row->id); ?>
                        </td>
                        <td>
                            <a href="<?php echo $link; ?>">
                                <?php echo $row->name; ?>
                            </a>
            <!--				<td>-->
                            <!--				--><?php //echo $row->username;         ?>
                            <!--				</td>-->
                        </td>
                        <td>
                            <a href="<?php echo $link; ?>">
                                <?php echo $row->email; ?>
                            </a>
                        </td>
                        <td align="center">
                            <?php echo $row->loggedin ? '<img src="images/tick.png" width="12" height="12" border="0" alt="" />' : ''; ?>
                        </td>
                        <td>
                            <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>', '<?php echo $task; ?>')">
                                <img src="images/<?php echo $img; ?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                            </a>
                        </td>
                        <td>
                            <?php
                            $query = "SELECT departments_name FROM tbl_new_user_group AS NUG, tbl_mix_user_group AS MUG WHERE NUG.id = MUG.user_group_id AND MUG.user_id = $row->id";
                            $database->setQuery($query);
                            $departments_name = $database->loadResult();

                            if ($departments_name) {
                                echo $departments_name;
                                if ($row->gid == 25)
                                    echo " (" . $row->groupname . ")";
                            } else {
                                echo $row->groupname;
                            }
                            ?>
                        </td>

                        <td nowrap="nowrap">
                            <?php echo $row->lastvisitDate; ?>
                        </td>
                        <td>
                            <?php echo $row->id; ?>
                        </td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <?php
    }

    function edituser(&$row, &$contact, &$lists, $option, $uid, &$params, &$bloomex_bucks, $user_credits, $user_credits_uses) {
        mosCommonHTML::loadBootstrap(true);
        global $my, $acl;
        global $mosConfig_live_site;
        $tabs = new mosTabs(0);

        mosCommonHTML::loadOverlib();
        $canBlockUser = $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'user properties', 'block_user');
        $canEmailEvents = $acl->acl_check('workflow', 'email_events', 'users', $acl->get_group_name($row->gid, 'ARO'));
        ?>
        <script language="javascript" type="text/javascript">
            var digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            var uppercase = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
            var lowercase = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
            var character = ['!', '@', '#', '$', '^',  '?', '+', '=', '(', ')'];
    
            function submitbutton(pressbutton) {
                var form = document.adminForm;
                if (pressbutton == 'cancel') {
                    submitform(pressbutton);
                    return;
                }
                var password_length = form.password.value.length;
                if (password_length > 0) {

                    if (password_length < 8 || password_length > 20) {
                        alert('The length of the password must be between 8 and 20 characters.');

                        return false;
                    }


                    var isset_digits = false;
                    var isset_uppercase = false;
                    var isset_lowercase = false;
                    var isset_character = false;

                    form.password.value.split('').forEach(function (item, i, arr) {
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
                }
                var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+]", "i");

                // do field validation
                if (trim(form.name.value) == "") {
                    alert("You must provide a name.");
                    // } else if (form.username.value == "") {
                    // 	alert( "You must provide a user login name." );
                    // } else if (r.exec(form.username.value) || form.username.value.length < 3) {
                    // 	alert( "You login name contains invalid characters or is too short." );
                } else if (trim(form.email.value) == "") {
                    alert("You must provide an email address.");
                } else if (form.gid.value == "") {
                    alert("You must assign user to a group.");
                } else if (trim(form.password.value) != "" && form.password.value != form.password2.value) {
                    alert("Password do not match.");
                } else if (form.gid.value == "") {
                    alert("Please select another user group");
                } else if (form.gid.value == "29") {
                    alert("Please select another group as `Public Frontend` is not a selectable option");
                } else if (form.gid.value == "30") {
                    alert("Please select another group as `Public Backend` is not a selectable option");
                } else {
                    submitform(pressbutton);
                }
            }
            document.addEventListener('DOMContentLoaded', function (event) {

                document.getElementById('gpassword_btn').addEventListener('click', function (event) {
                    event.preventDefault();

                    var new_password = PasswordGenerator();

                    document.getElementById('gpassword').value = new_password;
                    document.getElementsByName('password')[0].value = new_password;
                    document.getElementsByName('password2')[0].value = new_password;

                    var copyText = document.getElementById('gpassword');
                    copyText.select();

                    document.execCommand('Copy');

                    var selection = window.getSelection();
                    selection.empty();
                });

            });

            function PasswordGenerator() {
                var g_password = '';

                max = 20;
                min = 8;
                var password_length = parseInt(Math.random() * (max - min) + min);

                var isset_digits = false;
                var isset_uppercase = false;
                var isset_lowercase = false;
                var isset_character = false;

                for (var i = 0; i < password_length; i++) {

                    var random_variant = parseInt(Math.random() * 4);

                    if (random_variant == 0) {
                        g_password += digits[parseInt(Math.random() * digits.length)];
                        isset_digits = true;
                    }
                    if (random_variant == 1) {
                        g_password += uppercase[parseInt(Math.random() * uppercase.length)];
                        isset_uppercase = true;
                    }
                    if (random_variant == 2) {
                        g_password += lowercase[parseInt(Math.random() * lowercase.length)];
                        isset_lowercase = true;
                    }
                    if (random_variant == 3) {
                        g_password += character[parseInt(Math.random() * character.length)];
                        isset_character = true;
                    }
                }

                if (isset_digits && isset_uppercase && isset_lowercase && isset_character) {

                    return g_password;
                } else {
                    return PasswordGenerator();
                }
            }
            function gotocontact(id) {
                var form = document.adminForm;
                form.contact_id.value = id;
                submitform('contact');
            }

            jQuery(document).ready(function () {
                jQuery("input[name='username']").change(function () {
                    jQuery("input[name='email']").val(jQuery(this).val());
                });
                jQuery("#send_new_password").click(function () {
                    if (confirm('Are you sure You want to send new password')) {

                        jQuery.post("index2.php",
                                {option: "com_users",
                                    task: "send_new_password",
                                    user_id: jQuery(this).attr('user_id')
                                },
                                function (data) {
                                    if (data == 'success') {
                                        jQuery("#send_new_password_result").text('new password sent successfully')
                                    }
                                }
                        );

                    }
                });


                jQuery("#send_to_customer_bucks").click(function () {
                    jQuery("#send_to_customer_bucks").val('Please wait ....');
                    jQuery.post("index2.php",
                            {option: "com_users",
                                task: "send_bucks",
                                bucks: jQuery('#bucks').text(),
                                email: jQuery("input[name='email']").val()
                            },
                            function (data) {
                                jQuery("#send_to_customer_bucks").val('Send Bucks to Customer');
                                jQuery("#send_bucks_result").text(data)
                            }
                    );

                });


                $('#update_user_block').click(function () {
                    $(".alert").remove();
                    var block_reason = $("#block_reason").val();
                    var block_reason_type = $("#block_reason_type").val();
                    if (block_reason.length > 5) {
                        var user_id = $(this).attr('data-user_id');
                        $('#open_block_dialog').removeClass('notActive').addClass('active');
                        $('#update_user_unblock').removeClass('active').addClass('notActive');
                        jQuery.post("index2.php",
                                {option: "com_users",
                                    task: "update_user_block",
                                    block_reason: block_reason,
                                    block_reason_type: block_reason_type,
                                    block: '1',
                                    user_id: user_id
                                });
                        $('#blockmodal').modal('toggle');
                        $('#blockUser').val('1');
                        $("#block_reason").val('');
                    } else {
                        $('#block_reason').before('<div class="alert alert-danger" role="alert">Enter some reason for block please</div>');

                    }
                })
                $('#update_user_unblock').click(function () {
                    var user_id = $(this).attr('data-user_id');
                    $('#open_block_dialog').removeClass('active').addClass('notActive');
                    $('#update_user_unblock').removeClass('notActive').addClass('active');
                    jQuery.post("index2.php",
                            {option: "com_users",
                                task: "update_user_block",
                                block: "0",
                                user_id: user_id
                            });
                    $('#blockUser').val('0');
                })
            });
        </script>
        <style>
            .modal-backdrop {
                display: none !important;
            }
            .modal-body{
                max-height: 400px;
                overflow-y: auto;
            }
        </style>
        <div class="modal" tabindex="-1" role="dialog" id="blockmodal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="float:left">Please enter reason for block (at least 6 characters):</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input class="form-control input-lg" id="block_reason"></input>
                        <br>
                        <select class="form-control" id="block_reason_type">
                            <option>Default</option>
                            <option>Chargeback</option>
                            <option>Suspected Fraud</option>
                            <option>Script</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-user_id="<?php echo $row->id; ?> " id="update_user_block" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <form action="index2.php" method="post" name="adminForm">

            <table class="adminheading">
                <tr>
                    <th class="user">
                        User: <small><?php echo $row->id ? 'Edit' : 'Add'; ?></small>
                    </th>
                </tr>
            </table>

            <table width="100%">
                <tr>
                    <td width="60%" valign="top">
                        <table class="adminform">
                            <tr>
                                <th colspan="2">
                                    User Details
                                </th>
                            </tr>
                            <tr>
                                <td width="130">
                                    Name:
                                </td>
                                <td>
                                    <input type="text" name="name" class="inputbox" size="40" value="<?php echo $row->name; ?>" maxlength="50" />
                                </td>
                            </tr>
                            <tr style="display: none">
                                <td>
                                    Username:
                                </td>
                                <td>
                                    <input type="text" name="username" class="inputbox" size="40" value="<?php echo $row->email; ?>" maxlength="25" />
                                </td>
                            <tr>
                                <td>
                                    Email:
                                </td>
                                <td>
                                    <input class="inputbox" type="text" name="email" size="40" value="<?php echo $row->email; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    New Password:
                                </td>
                                <td>
                                    <input class="inputbox" type="password" name="password" size="40" value="" /><br/>
                                    The password must consist of 8 to 20 characters.<br/>
                                    Must have at least an one digit, lowercase and upper case letters and a non-letter numeric character.<br/>
                                    You can also use the password generator.
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Verify Password:
                                </td>
                                <td>
                                    <input class="inputbox" type="password" name="password2" size="40" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Password Generator:</b>
                                </td>
                                <td>
                                    <input class="inputbox" type="text" id="gpassword" name="gpassword" size="40" value="" autocomplete="new-password"/>
                                    <input class="inputbox" type="submit" id="gpassword_btn" value="Generate" />
                                </td>
                                </td>
                                <?php if ($row->id) { ?>
                                <tr >
                                    <td>
                                        <input type="button" user_id="<?php echo $row->id; ?> " style="    background: #8BC34A;
                                               border-color: #8BC34A;
                                               color: white;
                                               padding: 3px;
                                               font-weight: bold;
                                               font-size: 13px;cursor: pointer" id="send_new_password" value="Send New Password">
                                    </td>
                                    <td>
                                        <span id="send_new_password_result" style="color:blue"></span>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr <?php
                            if ($my->gid != 25) {
                                echo "style='display: none;'";
                            }
                            ?>>
                                <td valign="top">
                                    Group:
                                </td>
                                <td>					
                                    <?php echo $lists['newgroup']; ?>
                                </td>
                            </tr>
                            <?php
                            if ($canBlockUser) {
                                ?>
                                <tr>
                                    <td>
                                        Block User
                                    </td>
                                    <td>

                                        <div class="form-group">
                                            <div class="col-sm-6 col-md-6">
                                                <div class="input-group">
                                                    <div id="radioBtn" class="btn-group">
                                                        <a data-user_id="<?php echo $row->id; ?> "  class="btn btn-primary btn-sm <?php echo $row->block ? 'active' : 'notActive'; ?>" id="open_block_dialog" data-toggle="modal" data-target="#blockmodal">YES</a>
                                                        <a data-user_id="<?php echo $row->id; ?> "  class="btn btn-primary btn-sm <?php echo $row->block ? 'notActive' : 'active'; ?>" id="update_user_unblock">NO</a>
                                                    </div>
                                                    <input type="hidden" name="block" id="blockUser" value="<?php echo $row->block; ?>">
                                                </div>
                                            </div>
                                            <?php if ($lists['user_block_history']) { ?>
                                                <div class="col-sm-6 col-md-6">
                                                    <a  class="show_block_history" data-toggle="modal" data-target="#blockhistorymodal">Show Blocking History</a>
                                                    <div class="modal" tabindex="-1" role="dialog" id="blockhistorymodal">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" style="float:left">User block history</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th scope="col">Username</th>
                                                                                <th scope="col">Block</th>
                                                                                <th scope="col">Reason</th>
                                                                                <th scope="col">Type</th>
                                                                                <th scope="col">Datetime</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            foreach ($lists['user_block_history'] as $k => $ubh) {

                                                                                echo '<tr>
                                                        <td>' . $ubh->username . '</td>
                                                        <td>' . (($ubh->block) ? "Yes" : "No") . '</td>
                                                        <td>' . $ubh->reason . '</td>
                                                                <td>' . $ubh->reason_type . '</td>
                                                        <td>' . $ubh->datetime . '</td>
                                                    </tr>';
                                                                            }
                                                                            ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <table class="table block_history_table" style="display: none">
                                                        <tr>
                                                            <th>username</th>
                                                            <th>block</th>
                                                            <th>datetime</th>
                                                        </tr>
                                                        <?php
                                                        foreach ($lists['user_block_history'] as $ubh) {
                                                            echo '<tr>
                                                        <td>' . $ubh->username . '</td>
                                                        <td>' . (($ubh->block) ? "Yes" : "No") . '</td>
                                                        <td>' . $ubh->datetime . '</td>
                                                    </tr>';
                                                        }
                                                        ?>
                                                    </table>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            if ($canEmailEvents) {
                                ?>
                                <tr>
                                    <td>
                                        Receive System Emails
                                    </td>
                                    <td>
                                        <?php echo $lists['sendEmail']; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            if ($uid) {
                                ?>
                                <tr>
                                    <td>
                                        Register Date
                                    </td>
                                    <td>
                                        <?php
                                        $date = new DateTime($row->registerDate);
                                        $date->add(new DateInterval('PT10H'));
                                        echo $date->format('Y-m-d H:m:s');
                                        ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Last Visit Date
                                    </td>
                                    <td>
                                        <?php echo $row->lastvisitDate; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr id="receive_user"></tr>
                            <tr id="corp_stakeholder"></tr>
                            <tr>
                                <td colspan="2">&nbsp;

                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="40%" valign="top">
                        <?php if ($bloomex_bucks) { ?>
                            <table class="adminform">

                                <tr>
                                    <td colspan="2">
                                        <?php echo $bloomex_bucks; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="button" style="    background: #8BC34A;
                                               border-color: #8BC34A;
                                               color: white;
                                               padding: 3px;
                                               font-weight: bold;
                                               font-size: 13px;cursor: pointer" id="send_to_customer_bucks"
                                               value="Send Bucks to Customer">

                                    </td>
                                    <td>
                                        <span id="send_bucks_result" style="color:blue;font-size: 15px;font-weight: bold; "></span>
                                    </td>
                                </tr>

                            </table>
                        <?php } ?>
                        <table class="adminform">
                            <tr>
                                <th>
                                    User Credits
                                </th>
                            </tr>
                            <?php
                            if (is_array($user_credits_uses) AND (sizeof($user_credits_uses) > 0)) {
                                ?>
                                <tr>
                                    <td>
                                        <div id="credits_history">
                                            <table class="adminform" border="1">
                                                <tr>
                                                    <td>Order Id</td>
                                                    <td>Used Credits</td>
                                                    <td>Comment</td>
                                                    <td>Date</td>
                                                    <td>Username</td>
                                                </tr>
                                                <?php
                                                foreach ($user_credits_uses as $user_credits_use) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php if ((int) $user_credits_use->order_id > 0) {
                                                                ?>
                                                                <a target="_blank" href="./index2.php?page=order.order_list&show=&option=com_virtuemart&order_id_filter=<?php echo $user_credits_use->order_id; ?>">
                                                                    <?php echo $user_credits_use->order_id; ?>
                                                                </a>
                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php echo!empty($user_credits_use->credits) ? '$' . $user_credits_use->credits : ''; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $user_credits_use->comments; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $user_credits_use->datetime; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $user_credits_use->username; ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td>
                                    Current credits value: <input type="text" name="user_credits" value="<?php echo $user_credits !== NULL ? $user_credits->credits : 0; ?>" size="5">
                                </td>
                            </tr>
                        </table>
                        <table class="adminform">
                            <tr>
                                <th colspan="1">
                                    <?php echo 'Parameters'; ?>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <?php //echo $params->_form_editor_list('editor', '', 'params');  ?>
                                    <?php //echo $params->render();  ?>
                                </td>
                            </tr>
                        </table>

                        <?php
                        if (!$contact) {
                            ?>
                            <table class="adminform">
                                <tr>
                                    <th>
                                        Contact Information
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <br />
                                        No Contact details linked to this User:
                                        <br />
                                        See 'Components -> Contact -> Manage Contacts' for details.
                                        <br /><br />
                                    </td>
                                </tr>
                            </table>
                            <?php
                        } else {
                            ?>
                            <table class="adminform">
                                <tr>
                                    <th colspan="2">
                                        Contact Information
                                    </th>
                                </tr>
                                <tr>
                                    <td width="15%">
                                        Name:
                                    </td>
                                    <td>
                                        <strong>
                                            <?php echo $contact[0]->name; ?>
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Position:
                                    </td>
                                    <td >
                                        <strong>
                                            <?php echo $contact[0]->con_position; ?>
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Telephone:
                                    </td>
                                    <td >
                                        <strong>
                                            <?php echo $contact[0]->telephone; ?>
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Fax:
                                    </td>
                                    <td >
                                        <strong>
                                            <?php echo $contact[0]->fax; ?>
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td >
                                        <strong>
                                            <?php echo $contact[0]->misc; ?>
                                        </strong>
                                    </td>
                                </tr>
                                <?php
                                if ($contact[0]->image) {
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td valign="top">
                                            <img src="<?php echo $mosConfig_live_site; ?>/images/stories/<?php echo $contact[0]->image; ?>" align="middle" alt="Contact" />
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td colspan="2">
                                        <br /><br />
                                        <input class="button" type="button" value="change Contact Details" onclick="javascript: gotocontact('<?php echo $contact[0]->id; ?>')">
                                        <i>
                                            <br />
                                            'Components -> Contact -> Manage Contacts'.
                                        </i>
                                    </td>
                                </tr>
                            </table>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>

            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="contact_id" value="" />
            <?php
            if (!$canEmailEvents) {
                ?>
                <input type="hidden" name="sendEmail" value="0" />
                <?php
            }
            ?>
        </form>
        <style>
            #radioBtn .notActive{
                color: #3276b1;
                background-color: #fff;
            }
            .show_block_history{
                cursor: pointer;
                margin-bottom: 10px;
                display: block
            }
        </style>
        <?php
    }

}
?>