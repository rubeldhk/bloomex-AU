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


/* Set Dynamic Page Title when applicable */
$mainframe->setPageTitle($VM_LANG->_PHPSHOP_ACCOUNT_TITLE);
if ($perm->is_registered_customer($auth['user_id'])) {
    global $mosConfig_live_site, $iso_client_lang;
    ?>
    <div class="container white">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> 
                <table border="0" cellspacing="0" cellpadding="10" width="100%" align="center">
                    <tr>
                        <td>
                            <strong><?php echo $VM_LANG->_PHPSHOP_ACC_CUSTOMER_ACCOUNT ?></strong>
                            <?php echo $auth["first_name"] . " " . $auth["last_name"]; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
            <?php
                            $query_bucks = "SELECT bucks FROM `tbl_bucks`  WHERE user_id = ".$auth['user_id'];
                            $database->setQuery($query_bucks);
                            $bucks = $database->loadResult();
                            $bloomex_bucks = 'There is no Bucks';
                            if($bucks){
                            $bucks =  number_format($bucks, 2, ".", "");
                            $query = "SELECT * FROM `tbl_bucks_history`  WHERE user_id = ".$auth['user_id'];
                            $database->setQuery($query);
                            $bucks_history = $database->loadObjectList();
                            if($bucks_history){
                            $bloomex_bucks = "
                            <div id='bucks_history'>
                                <table class=\"w-100 table  table-striped  table-bordered\" border='1'>
                                    <tr style='text-align: center;background: #e6e1e9;'>
                                        <th>Order Id</th>
                                        <th>Used Bucks</th>
                                        <th>Comment</th>
                                        <th>Date</th>
                                    </tr>";

                                    foreach($bucks_history as $o){
                                    $bloomex_bucks .=
                                    "<tr style='text-align: center'>
                                        <td>$o->order_id</td>
                                        <td>$o->used_bucks</td>
                                        <td>$o->comment</td>
                                        <td>$o->date_added</td>
                                    </tr>";
                                    }
                                    $bloomex_bucks .= "
                                </table>
                            </div>"; }
                            $bloomex_bucks .= "<div id='bucks' style='font-size: 14px;font-weight: bold;margin: 5px;color: #c46055;'>Current Bloomex Bucks accumulated is: $".$bucks."</div>";

                            }
                            echo $bloomex_bucks;
                            ?>
                        </td>
                    </tr>

                </table>
        </div>
    </div>
</div>
    <!-- Body ends here -->
    <?php
} else {
    include(PAGEPATH . 'checkout.login_form.php');
}
?>
