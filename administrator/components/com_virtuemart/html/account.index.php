<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: account.index.php,v 1.7.2.1 2006/03/10 15:55:15 soeren_nb Exp $
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
mm_showMyFileName(__FILE__);

if(isset($_REQUEST['session_id'])){
    if(isset($_REQUEST['order_id'])){
        setStripeOrderAsPaid();
        mosRedirect('/account?mosmsgsuccess=true&mosmsg=Payment executed by Stripe Successfully');
    }elseif(isset($_REQUEST['bulk_id'])){
        setStripeBulkOrdersAsPaid();
        mosRedirect('/account?mosmsgsuccess=true&mosmsg=Payment executed by Stripe Successfully');
    }
}


require_once(CLASSPATH . 'ps_order.php');
$ps_order = new ps_order;
require_once(CLASSPATH . 'ps_perm.php');
$ps_perm = new ps_perm;
/* Set Dynamic Page Title when applicable */
$mainframe->setPageTitle($VM_LANG->_PHPSHOP_ACCOUNT_TITLE);
if ($perm->is_registered_customer($auth['user_id'])) {
    global $mosConfig_live_site, $iso_client_lang,$sef;
    ?>
    <div class="container white">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> 
                <table border="0" cellspacing="0" cellpadding="10" width="100%" align="center">
                    <tr>
                        <td>
                            <p class="pull-left"><strong>Hi </strong><?php echo $auth["first_name"] . " " . $auth["last_name"]." (".$auth["username"].")"; ?></p>

                            <form style="color: rgb(102, 51, 102);" action="/logout/" method="post" name="login" id="login">
                                <input type="submit" class="new_checkout_login_button btn btn-danger float-end account_button" name="Submit" value="<?php echo $VM_LANG->_PHPSHOP_LOGOUT_TITLE ?>">
                                <a href="/update-password/" target="_blank"><input type="button" class="btn btn-success float-end mr-10 account_button" value="<?php echo _UPDATE_PASSWORD ?>"></a>
                                <input type="hidden" name="op2" value="logout">
                                <input type="hidden" name="option" value="logout">
                                <input type="hidden" name="return" value="/<?php echo $sef->real_uri; ?>/" />
                                <input type="hidden" name="lang" value="<?php echo $iso_client_lang; ?>">
                                <input type="hidden" name="message" value="1">
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px;">
                            <div style="display: flex; justify-content: flex-start; gap: 40px; flex-wrap: wrap;">
                                <div style="text-align: center;">
                                    <b><?php echo $VM_LANG->_check_bucks; ?></b><br/>
                                    <a href="/account/your-bloomex-buck-history/">
                                        <img src="/images/bucks.png" alt="bucks" height="50" style="display: block; margin: 0 auto;">
                                    </a>
                                </div>
                                <div style="text-align: center;">
                                    <b><?php echo $VM_LANG->_check_addresses; ?></b><br/>
                                    <a href="/account/addresses/">
                                        <img src="/images/addresses.png" alt="addresses" height="50" style="display: block; margin: 0 auto;">
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr><td>&nbsp;</td></tr>

                    <tr>
                        <td class="account_table">
                            <hr />
                            <strong style="margin-bottom: 10px;display: block"><?php
                                echo "<img src=\"" . IMAGEURL . "ps_image/package.png\" align=\"middle\" height=\"32\" width=\"32\" border=\"0\" alt=\"" . $VM_LANG->_PHPSHOP_ACC_ORDER_INFO . "\" />&nbsp;&nbsp;&nbsp;";
                                echo $VM_LANG->_PHPSHOP_AFFILIATE_LIST_ORDERS
                                ?>
                            </strong>
                            <?php $ps_order->list_order("A", "1"); ?>
                        </td>
                    </tr>

                </table>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
    const logoutButton = document.querySelector("form#login .new_checkout_login_button");

        if (logoutButton) {
            document.getElementById("login").addEventListener("submit", function (event) {
                logoutButton.disabled = true;
                logoutButton.textContent = "Logging out...";
            });
        }
    });

    </script>
    <!-- Body ends here -->
    <?php
} else {
    include(PAGEPATH . 'checkout.login_form.php');
}
?>