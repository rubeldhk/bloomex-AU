<?php
/**
 * @version $Id: admin.Category.html.php 10002 2008-02-08 10:56:57Z willebil $
 * @package Joomla
 * @subpackage Category
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
 * @package Joomla
 * @subpackage Category
 */
class HTML_StripeOrdersLogs {

    static function showStripeOrdersLogs( &$rows, &$pageNav, $option, $user_email,$order_id,$order_status,$transaction_id,$session_id,$paid_users_count,$leave_users_count,$came_back_users_count,$first_time_payed_users_count  ) {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Stripe Orders Logs</th>
                </tr>
                <tr>
                    <td align="left" style="padding:0px 20px 10px 0px;">
                        <p>Total Paid Users : <?php echo $paid_users_count??0;?></p>
                        <p>Total First Time Paid Users : <?php echo $first_time_payed_users_count??0;?></p>
                        <p>Users Came Back And Pay: <?php echo $came_back_users_count??0;?></p>
                        <p>Users Leave without Paying: <?php echo $leave_users_count??0;?></p>
                    </td>
                </tr>
                <tr>
                    <td align="right" style="padding:0px 20px 10px 0px;">
                        <b>Filter By:&nbsp;</b>
                        user_email : <input type="text" value="<?php echo $user_email;?>" name="user_email" size="30" />
                        order_id : <input type="text" value="<?php echo $order_id;?>" name="order_id" size="30" />
                        transaction_id : <input type="text" value="<?php echo $transaction_id;?>" name="transaction_id" size="30" />
                        session_id : <input type="text" value="<?php echo $session_id;?>" name="session_id" size="30" />
                        order_status :
                        <select  name="order_status">
                            <option value="">--select payment status--</option>
                            <option value="paid" <?php echo ($order_status=='paid')?'selected':''; ?>>paid</option>
                            <option value="pending_stripe" <?php echo ($order_status=='pending_stripe')?'selected':''; ?>>pending_stripe</option>
                            <option value="canceled" <?php echo ($order_status=='canceled')?'selected':''; ?>>canceled</option>
                        </select>
                        <input type="submit" class="button" value="filter" />
                    </td>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th width="40">#</th>
                    <th >User Id</th>
                    <th >User Name</th>
                    <th >User Email</th>
                    <th >Order Total</th>
                    <th >Payment Status</th>
                    <th >Order Id</th>
                    <th >Log Datetime</th>
                </tr>
                <?php
                $k = 0;
                for ($i=0, $n=count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);
                    $link 	= 'index2.php?option=com_stripe_orders_logs&task=details&id='. $row->id;

                    ?>
                    <tr align="center" class="<?php echo "row$k"; ?>">
                        <td><a href="<?php echo $link; ?>"><?php echo  $row->id ; ?></a></td>
                        <td><?php echo $row->user_id; ?></td>
                        <td><?php echo $row->user_name; ?></td>
                        <td><?php echo $row->user_email; ?></td>
                        <td><?php echo $row->order_total; ?></td>
                        <td><?php echo $row->order_status; ?></td>
                        <td><?php echo $row->order_id; ?></td>
                        <td><?php echo $row->date_added; ?></td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="act" value="" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <?php
    }


    static function showDetails( &$row, $option ) {
        global $mosConfig_live_site, $mosConfig_absolute_path;
        mosCommonHTML::loadBootstrap();
        ?>

        <table class="adminheading">
            <tr>
                <th>
                    Stripe Log: #<?php echo $row->id; ?>
                </th>
            </tr>
        </table>

        <table width="100%" border="1" class="adminform">
            <tr>
                <th colspan="2">Stripe Order Details</th>
            <tr>
            <tr>
                <td width="15%"><b>User Id:</b></td>
                <td><?php echo $row->user_id; ?></td>
            </tr>
            <tr>
                <td width="15%"><b>User Name:</b></td>
                <td><?php echo $row->user_name; ?></td>
            </tr>

            <tr>
                <td width="15%"><b>User Email:</b></td>
                <td><?php echo $row->user_email; ?></td>
            </tr>

            <tr>
                <td width="15%"><b>Order Total:</b></td>
                <td><?php echo $row->order_total; ?></td>
            </tr>

            <tr>
                <td width="15%"><b>Order Id:</b></td>
                <td><?php echo $row->order_id; ?></td>
            </tr>

            <tr>
                <td width="15%"><b>Payment Status:</b></td>
                <td><?php echo $row->order_status; ?></td>
            </tr>

            <tr>
                <td width="15%"><b>Transaction  Details:</b></td>
                <td><?php echo $row->transaction_details; ?></td>
            </tr>

            <tr>
                <td width="15%"><b>Order Data:</b></td>
                <td><?php echo "<pre>"; print_r(unserialize($row->order_data)); ?></td>
            </tr>



        </table>
        <input type="hidden" name="option" value="<?php echo $option; ?>" />
        <input type="hidden" name="act" value="" />
        <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
        <input type="hidden" name="task" value="" />

        <?php
    }

}
?>
